<?php
/**
 * Created by PhpStorm.
 * User: developergeorg
 * Date: 24.03.18
 * Time: 21:54
 */

namespace App\Tariff\Api;


class Tariff implements \JsonSerializable
{

    /**
     * @var int
     */
    protected $distanceStart;
    /**
     * @var int
     */
    protected $distanceEnd;
    /**
     * @var int
     */
    protected $minuteStart;
    /**
     * @var int
     */
    protected $minuteEnd;
    /**
     * @var Price
     */
    protected $price;

    public function __construct(
        Price $price,
        $distanceStart = 0,
        $distanceEnd = 0,
        $minuteStart = 0,
        $minuteEnd = 0
    )
    {
        $this->price = $price;
        $this->distanceStart = $distanceStart;
        $this->distanceEnd = $distanceEnd;
        $this->minuteStart = $minuteStart;
        $this->minuteEnd = $minuteEnd;
    }


    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'price' => $this->price,
            'distanceStart' => $this->distanceStart,
            'distanceEnd' => $this->distanceEnd,
            'minuteStart' => $this->minuteStart,
            'minuteEnd' => $this->minuteEnd,
        ];
    }
}