<?php


namespace HomeSheer\LaravelAssembler\Test\Stubs;

class DtoWithoutGetter
{
    public $name = 'test dto without getter';

    public $gender = 1;

    public $age = 18;

    public $address = null;

    public function __construct()
    {
        $this->address = new AddressWithoutGetter;
    }

}
