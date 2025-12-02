<?php

declare(strict_types=1);

namespace Dot\GeoIP\Data;

use Laminas\Stdlib\ArraySerializableInterface;

class ContinentData implements ArraySerializableInterface
{
    public function __construct(
        private ?string $code = null,
        private ?string $name = null,
        private ?string $error = null,
    ) {
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

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
            ->setName($array['name'] ?? null)
            ->setCode($array['code'] ?? null)
            ->setError($array['error'] ?? null);
    }

    public function getArrayCopy(): array
    {
        return [
            'code'  => $this->getCode(),
            'name'  => $this->getName(),
            'error' => $this->getError(),
        ];
    }
}
