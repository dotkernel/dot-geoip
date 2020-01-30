<?php

declare(strict_types=1);

namespace Dot\GeoIP\Data;

use Laminas\Stdlib\ArraySerializableInterface;

/**
 * Class OrganizationData
 * @package Dot\GeoIP\Data
 */
class OrganizationData implements ArraySerializableInterface
{
    /** @var int $asn */
    protected $asn;

    /** @var string $name */
    protected $name;

    /**
     * @return int|null
     */
    public function getAsn(): ?int
    {
        return $this->asn;
    }

    /**
     * @param int $asn
     * @return OrganizationData
     */
    public function setAsn(int $asn): self
    {
        $this->asn = $asn;

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
     * @return OrganizationData
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param array $data
     * @return OrganizationData
     */
    public function exchangeArray(array $data): self
    {
        return $this->setAsn($data['asn'])->setName($data['name']);
    }

    /**
     * @return array
     */
    public function getArrayCopy(): array
    {
        return [
            'asn' => $this->getAsn(),
            'name' => $this->getName()
        ];
    }
}
