<?php

declare(strict_types=1);

namespace Dot\GeoIP\Data;

use Laminas\Stdlib\ArraySerializableInterface;

/**
 * Class LocationData
 * @package Dot\GeoIP\Data
 */
class LocationData implements ArraySerializableInterface
{
    protected ?ContinentData $continent;
    protected ?CountryData $country;
    protected ?float $latitude;
    protected ?float $longitude;
    protected ?OrganizationData $organization;
    protected ?string $timeZone;

    /**
     * @return ContinentData|null
     */
    public function getContinent(): ?ContinentData
    {
        return $this->continent;
    }

    /**
     * @param ContinentData|null $continent
     * @return $this
     */
    public function setContinent(?ContinentData $continent): self
    {
        $this->continent = $continent;
        return $this;
    }

    /**
     * @return CountryData|null
     */
    public function getCountry(): ?CountryData
    {
        return $this->country;
    }

    /**
     * @param CountryData|null $country
     * @return $this
     */
    public function setCountry(?CountryData $country): self
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float|null $latitude
     * @return $this
     */
    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float|null $longitude
     * @return $this
     */
    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * @return OrganizationData|null
     */
    public function getOrganization(): ?OrganizationData
    {
        return $this->organization;
    }

    /**
     * @param OrganizationData|null $organization
     * @return $this
     */
    public function setOrganization(?OrganizationData $organization): self
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTimeZone(): ?string
    {
        return $this->timeZone;
    }

    /**
     * @param string|null $timeZone
     * @return $this
     */
    public function setTimeZone(?string $timeZone): self
    {
        $this->timeZone = $timeZone;
        return $this;
    }

    /**
     * @param array $data
     * @return LocationData
     */
    public function exchangeArray(array $data): self
    {
        return $this
            ->setContinent($data['continent'])
            ->setCountry($data['country'])
            ->setLatitude($data['latitude'])
            ->setLongitude($data['longitude'])
            ->setOrganization($data['organization'])
            ->setTimeZone($data['timeZone']);
    }

    /**
     * @return array
     */
    public function getArrayCopy(): array
    {
        return [
            'continent' => $this->getContinent()->getArrayCopy(),
            'country' => $this->getCountry()->getArrayCopy(),
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
            'organization' => $this->getOrganization()->getArrayCopy(),
            'timeZone' => $this->getTimeZone()
        ];
    }
}
