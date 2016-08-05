<?php
namespace ITECH\Admin\Component;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View\Simple as View;
use ITECH\Datasource\Repository\LinkRepository;
use ITECH\Datasource\Lib\Constant;

class ConfigComponent extends Component
{
    /**
     * @author Vu.Tran
     */
    public function load_file($params)
    {
        $file = App::$path . DS . 'app' . DS . str_replace('.', DS, $module) . '.xml';

        if (!file_exists($file))
        {
            throw new Exception("The <b>config file</b> of module <b>{$module}</b> does not exist");
        }  
        
        $xml = json_decode(json_encode((array) simplexml_load_file($file)), true);
        
        if (!isset($xml['@attributes']['filename']) || $xml['@attributes']['filename'] == "")
        {
            throw new Exception('Tập tin cấu hình của chức năng <b>'.$module.'</b> thiếu thuộc tính <b>filename</b> trong thẻ <b>config</b>. Đây là thuộc tính bắt buộc, vui lòng kiểm tra lại');    
        }
                
        return $xml;
    }
}
