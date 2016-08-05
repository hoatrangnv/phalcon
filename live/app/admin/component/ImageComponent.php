<?php
namespace ITECH\Admin\Component;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View\Simple as View;
use ITECH\Datasource\Repository\CategoryRepository;
use ITECH\Datasource\Lib\Constant;

class ImageComponent extends Component
{
    /**
     * @author Vu.Tran
     */
    public function dir($result, $i, $path)
    {
        
        if (count($result) > 0) {
            $dir_layout = '<ul>';
            $j = 0;
            foreach ( $result as $key => $item) {
                $dir_layout .= '<li data-folder ="'. $key .'" id="id'. $i . '.' . $j . '" title="'. $path . '/' . $key . '" class="folder">' . $key;
                if (count($item) > 0) {
                    $new_path .= $path . '/' . $key;
                    $dir_layout .= $this->dir($item, $j, $new_path);
                }
                $j++;
            } 
            $dir_layout .= '</ul>';
        }
        else {
            $dir_layout .= '';
        }
        
        return $dir_layout;
    }
    
    /**
     * @author Vu.Tran
     */
    public function dirSelect($result, $level, $path)
    {
        
        if (count($result) > 0) {
            $dir_layout = '';
            $level .= '-';
            foreach ( $result as $key => $item) {
                $dir_layout .= '<option value="'. $path . '/' . $key . '">'. $level . $key .'</option>';
                if (count($item) > 0) {
                    $new_path .= $path . '/' . $key;
                    $dir_layout .= $this->dir($item, $level, $new_path);
                }
            }
        }
        else {
            $dir_layout .= '';
        }
        
        return $dir_layout;
    }
}
