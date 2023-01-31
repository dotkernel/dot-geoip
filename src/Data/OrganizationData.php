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
    protected ?int $asn;
    protected ?string $name;

    /**
     * @return int|null
     */
    public function getAsn(): ?int
    {
        return $this->asn;
    }

    /**
     * @param int|null $asn
     * @return $this
     */
    public function setAsn(?int $asn): self
    {
        $this->asn = $asn;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): self
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
