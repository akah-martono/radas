<?php
namespace Radas\Admin\Rest\CPT;

use Radas\Admin\Option_Page;
use Radas\Lib\Helper\Util;
use WP_REST_Request;
use WP_REST_Response;

/** 
 * @package Radas\Admin\Rest\CPT 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
*/
class REST_Option_Page {
    protected static $route="/cpt/option-pages";
    protected static $post_type = 'rds_option_page';

    /** @return void  */
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_rest']);
    }

    /** @return void  */
    public function register_rest(){
            // Register a new endpoint to manage a custom option
            register_rest_route(RADAS_REST_NAMESPACE, self::$route . "/(?P<id>\d+)", array(
                'methods' => array('GET', 'PUT', 'DELETE'), // Supported HTTP methods
                'callback' =>  [$this, 'rest_handle'], // Callback function to handle the request
                'permission_callback' => [$this, 'rest_permission'], // Permission check for this endpoint
            ));

            register_rest_route(RADAS_REST_NAMESPACE, self::$route, array(
                'methods' => array('GET', 'POST'), // Supported HTTP methods
                'callback' =>  [$this, 'rest_handle'], // Callback function to handle the request
                'permission_callback' => [$this, 'rest_permission'], // Permission check for this endpoint
            ));                 
    }

    public function rest_permission(\WP_REST_Request $request){
        
        if ( ! current_user_can('manage_options') ) {
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
        switch ($method) {
            case 'GET':
                return $this->get($request);
            case 'POST':
                return $this->post($request);
            case 'PUT':
                return $this->put($request);
            case 'DELETE':
                return $this->delete($request);
            default:
                return Util::rest_response(
                    code: 'not_supported', 
                    message: "Method not supported",
                    data: [],
                    status: 405
                );            
        }
    }

    /**
     * @param WP_REST_Request $request 
     * @return mixed 
     */
    protected function delete(\WP_REST_Request $request){
        $id = $request->get_param('id');
        if(!$id) {
            return Util::rest_response(
                code: 'rds_not_found', 
                /* translators: %s: the post ID to be deleted */
                message: sprintf(__('Radas options page for id %s not found'), $id),
                data: [
                    'id' => $id,     
                ],
                status: 404
            );
        }     
        
        $page = json_decode(get_post_meta($id, 'op_json', true), true);
        if(get_option($page['slug']) && !delete_option($page['slug'])){
            return Util::rest_response(
                code: 'failed', 
                message: __('Something wrong, option cannot be deleted'),
                data: [
                    'id' => $id
                ] ,
                status: 500
            );    
        }

        if(wp_delete_post($id, true)) {
            return Util::rest_response(
                code: 'success', 
                message: __('Your data has been successfully deleted'),
                data: [
                    'id' => $id, 
                ] ,
                status: 200
            );            
        }

        return Util::rest_response(
            code: 'failed', 
            message: 'unknown error',
            data: [
                'id' => $id
            ] ,
            status: 500
        );          
            
    }

    /**
     * @param WP_REST_Request $request 
     * @return mixed 
     */
    protected function get(\WP_REST_Request $request){
        $id = $request->get_param('id');

        if(!$id) {
            return $this->get_list($request);
        }

        $data = json_decode(get_post_meta($id,'op_json', true), true);

        if(!$data) {
            return Util::rest_response(
                code: 'rds_not_found', 
                message: "Radas options page for id {$id} not found",
                data: [
                    'id' => $id,     
                ],
                status: 404
            );
        }

        return Util::rest_response(
            code: 'success', 
            message: "Radas options page for id {$id}",
            data: [
                'id' => $id, 
                'detail' => $data
            ] ,
            status: 200
        );
    }

    /**
     * @param WP_REST_Request $request 
     * @return WP_REST_Response 
     */
    protected function get_list(\WP_REST_Request $request){
        $posts = [];
        
        // The Query.
        $the_query = new \WP_Query( [
            'post_type' => self::$post_type, 
            'nopaging' => true
        ] );        

        // The Loop.
        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                $post = get_post();
                $page = json_decode(get_post_meta($post->ID, 'op_json', true), true) ;
                if($page){
                    $posts[] = [
                        'id'            => $post->ID,
                        'title'         => $post->post_title,
                        'slug'          => $page['slug'],
                        'capability'    => $page['capability'],
                        'type'          => $page['type'],
                        'menu'          => ($page['type'] == 'menu') ? $page['slug'] : $page['parent'],
                        'details'       => $page
                    ];    
                }
            }
        } else {
            // esc_html_e( 'Sorry, no posts matched your criteria.' );
        }
        // Restore original Post Data.
        wp_reset_postdata();

        return Util::rest_response(
            code: 'success', 
            /* translators: %s: the number of rows found */
            message: sprintf(__('Found %s rows'), $the_query->post_count),
            data: $posts ,
            status: 200
        );
    }
    
    /**
     * @param WP_REST_Request $request 
     * @return mixed 
     */
    protected function put(\WP_REST_Request $request){              
        $id = $request->get_param('id');
        $data = $request->get_json_params();

        $post = get_post($id);

        if($post->post_name !== $data['slug']) {
            return Util::rest_response(
                code: 'failed', 
                message: __('The slug must not be changed'),
                data: [
                    'id' => $id
                ],
                status: 406
            );             
        }

        $update = wp_update_post([
            'ID'           => $id,
            'post_title'    => $data['title'],
            'post_name'     => $data['slug'],
            'post_author'   => get_current_user_id(),
        ]);

        if ( is_wp_error( $update ) ) {
            return Util::rest_response(
                code: 'failed', 
                message: $update->get_error_message(),
                data: [
                    'id' => $id
                ] ,
                status: 500
            );            
        }  

        update_post_meta($id, 'op_json', $request->get_body());

        // re-set chache
        wp_cache_delete(Option_Page::class . '_' . $id, 'radas_instance');
        Util::get_instance_with_param(Option_Page::class, $id, $id);

        return Util::rest_response(
            code: 'success', 
            message: __('Your data has been successfully updated'),
            data: [
                'id' => $id
            ] ,
            status: 200
        );        
    }

    /**
     * @param WP_REST_Request $request 
     * @return mixed 
     */
    protected function post(\WP_REST_Request $request){              
        $data = $request->get_json_params();

        $exist = get_posts(array(
            'name' => $data['slug'],
            'post_type' => self::$post_type,
        ));        
         
        if($exist){
            return Util::rest_response(
                code: 'failed', 
                message: __('This slug is not available, please change the slug'),
                data: [] ,
                status: 409
            );
        }

        
        // if(menu_page_url($data['slug'], false)){
        //     return Util::rest_response(
        //         code: 'failed', 
        //         message: "Slug already exist, please change the slug",
        //         data: [] ,
        //         status: 409
        //     );
        // }

        if(get_option($data['slug'])){
            return Util::rest_response(
                code: 'failed', 
                message: __('This slug has already been used by another option, please change the slug'),
                data: [] ,
                status: 409
            );
        }
                
        $new_id = wp_insert_post([
            'post_title'    => $data['title'],
            'post_name'     => $data['slug'],
            'post_author'   => get_current_user_id(),
            'post_status'   => 'publish',
            'post_type'     => self::$post_type,
            'meta_input'    => array(
                'op_json'       => $request->get_body(),
            ),
        ]); 

        if ( is_wp_error( $new_id ) ) {
            return Util::rest_response(
                code: 'failed', 
                message: $new_id->get_error_message(),
                data: [] ,
                status: 500
            );            
        }        

        // create option page and save it to chache
        Util::get_instance_with_param(Option_Page::class, $new_id, $new_id);

        return Util::rest_response(
            code: 'success', 
            message: __('Your data has been successfully created'),
            data: [
                'id' => $new_id,
                'detail' => $data
            ] ,
            status: 200
        );
    }
}