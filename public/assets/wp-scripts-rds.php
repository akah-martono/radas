<?php
namespace Radas\Public\Assets;

use Radas\Lib\Classes\Abstracts\Scripts;

class WP_Scripts_Rds extends Scripts {
    protected const script_hook = 'wp_enqueue_scripts';
    
    private const script_url = RADAS_URL . 'public/assets/js/';

    protected static $scripts = [
        'radas-script' => [
            'src'   => self::script_url . 'script.js',
            'deps'  => ['jquery'],
            'ver'   => '0.0.1',
            'args' => ['strategy'=>'defer'],
            'type' => 'module',
        ],          
    ];
}