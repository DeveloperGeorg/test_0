<?php
/**
 * Created by PhpStorm.
 * User: developergeorg
 * Date: 24.03.18
 * Time: 21:29
 */

namespace App\Tariff\Api;


class Stage implements \JsonSerializable
{
    protected $name;
    protected $code;
    /**
     * @var Tariff[]
     */
    protected $tariffList;

    protected $priceLimitList;

    public function __construct($name, $code, array $tariffList, array $priceLimitList = [])
    {
        $this->name = $name;
        $this->code = $code;
        foreach ($tariffList as $tariff) {
            if ($tariff instanceof Tariff) {
                $this->tariffList[] = $tariff;
            }
        }

        foreach ($priceLimitList as $priceLimit) {
            if ($priceLimit instanceof PriceLimit) {
                $this->priceLimitList[] = $priceLimit;
            }
        }
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'tariffList' => $this->tariffList,
            'priceLimitList' => $this->priceLimitList,
        ];
    }
}