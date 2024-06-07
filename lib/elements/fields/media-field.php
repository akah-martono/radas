<?php
namespace Radas\Lib\Elements\Fields;

use Radas\Lib\Assets\Admin_Scripts_Lib;
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
class Media_Field extends Field{
    /**
     * @inheritDoc
     */ 
    public function __construct($id) {
        $this->type = 'media';
        parent::__construct($id);
    }

    /**
     * @inheritDoc
     */ 
    public function get_sanitized_value(){
        // return sanitize_url($this->get_value());
        return sanitize_text_field($this->get_value());
    }

    /**
     * @param string $type 
     * @return $this 
     */
    public function set_media_type(string $type){
        $this->attributes['data-media-type'] = $type;
        return $this;
    }

    /**
     * @param string $title 
     * @return $this 
     */
    public function set_media_frame_title(string $title){
        $this->attributes['data-frame-title'] = $title;
        return $this;
    }

    /**
     * @param string $text 
     * @return $this 
     */
    public function set_media_frame_button_text(string $text){
        $this->attributes['data-frame-button-text'] = $text;
        return $this;
    }    

    /**
     * @inheritDoc
     */ 
    public function render_input() {
        add_filter('upload_mimes', function ($mime){
            $mime_types['svg'] = 'image/svg+xml'; //Adding svg extension
            // $mime_types['psd'] = 'image/vnd.adobe.photoshop'; //Adding photoshop files
            return $mime_types;
        }, 1, 1);        
        wp_enqueue_media();
        Admin_Scripts_Lib::enqueue('rds-media-box');
		?>
        <div class="rds-media-input">
            <div class="media-preview"><?php echo ($this->get_sanitized_value() ? wp_get_attachment_image($this->get_sanitized_value(), [ 129, 129 ]) : '');  ?></div>
            <input type="hidden" class="media-id" value="<?php echo $this->get_sanitized_value(); ?>" <?php $this->render_attributes(); ?>>
            <div style="display: flex; align-items: center; flex-wrap: nowrap; column-gap: 8px; margin-top: 4px;">
                <input type="button" class="button upload-media-button" value="Upload">
                <input type="button" class="button no-button remove-media-button<?php echo ($this->get_sanitized_value() ? '' : ' hide'); ?>" value="Remove" style="color:#a00;">
            </div>
        </div>
        <?php
    }
    
}