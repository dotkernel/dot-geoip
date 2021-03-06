<?php

declare(strict_types=1);

namespace Dot\GeoIP\Data;

use Laminas\Stdlib\ArraySerializableInterface;

/**
 * Class ContinentData
 * @package Dot\GeoIP\Data
 */
class ContinentData implements ArraySerializableInterface
{
    protected ?string $code;

    protected ?string $name;

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return $this
     */
    public function setCode(?string $code): self
    {
        $this->code = $code;
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
