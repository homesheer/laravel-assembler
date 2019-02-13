<?php

namespace HomeSheer\LaravelAssembler;

class Assembler
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var \Traversable
     */
    protected $dtoCollection = [];

    /**
     * @var Object
     */
    protected $dto = null;

    /**
     * @var Assembler
     */
    protected $assembler = null;

    /**
     * Assembler constructor.
     *
     * @param $dto
     *
     * @throws \Exception
     */
    public function __construct($dto)
    {
        $this->setConfig(config('assembler'));
        $this->setFields($this->getRequestFields());

        if (is_iterable($dto)) {
            $this->setDtoCollection($dto);
        } else {
            $this->setDto($dto);
        }
    }

    /**
     * @param array $config
     *
     * @throws \Exception
     */
    public function setConfig(array $config): void
    {
        if (empty($config['query_name'])) {
            throw new \Exception('Invalid assembler config "query_name"');
        }

        if (!is_string($config['getter_name_prefix'])) {
            throw new \Exception('Invalid assembler config "getter_name_prefix"');
        }

        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return string
     */
    protected function getRequestFields(): string
    {
        return app('request')->get($this->config['query_name'], '');
    }

    /**
     * @param $fields
     */
    public function setFields($fields): void
    {
        if (is_array($fields)) {
            $this->fields = $fields;
        } else {
            $this->fields = $this->parseFields($fields);
        }
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param string $fields
     *
     * @return array
     */
    protected function parseFields(string $fields): array
    {
        return $this->assembleFields($this->parseFieldsToWordStack($fields));
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    protected function assembleFields(array $fields): array
    {
        $parsedFields = [];

        while (($word = array_shift($fields)) !== null) {
            if (current($fields) === '{') {
                $subFields = array_splice($fields, 0, array_search('}', $fields) + 1);
                $subFields = array_splice($subFields, 1, count($subFields));
                $parsedFields[$word] = $this->assembleFields($subFields);
                continue;
            }

            if ($word !== '}') {
                $parsedFields[$word] = $word;
            }
        }

        return $parsedFields;
    }

    /**
     * @param string $fields
     *
     * @return array
     */
    protected function parseFieldsToWordStack(string $fields): array
    {
        $wordStack = [];
        $word = '';
        $end = mb_strlen($fields) - 1;

        for ($i = 1; $i < $end; $i++) {
            $char = $fields[$i];

            if (empty($char)) {
                continue;
            }

            switch ($char) {
                case '{':
                case '}':
                    if ($word) {
                        array_push($wordStack, $word);
                        $word = '';
                    }

                    array_push($wordStack, $char);
                    break;

                case ',':
                    array_push($wordStack, $word);
                    $word = '';
                    break;

                default:
                    $word .= $char;
            }
        }

        return $wordStack;
    }

    /**
     * @param Object $dto
     */
    public function setDto(Object $dto): void
    {
        $this->dto = $dto;
    }

    /**
     * @return Object|null
     */
    public function getDto(): ?Object
    {
        return $this->dto;
    }

    /**
     * @param \Traversable $dtoCollection
     */
    public function setDtoCollection(\Traversable $dtoCollection): void
    {
        $this->dtoCollection = $dtoCollection;
    }

    /**
     * @return \Traversable
     */
    public function getDtoCollection()
    {
        return $this->dtoCollection;
    }

    /**
     * @param $assembler
     */
    public function setAssembler(\stdClass $assembler): void
    {
        $this->assembler = $assembler;
    }

    /**
     * @return Assembler
     */
    public function getAssembler(): self
    {
        return $this->assembler ?? $this;
    }

    /**
     * @return mixed
     */
    public function getAssembledData()
    {
        if (!$this->getFields()) {
            return $this->getDtoCollection() ?? $this->getDto();
        }

        if ($this->getDtoCollection()) {
            $assembledData = [];

            foreach ($this->getDtoCollection() as $key => $dto) {
                if ($key === 0 && !$this->assembler) {
                    $this->setAssemblerOnConfig($dto);
                }

                $this->setDto($dto);
                $assembledData[] = $this->assembleData($this->fields, $this->getAssembler());
            }

            return $assembledData;
        } else {
            if (!$this->assembler) {
                $this->setAssemblerOnConfig($this->getDto());
            }

            return $this->assembleData($this->fields, $this->getAssembler());
        }
    }

    /**
     * @param Object $dto
     */
    protected function setAssemblerOnConfig(Object $dto): void
    {
        $dtoClassName = get_class($dto);
        $config = $this->getConfig();

        if (isset($config['maps'][$dtoClassName])) {
            $assembler = new $config['maps'][$dtoClassName];
            $this->setAssembler($assembler);
        }
    }

    /**
     * @param array     $fields
     * @param Assembler $assembler
     *
     * @return array
     */
    protected function assembleData(array $fields, Assembler $assembler): array
    {
        $assembledData = [];

        foreach ($fields as $fieldName => $field) {
            $getter = $this->getConfig()['getter_name_prefix'] . $fieldName;

            if (method_exists($assembler, $getter)) {
                $data = $assembler->{$getter}();
            } elseif (method_exists($assembler->getDto(), $getter)) {
                $data = $assembler->getDto()->{$getter}();
            } else {
                $data = $assembler->getDto()->{$fieldName};
            }

            if (is_array($field) && isset($data)) {
                $assembledData[$fieldName] = $this->assembleData($field, $data);
            } else {
                $assembledData[$fieldName] = $data;
            }
        }

        return $assembledData;
    }

}
