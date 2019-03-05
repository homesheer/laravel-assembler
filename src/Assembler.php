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
    public function __construct($dto = null)
    {
        $config = require __DIR__ . '/config/assembler.php';

        $this->setConfig($config);
        $this->setFields($this->getRequestFields());

        if (is_array($dto)) {
            $this->setDto($dto);
        } elseif (is_iterable($dto)) {
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
        if (function_exists('app')) {
            return app('request')->get($this->config['query_name'], '');
        }

        return '';
    }

    /**
     * @param $fields
     *
     * @throws \Exception
     */
    public function setFields($fields): void
    {
        if (is_array($fields)) {
            $this->fields = $fields;
        } elseif (is_string($fields) && $fields !== '') {
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
     * @throws \Exception
     */
    protected function parseFields(string $fields): array
    {
        if ($fields[0] !== '{' || $fields[mb_strlen($fields) - 1] !== '}') {
            throw new \Exception('the fields is not wrapped in "{" and "}"');
        }

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
        $len = mb_strlen($fields);
        $end = $len - 1;

        for ($i = 1; $i < $len; $i++) {
            $char = $fields[$i];

            if (empty($char) && $char !== '0') {
                continue;
            }

            switch ($char) {
                case '{':
                case '}':
                    if ($word) {
                        array_push($wordStack, $word);
                        $word = '';
                    }

                    if ($i !== $end) {
                        array_push($wordStack, $char);
                    }

                    break;

                case ',':
                    if ($word) {
                        array_push($wordStack, $word);
                        $word = '';
                    }

                    break;

                default:
                    $word .= $char;
            }
        }

        return $wordStack;
    }

    /**
     * @param array|Object $dto
     */
    public function setDto($dto): void
    {
        $this->dto = $dto;
    }

    /**
     * @return array|Object
     */
    public function getDto()
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
     * @param Object $assembler
     */
    public function setAssembler(Object $assembler): void
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
                if ($key === 0 && !$this->assembler && !is_subclass_of($this, Assembler::class)) {
                    $this->setAssemblerOnConfig($dto);
                }

                $this->setDto($dto);
                $assembledData[] = $this->assembleDataOfObject($this->fields, $this->getAssembler());
            }

            return $assembledData;
        } elseif (is_object($this->getDto())) {
            if (!$this->assembler && !is_subclass_of($this, Assembler::class)) {
                $this->setAssemblerOnConfig($this->getDto());
            }

            return $this->assembleDataOfObject($this->fields, $this->getAssembler());
        } else {
            return $this->assembleDataOfArray($this->fields, $this->getDto());
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
            $assembler = new $config['maps'][$dtoClassName]($dto);
            $this->setAssembler($assembler);
        }
    }

    /**
     * @param array  $fields
     * @param Object $assembler
     *
     * @return array
     */
    protected function assembleDataOfObject(array $fields, Object $assembler): array
    {
        $assembledData = [];

        foreach ($fields as $fieldName => $field) {
            $getter = $this->getConfig()['getter_name_prefix'] . $fieldName;

            if (method_exists($assembler, $getter)) {
                $data = $assembler->{$getter}();
            } elseif (method_exists($assembler, 'getDto') && method_exists($assembler->getDto(), $getter)) {
                $data = $assembler->getDto()->{$getter}();
            } elseif (method_exists($assembler, 'getDto')) {
                $data = $assembler->getDto()->{$fieldName};
            } else {
                $data = $assembler->{$fieldName};
            }

            if (is_array($field) && is_object($data)) {
                $assembledData[$fieldName] = $this->assembleDataOfObject($field, $data);
            } else {
                $assembledData[$fieldName] = $data;
            }
        }

        return $assembledData;
    }

    /**
     * @param array $fields
     * @param array $originalArray
     *
     * @return array
     */
    protected function assembleDataOfArray(array $fields, array $originalArray): array
    {
        $assembledData = [];

        foreach ($fields as $fieldName => $field) {
            $data = $originalArray[$fieldName];

            if (is_array($field) && is_object($data)) {
                $assembledData[$fieldName] = $this->assembleDataOfObject($field, $data);
            } elseif (is_array($field) && is_array($data)) {
                $assembledData[$fieldName] = $this->assembleDataOfArray($field, $data);
            } else {
                $assembledData[$fieldName] = $data;
            }
        }

        return $assembledData;
    }

}
