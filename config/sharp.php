<?php

return [
    'name' => 'Dealer product Crawler',
    'custom_url_segment' => 'admin',
    'entities' => [
        'vendor' => [
            'list' => \App\Sharp\Entities\Vendor\ListVendor::class,
            'show' => \App\Sharp\Entities\Vendor\ShowVendor::class,
        ],
        'category' => [
            'list' => \App\Sharp\Entities\Category\ListCategory::class,
            'show' => \App\Sharp\Entities\Category\ShowCategory::class,
        ],
        'product' => [
            'list' => \App\Sharp\Entities\Product\ListProduct::class,
            'show' => \App\Sharp\Entities\Product\ShowProduct::class,
        ],
        'priceOption' => [
            'list' => \App\Sharp\Entities\PriceOption\ListPriceOption::class,
        ]
    ],
    'auth' => [
        'login_attribute' => 'email',
        'password_attribute' => 'password',
        'display_attribute' => 'name',
    ],
    'menu' => [
        [
            'label' => 'Vendor',
            'icon' => 'fa-superpowers',
            'entity' => 'vendor'
        ],
        [
            'label' => 'Category',
            'icon' => 'fa-superpowers',
            'entity' => 'category'
        ],
        [
            'label' => 'Product',
            'icon' => 'fa-superpowers',
            'entity' => 'product'
        ],
    ],
    'uploads' => [
        'thumbnails_disk' => env('FILESYSTEM_DRIVER', 'local'),
        'tmp_dir' => env('SHARP_UPLOADS_TMP_DIR', 'tmp'),
        'thumbnails_dir' => env('SHARP_UPLOADS_THUMBS_DIR', 'thumbnails'),
    ]
];
