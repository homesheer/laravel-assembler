<?php

namespace LaravelAssembler;

class Assembler
{
    /**
     * @var string
     */
    protected $fields;

    /**
     * @var Object
     */
    protected $dto;

    /**
     * @var Object
     */
    protected $assembler;

    /**
     * Assembler constructor.
     *
     * @param $dto
     *
     * @throws \Exception
     */
    public function __construct($dto)
    {
        $config = config('assembler');

        if (empty($config['query_name'])) {
            throw new \Exception('Invalid assembler config "query_name"');
        }

        $this->fields = request()->get($config['query_name']);

        $dtoClassName = get_class($dto);

        if (isset($config['maps'][$dtoClassName])) {
            $this->setAssembler(new $config['maps'][$dtoClassName]);
        } else {
            $this->assembler = $dto;
        }

        $this->dto = $dto;
    }

    public function setFields(string $fields)
    {
        $this->fields = $fields;
    }

    public function setAssembler($assembler)
    {
        $this->assembler = $assembler;
    }

    public function __get($name)
    {
        return $this->assembler->$name;
    }

}
