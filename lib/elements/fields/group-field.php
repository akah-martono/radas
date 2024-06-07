<?php
namespace Radas\Lib\Elements\Fields;

use Radas\Lib\Classes\Abstracts\Field;

class Group_Field extends Field{
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
        <input type="checkbox" value="1" <?php checked($this->get_sanitized_value()); $this->render_attributes(); ?>>
        <?php         
    }
}