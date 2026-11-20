<?php

declare(strict_types=1);

namespace DotTest\GeoIP;

trait DefaultsTrait
{
    /**
     * @var array{
     *     continent: array{code: string, name: string, error: string},
     *     country: array{isEuMember: bool, isoCode: string, name: string, error: string},
     *     organization: array{asn: int, name: string, error: string},
     *     city: array{name: string, error: string},
     *     latitude: float,
     *     longitude: float,
     *     timeZone: string
     * }
     */
    protected array $defaults = [
        'continent'    => [
            'code'  => 'code',
            'name'  => 'name',
            'error' => 'error',
        ],
        'country'      => [
            'isEuMember' => false,
            'isoCode'    => 'isoCode',
            'name'       => 'name',
            'error'      => 'error',
        ],
        'organization' => [
            'asn'   => 123456,
            'name'  => 'name',
            'error' => 'error',
        ],
        'city'         => [
            'name'  => 'name',
            'error' => 'error',
        ],
        'latitude'     => 12.34,
        'longitude'    => 12.34,
        'timeZone'     => 'timeZone',
    ];
}
