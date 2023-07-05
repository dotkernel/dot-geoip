<?php

declare(strict_types=1);

namespace Dot\GeoIP\Data;

use Laminas\Stdlib\ArraySerializableInterface;

class ContinentData implements ArraySerializableInterface
{
    protected ?string $code;
    protected ?string $name;

    public function __construct(?string $code = null, ?string $name = null)
    {
        $this->code = $code;
        $this->name = $name;
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

    public function exchangeArray(array $array): self
    {
        return $this
            ->setName($array['name'] ?? null)
            ->setCode($array['code'] ?? null);
    }

    public function getArrayCopy(): array
    {
        return [
            'code' => $this->getCode(),
            'name' => $this->getName(),
        ];
    }
}
