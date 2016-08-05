<?php
namespace ITECH\Admin\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use ITECH\Datasource\Lib\Constant;

class ConfigForm extends Form
{
    /**
     * @author Vu.Tran
     */
    public function initialize($model, $options)
    {		 	 			
        $site_title = new Text('site_title', array('placeholder' => 'Công ty Subdevil'));
        $site_title->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập tiêu đề cho website.'
            )),
            new StringLength(array(
                'min' => 3,
                'messageMinimum' => 'Tiêu đề phải lớn hơn hoặc bằng 3 ký tự.'
            ))
        ));
        $site_title->setFilters(array('striptags', 'trim'));
        $this->add($site_title);
        
        $float_banner = new TextArea('float_banner');
        $float_banner->setFilters(array('striptags', 'trim'));
        $this->add($float_banner);

        $float_banner_title = new Text('float_banner_title');
        $float_banner_title->setFilters(array('striptags', 'trim'));
        $this->add($float_banner_title);

        $title_h2 = new TextArea('title_h2');
        $title_h2->setFilters(array('striptags', 'trim'));
        $this->add($title_h2);

        $noidung_h2 = new TextArea('noidung_h2');
        $noidung_h2->setFilters(array('striptags', 'trim'));
        $this->add($noidung_h2);

        $title_h3 = new TextArea('title_h3');
        $title_h3->setFilters(array('striptags', 'trim'));
        $this->add($title_h3);

        $noidung_h3 = new TextArea('noidung_h3');
        $noidung_h3->setFilters(array('striptags', 'trim'));
        $this->add($noidung_h3);

        $meta_title = new TextArea('meta_title');
        $meta_title->setFilters(array('striptags', 'trim'));
        $this->add($meta_title);
        
        $meta_description = new TextArea('meta_description');
        $meta_description->setFilters(array('striptags', 'trim'));
        $this->add($meta_description);	
         	
        $meta_keyword = new TextArea('meta_keyword');
        $meta_keyword->setFilters(array('striptags', 'trim'));
        $this->add($meta_keyword);
        
        $geo_region = new Text('geo_region', array('placeholder' => 'VN-SG'));
        $geo_region->setFilters(array('striptags', 'trim'));
        $this->add($geo_region);
        
        $geo_placename = new Text('geo_placename', array('placeholder' => 'Địa chỉ công ty'));
        $geo_placename->setFilters(array('striptags', 'trim'));
        $this->add($geo_placename);
        
        $geo_position = new Text('geo_position', array('placeholder' => '10.7703844; 106.6752956'));
        $geo_position->setFilters(array('striptags', 'trim'));
        $this->add($geo_position);
        
        $icbm = new Text('icbm', array('placeholder' => '10.7703844,106.6752956'));
        $icbm->setFilters(array('striptags', 'trim'));
        $this->add($icbm);
        
        $social_twitter = new Text('social_twitter', array('placeholder' => 'http://'));
        $social_twitter->setFilters(array('striptags', 'trim'));
        $this->add($social_twitter);
        
        $social_facebook = new Text('social_facebook', array('placeholder' => '357584370994953'));
        $social_facebook->setFilters(array('striptags', 'trim'));
        $this->add($social_facebook);
        
        $fb_admins = new Text('fb_admins', array('placeholder' => '100000091680510,100000866550113'));
        $fb_admins->setFilters(array('striptags', 'trim'));
        $this->add($fb_admins);
        
        $social_google = new Text('social_google', array('placeholder' => 'https://plus.google.com/+google_flus_name'));
        $social_google->setFilters(array('striptags', 'trim'));
        $this->add($social_google);
        
        $google_analytics = new Text('google_analytics', array('placeholder' => 'XX-XXXXXXX-X'));
        $google_analytics->setFilters(array('striptags', 'trim'));
        $this->add($google_analytics);
        
        $google_analytics_domain = new Text('google_analytics_domain', array('placeholder' => 'Domain'));
        $google_analytics_domain->setFilters(array('striptags', 'trim'));
        $this->add($google_analytics_domain);
        
        $email_name = new Text('email_name', array('placeholder' => '__@__.__'));
        $email_name->setFilters(array('striptags', 'trim'));
        $this->add($email_name);
        
        $email_password = new Password('email_password');
        $email_name->setFilters(array('striptags', 'trim'));
        $this->add($email_password);
        
        $email_title = new Text('email_title', array('placeholder' => 'Tiêu đề gửi email'));
        $email_title->setFilters(array('striptags', 'trim'));
        $this->add($email_title);
        
        $email_signature = new TextArea('email_signature');
        $email_signature->setFilters(array('striptags', 'trim'));
        $this->add($email_signature);
        
        $logo_w = new Text('logo_w');
        $logo_w->setFilters(array('striptags', 'trim'));
        $this->add($logo_w);
        
        $logo_h = new Text('logo_h');
        $logo_h->setFilters(array('striptags', 'trim'));
        $this->add($logo_h);
        
        $google_maps = new TextArea('google_maps');
        $google_maps->setFilters(array('striptags', 'trim'));
        $this->add($google_maps);
        
        $latitude = new Text('latitude');
        $latitude->setFilters(array('striptags', 'trim'));
        $this->add($latitude);
        
        $longitude = new Text('longitude');
        $longitude->setFilters(array('striptags', 'trim'));
        $this->add($longitude);
        
        $page_face_book = new Text('page_face_book');
        $page_face_book->setFilters(array('striptags', 'trim'));
        $this->add($page_face_book);
        
        $footer = new TextArea('footer');
        $footer->setFilters(array('striptags', 'trim'));
        $this->add($footer);

        $footer_title = new TextArea('footer_title');
        $footer_title->setFilters(array('striptags', 'trim'));
        $this->add($footer_title);
        
        $footer_navigation = new Text('footer_navigation', array('placeholder' => 'ID nhóm liên kết: 1,2,3,4'));
        $footer_navigation->setFilters(array('striptags', 'trim'));
        $this->add($footer_navigation);
        
        $tabs = new Text('tabs', array('placeholder' => 'ID Danh mục: 1,2,3,4'));
        $tabs->setFilters(array('striptags', 'trim'));
        $this->add($tabs);
        
        $noi_bat = new Text('noi_bat', array('placeholder' => 'Số Lượng Tin : 3'));
        $noi_bat->setFilters(array('striptags', 'trim'));
        $this->add($noi_bat);

        $tin_hot = new Text('tin_hot', array('placeholder' => 'Số Lượng Tin : 6'));
        $tin_hot->setFilters(array('striptags', 'trim'));
        $this->add($tin_hot);

        $tieu_diem = new Text('tieu_diem', array('placeholder' => 'Số Lượng : 4'));
        $tieu_diem->setFilters(array('striptags', 'trim'));
        $this->add($tieu_diem);

        $moi_nhat = new Text('moi_nhat', array('placeholder' => 'Số Lượng: 4'));
        $moi_nhat->setFilters(array('striptags', 'trim'));
        $this->add($moi_nhat);

        $nhieu_nhat = new Text('nhieu_nhat', array('placeholder' => 'Số Lượng : 4'));
        $nhieu_nhat->setFilters(array('striptags', 'trim'));
        $this->add($nhieu_nhat);
 
        $moi_hon = new Text('moi_hon', array('placeholder' => 'Số Lượng: 4'));
        $moi_hon->setFilters(array('striptags', 'trim'));
        $this->add($moi_hon);

        $cu_hon = new Text('cu_hon', array('placeholder' => 'Số Lượng: 4'));
        $cu_hon->setFilters(array('striptags', 'trim'));
        $this->add($cu_hon);

        $hien_thi = new Text('hien_thi', array('placeholder' => 'Số Lượng: 4'));
        $hien_thi->setFilters(array('striptags', 'trim'));
        $this->add($hien_thi);

        $hotline = new Text('hotline', array('placeholder' => '0909 999 999'));
        $hotline->setFilters(array('striptags', 'trim'));
        $this->add($hotline);
        
        $theme = new Select('theme', Constant::themeSelect());
        $theme->setFilters(array('striptags', 'trim'));
        $this->add($theme);
        // ads
        $url = $this->config->application->api_url . 'category/list?session_token=cb2663ce82a9f4ba448ba435091e27bb';
        $r = json_decode(\ITECH\Datasource\Lib\Util:: curlGet($url), true);
        $optionsValue = array();
        $optionsValue['all'] = 'Tất cả tin tiêu đểm';
        if ($r['status'] == 200) {
            foreach ($r['result'] as $key => $value) {

                if (isset($value['sub_category'])) {
                    foreach ($value['sub_category'] as $_key => $_value) {
                        $optionsValue[$value['name']][$_value['id']] = $_value['name'];
                    }
                } else {
                    $optionsValue[$value['id']] = $value['name'];
                }
            }
        }
        $ads = new Select('ads', $optionsValue);
        $this->add($ads);
        // end ads
    }
}