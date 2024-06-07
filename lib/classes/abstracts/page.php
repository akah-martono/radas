<?php
namespace Radas\Lib\Classes\Abstracts;

use Radas\Lib\Classes\Trait\Element_Skeleton;
use Radas\Lib\Assets\Admin_Styles_Lib;

/** 
 * @package Radas\Lib\Classes\Abstracts
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
 * */
abstract class Page {
    use Element_Skeleton;

    abstract protected function content();

    final public function render(){
        Admin_Styles_Lib::enqueue('rds-page');
        // Libs_Scripts::enqueue('rds-page');

        echo "<div id=\"rds-page\" {$this->render_attributes()}>";
        ?>        
        <div id="rds-page-title">
            <h1><?php echo esc_attr($this->label); ?></h1>
            <button class="btn-save-page button button-primary">Save</button>
        </div> 
        <div id="rds-admin-notice"></div>        
        <div id="rds-content">
            <?php
            if($this->description) {
                printf('<p class="rds-description description">%s</p>', esc_html($this->description));
            }    
            ?>
            <?php $this->content(); ?>
        </div>
        <div id="rds-page-footer">
            <button class="btn-save-page button button-primary">Save</button>
        </div>
        <?php
        echo "</div>";
    }
}