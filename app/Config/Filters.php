<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use App\Filters\filter_jwt;
use App\Filters\filter_jwtadmin;


class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array
     */
    public $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'filter_jwt'    => filter_jwt::class,
        'filter_jwtadmin'   => filter_jwtadmin::class
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array
     */
    public $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['csrf', 'throttle']
     *
     * @var array
     */
    public $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array
     */
    public $filters = [
        'filter_jwt'    => [
            'before'    => [
                'buskidicoin/publics/getData/*',
                'buskidicoin/publics/topup',
                'buskidicoin/publics/transfer',
                'buskidicoin/admin/*',
                'buskimarket/AddItem','Buskimarket-AddItem',
                'buskimarket/Buy','Buskimarket-Buy',
                'buskimarket/Buy/KCN_Pay','Buskimarket-Buy-KCN_Pay',
                'buskimarket/Buy/Buski_Coins','Buskimarket-Buy-Buski_Coins',
                'buskimarket/Buy/CuanIND','Buskimarket-Buy-CuanIND',
                'buskimarket/Buy/MoneyZ','Buskimarket-Buy-MoneyZ',
                'buskimarket/Buy/Gallecoins','Buskimarket-Buy-Gallecoins',
                'buskimarket/Buy/Talangin','Buskimarket-Buy-Talangin',
                'buskimarket/Buy/PeacePay','Buskimarket-Buy-PeacePay',
                'buskimarket/Buy/PadPay','Buskimarket-Buy-PadPay',
                'buskimarket/Buy/PayPhone','Buskimarket-Buy-PayPhone',
                'buskimarket/Buy/PayFresh','Buskimarket-Buy-PayFresh',
            ]
        ],
        'filter_jwtadmin'   => [
            'before'    => [
                'buskidicoin/admin/*'
            ]
        ]
    ];
}
