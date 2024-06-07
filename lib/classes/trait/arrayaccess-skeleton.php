<?php
namespace Radas\Lib\Classes\Trait;

/** 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
*/
trait ArrayAccess_Skeleton {
    /**
     * @param mixed $offset 
     * @return bool 
     */
    public function offsetExists($offset): bool {
        return property_exists($this, $offset);
    }

    /**
     * @param mixed $offset 
     * @return mixed 
     */
    public function offsetGet($offset): mixed {
        return @$this->$offset;
    }

    /**
     * @param mixed $offset 
     * @param mixed $value 
     * @return void 
     */
    public function offsetSet($offset, $value): void {
        $this->$offset = $value;
    }

    /**
     * @param mixed $offset 
     * @return void 
     */
    public function offsetUnset($offset): void {
        unset($this->$offset);
    } 
}