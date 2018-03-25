<?php

namespace App\Http\Controllers;

use App\Tariff\Api\Price;
use App\Tariff\Api\PriceFactory;
use App\Tariff\Api\PriceLimit;
use App\Tariff\Api\PriceTypeEnum;
use App\Tariff\Api\Stage;
use App\Tariff\Api\Tariff;
use App\Tariff\Model\CarToType;
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
        $userId = (int)$request->get('userId');
        $carId = (int)$request->get('carId');
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
            $dbStageList = \App\Tariff\Model\Stage::where('code', $requestStageCode)
                ->orderBy('id', 'asc')
                ->get();
            $requestStageId = $dbStageList->first()->id;
        } else {
            $dbStageList = \App\Tariff\Model\Stage::all();
        }
        $stageList = [];
        foreach ($dbStageList as $dbStage) {
            $stageList[] = [
                'id' => $dbStage->id,
                'name' => $dbStage->name,
                'code' => $dbStage->code,
            ];
        }

        //UserTypes
        $userTypeList = [];
        $dbUserTypeList = UserToType::where('user_id', $userId)
            ->orderBy('id', 'asc')
            ->get();
        foreach ($dbUserTypeList as $dbUserType) {
            $userTypeList[] = $dbUserType->user_type_id;
        }

        //carTypes
        $carTypeList = [];
        $dbCarTypeList = CarToType::where('car_id', $carId)
            ->orderBy('id', 'asc')
            ->get();
        foreach ($dbCarTypeList as $dbCarType) {
            $carTypeList[] = $dbCarType->id;
        }

        //tariffIds
        $tariffRelationList = [];
        $dbTeriff = new TariffRelation();
        $dbTeriff
            ->whereIn('user_type_id', $userTypeList)
            ->whereIn('car_type_id', $carTypeList);
        if ($requestStageId > 0) {
            $dbTeriff->where('stage_id', [0, $requestStageId]);
        }
        $dbTariffIdList = $dbTeriff
            ->orderBy('id', 'asc')
            ->get();
        foreach ($dbTariffIdList as $dbTariff) {
            $tariffRelationList[$dbTariff->tariff_id][] = (int)$dbTariff->stage_id;
        }

        //tariff
        $tariffKeyRelationList = [];
        //@todo добавить minute_start filter
        //@todo добавить minute_end filter
        //@todo добавить distance_start filter
        //@todo добавить distance_end filter
        $dbTariff = new \App\Tariff\Model\Tariff();
        $dbTariff
            ->whereIn('id', array_keys($tariffRelationList))
            ->where('time_start', '<', $minutes)
            ->where('time_end', '>', $minutes);
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
        $dbTariffList = $dbTariff
            ->orderBy('id', 'asc')
            ->get();

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
        $dbPriceLimitationRelation = new PriceLimitationRelation();
        $dbPriceLimitationRelation
            ->whereIn('user_type_id', $userTypeList)
            ->whereIn('car_type_id', $carTypeList);

        if ($requestStageId > 0) {
            $dbPriceLimitationRelation->where('stage_id', [0,$requestStageId]);
        }

        $dbPriceLimitationIdList = $dbPriceLimitationRelation
            ->orderBy('id', 'asc')
            ->get();
        foreach ($dbPriceLimitationIdList as $dbPriceLimitation) {
            $priceLimitationIdList[] = $dbPriceLimitation->price_limitation_id;
            $priceLimitationRelationIdList[(int)$dbPriceLimitation->stage_id][] = $dbPriceLimitation;
        }

        //priceLimitations
        $priceLimitationList = [];
        $dbPriceLimitationList = \App\Tariff\Model\PriceLimitation::whereIn('id', $priceLimitationIdList)
            ->orderBy('id', 'asc')
            ->get();
        foreach ($dbPriceLimitationList as $dbPriceLimitation) {
            $priceLimitationList[(int)$dbPriceLimitation->id] = $dbPriceLimitation;
        }

        $priceTypeList = [];
        $dbPriceTypeList = PriceType::all();
        foreach ($dbPriceTypeList as $item) {
            $priceTypeList[$item->id] = $item;
        }
        $priceLimitTypeList = [];
        $dbPriceLimitTypeList = PriceLimitType::all();
        foreach ($dbPriceLimitTypeList as $dbPriceLimitType) {
            $priceLimitTypeList[$dbPriceLimitType->id] = $dbPriceLimitType;
        }

        //Генерация ответа Rest API
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

        return [
            'stageList' => $stageList,
            'priceLimitList' => $priceLimitList,
            'userId' => $request->get('userId'),
            'time' => $timestampNow,
        ];
    }
}
