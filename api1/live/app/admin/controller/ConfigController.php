<?php
namespace ITECH\Admin\Controller;

use Phalcon\Exception;
use ITECH\Admin\Controller\BaseController;
use ITECH\Admin\Form\ConfigForm;
use ITECH\Datasource\Model\Config;
use ITECH\Datasource\Lib\Util;
use ITECH\Datasource\Lib\Constant;
use ITECH\Admin\Lib\Config as LocalConfig;



class ConfigController extends BaseController
{
    
    public function initialize()
    {
        parent::initialize();
        parent::authenticate();
        parent::allowRole(array(Constant::ADMIN_TYPE_ROOT, Constant::ADMIN_TYPE_ADMIN));
    }

    public function uploadImageAjaxAction()
    {
        ini_set('display_errors', 'off');
        $response = array(
            'status' => \MBN\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                $fileSize = 508;

                if ($this->request->hasFiles() == true) {
                    $file = $this->request->getUploadedFiles();
                    if (isset($file[0])) {
                        $resource = array(
                            'name' => $file[0]->getName(),
                            'type' => $file[0]->getType(),
                            'tmp_name' => $file[0]->getTempName(),
                            'error' => $file[0]->getError(),
                            'size' => $file[0]->getSize()
                        );
                        
                        list($w) = getimagesize($file[0]->getTempName());
                        if (isset($w) && $w >= $fileSize) {
                            $width = $fileSize;
                        } else {
                            $width = $w;
                        }
                        
                        $response = parent::uploadImageToLocal(ROOT . '/web/admin/asset/upload/', '', $width, $resource);
                        if (isset($response['status']) && $response['status'] == \MBN\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                            $imageUpload = date('Y') . '/' . date('m') . '/' . date('d') . '/' . $response['result'];
                            parent::uploadImageToCdn(ROOT . '/web/admin/asset/upload/', 'upload-image', $response['result']);
                            parent::deleteImageFromLocal(ROOT . '/web/admin/asset/upload/', $response['result']);
                        }
                    }
                }
                
                $response = array(
                    'status' => \MBN\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Cập nhật hình ảnh thành công',
                    'result' => array(
                        'image' => $imageUpload,
                        'image_url' => $this->config->asset->frontend_url . 'img/upload-image/' . $imageUpload
                    )
                );

                goto RETURN_RESPONSE;
            }
        }

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }

    public function indexAction()
    {
        $config = new Config();
        $result = LocalConfig::setting();
        if ($result != '' && !empty($result)) {
            $config->ads = $result['ads'];
            $config->site_title = $result['site_title'];
            $config->title_h2 = Util::htmlDecode($result['title_h2']);
            $config->noidung_h2 =Util::htmlDecode($result['noidung_h2']);
            $config->title_h3 =Util::htmlDecode($result['title_h3']);
            $config->noidung_h3=Util::htmlDecode($result['noidung_h3']);
            $config->float_banner = Util::htmlDecode($result['float_banner']);
            $config->meta_title = $result['meta_title'];
            $config->meta_description = $result['meta_description'];
            $config->meta_keyword = $result['meta_keyword'];
            $config->geo_region = $result['geo_region'];
            $config->geo_placename = $result['geo_placename'];
            $config->geo_position = $result['geo_position'];
            $config->icbm = $result['icbm'];
            $config->logo = $result['logo'];
            $config->float_banner_title = Util::htmlDecode($result['float_banner_title']);
            // $config->logo1 =$result['logo1'];
            $config->logo_w = $result['logo_w'];
            $config->logo_h = $result['logo_h'];
            $config->footer_title = Util::htmlDecode($result['footer_title']);
            $config->footer = Util::htmlDecode($result['footer']);
            $config->page_face_book = $result['page_face_book'];
            $config->google_maps = Util::htmlDecode($result['google_maps']);
            $config->latitude = $result['latitude'];
            $config->longitude = $result['longitude'];
            $config->social_twitter = $result['social_twitter'];
            $config->social_facebook = $result['social_facebook'];
            $config->fb_admins = $result['fb_admins'];
            $config->social_google = $result['social_google'];
            $config->google_analytics = $result['google_analytics'];
            $config->google_analytics_domain = $result['google_analytics_domain'];
            $config->email_name = $result['email_name'];
            $config->email_title = $result['email_title'];
            $config->email_password = $result['email_password'];
            $config->email_signature = $result['email_signature'];
            $config->footer_navigation = $result['footer_navigation'];
            $config->tabs = $result['tabs'];
            $config->noi_bat = $result['noi_bat'];
            $config->tin_hot = $result['tin_hot'];
            $config->tieu_diem = $result['tieu_diem'];
            $config->moi_nhat = $result['moi_nhat'];
            $config->nhieu_nhat = $result['nhieu_nhat'];
            $config->moi_hon = $result['moi_hon'];
            $config->cu_hon = $result['cu_hon'];
            $config->hien_thi = $result['hien_thi'];
            $config->hotline = $result['hotline'];
            $config->theme = $result['theme'];
        }
        $form = new ConfigForm($config);
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            if ($this->request->hasFiles()) {
                $file = $this->request->getUploadedFiles();
                if (isset($file[0])) {
                    $resource = array(
                        'name' => $file[0]->getName(),
                        'type' => $file[0]->getType(),
                        'tmp_name' => $file[0]->getTempName(),
                        'error' => $file[0]->getError(),
                        'size' => $file[0]->getSize()
                    );                        

                    $response = parent::uploadLocalImage(ROOT . '/web/admin/asset/images/', '', 500, $resource);
                    if (!empty($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
                        $logo = array('logo' => $response['result']);
                        parent::uploadRemoteImage(ROOT . '/web/admin/asset/images/', 'default', $response['result']);
                        parent::deleteLocalImage(ROOT . '/web/admin/asset/images/', $response['result']);
                    } 
                }
            } else {
                $logo = array('logo' => $result['logo']);
            }
            if(empty($logo)){
                $logo = array('logo' => 'logo.png');
            }
            $data_new = array_merge($data, $logo);
            $config_admin = "<?php \nnamespace ITECH\Admin\Lib;\n\nclass Config {\n       /**\n        *@author Vu.Tran\n        */
            public static function setting() {\n                return array(\n";
                foreach($data_new as $k => $v)
                    {
                        $config_admin .= "        \t'".$k."' => " . (is_numeric($v) ? $v : "'". Util::htmlEncode($v) ."'") .",\n";
                    }
            $config_admin .= "                );\n            }\n}";
            
            $config_home = "<?php \nnamespace ITECH\Home\Lib;\n\nclass Config {\n       /**\n        *@author Vu.Tran\n        */
            public static function setting() {\n                return array(\n";
                foreach($data_new as $k => $v)
                    {
                        $config_home .= "        \t'". $k ."' => " . (is_numeric($v) ? $v : "'". Util::htmlEncode($v) ."'") .",\n";
                    }
            $config_home .= "                );\n            }\n}";
            
            $config_admin_file = ROOT . '/app/admin/lib/Config.php';
            $config_home_file = ROOT . '/app/home/lib/Config.php';
            $fw_admin = fopen($config_admin_file, "w");
            $fw_home = fopen($config_home_file, "w");
            if (!$fw_admin || !$fw_home) {
                $this->flashSession->error('Lỗi, không thể cập nhật.');
            } else {
                fwrite($fw_admin, $config_admin);
                fclose($fw_admin);
                fwrite($fw_home, $config_home);
                fclose($fw_home);
                $this->flashSession->success('Cập nhật thành công.');
            }
        }

        $page_header = 'Thiết lập hệ thống';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'form' => $form,
            'result' => $result
        ));
        $this->view->pick('config/index');
    }

    public function dropdown_timezone()
    {
        return array(
            'Pacific/Midway' => '(GMT-11:00) Midway Island, Samoa',
            'America/Adak' => '(GMT-10:00) Hawaii-Aleutian',
            'Etc/GMT+10' => '(GMT-10:00) Hawaii',
            'Pacific/Marquesas' => '(GMT-09:30) Marquesas Islands',
            'Pacific/Gambier' => '(GMT-09:00) Gambier Islands',
            'America/Anchorage' => '(GMT-09:00) Alaska',
            'America/Ensenada' => '(GMT-08:00) Tijuana, Baja California',
            'Etc/GMT+8' => '(GMT-08:00) Pitcairn Islands',
            'America/Los_Angeles' => '(GMT-08:00) Pacific Time (US & Canada)',
            'America/Denver' => '(GMT-07:00) Mountain Time (US & Canada)',
            'America/Chihuahua' => '(GMT-07:00) Chihuahua, La Paz, Mazatlan',
            'America/Dawson_Creek' => '(GMT-07:00) Arizona',
            'America/Belize' => '(GMT-06:00) Saskatchewan, Central America',
            'America/Cancun' => '(GMT-06:00) Guadalajara, Mexico City, Monterrey',
            'Chile/EasterIsland' => '(GMT-06:00) Easter Island',
            'America/Chicago' => '(GMT-06:00) Central Time (US & Canada)',
            'America/New_York' => '(GMT-05:00) Eastern Time (US & Canada)',
            'America/Havana' => '(GMT-05:00) Cuba',
            'America/Bogota' => '(GMT-05:00) Bogota, Lima, Quito, Rio Branco',
            'America/Caracas' => '(GMT-04:30) Caracas',
            'America/Santiago' => '(GMT-04:00) Santiago',
            'America/La_Paz' => '(GMT-04:00) La Paz',
            'Atlantic/Stanley' => '(GMT-04:00) Faukland Islands',
            'America/Campo_Grande' => '(GMT-04:00) Brazil',
            'America/Goose_Bay' => '(GMT-04:00) Atlantic Time (Goose Bay)',
            'America/Glace_Bay' => '(GMT-04:00) Atlantic Time (Canada)',
            'America/St_Johns' => '(GMT-03:30) Newfoundland',
            'America/Araguaina' => '(GMT-03:00) UTC-3',
            'America/Montevideo' => '(GMT-03:00) Montevideo',
            'America/Miquelon' => '(GMT-03:00) Miquelon, St. Pierre',
            'America/Godthab' => '(GMT-03:00) Greenland',
            'America/Argentina/Buenos_Aires' => '(GMT-03:00) Buenos Aires',
            'America/Sao_Paulo' => '(GMT-03:00) Brasilia',
            'America/Noronha' => '(GMT-02:00) Mid-Atlantic',
            'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
            'Atlantic/Azores' => '(GMT-01:00) Azores',
            'Europe/Belfast' => '(GMT) Greenwich Mean Time : Belfast',
            'Europe/Dublin' => '(GMT) Greenwich Mean Time : Dublin',
            'Europe/Lisbon' => '(GMT) Greenwich Mean Time : Lisbon',
            'Europe/London' => '(GMT) Greenwich Mean Time : London',
            'Africa/Abidjan' => '(GMT) Monrovia, Reykjavik',
            'Europe/Amsterdam' => '(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna',
            'Europe/Belgrade' => '(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague',
            'Europe/Brussels' => '(GMT+01:00) Brussels, Copenhagen, Madrid, Paris',
            'Africa/Algiers' => '(GMT+01:00) West Central Africa',
            'Africa/Windhoek' => '(GMT+01:00) Windhoek',
            'Asia/Beirut' => '(GMT+02:00) Beirut',
            'Africa/Cairo' => '(GMT+02:00) Cairo',
            'Asia/Gaza' => '(GMT+02:00) Gaza',
            'Africa/Blantyre' => '(GMT+02:00) Harare, Pretoria',
            'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
            'Europe/Minsk' => '(GMT+02:00) Minsk',
            'Asia/Damascus' => '(GMT+02:00) Syria',
            'Europe/Moscow' => '(GMT+03:00) Moscow, St. Petersburg, Volgograd',
            'Africa/Addis_Ababa' => '(GMT+03:00) Nairobi',
            'Asia/Tehran' => '(GMT+03:30) Tehran',
            'Asia/Dubai' => '(GMT+04:00) Abu Dhabi, Muscat',
            'Asia/Yerevan' => '(GMT+04:00) Yerevan',
            'Asia/Kabul' => '(GMT+04:30) Kabul',
            'Asia/Yekaterinburg' => '(GMT+05:00) Ekaterinburg',
            'Asia/Tashkent' => '(GMT+05:00) Tashkent',
            'Asia/Kolkata' => '(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi',
            'Asia/Katmandu' => '(GMT+05:45) Kathmandu',
            'Asia/Dhaka' => '(GMT+06:00) Astana, Dhaka',
            'Asia/Novosibirsk' => '(GMT+06:00) Novosibirsk',
            'Asia/Rangoon' => '(GMT+06:30) Yangon (Rangoon)',
            'Asia/Bangkok' => '(GMT+07:00) Bangkok, Hanoi, Jakarta',
            'Asia/Krasnoyarsk' => '(GMT+07:00) Krasnoyarsk',
            'Asia/Hong_Kong' => '(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi',
            'Asia/Irkutsk' => '(GMT+08:00) Irkutsk, Ulaan Bataar',
            'Australia/Perth' => '(GMT+08:00) Perth',
            'Australia/Eucla' => '(GMT+08:45) Eucla',
            'Asia/Tokyo' => '(GMT+09:00) Osaka, Sapporo, Tokyo',
            'Asia/Seoul' => '(GMT+09:00) Seoul',
            'Asia/Yakutsk' => '(GMT+09:00) Yakutsk',
            'Australia/Adelaide' => '(GMT+09:30) Adelaide',
            'Australia/Darwin' => '(GMT+09:30) Darwin',
            'Australia/Brisbane' => '(GMT+10:00) Brisbane',
            'Australia/Hobart' => '(GMT+10:00) Hobart',
            'Asia/Vladivostok' => '(GMT+10:00) Vladivostok',
            'Australia/Lord_Howe' => '(GMT+10:30) Lord Howe Island',
            'Etc/GMT-11' => '(GMT+11:00) Solomon Is., New Caledonia',
            'Asia/Magadan' => '(GMT+11:00) Magadan',
            'Pacific/Norfolk' => '(GMT+11:30) Norfolk Island',
            'Asia/Anadyr' => '(GMT+12:00) Anadyr, Kamchatka',
            'Pacific/Auckland' => '(GMT+12:00) Auckland, Wellington',
            'Etc/GMT-12' => '(GMT+12:00) Fiji, Kamchatka, Marshall Is.',
            'Pacific/Chatham' => '(GMT+12:45) Chatham Islands',
            'Pacific/Tongatapu' => '(GMT+13:00) Nukualofa',
            'Pacific/Kiritimati' => '(GMT+14:00) Kiritimati'
        );
    }
}