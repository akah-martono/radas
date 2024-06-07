<?php
namespace Radas\Lib\Helper;

use Radas\Lib\Classes\Abstracts\Wrapper;
use Radas\Lib\Classes\Abstracts\Element;
use Radas\Lib\Elements\Fields\Checkbox_Field;
use Radas\Lib\Elements\Fields\Date_Field;
use Radas\Lib\Elements\Fields\Email_Field;
use Radas\Lib\Classes\Abstracts\Field;
use Radas\Lib\Elements\Fields\Number_Field;
use Radas\Lib\Elements\Fields\Phone_Field;
use Radas\Lib\Elements\Fields\Select_Field;
use Radas\Lib\Elements\Fields\Text_Field;
use Radas\Lib\Elements\Fields\TextArea_Field;

/**
 * Static utility functions
 * @package Radas\Lib\Helper
 * @author Vaksin <dev@vaks.in>
 * @since 0.0.1
 */
class Util {    

    private static $instance;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * To format phone number
     * <code>
     * $formated
     * </code>
     * @param string $phone  Example: +6281220003131
     * @param string $format Example: 3|0xxx-xxxx-xxxxx
     * @return string Result from example: 0812-2000-3131
     */
    public static function format_phone($phone, $format){
        
        if(!$format) {
            return $phone;
        }

        $opts = explode('|', $format);        
        $start = 0;
        
        if(count($opts)>1) {
            $start = intval($opts[0]);
            $format = $opts[1];
        }

        $arr_format = str_split($format);
        $arr_phone = str_split($phone);

        $i = $start;
        $result = '';

        foreach($arr_format as $value){
            if($value == 'x'){
                if($i >= count($arr_phone)){
                    break;
                }

                $result .= $arr_phone[$i];

                $i += 1;
            } else {
                $result .= $value;
            }
        }

        if($i <= count($arr_phone)){
            $result .= substr($phone, $i);
        }

        return $result;
    }

    /** @return string  */
    public static function get_current_url(){
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    /**
     * @param mixed $var 
     * @return void 
     */
    public static function print_debug($var){
        self::add_admin_notice('notice', $var);
        // echo "< !-- \n DEBUG: \n";
        // print_r($var);
        // echo "\n --> ";
    }

    /** @return string|false  */
    public static function get_logo_url(){
        $logo_id = get_theme_mod('custom_logo');
        return wp_get_attachment_image_url($logo_id, 'full') ?? '';
    }

    /**
     * @param array $array 
     * @return array 
     */
    public static function remove_empty_array_deep(array $array){
        foreach($array as $key => $value) {
            if(empty($value)) {
                unset($array[$key]);
            }elseif(is_array($value)){
                $array[$key] = self::remove_empty_array_deep($value);
            }
        }
        return $array;
    }

    /**
     * @param Field $field 
     * @return string|float|int|void 
     */
    public static function sanitize_field (Field $field ) { 
    
		switch(true){
			case is_a($field, Text_Field::class):
				return sanitize_text_field($field['value']);
				break;
            case is_a($field, TextArea_Field::class):
                return esc_textarea($field['value']);
                break;
			case is_a($field, Select_Field::class):
				return sanitize_text_field($field['value']);
				break;	
			case is_a($field, Date_Field::class):
				return sanitize_text_field($field['value']);
				break;
			case is_a($field, Checkbox_Field::class):
				return sanitize_text_field($field['value']);
				break;
			case is_a($field, Number_Field::class):
                $value = sanitize_text_field($field['value']);
                
                if($field['number_type'] = 'float') {
                    return floatval($value);
                }

                return intval($value);

				break;                    
			case is_a($field, Email_Field::class):
				return sanitize_email($field['value']);
				break;                    
			default:
				break;
		}
    }

    public static function create_shortcode_fields(string $tag, array $fields, callable $get_value){
        add_shortcode( $tag, function ( $attr ) use ($fields, $get_value) {
            $args = shortcode_atts( array(     
                'field' => '',
                'format' => ''
            ), $attr );

            $field_arg = strtolower((string) $args['field']);
            $value = '';

            foreach($fields as $field){
                $field_id = strtolower($field->id);
                
                if($field_arg == $field_id){
                    $value = call_user_func($get_value, $field);
                    if (is_a($field, TextArea_Field::class)){
                        $value = wpautop($value);
                    }
                    break;
                }

                if($field_arg == $field_id . '_url'){
                    if(is_a($field, Phone_Field::class)){
                        $value = 'tel:' . call_user_func($get_value, $field);                        
                        break;    
                    }
                    if(is_a($field, Email_Field::class)){
                        $value = 'mailto:' . call_user_func($get_value, $field);
                        break;    
                    }
                }

                if($field_arg == $field_id . '_avatar'){
                    if(is_a($field, Email_Field::class) && $field['enable_avatar']){
                        $avatar_args =  $args['size'] ? ['size' => $args['size']] : [];
                        $value = get_avatar_url(call_user_func($get_value, $field), $avatar_args);
                        break;    
                    }                            
                }

                if($field_arg == $field_id . '_format_phone'){
                    if(is_a($field, Phone_Field::class)){
                        $format = apply_filters('radas_phone_format', RADAS_PHONE_FORMAT, $field_id);
                        $value = Util::format_phone(call_user_func($get_value, $field), $format);
                        break;    
                    }                            
                }
                            
                if($field_arg == $field_id . '_label'){
                    $value = call_user_func($get_value, $field);
                    if(is_a($field, Select_Field::class)){
                        foreach($field['options'] as $key => $label){
                            if($value == $key){
                                $value = $label;
                                break;    
                            }
                        }                                
                    }
                }
            }
            return $value;
        } );
    }

	/**
	 * @param string $text 
	 * @return string 
	 */
	public static function parse_shortcode_dynamic_var( $text ) {

		if ( strpos( $text, '{page_title}' ) !== false ) {
			$dynamic_var['page_title'] = get_the_title();
		}		

		if ( strpos( $text, '{url}' ) !== false ) {
			$dynamic_var['url'] = get_permalink( get_the_ID() );
		}

		if ( !empty( $dynamic_var ) ) {
			foreach ( $dynamic_var as $key => $value ) {
				$text = str_replace( '{' . $key . '}', $value, $text );
			}
		}

		return str_replace("'", "`", $text);
	}

	/**
	 * @param string $text 
	 * @param array $args 
	 * @return string 
	 */
	public static function replace_shortcode_dynamic_var_with_atts( $text, $args ) {
		if ( !empty( $args ) ) {
			foreach ( $args as $key => $value ) {
				if ( $value ) {
					$text = str_replace( '{' . $key . '}', do_shortcode( $value ), $text );
				}
			}
		}

		return str_replace("'", "`", $text);
	}

    /**
     * @param mixed $key 
     * @param mixed $array 
     * @return mixed 
     */
    public static function get_key_value($key, $array) {
        if(array_key_exists ($key, $array)){
            return $array[$key];
        }else{
            foreach($array as $value){
                if(is_array($value)){
                    $val = static::get_key_value($key, $value);
                    if(!is_null($val)){
                        return $val;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param mixed $place 
     * @return string 
     */
    public static function google_map_url($place){
        $url = 'https://www.google.com/maps/search/?api=1&query='. urlencode($place);
        return esc_url_raw($url);
    }

    /**
     * To remove empty muldidimensional array
     * @param array $array 
     * @return array 
     */
    public static function remove_empty(array $array){
        $array = array_filter($array, function($var){
            return !empty($var) || $var === '0';
        });

        foreach($array as $key => $value){
            if(is_array($value)){
                $value = self::remove_empty($value);
            }
            $array[$key] = $value;
        }
        return $array;
    }

    /**
     * to generate random chars
     * @param int $length 
     * @param string|array $exclude_chars 
     * @param string|array $exclude_types 
     * @return string 
     */
    public static function random_chars( $length = 8, $exclude_chars = [], $exclude_types = []) {
        
        if(is_string($exclude_chars)) $exclude_chars=str_split($exclude_chars); 

        if(is_string($exclude_types)) {
            $exclude_types = str_replace(' ', '', $exclude_types);
            $exclude_types=explode(',', $exclude_types);
        }

        $array_chars = [
            'lower' => "abcdefghijklmnopqrstuvwxyz",
            'upper' => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
            'numeric' => "0123456789",
            'special' => "!@#%^&*()_-=+;:,.?",
        ];

        foreach($exclude_types as $type){
            unset($array_chars[strtolower($type)]);
        }

        $password = '';
        $all_type_chars = '';
        $i = count($array_chars);

        foreach($array_chars as $chars){
            if($exclude_chars){
                $chars = str_replace($exclude_chars, '', $chars);
            }
            
            $all_type_chars .= $chars;

            $password .= substr( str_shuffle( $chars ), 0, wp_rand(1, $length - strlen($password) - (--$i)) );  
        }

        $password = str_shuffle( $password );

        while(strlen($password) < $length){
            $password = substr( $password . str_shuffle( $all_type_chars ), 0, $length );
        }

        return $password;

    }

    /**
     * To get Instance from wp_cache
     * @param string $class 
     * @return mixed 
     */
    public static function get_cached_instance($class){
        $instance = wp_cache_get($class, 'radas_instance');
        if(!$instance){
            if(!class_exists($class)){
                return false;
            }
            $instance = new $class();
            wp_cache_set($class, $instance, 'radas_instance');
        }
        // wp_cache_flush_group( 'vxn_express_class' );
        return $instance;
    }

    /**
     * To get cached instance with params from wp_cache
     * @param string  $class 
     * @param string  $key 
     * @param mixed $param 
     * @return mixed 
     */
    public static function get_instance_with_param($class, $key, ...$param){
        $instance = wp_cache_get("{$class}_{$key}", 'radas_instance');
        if(!$instance){
            if(!class_exists($class)){
                return false;
            }
            $instance = new $class(...$param);
            wp_cache_set("{$class}_{$key}", $instance, 'radas_instance');
        }
        return $instance;
    }    

    /**
     * @param string $method 
     * @param string $key 
     * @param string $data 
     * @return string 
     */
    public static function encrypt($method, $key, $data){
        // $method check https://www.php.net/manual/en/function.openssl-get-cipher-methods.php
        $plaintext = $data;
        $ivlen = openssl_cipher_iv_length($cipher = $method);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
        return $ciphertext;
    }

    /**
     * @param string $method 
     * @param string $key 
     * @param string $data 
     * @return string|false|void 
     */
    public static function decrypt($method, $key, $data) {
        $c = base64_decode($data);
        $ivlen = openssl_cipher_iv_length($cipher = $method);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        if (hash_equals($hmac, $calcmac))
        {
            return $original_plaintext;
        }
    }

    /**
     * @param string $post_types 
     * @return array 
     */
    public static function get_public_post_urls($post_types=null){        
        if(!$post_types){
            $post_types = get_post_types( ['public' => true]);
            unset($post_types['attachment']);
        }

        $query_args = [
            'post_type' => $post_types,
            'post_status' => 'publish',
            'cache_results'  => false
        ];

        $wp_query = new \WP_Query( $query_args );
        $urls = [];
        if($wp_query->have_posts()){
            while ( $wp_query->have_posts() ) {
                $wp_query->the_post();
                $mod_date = new \DateTime(get_the_modified_date('Y-m-d H:i:s'));
                $mod_date->setTimezone(new \DateTimeZone('Asia/Jakarta'));
                $urls[]=[
                    'url' => get_the_permalink(),
                    'modified' => date_format($mod_date, 'Y-m-d H:i:s')
                ];                       
            }
        }

        return $urls;
    }

    /**
     * @param string $class value: 'error' | 'notice' | 'update' 
     * @param string $message 
     * @return void 
     */
    public static function add_admin_notice($class, $message){
        // $class = 'error' | 'notice' | 'update'        
        add_action('admin_notices', function() use ($class, $message) {
            echo '
                <div class="' . esc_attr($class) . '">
                    <p>' .
                    wp_kses_post($message) .
                    '<p>
                </div>';					
        });
    }

    public static function get_breadcrumb($home_text = null) :array {
        $breadcrumbs = [];        

        if (!is_front_page()) {
            $home_url = get_option('home');            
            if (!$home_text){
                $home_text = __('Home');
            }

            $breadcrumbs[] = [
                'title' => $home_text,
                'url' => $home_url
            ];
            
            if (is_single() ){
                $post_type = get_post_type();
                if ( $post_type ){
                    if($post_type == 'post'){
                        $page_for_posts_id = get_option( 'page_for_posts' );                        
                        if($page_for_posts_id){
                            $breadcrumbs[] = [
                                'text' => get_the_title($page_for_posts_id),
                                'url' => get_permalink($page_for_posts_id)
                            ];
                        }
                    }else{
                        $post_type_data = get_post_type_object( $post_type );
                        $breadcrumbs[] = [
                            'title' => $post_type_data->labels->name,
                            'url' => $home_url . '/' . $post_type_data->rewrite['slug']
                        ];                        
                    }                    
                }
                $breadcrumbs[] = [
                    'title' => get_the_title(),
                    'url' => self::get_current_url()
                ];                       
            } elseif (is_page()) {
                $breadcrumbs[] = [
                    'title' => the_title(),
                    'url' => self::get_current_url()
                ];                       
            } elseif (is_home()){
                global $post;
                $page_for_posts_id = get_option('page_for_posts');
                if ( $page_for_posts_id ) { 
                    $breadcrumbs[] = [
                        'title' => get_the_title($page_for_posts_id),
                        'url' => self::get_current_url()
                    ];
                }
            } elseif (is_category()){
                $breadcrumbs[] = [
                    'title' => single_term_title(),
                    'url' => self::get_current_url()
                ];                
            } elseif (is_archive()){                
                if ( is_day() ) {
                    $breadcrumbs[] = [
                        'title' => get_the_date(),
                        'url' => self::get_current_url()
                    ];                      
                } elseif ( is_month() ) {
                    $breadcrumbs[] = [
                        'title' => get_the_date( _x( 'F Y', 'monthly archives date format', 'text_domain' ) ),
                        'url' => self::get_current_url()
                    ];                      
                } elseif ( is_year() ) {
                    $breadcrumbs[] = [
                        'title' => get_the_date( _x( 'Y', 'yearly archives date format', 'text_domain' ) ),
                        'url' => self::get_current_url()
                    ];                          
                } else {
                    $title = explode(':', get_the_archive_title(), 2);
                    $breadcrumbs[] = [
                        'title' => trim($title[count($title)-1]),
                        'url' => self::get_current_url()
                    ];                         
                }
            }
        }

        return $breadcrumbs;
    }

    public static function replace_translated_text(array $translations, string $domain){
        add_filter( 'gettext',  function ( $translated_text, $text, $the_domain ) use($translations, $domain) {
            if(!is_admin() && $the_domain == $domain){
                foreach($translations as $key => $val){
                    if(str_contains($translated_text, $key)){
                        return str_replace($key, $val, $translated_text);
                    }
                }    
            }
            return $translated_text;
        }, 20, 3 );
    }

    public static function get_echo(callable $callback){
        ob_start();
        call_user_func($callback);
        return ob_get_clean();
    }

    public static function get_function_return_type($function){
        return (new \ReflectionFunction($function))->getReturnType();
    }
    
    public static function get_element_fields(Element $element){
        $fields = [];

        if($element instanceof Field){
            $fields[$element->id] = $element;
        }

        if($element instanceof Wrapper){
            foreach($element->elements as $_element){        
                if($_element instanceof Field){
                    $fields[$_element->id] = $_element;
                }

                if($_element instanceof Wrapper){
                    /** diloop supaya bisa dipush */
                    foreach(self::get_element_fields($_element) as $__element){ //nanti dicek
                        $fields[$__element->id] = $__element;
                    }
                }
            }   
        }
        return $fields;
    }    
 
    public static function set_fields_value(Element &$element, array $values){
        if($element instanceof Field && array_key_exists($element->id, $values)){
            $element->set_value($values[$element->id]);
        }

        if($element instanceof Wrapper){
            foreach($element->elements as $_element){        

                if($_element instanceof Field && array_key_exists($_element->id, $values)){
                    $_element->set_value($values[$_element->id]);
                }
        
                if($_element instanceof Wrapper){
                    self::set_fields_value($_element, $values);
                }
            }   
        }
    }    

    public static function rest_response(string $code, string $message, string|array|null $data, int $status){
        $response = [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
        return new \WP_REST_Response($response, $status);
    }
}
