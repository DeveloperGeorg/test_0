<?php
/**
 * Created by PhpStorm.
 * User: developergeorg
 * Date: 25.03.18
 * Time: 21:19
 */

namespace App\Tariff\Api;

class PriceFactory
{
    /**
     * @param string $code
     * @param float $price
     * @return Price
     */
    public static function getInstance($code, $price)
    {
        switch ($code) {
            case 'price_once':
                return new Price($price);
                break;
            case 'price_minute':
                return new Price(0.0, $price);
                break;
            case 'price_distance':
                return new Price(0.0, 0.0, $price);
                break;
            default:
                throw new \InvalidArgumentException('Неверный код типа цены');
        }
    }
}