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
class Editor_Field extends Field{

    protected $editor = false;
    protected $media_buttons = true;
    protected $drag_drop_upload = true;

    /**
     * @inheritDoc
     */    
    public function __construct($id, $editor=false) {
        $this->type = 'editor';

        // $this->attributes['rows'] = 7;
        parent::__construct($id);
    }

    /**
     * @param int $rows 
     * @return $this 
     */
    public function set_rows($rows) {
        $this->attributes['rows'] = $rows;
        return $this;
    }

    /**
     * @param bool $disable 
     * @return $this 
     */
    public function disable_media_tuttons(bool $disable = true){
        $this->media_buttons = !$disable;
        return $this;
    }

    /**
     * @param bool $disable 
     * @return $this 
     */
    public function disable_drag_drop_upload(bool $disable = true){
        $this->drag_drop_upload = !$disable;
        return $this;
    }    
    /**
     * @inheritDoc
     */ 
    public function get_sanitized_value(){
        return wp_kses_post($this->get_value());
    }
       
    /**
     * @inheritDoc
     */ 
    public function render_input() {
        if(!array_key_exists('name', $this->attributes)){
            $this->attributes['name'] = $this->id;
        }
        $settings = [
            'textarea_name'=>$this->attributes['name'], 
            'textarea_rows'=>$this->attributes['rows'],
            'media_buttons' => $this->media_buttons,
            'drag_drop_upload' => $this->drag_drop_upload,
        ];
        wp_editor($this->get_sanitized_value(), $this->id, $settings);
    }
}