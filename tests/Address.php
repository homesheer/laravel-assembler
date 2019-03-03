<?php

namespace HomeSheer\LaravelAssembler\Test;

class Address
{
    private $province = 'GuangDong';

    private $city = 'GuangZhou';

    /**
     * @return string
     */
    public function getProvince(): string
    {
        return $this->province;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

}
