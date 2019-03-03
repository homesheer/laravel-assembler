<?php

namespace HomeSheer\LaravelAssembler\Test;

use HomeSheer\LaravelAssembler\Assembler;
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

}
