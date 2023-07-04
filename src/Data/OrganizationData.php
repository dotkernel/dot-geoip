<?php

declare(strict_types=1);

namespace Dot\GeoIP\Data;

use Laminas\Stdlib\ArraySerializableInterface;

class OrganizationData implements ArraySerializableInterface
{
    protected ?int $asn;
    protected ?string $name;

    public function __construct(?int $asn = null, ?string $name = null)
    {
        $this->asn  = $asn;
        $this->name = $name;
    }

    public function getAsn(): ?int
    {
        return $this->asn;
    }

    public function setAsn(?int $asn): self
    {
        $this->asn = $asn;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function exchangeArray(array $array): self
    {
        return $this
            ->setAsn($array['asn'] ?? null)
            ->setName($array['name'] ?? null);
    }

    public function getArrayCopy(): array
    {
        return [
            'asn'  => $this->getAsn(),
            'name' => $this->getName(),
        ];
    }
}
