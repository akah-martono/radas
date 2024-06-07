<?php
namespace Radas\Lib\Elements\Fields;

use ArrayAccess;
use Radas\Lib\Classes\Abstracts\Field;
use Radas\Lib\Classes\Trait\ArrayAccess_Skeleton;

class Field_Group implements ArrayAccess  {
use ArrayAccess_Skeleton;
    /** @propery string $id The ID of the section */    
    protected $id;

    /** @var string $title The title of the section */
    protected $title;

    /** @var string $info The information of the section, will shown below of the title */
    protected $info;

    /** @var Field[] $fields Fields in the section */
    protected $fields = [];

    /**
     * @param string $id 
     * @return void 
     */
    public function __construct(string $id) {
        $this->id = $id;
    }

    /**
     * @param string $title 
     * @return $this 
     */
    public function set_title(string $title){
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $info 
     * @return $this 
     */
    public function set_info(string $info){
        $this->info = $info;
        return $this;
    }

    /**
     * @param Field $field 
     * @return $this 
     */
    public function add_field( Field|Field_Group $field) {
        $this->fields[$field->id] = $field;        
        return $this;
    }

    public function render(){
        ?>
        <table id="<?php echo esc_attr("group_{$this->id}") ?>" class="form-table"><tbody>            
            <?php 
            if ($this->info) 
                echo '<p>' . esc_html($this->info) . '</p>'; 
            
            foreach($this->fields as $field){
                /** @var Field $field */
                if($field instanceof Field){
                    $field->render();
                }
                
                /** @var Field_Group $field */
                if($field instanceof Field_Group){
                    $field->render();
                }
                
            }
            ?>
        </tbody></table>          
        <?php        
    } 

    /**
     * @param mixed $name 
     * @return mixed 
     */
    public function __get($name): mixed {
        if(property_exists($this, $name)){
            return @$this->$name;
        }
    }         
}