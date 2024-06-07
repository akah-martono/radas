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
class Color_Field extends Field{
    /**
     * @inheritDoc
     */
    public function __construct($id) {
        $this->type = 'checkbox';
        parent::__construct($id);
    }

    /**
     * @inheritDoc
     */
    public function get_sanitized_value(){
        return sanitize_text_field($this->get_value());
    }

    /**
     * @inheritDoc
     */ 
    public function render_input() {
		?>
        <input type="color" value="<?php echo $this->get_sanitized_value(); ?>" <?php $this->render_attributes(); ?>>
        <?php         
    }
}