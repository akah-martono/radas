<?php
namespace Radas\Admin;

use Radas\Admin\Assets\Admin_Scripts_Rds;
use Radas\Lib\Assets\Admin_Styles_Lib;
use Radas\Lib\Classes\Abstracts\Element;
use Radas\Lib\Classes\Abstracts\Field;
use Radas\Lib\Factories\Element_Factory;
use Radas\Lib\Helper\Util;

class Option_Page {
    protected $slug;
    protected $page;
    protected $elements;
    protected $route;
    protected $option_values;
    protected $fields;

    public function __construct($post_id){
        $this->page = json_decode(get_post_meta($post_id, 'op_json', true), true);
        if($this->page){
            $this->elements = Element_Factory::getInstance()($this->page['fields'][0]);
            $this->fields = Util::get_element_fields($this->elements);
            $this->route = '/options/' . $this->page['slug'];
        }
    }

    public function register(){
        if(!$this->page) return;
        
        $slug = $this->page['slug'];

        add_action('admin_init', function() use($slug) {
            /** no need to check nonce, this condition just to make sure the script run on right page (to prevent bloated)*/
            if( array_key_exists('page', $_GET) && $_GET['page'] === $slug ){
                Admin_Scripts_Rds::enqueue('_rds-option-page');
                Admin_Styles_Lib::enqueue('rds-page');
            }
        });

        if($this->page['type'] == 'menu'){
            add_action( 'admin_menu', function () {
                add_menu_page(
                    page_title: $this->page['title'],
                    menu_title: $this->page['menu_title'],
                    capability: $this->page['capability'],
                    menu_slug: $this->page['slug'],
                    callback: fn()=> $this->render(),
                    icon_url: $this->page['icon'],
                    position: $this->page['position'] ? $this->page['position'] : null
                );    
            }, 10);       
        } else {
            /** set priority 99 to make sure it run after admin menu done */
            add_action( 'admin_menu', function () {
                add_submenu_page(
                    parent_slug: $this->page['parent'],
                    page_title: $this->page['title'],
                    menu_title: $this->page['title'],
                    capability: $this->page['capability'],
                    menu_slug: $this->page['slug'],
                    callback: fn() => $this->render(),
                    position: $this->page['position'] ? $this->page['position'] : null
                );            
            }, 99);    
        }

        add_action('rest_api_init', [$this, 'register_rest']); 
        
        add_shortcode( $this->page['slug'], [$this, 'create_shortcode'] );        
    }

    protected function render(){
        
        $end_point = RADAS_REST_NAMESPACE . $this->route;

        ?>
        <div class="wrap">
            <h1><?php echo esc_html($this->page['title']); ?></h1>
            <?php 
            if ($this->page['description']){
                printf('<p class="description">%s</p>', wp_kses_post($this->page['description']));
            }
            ?>
            <form data-endpoint="<?php echo esc_attr($end_point); ?>">
            <?php 
                $this->form_content(); 
                submit_button();
            ?>
            </form>
        </div>
        <div class="rds-spinner">
            <span class="loader"></span>
        </div>
        <?php
    }

    protected function form_content(){
        if(!$this->elements instanceof Element){
            echo __('Something wrong with your data', RADAS_DOMAIN);
            return;
        }

        $option_values = $this->get_option_values();
        if(is_array($option_values) && !empty($option_values)){
            Util::set_fields_value($this->elements, $option_values);
        }
        $this->elements->render();
    }

    public function register_rest(){
        // Register a new endpoint to manage a custom option
        register_rest_route(RADAS_REST_NAMESPACE, $this->route, array(
            'methods' => 'POST', // Supported HTTP methods
            'callback' =>  [$this, 'rest_handle'], // Callback function to handle the request
            'permission_callback' => [$this, 'rest_permission'], // Permission check for this endpoint
        ));               
    }
    
    public function rest_permission(\WP_REST_Request $request){
        
        // Return false if user does not have capability.
        if ( ! $this->isCapable() ) {
            return false;
        }

        return true;
    }
    
    /**
     * Callback function to handle the custom option requests.
     * 
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function rest_handle(\WP_REST_Request $request) {
        $method = $request->get_method(); // Determine the HTTP method
        if(!$method == 'POST'){
            return Util::rest_response(
                code: 'not_supported', 
                message: __('Method not supported'),
                data: [],
                status: 405
            );            
        }

        $params = $request->get_params();
         
        $sanitized_values = $this->sanitize_values($params);
      
        if(!get_option($this->page['slug'])){
            $result = add_option($this->page['slug'], $sanitized_values);
        } else {
            $result = update_option($this->page['slug'], $sanitized_values);
        }

        // if(!$result) {
        //     return Util::rest_response(
        //         code: 'failed', 
        //         message: "Something wrong, please check your params",
        //         data: [
        //             'params' => $params
        //         ] ,
        //         status: 500
        //     );  
        // }

        return Util::rest_response(
            code: 'success', 
            message: __('Your data has been successfully updated'),
            data: get_option($this->page['slug'], null),
            status: 200
        );               
    }

    public function isCapable(){
        return current_user_can($this->page['capability']);
    }

    public function create_shortcode( $atts ) {
        if( !array_key_exists('field', $atts) || !array_key_exists($atts['field'], $this->fields) ){
            return ;
        }

        /** get options value ana insert to fields */
        $this->get_option_values();

        /** @var Field $field */
        $field = $this->fields[$atts['field']];        

        if( array_key_exists('format', $atts) && $atts['format'] == 'raw' ){
            return $field->get_sanitized_value();
        }

        // if($field->type == 'media' && $field->attributes['data-media-type'] == 'image'){
        if($field->type == 'media'){
            $atts = shortcode_atts( [
                'size'  => 'thumbnail',
                'width' => '',
                'height' => '',
                'format' => '',          
            ], $atts);
    
            if($atts['format'] == 'id') {
                return $field->get_sanitized_value();
            }

            if($atts['format'] == 'url') {
                return wp_get_attachment_url($field->get_sanitized_value());
            }

            $ar_size = [];
            if($atts['width']) {
                array_push($ar_size, $atts['width']);
                if($atts['height']) {
                    array_push($ar_size, $atts['height']);                    
                }    
                return wp_get_attachment_image($field->get_sanitized_value(), $ar_size);
            }

            if($atts['size']) {
                return wp_get_attachment_image($field->get_sanitized_value(), $atts['size']);
            }
        }

        if($field->type == 'email'){
            $atts = shortcode_atts( [
                'format' => '',          
            ], $atts);
                
            if($atts['format'] == 'url') {
                return esc_url('mailto:' . $field->get_sanitized_value());
            }
        }

        if($field->type == 'tel'){
            $atts = shortcode_atts( [
                'format' => '',          
            ], $atts);
            
            if($atts['format'] == 'url') {
                return esc_url('tel:' . $field->get_sanitized_value());
            }
        }

        if($field->type == 'date'){
            $atts = shortcode_atts( [
                'format' => 'default',          
            ], $atts);
            
            $date = date_create($field->get_sanitized_value());

            if($atts['format'] == 'default') {
                return date_format($date, get_option( 'date_format' ));                
            }

            return date_format($date, $atts['format']);                
        }        

        if($field->type == 'select' || $field->type == 'radio'){
            $atts = shortcode_atts( [
                'format' => 'value',          
            ], $atts);
            
            if($atts['format'] == 'value') {
                return $field->get_sanitized_value();
            }

            if($atts['format'] == 'text') {
                return esc_html($field->options[$field->get_sanitized_value()]);
            }            
        }
        
        return $field->get_sanitized_value();
    }

    /**
     * @param array $values 
     * @return array sanitized values of each element/field
     */
    protected function sanitize_values(array $values){
        $sanitized = [];
        /** @var Field $field */
        foreach($this->fields as $field){
            if(array_key_exists($field->id, $values)){
                // sanitize value
                $field->set_value($values[$field->id]);
                $sanitized[$field->id] = $field->get_sanitized_value();
            }
        }
        return $sanitized;
    }
    
    /** @return mixed sanitized values of options*/
    protected function get_option_values(){
        if (!$this->option_values) {
            $option = get_option($this->page['slug'], null);
            if(is_array($option)){
                $this->option_values = $this->sanitize_values($option);
            }
        }
        return $this->option_values;
    }
}
