<?php
namespace ITECH\Admin\Component;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View\Simple as View;
use ITECH\Datasource\Repository\CategoryRepository;
use ITECH\Datasource\Lib\Constant;

class CategoryComponent extends Component
{
    /**
     * @author Vu.Tran
     */
    public function sub_select($params, $sub_category_layout, $level, $category)
    {
        $category_repository = new CategoryRepository();
        $sub_categories = $category_repository->getList($params);
        if (count($sub_categories) > 0) {
            $level .= '-';
            $sub_category_layout = '';
            foreach ($sub_categories as $sub_item):
                $active = '';
                if ($category == $sub_item->id) {
                    $active = 'selected="selected"';
                }
                $sub_category_layout .= '<option ' . $active .' value="' . $sub_item->id . '">' . $level . $sub_item->name .'</option>';
                
                $params = array(
                    'conditions' => array(
                        'module' => $params['conditions']['module'],
                        'parent_id' => $sub_item->id,
                    ),
                );
                $sub_category_layout .= $this->sub_select($params, $sub_category_layout, $level, $category);
            endforeach; 
            
        }
        else {
            $sub_category_layout = '';
        }
        
        return $sub_category_layout;
    }
    
    /**
     * @author Vu.Tran
     */
    public function sub_checkbox($params, $sub_category_layout, $level, $in_array)
    {
        $category_repository = new CategoryRepository();
        $sub_categories = $category_repository->getList($params);
        if (count($sub_categories) > 0) {
            $level += 10;
            $sub_category_layout = '';
            foreach ($sub_categories as $sub_item):
                $active = '';
                if (in_array($sub_item->id, $in_array)) {
                    $active = 'checked="checked"';
                }
                $sub_category_layout .= '<div style="position:relative;min-height:27px;padding-left:20px; padding-top:7px; left:'. $level .'%" class="checkbox"><input type="checkbox" name="category[]" value="' . $sub_item->id . '" class="red" ' . $active . '>' . $sub_item->name . '</div>';
                
                $params = array(
                    'conditions' => array(
                        'module' => $params['conditions']['module'],
                        'parent_id' => $sub_item->id,
                    ),
                );
                
                $sub_category_layout .= $this->sub_checkbox($params, $sub_category_layout, $level, $in_array);
            endforeach; 
            
        }
        else {
            $sub_category_layout = '';
        }
        
        return $sub_category_layout;
    }
    
    /**
     * @author Vu.Tran
     */
    public function file_sub_select($params, $sub_category_layout, $level, $category)
    {
        $category_repository = new CategoryRepository();
        $sub_categories = $category_repository->getList($params);
        if (count($sub_categories) > 0) {
            $level .= '-';
            $sub_category_layout = '';
            foreach ($sub_categories as $sub_item):
                $active = '';
                if ($category == $sub_item->id) {
                    $active = 'selected="selected"';
                }
                $sub_category_layout .= '<option ' . $active .' value="' . $sub_item->id . '">' . $level . $sub_item->name .'</option>';
                
                $params = array(
                    'conditions' => array(
                        'module' => $params['conditions']['module'],
                        'parent_id' => $sub_item->id,
                    ),
                );
                $sub_category_layout .= $this->sub_select($params, $sub_category_layout, $level, $category);
            endforeach; 
            
        }
        else {
            $sub_category_layout = '';
        }
        
        return $sub_category_layout;
    }
}
