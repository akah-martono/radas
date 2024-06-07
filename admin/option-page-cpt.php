<?php
namespace Radas\Admin;

use Radas\Admin\Assets\Admin_Scripts_Rds;
use Radas\Admin\Assets\Admin_Styles_Rds;
use Radas\Lib\Assets\Admin_Scripts_Lib;
use Radas\Lib\Assets\Admin_Styles_Lib;
use Radas\Lib\HTML_Templates\Field_Box_Template;

class Option_Page_CPT {
    protected $page_slug = 'radas-option-page';

    public function __construct(){

        add_action( 'admin_menu', function () {
            add_submenu_page(
                parent_slug: 'tools.php',
                page_title: 'Radas Options Page',
                menu_title: 'Radas Options Page',
                capability: 'manage_options',
                menu_slug: 'radas-option-page',
                callback: fn() => $this->render(),
                position: null
            );            
        });   

        
        add_action('admin_init', function(){
            /** no need to check nonce, this condition just to make sure the script run on right page (to prevent bloated)*/
            if( array_key_exists('page', $_GET) &&  $_GET['page'] === 'radas-option-page' ){
                Admin_Scripts_Rds::enqueue("_rds-option-page-cpt");
                Admin_Styles_Rds::enqueue(['_rds-fieldbox']);                
            }
        });

    }

    protected function render(){
        
        $end_point = RADAS_REST_NAMESPACE . '/cpt/option-pages';

        ?>
        <div id="rds-page">
            <div id="rds-page-title">
                <h1>Radas Options Page</h1>
                <div>
                    <div class="btn-warpper-header hide" style="min-width: 134px;">
                        <button class="btn-save-page button button-primary">Create</button>
                        <button class="btn-close-page button button-primary">Close</button>
                    </div>
                </div>
            </div> 
            <div id="rds-list" style="padding: 24px 32px;">
                <table id="option-page-list" data-endpoint="<?php echo esc_attr($end_point); ?>" class="display compact" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>    
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Capability</th>
                            <th>Type</th>                            
                            <th>Menu</th>
                            <th>Actions</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div id="rds-form" class="hide">
                <div id="rds-content">
                    <form data-endpoint="<?php echo esc_attr($end_point); ?>" onsubmit="return false;" noValidate>
                    <?php 
                    $this->the_content(); 
                    ?>
                    </form>
                </div>
                <div id="rds-page-footer">
                    <button class="btn-save-page button button-primary">Create</button>
                    <button class="btn-close-page button button-primary">Close</button>
                </div>
            </div>            
        </div>
        <div class="rds-spinner">
            <span class="loader"></span>
        </div>
        <?php
    }

    protected function the_content(){
        
        Field_Box_Template::register();

        $capability_options = function(){
            $wp_roles = wp_roles();
            $options = '';
            foreach($wp_roles->roles as $key=>$value){
                if(array_key_exists('capabilities', $value)){
                    foreach($value['capabilities'] as $ckey => $cvalue) {
                        if(substr($ckey,0,6) != 'level_'){
                            $options .= printf( 
                                '<option value="%s" %s>%s</option>', 
                                esc_attr($ckey), 
                                ($ckey=="manage_options") ? "selected" : "",
                                esc_html($ckey)
                            );
                        }
                    }
                }
            }     
            return $options;
        };

        $menu_options = function() {
            global $menu;
            $options = '';
            foreach ($menu as $key => $value) {
                $opt_val = $value[2];
                $opt_text = $value[0];
                
                if($opt_val == 'plugins.php' ) {
                    $opt_text = "Plugins";
                }

                if($opt_val == 'edit-comments.php' ) {
                    $opt_text = "Comments";
                }

                if( $opt_text != '' ) {
                    $options .= printf( '<option value="%s">%s</option>', esc_attr($opt_val), esc_html($opt_text));
                }
            }   
            return $options;
        };

        ?>
        <div id="rds-header" class="flex">                
            <input type="hidden" id="post_id" name="post_id" class="rds-input" data-handle="post_id">
            <div id="left_header" class="rg-px-8 rd-px-16 rg-basis-full rd-basis-1/2">
                <table class="rds-table form-table"><tbody>                    
                    <tr class="rds-field">
                        <th>
                            <label for="page_title" class="rds-field-label">Page Title</label>
                        </th>
                        <td>
                            <input type="text" id="page_title" name="page_title" class="rds-input regular-text" data-handle="title">
                        </td>
                    </tr>
                    <tr class="rds-field">
                        <th>
                            <label for="page_slug" class="rds-field-label">Slug</label>
                        </th>
                        <td>
                            <input type="text" id="page_slug" name="page_slug" required="" class="rds-input regular-text"  data-handle="slug">
                        </td>
                    </tr>
                    <tr class="rds-field">
                        <th>
                            <label for="page_description" class="rds-field-label">Description</label>
                            </th>
                        <td>
                            <textarea id="page_description" name="page_description" class="rds-input regular-text" data-handle="description"></textarea>
                        </td>
                    </tr>
                    <tr class="rds-field">
                        <th>
                            <label for="capability" class="rds-field-label">Capability</label>
                        </th>
                        <td>
                            <select id="capability" name="capability" required="" class="rds-input" data-handle="capability">
                                <?php $capability_options(); ?>
                            </select>
                        </td>
                    </tr>
                </tbody></table>
            </div>
            <div id="right_header" class="rg-px-8 rd-px-16 rg-basis-full rd-basis-1/2">
                <table class="rds-table form-table"><tbody>                    
                    <tr class="rds-field">
                        <th>
                            <label for="page_type" class="rds-field-label">Page Type</label>
                        </th>
                        <td>
                            <select id="page_type" name="page_type" class="rds-input" data-handle="type">
                                <option value="menu" selected>Menu Page</option>
                                <option value="sub_menu">Sub Menu Page</option>        
                            </select>
                        </td>
                    </tr>
                    <tr class="rds-field hide">
                        <th>
                            <label for="parent_menu" class="rds-field-label">Parent Menu</label>                            
                        </th>
                        <td>
                            <select id="parent_menu" name="parent_menu" class="rds-input" data-handle="parent_menu">                                
                                <?php 
                                printf( '<option value="">%s</option>', esc_html(__('Select...', RADAS_DOMAIN)));
                                $menu_options(); 
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="rds-field" >
                        <th>
                            <label for="menu_title" class="rds-field-label">Menu Title</label>
                        </th>
                        <td>
                            <input type="text" id="menu_title" name="menu_title" required="" class="rds-input regular-text" data-handle="menu_title">
                        </td>
                    </tr>
                    <tr class="rds-field">
                        <th>
                            <label for="page_icon" class="rds-field-label">Icon</label>
                        </th>
                        <td>
                            <div style="display: flex; gap:2px;">
                                <button id="btn_choose_icon" style="margin: 0;" class="button button-secondary dashicons-before dashicons-admin-generic" data-handle="btn_icon"></button>
                                <input type="text" value="dashicons-admin-generic" id="page_icon" name="page_icon" readonly="" style="width: 100%; max-width: 21.9em;" class="rds-input"  data-handle="page_icon">
                            </div>
                        </td>
                    </tr>
                    <tr class="rds-field">
                        <th>
                            <label for="position" class="rds-field-label">Position</label>
                        </th>
                        <td>
                            <input type="number" id="position" name="position" class="rds-input small-text"  data-handle="position">
                        </td>
                    </tr>                  
                </tbody></table>
            </div>
        </div>
        <div id="rds-fields-box-wrapper" class="rg-px-8 rd-px-16">
            <div class="rds-fields-header rm-p-8">
                <div class="rds-fields-title">
                    <h3>Fields</h3>
                    <button class="btn-new-field button button-secondary dashicons-before dashicons-plus"> New Field</button>
                </div>
                <div class="button-group expand-collapse-wrapper hide">
                    <button class="button button-secondary btn-expand-all">Expand All</button>
                    <button class="button button-secondary btn-collapse-all">Collapse All</button>                    
                </div>                
            </div>
            <div class="rds-fields-content">
                <div class="rds-field-boxes"></div>
                <div class="rds-btn-add-wrapper rds-btn-add-footer rg-pt-8"></div>
            </div>
        </div>           
        <?php        
    }
}
