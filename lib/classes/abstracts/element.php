<?php
namespace Radas\Lib\Classes\Abstracts;

use Radas\Lib\Classes\Interfaces\Element_Interface;
use Radas\Lib\Classes\Trait\Element_Skeleton;

/** 
 * @package Radas\Lib\Classes\Abstracts
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
 * 
 * @property-read string $id
 * @property-read string $label
 * @property-read string $description
 * @property-read array $attributes
 * @property-read array $classes
*/
abstract class Element implements Element_Interface{
    use Element_Skeleton;
    abstract public function render();
}