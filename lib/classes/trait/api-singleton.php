<?php
namespace Radas\Lib\Classes\Trait;

/** 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
*/
trait API_Singleton{
    use Singleton;
    private static $version = '0.0.1';

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public function ver() {
        return self::$version;
    }
}
