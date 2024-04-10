<?php

declare(strict_types=1);

namespace Dot\GeoIP\Data;

use Laminas\Stdlib\ArraySerializableInterface;

class CountryData implements ArraySerializableInterface
{
    protected ?bool $isEuMember;
    protected ?string $isoCode;
    protected ?string $name;
    protected ?string $error;

    public function __construct(
        ?bool $isEuMember = false,
        ?string $isoCode = null,
        ?string $name = null,
        ?string $error = null
    ) {
        $this->isEuMember = $isEuMember;
        $this->isoCode    = $isoCode;
        $this->name       = $name;
        $this->error      = $error;
    }

    public function getIsEuMember(): ?bool
    {
        return $this->isEuMember;
    }

    public function setIsEuMember(?bool $isEuMember): self
    {
        $this->isEuMember = $isEuMember;

        return $this;
    }

    public function getIsoCode(): ?string
    {
        return $this->isoCode;
    }

    public function setIsoCode(?string $isoCode): self
    {
        $this->isoCode = $isoCode;

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

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): self
    {
        $this->error = $error;
        return $this;
    }

    public function exchangeArray(array $array): self
    {
        return $this
            ->setIsEuMember($array['isEuMember'] ?? null)
            ->setIsoCode($array['isoCode'] ?? null)
            ->setName($array['name'] ?? null)
            ->setError($array['error'] ?? null);
    }

    public function getArrayCopy(): array
    {
        return [
            'isEuMember' => $this->getIsEuMember(),
            'isoCode'    => $this->getIsoCode(),
            'name'       => $this->getName(),
            'error'      => $this->getError(),
        ];
    }
}
