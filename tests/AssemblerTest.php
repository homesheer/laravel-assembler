<?php

namespace HomeSheer\LaravelAssembler\Test;

use HomeSheer\LaravelAssembler\Assembler;
use HomeSheer\LaravelAssembler\Test\Stubs\DtoWithGetter;
use HomeSheer\LaravelAssembler\Test\Stubs\DtoWithoutGetter;
use PHPUnit\Framework\TestCase;

class AssemblerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testDtoWithGetter()
    {
        $dtoWithGetter = new DtoWithGetter;
        $assembler = new Assembler($dtoWithGetter);
        $assembler->setFields('{name,age,gender,address{province,city}}');
        print_r($assembler->getAssembledData());
    }

    /**
     * @throws \Exception
     */
    public function testDtoWithoutGetter()
    {
        $dtoWithoutGetter = new DtoWithoutGetter;
        $assembler = new Assembler($dtoWithoutGetter);
        $assembler->setFields('{name,age,gender,address{province,city}}');
        print_r($assembler->getAssembledData());
    }

    /**
     * @throws \Exception
     */
    public function testDtoIsArray()
    {
        $dto = [
            'name'    => 'test dto is array',
            'age'     => 18,
            'gender'  => 1,
            'address' => [
                'province' => 'GuangDong',
                'city'     => 'GuangZhou',
            ],
        ];

        $assembler = new Assembler($dto);
        $assembler->setFields('{name,age,gender,address{province,city}}');
        print_r($assembler->getAssembledData());
    }

}
