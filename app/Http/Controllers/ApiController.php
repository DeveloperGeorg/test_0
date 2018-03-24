<?php

namespace App\Http\Controllers;

use App\Tariff\Api\Price;
use App\Tariff\Api\PriceLimit;
use App\Tariff\Api\PriceTypeEnum;
use App\Tariff\Api\Stage;
use App\Tariff\Api\Tariff;
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
        $timestamp = (int)$request->get('timestamp');
        $time = new \DateTime();
        if ($timestamp > 0) {
            $time = new \DateTime($timestamp);
        }

        //@todo Выборка и формирование по принципу как сделано ниже

        $stageList = [
            new Stage(
                'Бронирование',
                'reservation',
                [
                    new Tariff(
                        new Price(0.0, 0.0),
                        0,0,0,0
                    ),
                    new Tariff(
                        new Price(0.0, 2.0),
                        0,0,20,0
                    )
                ]
            ),
            new Stage(
                'Вождение',
                'driving',
                [
                    new Tariff(
                        (new Price(0.0, 5.0))
                        ->withExtraPrice(new Price(300.0, 2.0)),
                        0,0,0,0
                    )
                ]
            )
        ];

        $priceLimitList = [
            new PriceLimit(
                'default',
                2700.0,
                0,
                70
            )
        ];

        return [
            'stageList' => $stageList,
            'priceLimitList' => $priceLimitList,
            'userId' => $request->get('userId')
        ];
    }
}
