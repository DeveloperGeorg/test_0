<?php
/**
 * Created by PhpStorm.
 * User: developergeorg
 * Date: 25.03.18
 * Time: 1:12
 */

namespace App\Tariff\Api;


class PriceLimit implements \JsonSerializable
{
    protected $priceLimitType;
    protected $price;
    protected $distanceStart;
    protected $distanceEnd;
    /**
     * @var PriceLimit[]
     */
    protected $extraPriceLimitList = [];

    public function __construct(
        $priceLimitType,
        $price,
        $distanceStart = 0,
        $distanceEnd = 0
    )
    {
        $this->priceLimitType = (string)$priceLimitType;
        $this->price = (float)$price;
        $this->distanceStart = (int)$distanceStart;
        $this->distanceEnd = (int)$distanceEnd;
    }

    public function withExtraPriceLimit(PriceLimit $priceLimit)
    {
        $this->extraPriceLimitList[$priceLimit->getPriceLimitType()] = $priceLimit;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'priceLimitType' => $this->priceLimitType,
            'price' => $this->price,
            'distanceStart' => $this->distanceStart,
            'distanceEnd' => $this->distanceEnd,
        ];
    }

}