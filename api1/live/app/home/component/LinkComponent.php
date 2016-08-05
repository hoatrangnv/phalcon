<?php
namespace ITECH\Home\Component;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View\Simple as View;
use ITECH\Datasource\Repository\LinkRepository;
use ITECH\Datasource\Model\LinkGroup;

class LinkComponent extends Component
{
    /**
     * @author Vu.Tran
     */
    public function mainMenu($controller, $theme, array $params)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'LinkComponent',
            'mainMenu',
            'LinkRepository',
            'getList',
            $params
        )));

        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $link_main_menu_repository = new LinkRepository();
            $result = $link_main_menu_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }

        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/link/');
        $view->render('main_menu', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function homeMenu($controller, $theme, array $params)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'LinkComponent',
            'homeMenu',
            'LinkRepository',
            'getList',
            $params
        )));

        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $link_main_menu_repository = new LinkRepository();
            $result = $link_main_menu_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }

        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/link/');
        $view->render('home_menu', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function topMenu($controller, $theme, array $params)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'LinkComponent',
            'mainMenu',
            'LinkRepository',
            'getList',
            $params
        )));

        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $link_main_menu_repository = new LinkRepository();
            $result = $link_main_menu_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }

        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/link/');
        $view->render('top_menu', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function custom($controller, $theme, array $params, $layout)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'LinkComponent',
            'mainMenu',
            'LinkRepository',
            'getList',
            $params
        )));

        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $link_main_menu_repository = new LinkRepository();
            $result = $link_main_menu_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }

        $cache_name = md5(serialize(array(
            'LinkComponent',
            'custom',
            'GroupLink',
            'findFirst',
            $params['conditions']['group_id']
        )));

        $group_link = $controller->cache->get($cache_name);
        if (!$group_link) {
            $group_link = LinkGroup::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array('id' => $params['conditions']['group_id'])
            ));
            $controller->cache->save($cache_name, $group_link);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/link/');
        $view->render($layout, array(
            'group_link' => $group_link,
            'result' => $result
        ));

        return $view->getContent();
    }
}
