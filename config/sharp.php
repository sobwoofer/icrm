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
            'form' => \App\Sharp\Entities\Category\FormCategory::class,
        ],
        'product' => [
            'list' => \App\Sharp\Entities\Product\ListProduct::class,
            'show' => \App\Sharp\Entities\Product\ShowProduct::class,
            'form' => \App\Sharp\Entities\Product\FormProduct::class,
        ],
        'priceOption' => [
            'list' => \App\Sharp\Entities\PriceOption\ListPriceOption::class,
            'form' => \App\Sharp\Entities\PriceOption\FormPriceOption::class,
        ],
        'image' => [
            'list' => \App\Sharp\Entities\Image\ListImage::class,
        ],
        'foreignOption' => [
            'list' => App\Sharp\Entities\ForeignOption\ListForeignOption::class,
            'form' => App\Sharp\Entities\ForeignOption\FormForeignOption::class,
            'show' => App\Sharp\Entities\ForeignOption\ShowForeignOption::class,
        ],
        'clientSite' => [
            'list' => App\Sharp\Entities\ClientSite\ListClientSite::class,
            'form' => App\Sharp\Entities\ClientSite\FormClientSite::class,
            'show' => App\Sharp\Entities\ClientSite\ShowClientSite::class,
        ],
        'productToClient' => [
            'list' => App\Sharp\Entities\ProductToClient\ListProductToClient::class,
            'form' => App\Sharp\Entities\ProductToClient\FormProductToClient::class,
        ]

    ],
    'dashboards' => [
        'company_dashboard' => [
            "view" => \App\Sharp\CompanyDashboard::class,
//            "policy" => \App\Sharp\Policies\CompanyDashboardPolicy::class,
        ],
    ],
    'auth' => [
        'login_attribute' => 'email',
        'password_attribute' => 'password',
        'display_attribute' => 'name',
    ],
    'menu' => [
        [
            "label" => "Dashboard",
            "icon" => "fa-superpowers",
            "dashboard" => "company_dashboard"
        ],
        [
            'label' => 'Crawled Entities',
            'entities' => [
                [
                    'label' => 'Vendors',
                    'icon' => 'fa-superpowers',
                    'entity' => 'vendor'
                ],
                [
                    'label' => 'Categories',
                    'icon' => 'fa-superpowers',
                    'entity' => 'category'
                ],
                [
                    'label' => 'Products',
                    'icon' => 'fa-superpowers',
                    'entity' => 'product'
                ],
                [
                    'label' => 'Price Options',
                    'icon' => 'fa-superpowers',
                    'entity' => 'priceOption'
                ],
            ]
        ],
        [
            'label' => 'Client Options',
            'icon' => 'fa-superpowers',
            'entity' => 'foreignOption'
        ],
        [
            'label' => 'Client Sites',
            'icon' => 'fa-superpowers',
            'entity' => 'clientSite'
        ],
        [
            'label' => 'Logs',
            'icon' => 'fas fa-globe',
            'url' => '/admin/logs'
        ],
    ],
    'uploads' => [
        'thumbnails_disk' => env('FILESYSTEM_DRIVER', 'local'),
        'tmp_dir' => env('SHARP_UPLOADS_TMP_DIR', 'tmp'),
        'thumbnails_dir' => env('SHARP_UPLOADS_THUMBS_DIR', 'thumbnails'),
    ]
];
