<?php
namespace Radas\Lib\Elements\Fields;

use Radas\Lib\Classes\Abstracts\Field;

class File_Field extends Field{
    
    /**
     * @inheritDoc
     */ 
    public function __construct($id) {
        $this->type = 'file';
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
        <input type="url" id="<?php echo esc_attr($this->id); ?>" value="<?php echo $this->get_sanitized_value(); ?>">
        <input type="file" id="<?php echo esc_attr($this->attributes['name']); ?>"  name="<?php echo esc_attr($this->attributes['name']); ?>">
        <?php           
    }
    
}