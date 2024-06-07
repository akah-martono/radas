<?php

namespace Radas\Lib\Classes\Trait;

/** 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
*/
trait Singleton{
    private static $instance;

    final public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}
