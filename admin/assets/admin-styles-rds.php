<?php
namespace Radas\Admin\Assets;

use Radas\Lib\Classes\Abstracts\Styles;

class Admin_Styles_Rds extends Styles {
    protected const script_hook = 'admin_enqueue_scripts';

    private const asset_url = RADAS_URL . 'admin/assets/css/';

    protected static $styles = [
        '_rds-style' => [
            'src'   => self::asset_url . 'style.css',
            'deps'  => [],
            'ver'   => '0.0.0.b',
            'media' => 'all'             
        ],
        '_rds-fieldbox' => [
            'src'   => self::asset_url . 'fieldbox.css',
            'deps'  => ['rds-datatables', 'rds-page'],
            'ver'   => '0.0.0.b',
            'media' => 'all'             
        ],        
    ];
}