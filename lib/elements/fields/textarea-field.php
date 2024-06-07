<?php
namespace Radas\Lib\Elements\Fields;

use Radas\Lib\Classes\Abstracts\Field;

/** 
 * @package Radas\Lib\Fields 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
 * 
 * @property-read string $id
 * @property-read array $attributes
 * @property-read array $classes
 * @property-read string $type
 * @property-read string $label
 * @property-read string $default
 * @property-read string $description 
 * @property-read string $text_right
*/
class TextArea_Field extends Field{
    /**
     * @inheritDoc
     */    
    public function __construct(string $id) {
        $this->type = 'textarea';
        parent::__construct($id);
    }

    /**
     * @param int $rows 
     * @return $this 
     */
    public function set_rows(int $rows) {
        $this->attributes['rows'] = $rows;
        return $this;
    }

    /**
     * @param int $cols 
     * @return $this 
     */
    public function set_cols(int $cols) {
        $this->attributes['cols'] = $cols;
        return $this;
    }

    /**
     * @inheritDoc
     */ 
    public function get_sanitized_value(){
        return sanitize_textarea_field($this->get_value());
    }
       
    /**
     * @inheritDoc
     */ 
    public function render_input() {
        ?>
        <textarea <?php $this->render_attributes(); ?>><?php echo $this->get_sanitized_value() ?></textarea>
        <?php    
    }
}