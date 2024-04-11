<?php

declare(strict_types=1);

namespace Dot\GeoIP\Data;

use Laminas\Stdlib\ArraySerializableInterface;

class CityData implements ArraySerializableInterface
{
    protected ?string $name;
    protected ?string $error;

    public function __construct(?string $name = null, ?string $error = null)
    {
        $this->name  = $name;
        $this->error = $error;
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
        return ($this)
            ->setName($array['name'] ?? null)
            ->setError($array['error'] ?? null);
    }

    public function getArrayCopy(): array
    {
        return [
            'name'  => $this->getName(),
            'error' => $this->getError(),
        ];
    }
}
