<?php
namespace Radas\Public\Assets;

use Radas\Lib\Classes\Abstracts\Styles;

class WP_Styles_Rds extends Styles {
    protected const script_hook = 'wp_enqueue_scripts';

    private const asset_url = RADAS_URL . 'public/assets/css/';

    protected static $styles = [
        'radas-style' => [
            'src'   => self::asset_url . 'style.css',
            'deps'  => [],
            'ver'   => '0.0.0',
            'media' => 'all'             
        ],
    ];
}