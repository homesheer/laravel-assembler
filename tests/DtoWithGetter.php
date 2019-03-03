<?php


namespace HomeSheer\LaravelAssembler\Test;

class DtoWithGetter
{
    private $name = 'test dto with getter';

    private $gender = 1;

    private $age = 18;

    private $address = null;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getGender(): int
    {
        return $this->gender;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return new Address;
    }

}
