<?php
namespace ITECH\Home\Controller;

use Phalcon\Mvc\Controller;

use ITECH\Home\Component\LinkComponent;
use ITECH\Home\Component\ArticleComponent;
use ITECH\Datasource\Lib\MobileDetect;
use ITECH\Home\Lib\Config as LocalConfig;
use ITECH\Datasource\Lib\Constant;

class ErrorController extends Controller
{
    public static $theme;

    /**
     * @author Vu.Tran
     */
    public function onConstruct()
    {

    }

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {

        $setting = LocalConfig::setting();
        $tabs_for_layout = isset($setting['tabs']) ? $setting['tabs'] : '';
        $device = new MobileDetect();
        $__theme = '' . isset($setting['theme']) ? $setting['theme'] : 'default' . '';
        if (!$device->isMobile()) {
            $this->view->setMainView('default_error');
            self::$theme = $__theme;
        } else {
            $__theme = 'mobile';
             $this->view->setMainView('default_error');
            self::$theme = $__theme;
        }

        $social_facebook_for_layout = isset($setting['social_facebook']) ? $setting['social_facebook'] : '';
        $fb_admins_for_layout = isset($setting['fb_admins']) ? $setting['fb_admins'] : '';
        $social_google_for_layout = isset($setting['social_google']) ? $setting['social_google'] : '';
        $google_analytics_for_layout = isset($setting['google_analytics']) ? $setting['google_analytics'] : '';
        $google_analytics_domain_for_layout = isset($setting['google_analytics_domain']) ? $setting['google_analytics_domain'] : '';

        $this->view->setVars(array(
            '__theme' => $__theme,
            'social_facebook_for_layout' => $social_facebook_for_layout,
            'fb_admins_for_layout' => $fb_admins_for_layout,
            'social_google_for_layout' => $social_google_for_layout,
            'google_analytics_for_layout' => $google_analytics_for_layout,
            'google_analytics_domain_for_layout' => $google_analytics_domain_for_layout
        ));
    }

    /**
     * @author Vu.Tran
     */
    public function error404Action()
    {   
        $setting = LocalConfig::setting();
        $tabs_for_layout = isset($setting['tabs']) ? $setting['tabs'] : '';

        $article_component = new ArticleComponent();
        
        $title_for_layout = 'Error 404';

        $this->response->setStatusCode(404, 'Page not found.');

        $link_component = new LinkComponent();
        $params = array(
            'conditions' => array(
                'group_id' => 1,
                'ordering' => 'ITECH\Datasource\Model\Link.ordering ASC'
        ));
        $link_main_menu_layout = $link_component->mainMenu($this, self::$theme, $params);
        
        $arr = explode(',', $tabs_for_layout); 
        $cache_name = md5(serialize(array(
            'HomeController',
            'indexAction',
            'Category',
            'find',
            $arr
        )));
        $categories = $this->cache->get($cache_name);
        if (!$categories) {
            $categories = $this->modelsManager->createBuilder()
            ->from('ITECH\Datasource\Model\Category')
            ->inWhere('ITECH\Datasource\Model\Category.id', $arr)
            ->getQuery()
            ->execute();
            $this->cache->save($cache_name, $categories);
        }
        
        $cache_name = md5(serialize(array(
            'HomeController',
            'indexAction',
            'box_layout',
            $arr
        )));

        $box_layout = $this->cache->get($cache_name);
        if (!$box_layout) {
            $box_layout = array();
            foreach ($categories as $item) {
                $params = array(
                    'conditions' => array(
                        'category_id' => $item->id,
                        'module' => Constant::MODULE_ARTICLES,
                        'status' => Constant::STATUS_ACTIVED
                    ),
                    'order' => 'ITECH\Datasource\Model\Article.ordering',
                    'limit' => 6
                );

                $box_layout[$item->id] = $article_component->categoryHome($this, self::$theme, $params);
            }
            $this->cache->save($cache_name, $box_layout);
            
        }
        
        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'link_main_menu_layout' => $link_main_menu_layout,
            'box_layout' => $box_layout,
            'categories' => $categories
        ));
        $this->view->pick(self::$theme . '/error/error404');
    }

    /**
     * @author Vu.Tran
     */
    public function errorAction($e)
    {   
        $setting = LocalConfig::setting();
        $tabs_for_layout = isset($setting['tabs']) ? $setting['tabs'] : '';

        
        $article_component = new ArticleComponent();
        
        $title_for_layout = 'Error';
        $message = $e->getMessage();
        //$this->logger->log($message);
        $link_component = new LinkComponent();
        $params = array(
            'conditions' => array(
                'group_id' => 1,
                'ordering' => 'ITECH\Datasource\Model\Link.ordering ASC'
        ));
        $link_main_menu_layout = $link_component->mainMenu($this, self::$theme, $params);
        
        $arr = explode(',', $tabs_for_layout); 
        $cache_name = md5(serialize(array(
            'HomeController',
            'indexAction',
            'Category',
            'find',
            $arr
        )));
        $categories = $this->cache->get($cache_name);
        if (!$categories) {
            $categories = $this->modelsManager->createBuilder()
            ->from('ITECH\Datasource\Model\Category')
            ->inWhere('ITECH\Datasource\Model\Category.id', $arr)
            ->getQuery()
            ->execute();
            $this->cache->save($cache_name, $categories);
        }
        
        $cache_name = md5(serialize(array(
            'HomeController',
            'indexAction',
            'box_layout',
            $arr
        )));

        $box_layout = $this->cache->get($cache_name);
        if (!$box_layout) {
            $box_layout = array();
            foreach ($categories as $item) {
                $params = array(
                    'conditions' => array(
                        'category_id' => $item->id,
                        'module' => Constant::MODULE_ARTICLES,
                        'status' => Constant::STATUS_ACTIVED
                    ),
                    'order' => 'ITECH\Datasource\Model\Article.ordering',
                    'limit' => 6
                );

                $box_layout[$item->id] = $article_component->categoryHome($this, self::$theme, $params);
            }
            $this->cache->save($cache_name, $box_layout);
            
        }
        
        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'link_main_menu_layout' => $link_main_menu_layout,
            'message' => $message,
            'box_layout' => $box_layout,
            'categories' => $categories
        ));
        $this->view->pick(self::$theme . '/error/error');
    }
}