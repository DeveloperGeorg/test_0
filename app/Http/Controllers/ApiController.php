<?php

namespace App\Http\Controllers;

use App\Tariff\Api\Price;
use App\Tariff\Api\PriceFactory;
use App\Tariff\Api\PriceLimit;
use App\Tariff\Api\PriceTypeEnum;
use App\Tariff\Api\Stage;
use App\Tariff\Api\Tariff;
use App\Tariff\Model\CarToType;
use App\Tariff\Model\PriceLimitation;
use App\Tariff\Model\PriceLimitationRelation;
use App\Tariff\Model\PriceLimitType;
use App\Tariff\Model\PriceType;
use App\Tariff\Model\TariffRelation;
use App\Tariff\Model\UserToType;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index()
    {
        return 'ok';
    }

    public function getTariff(Request $request)
    {
        try {
            $userId = (int)$request->get('userId');
            if ($userId <= 0) {
                throw new \InvalidArgumentException('Неверный идентификатор пользователя');
            }
            $carId = (int)$request->get('carId');
            if ($carId <= 0) {
                throw new \InvalidArgumentException('Неверный идентификатор автомобиля');
            }
            $stageMinutesLeft = (int)$request->get('stageMinutesLeft');
            $stageDistanceLeft = (int)$request->get('stageDistanceLeft');
            $requestStageCode = trim((string)$request->get('stageCode'));
            $timestamp = (int)$request->get('timestamp');
            $timestampNow = new \DateTime();
            if ($timestamp > 0) {
                $timestampNow->setTimestamp($timestamp);
            }
            $timestampStart = new \DateTime('today');
            $minutes = (int)(($timestampNow->getTimestamp() - $timestampStart->getTimestamp()) / 60);

            //Stages
            $requestStageId = 0;
            if ($requestStageCode) {
                $dbStageList = (new \App\Tariff\Model\Stage)->where('code', $requestStageCode)
                    ->orderBy('id', 'asc')
                    ->get();
                $requestStageId = $dbStageList->first()->id;
            } else {
                $dbStageList = \App\Tariff\Model\Stage::all();
            }

            //UserTypes
            $userTypeList = [];
            $dbUserTypeList = $this->getUserTypeIdList($userId);
            foreach ($dbUserTypeList as $dbUserType) {
                $userTypeList[] = $dbUserType->user_type_id;
            }

            //carTypes
            $carTypeList = [];
            $dbCarTypeList = $this->getDbCarTypeList($carId);
            foreach ($dbCarTypeList as $dbCarType) {
                $carTypeList[] = $dbCarType->id;
            }

            //tariffIds
            $dbTariffIdList = $this->getDbTariffRelationCollection($dbUserTypeList, $dbUserTypeList, $requestStageId);
            $tariffRelationList = [];
            foreach ($dbTariffIdList as $dbTariff) {
                $tariffRelationList[$dbTariff->tariff_id][] = (int)$dbTariff->stage_id;
            }

            //tariff
            $tariffKeyRelationList = [];
            $dbTariffList = $this->getDbTariffCollection($dbTariffIdList, $minutes, $stageMinutesLeft, $stageDistanceLeft);

            foreach ($dbTariffList as $dbTariff) {
                $tmpStage = [];
                foreach ($tariffRelationList[$dbTariff->id] as $stageId) {
                    if (!in_array($stageId, $tmpStage)) {
                        $key = $dbTariff->minute_start . '-' . $dbTariff->minute_end . '-' . $dbTariff->distance_start . '-' . $dbTariff->distance_end;
                        $tariffKeyRelationList[$stageId][$key][] = $dbTariff;
                        $tmpStage[] = $stageId;
                    }
                }
            }


            //priceLimitationIds
            $priceLimitationIdList = [];
            $priceLimitationRelationIdList = [];
            $dbPriceLimitationIdList = $this->getDbPriceLimitationRelationCollection($dbUserTypeList, $dbCarTypeList, $requestStageId);
            foreach ($dbPriceLimitationIdList as $dbPriceLimitation) {
                $priceLimitationIdList[] = $dbPriceLimitation->price_limitation_id;
                $priceLimitationRelationIdList[(int)$dbPriceLimitation->stage_id][] = $dbPriceLimitation;
            }

            //priceLimitations
            $priceLimitationList = [];
            $dbPriceLimitationList = $this->getDbPriceLimitationCollection($dbPriceLimitationIdList);
            foreach ($dbPriceLimitationList as $dbPriceLimitation) {
                $priceLimitationList[(int)$dbPriceLimitation->id] = $dbPriceLimitation;
            }

            //Генерация ответа Rest API
            return [
                'status' => 1,
                'stageList' => $this->getRestApiStageList(
                    $dbStageList,
                    $dbTariffList,
                    $dbTariffIdList,
                    $priceLimitationRelationIdList,
                    $priceLimitationList
                ),
                'priceLimitList' => $this->getRestApiPriceLimitList(
                    $priceLimitationRelationIdList,
                    $priceLimitationList
                )
            ];
        } catch (\Exception $exception) {
            return [
                'status' => 0,
                'message' => $exception->getMessage()
            ];
        }
    }

    /**
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private function getUserTypeIdList($userId)
    {
        return (new UserToType())
            ->where('user_id', $userId)
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * @param int $carId
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private function getDbCarTypeList($carId)
    {
        return (new CarToType)
            ->where('car_id', $carId)
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * @param $dbUserTypeList
     * @param $dbCarTypeList
     * @param $requestStageId
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private function getDbTariffRelationCollection($dbUserTypeList, $dbCarTypeList, $requestStageId)
    {
        $userTypeList = [];
        foreach ($dbUserTypeList as $dbUserType) {
            $userTypeList[] = $dbUserType->user_type_id;
        }
        $carTypeList = [];
        foreach ($dbCarTypeList as $dbCarType) {
            $carTypeList[] = $dbCarType->id;
        }

        $dbTeriff = new TariffRelation();
        $dbTeriff
            ->whereIn('user_type_id', $userTypeList)
            ->whereIn('car_type_id', $carTypeList);
        if ($requestStageId > 0) {
            $dbTeriff->where('stage_id', [0, $requestStageId]);
        }
        return $dbTeriff
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * @param $dbTariffIdList
     * @param $minutes
     * @param $stageMinutesLeft
     * @param $stageDistanceLeft
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private function getDbTariffCollection($dbTariffIdList, $minutes, $stageMinutesLeft, $stageDistanceLeft)
    {
        $tariffRelationList = [];
        foreach ($dbTariffIdList as $dbTariff) {
            $tariffRelationList[] = (int)$dbTariff->tariff_id;
        }

        $dbTariff = \DB::table('tariffs');
        $dbTariff
            ->whereIn('id', $tariffRelationList)
            ->whereRaw('time_start <= ' . $minutes)
            ->whereRaw('time_end >= ' . $minutes);
        if ($stageMinutesLeft > 0) {
            $dbTariff
                ->where('minute_start', '<=', $stageMinutesLeft)
                ->where('minute_end', '>=', $stageMinutesLeft);
        }
        if ($stageDistanceLeft > 0) {
            $dbTariff
                ->where('distance_start', '<=', $stageDistanceLeft)
                ->where('distance_end', '>=', $stageDistanceLeft);

        }
        return $dbTariff
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * @param $dbUserTypeList
     * @param $dbCarTypeList
     * @param $requestStageId
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private function getDbPriceLimitationRelationCollection($dbUserTypeList, $dbCarTypeList, $requestStageId)
    {
        $userTypeList = [];
        foreach ($dbUserTypeList as $dbUserType) {
            $userTypeList[] = $dbUserType->user_type_id;
        }
        $carTypeList = [];
        foreach ($dbCarTypeList as $dbCarType) {
            $carTypeList[] = $dbCarType->id;
        }

        $dbPriceLimitationRelation = new PriceLimitationRelation();
        $dbPriceLimitationRelation
            ->whereIn('user_type_id', $userTypeList)
            ->whereIn('car_type_id', $carTypeList);

        if ($requestStageId > 0) {
            $dbPriceLimitationRelation->where('stage_id', [0, $requestStageId]);
        }

        return $dbPriceLimitationRelation
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * @param $dbPriceLimitationIdList
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private function getDbPriceLimitationCollection($dbPriceLimitationIdList)
    {
        $priceLimitationIdList = [];
        foreach ($dbPriceLimitationIdList as $dbPriceLimitation) {
            $priceLimitationIdList[] = $dbPriceLimitation->price_limitation_id;
        }
        return (new PriceLimitation())->whereIn('id', $priceLimitationIdList)
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * @return array
     */
    private function getPriceTypeList()
    {
        $priceTypeList = [];
        $dbPriceTypeList = PriceType::all();
        foreach ($dbPriceTypeList as $item) {
            $priceTypeList[$item->id] = $item;
        }
        return $priceTypeList;
    }

    /**
     * @return array
     */
    private function getPriceLimitTypeList()
    {
        static $priceLimitTypeList = [];

        if (empty($priceLimitTypeList)) {
            $dbPriceLimitTypeList = PriceLimitType::all();
            foreach ($dbPriceLimitTypeList as $dbPriceLimitType) {
                $priceLimitTypeList[$dbPriceLimitType->id] = $dbPriceLimitType;
            }
        }
        return $priceLimitTypeList;
    }

    private function getRestApiStageList($dbStageList, $dbTariffList, $dbTariffIdList, $priceLimitationRelationIdList, $priceLimitationList)
    {
        $priceTypeList = $this->getPriceTypeList();
        $priceLimitTypeList = $this->getPriceLimitTypeList();

        $tariffRelationList = [];
        foreach ($dbTariffIdList as $dbTariff) {
            $tariffRelationList[$dbTariff->tariff_id][] = (int)$dbTariff->stage_id;
        }
        foreach ($dbTariffList as $dbTariff) {
            $tmpStage = [];
            foreach ($tariffRelationList[$dbTariff->id] as $stageId) {
                if (!in_array($stageId, $tmpStage)) {
                    $key = $dbTariff->minute_start . '-' . $dbTariff->minute_end . '-' . $dbTariff->distance_start . '-' . $dbTariff->distance_end;
                    $tariffKeyRelationList[$stageId][$key][] = $dbTariff;
                    $tmpStage[] = $stageId;
                }
            }
        }

        $stageList = [];
        foreach ($dbStageList as $dbStage) {
            $tariffs = [];
            if (!empty($tariffKeyRelationList[(int)$dbStage->id])) {
                $priceToKey = [];
                foreach ($tariffKeyRelationList[(int)$dbStage->id] as $key => $tariffPrices) {
                    foreach ($tariffPrices as $tariffPrice) {
                        if (!array_key_exists($key, $priceToKey)) {
                            $priceToKey[$key] = PriceFactory::getInstance(
                                $priceTypeList[$tariffPrice->price_type_id]->code,
                                $tariffPrice->price
                            );
                        } elseif ($priceToKey[$key] instanceof Price) {
                            $priceToKey[$key]->withExtraPrice(PriceFactory::getInstance(
                                $priceTypeList[$tariffPrice->price_type_id]->code,
                                $tariffPrice->price
                            ));
                        }
                    }
                }
                foreach ($tariffKeyRelationList[(int)$dbStage->id] as $key => $tariffPrices) {
                    $tariff = reset($tariffPrices);
                    $key = $tariff->minute_start . '-' . $tariff->minute_end . '-' . $tariff->distance_start . '-' . $tariff->distance_end;
                    $tariffs[] = new Tariff(
                        $priceToKey[$key],
                        $tariff->distance_start,
                        $tariff->distance_end,
                        $tariff->minute_start,
                        $tariff->minute_end
                    );
                }
            }

            $priceLimits = [];
            if (!empty($priceLimitationRelationIdList[(int)$dbStage->id])) {
                foreach ($priceLimitationRelationIdList[(int)$dbStage->id] as $priceLimitationRelation) {
                    $priceLimitation = $priceLimitationList[$priceLimitationRelation->price_limitation_id];
                    $priceLimits[] = new PriceLimit(
                        $priceLimitTypeList[$priceLimitation->price_limit_type_id]->code,
                        $priceLimitation->price,
                        $priceLimitation->distance_start,
                        $priceLimitation->distance_end
                    );
                }
            }

            $stageList[] = new Stage(
                $dbStage->name,
                $dbStage->code,
                $tariffs,
                $priceLimits
            );
        }
        return $stageList;
    }

    /**
     * @param $priceLimitationRelationIdList
     * @param $priceLimitationList
     * @return array
     */
    private function getRestApiPriceLimitList($priceLimitationRelationIdList, $priceLimitationList)
    {
        $priceLimitTypeList = $this->getPriceLimitTypeList();
        $priceLimitList = [];
        if (!empty($priceLimitationRelationIdList[0])) {
            foreach ($priceLimitationRelationIdList[0] as $priceLimitationRelation) {
                $priceLimitation = $priceLimitationList[$priceLimitationRelation->price_limitation_id];
                $priceLimitList[] = new PriceLimit(
                    $priceLimitTypeList[$priceLimitation->price_limit_type_id]->code,
                    $priceLimitation->price,
                    $priceLimitation->distance_start,
                    $priceLimitation->distance_end
                );
            }
        }
        return $priceLimitList;
    }
}
