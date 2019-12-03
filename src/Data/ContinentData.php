<?php

declare(strict_types=1);

namespace Dot\GeoIP\Data;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * Class ContinentData
 * @package Dot\GeoIP\Data
 */
class ContinentData implements ArraySerializableInterface
{
    /** @var string $code */
    protected $code;

    /** @var string $name */
    protected $name;

    /**
     * @return null|string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return ContinentData
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ContinentData
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param array $data
     * @return ContinentData
     */
    public function exchangeArray(array $data): self
    {
        return $this->setName($data['name'])->setCode($data['code']);
    }

    /**
     * @return array
     */
    public function getArrayCopy(): array
    {
        return [
            'code' => $this->getCode(),
            'name' => $this->getName()
        ];
    }
}
