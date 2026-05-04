<?php

return [
    'cache_duration' => (int) env('EMISSIONS_CACHE_DURATION', 86400),

    'apis' => [
        'climatiq' => [
            'base_url' => env('CLIMATIQ_BASE_URL', 'https://api.climatiq.io/v1'),
            'estimate_path' => '/estimate',
            'api_key' => env('CLIMATIQ_API_KEY'),
            'emission_factor' => env('CLIMATIQ_ELECTRICITY_FACTOR', 'electricity-energy_import_ph'),
        ],
        'open_ceda' => [
            'base_url' => env('OPEN_CEDA_BASE_URL', 'https://openceda.org/api/v1'),
            'factors_path' => '/factors',
            'country' => env('EMISSIONS_COUNTRY', 'Philippines'),
        ],
    ],

    'device_wattages' => [
        'laptop' => 0.05,
        'smartphone' => 0.01,
        'tablet' => 0.02,
        'desktop_pc' => 0.10,
        'monitor' => 0.08,
    ],

    'transport_activity_map' => [
        'jeepney' => 'jeepney',
        'bus' => 'bus',
        'tricycle' => 'tricycle',
        'car' => 'car',
        'walking' => 'walking',
    ],

    'diet_activity_map' => [
        'meat' => 'meat',
        'average' => 'average',
        'vegetarian' => 'vegetables',
        'plant_based' => 'plant based',
    ],

    'fallback' => [
        'transport' => [
            'jeepney' => 0.15,
            'bus' => 0.12,
            'tricycle' => 0.10,
            'car' => 0.20,
            'walking' => 0.00,
        ],
        'diet' => [
            'meat' => 5.0,
            'average' => 3.5,
            'vegetarian' => 2.0,
            'plant_based' => 1.5,
        ],
        'gadgets' => [
            'per_hour' => 0.05,
        ],
    ],
];
