<?php
/**
 * Created by PhpStorm.
 * User: developergeorg
 * Date: 24.03.18
 * Time: 22:27
 */

namespace App\Tariff\Api;


class Price implements \JsonSerializable
{
    /**
     * @var float
     */
    protected $priceOnce;
    /**
     * @var float
     */
    protected $priceMinute;
    /**
     * @var float
     */
    protected $priceDistance;

    /**
     * @var Price[]
     */
    protected $extraPriceList = [];

    public function __construct($priceOnce = 0.0, $priceMinute = 0.0, $priceDistance = 0.0)
    {
        $this->priceOnce = (float)$priceOnce;
        $this->priceMinute = (float)$priceMinute;
        $this->priceDistance = (float)$priceDistance;
    }

    public function withExtraPrice(Price $price)
    {
        $this->extraPriceList[] = $price;
        return $this;
    }

    /**
     * @return float
     */
    public function getPriceOnce()
    {
        $price = $this->priceOnce;
        foreach ($this->extraPriceList as $extraPrice) {
            $price += $extraPrice->getPriceOnce();
        }
        return $price;
    }

    /**
     * @return float
     */
    public function getPriceMinute()
    {
        $price = $this->priceOnce;
        foreach ($this->extraPriceList as $extraPrice) {
            $price += $extraPrice->getPriceMinute();
        }
        return $price;
    }

    /**
     * @return float
     */
    public function getPriceDistance()
    {
        $price = $this->priceOnce;
        foreach ($this->extraPriceList as $extraPrice) {
            $price += $extraPrice->getPriceDistance();
        }
        return $price;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'priceOnce' => $this->getPriceOnce(),
            'priceMinute' => $this->getPriceMinute(),
            'priceDistance' => $this->getPriceDistance(),
        ];
    }

}