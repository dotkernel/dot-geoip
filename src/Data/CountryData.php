<?php

declare(strict_types=1);

namespace Dot\GeoIP\Data;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * Class CountryData
 * @package Dot\GeoIP\Data
 */
class CountryData implements ArraySerializableInterface
{
    /** @var bool $isEuMember */
    protected $isEuMember;

    /** @var string $isoCode */
    protected $isoCode;

    /** @var string $name */
    protected $name;

    /**
     * @return bool
     */
    public function isEuMember(): bool
    {
        return $this->isEuMember;
    }

    /**
     * @param bool $isEuMember
     * @return CountryData
     */
    public function setIsEuMember(bool $isEuMember): self
    {
        $this->isEuMember = $isEuMember;

        return $this;
    }

    /**
     * @return string
     */
    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     * @return CountryData
     */
    public function setIsoCode(string $isoCode): self
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return CountryData
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param array $data
     * @return CountryData
     */
    public function exchangeArray(array $data): self
    {
        return $this->setIsEuMember($data['isEuMember'])->setIsoCode($data['isoCode'])->setName($data['name']);
    }

    /**
     * @return array
     */
    public function getArrayCopy(): array
    {
        return [
            'isEuMember' => $this->isEuMember(),
            'isoCode' => $this->getIsoCode(),
            'name' => $this->getName()
        ];
    }
}
