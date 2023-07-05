<?php

declare(strict_types=1);

namespace Dot\GeoIP\Data;

use Laminas\Stdlib\ArraySerializableInterface;

class LocationData implements ArraySerializableInterface
{
    protected ?ContinentData $continent;
    protected ?CountryData $country;
    protected ?OrganizationData $organization;
    protected ?float $latitude;
    protected ?float $longitude;
    protected ?string $timeZone;

    public function __construct(
        ?ContinentData $continent = null,
        ?CountryData $country = null,
        ?OrganizationData $organization = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $timeZone = null
    ) {
        $this->continent    = $continent;
        $this->country      = $country;
        $this->organization = $organization;
        $this->latitude     = $latitude;
        $this->longitude    = $longitude;
        $this->timeZone     = $timeZone;
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

    public function setCountry(?CountryData $country): self
    {
        $this->country = $country;

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
            'organization' => $this->hasOrganization() ? $this->getOrganization()->getArrayCopy() : [],
            'latitude'     => $this->getLatitude(),
            'longitude'    => $this->getLongitude(),
            'timeZone'     => $this->getTimeZone(),
        ];
    }
}
