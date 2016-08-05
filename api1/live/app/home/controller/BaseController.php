<?php
namespace ITECH\Home\Controller;

use Phalcon\Logger;
use Phalcon\Exception;
use Phalcon\Mvc\Controller;
use ITECH\Datasource\Lib\MobileDetect;
use ITECH\Home\Component\LinkComponent;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Lib\Constant;
use ITECH\Home\Form\LoginForm;
use ITECH\Home\Form\RegisterForm;
use ITECH\Home\Lib\Config as LocalConfig;
use ITECH\Datasource\Lib\Facebook;
use ITECH\Datasource\Lib\Util;
class BaseController extends Controller
{
    public static $theme;

    /**
     * @author Vu.Tran
     */
    public function onConstruct()
    {

    }

    public function initialize()
    {
        $setting = LocalConfig::setting();
        $device = new MobileDetect();
        /*
        $mobile = $this->request->getQuery('mobile', array('striptags', 'trim', 'lower'), false);
        $this->session->set('MOBILE', $mobile);
        if ($this->session->has('MOBILE') && $this->session->get('MOBILE') == true) {
            $this->view->setMainView('mobile');
            self::$theme = 'mobile';
            $__theme = self::$theme;
        } else {
            $this->view->setMainView('default');
            self::$theme = 'default';
            $__theme = self::$theme;
        }
        */

        
        $__theme = '' . isset($setting['theme']) ? $setting['theme'] : 'default' . '';
        if (!$device->isMobile()) {
            $this->view->setMainView($__theme);
            self::$theme = $__theme;
        } else {
            if ($device->isTablet() || $device->version('iPad')) {
                $__theme = 'default';
                $this->view->setMainView($__theme);
                self::$theme = $__theme;
            } else {
                $__theme = 'mobile';
                $this->view->setMainView($__theme);
                self::$theme = $__theme;
            }
        }
        $ads = isset($setting['ads']) ? $setting['ads'] : '';
        $title_for_layout = isset($setting['meta_title']) ? $setting['meta_title'] : '';
        $float_banner = isset($setting['float_banner']) ? $setting['float_banner'] : '';
        $float_banner_title = isset($setting['float_banner_title']) ? $setting['float_banner_title'] : '';
        $title_h2 = isset($setting['title_h2']) ? $setting['title_h2'] : '';
        $title_h3 = isset($setting['title_h3']) ? $setting['title_h3'] : '';
        $noidung_h2 = isset($setting['noidung_h2']) ? $setting['noidung_h2'] : '';
        $noidung_h3 = isset($setting['noidung_h3']) ? $setting['noidung_h3'] : '';
        $description_for_layout = isset($setting['meta_description']) ? $setting['meta_description'] : '';
        $keywords_for_layout = isset($setting['meta_keyword']) ? $setting['meta_keyword'] : '';
        $site_name_for_layout = isset($setting['site_title']) ? $setting['site_title'] : '';

        $og_title = isset($setting['meta_title']) ? $setting['meta_title'] : '';
        $og_site_name = isset($setting['site_title']) ? $setting['site_title'] : '';
        $og_url = $this->config->application->base_url;
        $og_description = isset($setting['meta_description']) ? $setting['meta_description'] : '';
        $og_image = $this->config->asset->home_image_url . 'log_fb.png';

        $geo_region_for_layout = isset($setting['geo_region']) ? $setting['geo_region'] : '';
        $geo_placename_for_layout = isset($setting['geo_placename']) ? $setting['geo_placename'] : '';
        $geo_position_for_layout = isset($setting['geo_position']) ? $setting['geo_position'] : '';
        $icbm_for_layout = isset($setting['icbm']) ? $setting['icbm'] : '';
        $logo_for_layout = isset($setting['logo']) ? $setting['logo'] : '';
        $logo_w_for_layout = isset($setting['logo_w']) ? $setting['logo_w'] : '';
        $logo_h_for_layout = isset($setting['logo_h']) ? $setting['logo_h'] : '';
        $social_facebook_for_layout = isset($setting['social_facebook']) ? $setting['social_facebook'] : '';
        $fb_admins_for_layout = isset($setting['fb_admins']) ? $setting['fb_admins'] : '';
        $social_google_for_layout = isset($setting['social_google']) ? $setting['social_google'] : '';
        $google_analytics_for_layout = isset($setting['google_analytics']) ? $setting['google_analytics'] : '';
        $google_analytics_domain_for_layout = isset($setting['google_analytics_domain']) ? $setting['google_analytics_domain'] : '';
        $footer_for_layout = isset($setting['footer']) ? $setting['footer'] : '';
        $footer_title = isset($setting['footer_title']) ? $setting['footer_title'] : '';
        $page_face_book_for_layout = isset($setting['page_face_book']) ? $setting['page_face_book'] : '';
        $google_maps_for_layout = isset($setting['google_maps']) ? $setting['google_maps'] : '';
        $latitude_for_layout = isset($setting['latitude']) ? $setting['latitude'] : '';
        $longitude_for_layout = isset($setting['latitude']) ? $setting['latitude'] : '';
        $hotline_for_layout = isset($setting['hotline']) ? $setting['hotline'] : '';
        

        $canonical_layout = $this->config->application->base_url;
        $link_component = new LinkComponent();

        $params = array(
            'conditions' => array(
                'group_id' => 1,
                'ordering' => 'ITECH\Datasource\Model\Link.ordering ASC'
        ));
        $link_main_menu_layout = $link_component->mainMenu($this, self::$theme, $params);

        $params = array(
            'conditions' => array(
                'group_id' => 2,
                'ordering' => 'ITECH\Datasource\Model\Link.ordering DESC'
        ));
        $link_box_one_layout = $link_component->custom($this, self::$theme, $params, 'box_one');

        $params = array(
            'conditions' => array(
                'group_id' => 3,
                'ordering' => 'ITECH\Datasource\Model\Link.ordering DESC'
        ));
        $link_box_tow_layout = $link_component->custom($this, self::$theme, $params, 'box_tow');

        $params = array(
            'conditions' => array(
                'group_id' => 4,
                'ordering' => 'ITECH\Datasource\Model\Link.ordering DESC'
        ));
        $link_box_three_layout = $link_component->custom($this, self::$theme, $params, 'box_three');
        
        $form_l = new LoginForm();

        // ads
        $url = $this->config->application->api_url . 'article/premium-list?session_token=cb2663ce82a9f4ba448ba435091e27bb&limit=9';//var_dump($url);exit;
        if ($ads != '' && $ads != 'all') {
            $url = $this->config->application->api_url . 'article/list?session_token=cb2663ce82a9f4ba448ba435091e27bb&user_membership=vip|partner&limit=9&category_id=' . $ads;
        }
        $cache_name = md5(serialize(array(
            'BaseController',
            'PremiumList',
            $url
        )));
        $premiumList = $this->cache->get($cache_name);
        if (!$premiumList) {
            $premiumList = json_decode(Util::curlGet($url), true);
            $this->cache->save($cache_name, $premiumList['result']);
        }
        //var_dump($premiumList);exit;
        // end ads

        $form_r = new RegisterForm();
        $this->view->setVars(array(
            'premiumList'=> $premiumList,
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'canonical_layout' => $canonical_layout,
            'og_title' => $og_title,
            'og_site_name' => $og_site_name,
            'og_url' => $og_url,
            'og_description' => $og_description,
            'og_image' => $og_image,
            'link_main_menu_layout' => $link_main_menu_layout,
            'link_box_one_layout' => $link_box_one_layout,
            'link_box_tow_layout' => $link_box_tow_layout,
            'link_box_three_layout' => $link_box_three_layout,
            'site_name_for_layout' => $site_name_for_layout,
            'geo_region_for_layout' => $geo_region_for_layout,
            'geo_placename_for_layout' => $geo_placename_for_layout,
            'geo_position_for_layout' => $geo_position_for_layout,
            'icbm_for_layout' => $icbm_for_layout,
            'logo_for_layout' => $logo_for_layout,
            'social_facebook_for_layout' => $social_facebook_for_layout,
            'fb_admins_for_layout' => $fb_admins_for_layout,
            'social_google_for_layout' => $social_google_for_layout,
            'google_analytics_for_layout' => $google_analytics_for_layout,
            'google_analytics_domain_for_layout' => $google_analytics_domain_for_layout,
            'logo_w_for_layout' => $logo_w_for_layout,
            'logo_h_for_layout' => $logo_h_for_layout,
            'footer_for_layout' => $footer_for_layout,
            'footer_title'=> $footer_title,
            'noidung_h2'=>$noidung_h2,
            'noidung_h3'=>$noidung_h3,
            'title_h2'=>$title_h2,
            'title_h3'=>$title_h3,
            'float_banner' => $float_banner,
            'float_banner_title'=> $float_banner_title,
            'page_face_book_for_layout' => $page_face_book_for_layout,
            'google_maps_for_layout' => $google_maps_for_layout,
            'latitude_for_layout' => $latitude_for_layout,
            'longitude_for_layout' => $longitude_for_layout,
            'hotline_for_layout' => $hotline_for_layout,
            'ads'=>$ads,
            '__theme' => $__theme,
            'form_l' => $form_l,
            'form_r' => $form_r
        ));
    }

    /**
     * @author Vu.Tran
     */
    public function outputJSON($response)
    {
        $this->view->disable();

        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setJsonContent($response);
        $this->response->send();
    }
}