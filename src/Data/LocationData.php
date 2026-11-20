<?php

declare(strict_types=1);

namespace Dot\GeoIP\Data;

use Laminas\Stdlib\ArraySerializableInterface;

use function is_array;

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

    /**
     * Accept the nested data as objects or as arrays, so that the output of getArrayCopy() is valid input.
     *
     * @param array<string, mixed> $array
     */
    public function exchangeArray(array $array): self
    {
        return $this
            ->setContinent($this->toContinent($array['continent'] ?? null))
            ->setCountry($this->toCountry($array['country'] ?? null))
            ->setCity($this->toCity($array['city'] ?? null))
            ->setOrganization($this->toOrganization($array['organization'] ?? null))
            ->setLatitude($array['latitude'] ?? null)
            ->setLongitude($array['longitude'] ?? null)
            ->setTimeZone($array['timeZone'] ?? null);
    }

    /**
     * @return array{
     *     continent: array{code: ?string, name: ?string, error: ?string}|array{},
     *     country: array{isEuMember: ?bool, isoCode: ?string, name: ?string, error: ?string}|array{},
     *     city: array{name: ?string, error: ?string}|array{},
     *     organization: array{asn: ?int, name: ?string, error: ?string}|array{},
     *     latitude: ?float,
     *     longitude: ?float,
     *     timeZone: ?string
     * }
     */
    public function getArrayCopy(): array
    {
        return [
            'continent'    => $this->continent?->getArrayCopy() ?? [],
            'country'      => $this->country?->getArrayCopy() ?? [],
            'city'         => $this->city?->getArrayCopy() ?? [],
            'organization' => $this->organization?->getArrayCopy() ?? [],
            'latitude'     => $this->getLatitude(),
            'longitude'    => $this->getLongitude(),
            'timeZone'     => $this->getTimeZone(),
        ];
    }

    private function toContinent(mixed $value): ?ContinentData
    {
        return match (true) {
            $value instanceof ContinentData => $value,
            is_array($value) && $value !== [] => (new ContinentData())->exchangeArray($value),
            default => null,
        };
    }

    private function toCountry(mixed $value): ?CountryData
    {
        return match (true) {
            $value instanceof CountryData => $value,
            is_array($value) && $value !== [] => (new CountryData())->exchangeArray($value),
            default => null,
        };
    }

    private function toCity(mixed $value): ?CityData
    {
        return match (true) {
            $value instanceof CityData => $value,
            is_array($value) && $value !== [] => (new CityData())->exchangeArray($value),
            default => null,
        };
    }

    private function toOrganization(mixed $value): ?OrganizationData
    {
        return match (true) {
            $value instanceof OrganizationData => $value,
            is_array($value) && $value !== [] => (new OrganizationData())->exchangeArray($value),
            default => null,
        };
    }
}
