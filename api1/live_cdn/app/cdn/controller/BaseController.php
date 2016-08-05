<?php
namespace ITECH\Cdn\Controller;

use Phalcon\Mvc\Controller;
use ITECH\Cdn\Lib\Upload;

class BaseController extends Controller
{
    /**
     * @author Cuong.Bui
     */
    public function onConstruct()
    {

    }

    /**
     * @author Cuong.Bui
     */
    public function outputJSON($response)
    {
        $this->view->disable();

        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setJsonContent($response);
        $this->response->send();
    }

    public function getUploadThumbnail($data_source = null, $destination = '', $file_name = '')
    {
        $return = '';
        $file_name = explode('.', $file_name);
        if ($file_name[0] == '') {
            $file_name[0] =  uniqid() . time();
        }

        $upload = new Upload($data_source);
        $upload->allowed = array('image/*');
        $upload->forbidden = array('application/*');
        $upload->dir_chmod = 0775;
        $upload->dir_auto_create = true;

        if ($upload) {
            if ($upload->uploaded) {
                $upload->jpeg_quality = 100;
                $upload->image_resize = true;
                $upload->image_x = 500;
                $upload->image_ratio_y = true; 

                $upload->image_watermark = ROOT . '/web/cdn/asset/home/img/watermark.png';
                $upload->image_watermark_position = 'BR';
                //upload->image_watermark = ROOT . '/web/cdn/asset/home/img/logo_watermark.png';
                //$upload->image_watermark_position = 'BR';

                $upload->file_new_name_body = $file_name[0];

                $upload->process($destination . '/500/');
                chmod($destination . '/500', 0755);

                $upload->jpeg_quality = 100;
                $upload->image_resize = true;
                $upload->image_x = 250;
                $upload->image_ratio_y = true;


                $upload->file_new_name_body = $file_name[0];
                $upload->process($destination . '/250/');
                chmod($destination . '/250', 0755);

                $upload->jpeg_quality = 100;
                $upload->image_resize = true;
                $upload->image_x = 150;
                $upload->image_ratio_y = true;

                $upload->file_new_name_body = $file_name[0];
                $upload->process($destination . '/150/');
                chmod($destination . '/150', 0755);

                if ($upload->processed) {
                        $return = $file_name[0];
                        //$upload->clean();
                }
            } 
        }

        return $return;
    }

    public function getUploadContentThumbnail($data_source = null, $destination = '', $file_name = '')
    {
        $return = '';
        $file_name = explode('.', $file_name);
        if ($file_name[0] == '') {
            $file_name[0] =  uniqid() . time();
        }

        $upload = new Upload($data_source);
        $upload->allowed = array('image/*');
        $upload->forbidden = array('application/*');
        $upload->dir_chmod = 0775;
        $upload->dir_auto_create = true;

        if ($upload) {
            if ($upload->uploaded) {
                $upload->jpeg_quality = 100;
                $upload->image_resize = true;
                $upload->image_x = 500;
                $upload->image_ratio_y = true; 

                $upload->image_watermark = ROOT . '/web/cdn/asset/home/img/watermark.png';
                $upload->image_watermark_position = 'BR';
                //upload->image_watermark = ROOT . '/web/cdn/asset/home/img/logo_watermark.png';
                //$upload->image_watermark_position = 'BR';

                $upload->file_new_name_body = $file_name[0];

                $upload->process($destination . '/500/');
                chmod($destination . '/500', 0755);

                $upload->jpeg_quality = 100;
                $upload->image_resize = true;
                $upload->image_x = 250;
                $upload->image_ratio_y = true;

                $upload->file_new_name_body = $file_name[0];
                $upload->process($destination . '/250/');
                chmod($destination . '/250', 0755);

                $upload->jpeg_quality = 100;
                $upload->image_resize = true;
                $upload->image_x = 150;
                $upload->image_ratio_y = true;


                $upload->file_new_name_body = $file_name[0];
                $upload->process($destination . '/150/');
                chmod($destination . '/150', 0755);

                if ($upload->processed) {
                        $return = $file_name[0];
                        //$upload->clean();
                }
            }
        }

        return $return;
    }
}