<?php

declare(strict_types=1);

namespace Dot\GeoIP\Data;

use Laminas\Stdlib\ArraySerializableInterface;

class LocationData implements ArraySerializableInterface
{
    public function __construct(
        private ?ContinentData $continent = null,
        private ?CountryData $country = null,
        private ?CityData $city = null,
        private ?OrganizationData $organization = null,
        private ?float $latitude = null,
        private ?float $longitude = null,
        private ?string $timeZone = null,
    ) {
    }

    public function getContinent(): ?ContinentData
    {
        return $this->continent;
    }

    public function hasContinent(): bool
    {
        return $this->continent instanceof ContinentData;
    }

    public function setContinent(?ContinentData $continent): self
    {
        $this->continent = $continent;

        return $this;
    }

    public function getCountry(): ?CountryData
    {
        return $this->country;
    }

    public function hasCountry(): bool
    {
        return $this->country instanceof CountryData;
    }

    public function hasCity(): bool
    {
        return $this->city instanceof CityData;
    }

    public function setCountry(?CountryData $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?CityData
    {
        return $this->city;
    }

    public function setCity(?CityData $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getOrganization(): ?OrganizationData
    {
        return $this->organization;
    }

    public function hasOrganization(): bool
    {
        return $this->organization instanceof OrganizationData;
    }

    public function setOrganization(?OrganizationData $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getTimeZone(): ?string
    {
        return $this->timeZone;
    }

    public function setTimeZone(?string $timeZone): self
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    public function exchangeArray(array $array): self
    {
        return $this
            ->setContinent($array['continent'] ?? null)
            ->setCountry($array['country'] ?? null)
            ->setCity($array['city'] ?? null)
            ->setOrganization($array['organization'] ?? null)
            ->setLatitude($array['latitude'] ?? null)
            ->setLongitude($array['longitude'] ?? null)
            ->setTimeZone($array['timeZone'] ?? null);
    }

    public function getArrayCopy(): array
    {
        return [
            'continent'    => $this->hasContinent() ? $this->getContinent()->getArrayCopy() : [],
            'country'      => $this->hasCountry() ? $this->getCountry()->getArrayCopy() : [],
            'city'         => $this->hasCity() ? $this->getCity()->getArrayCopy() : [],
            'organization' => $this->hasOrganization() ? $this->getOrganization()->getArrayCopy() : [],
            'latitude'     => $this->getLatitude(),
            'longitude'    => $this->getLongitude(),
            'timeZone'     => $this->getTimeZone(),
        ];
    }
}
