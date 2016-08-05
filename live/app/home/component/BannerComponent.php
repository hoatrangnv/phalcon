<?php
namespace ITECH\Home\Component;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View\Simple as View;
use ITECH\Datasource\Repository\BannerRepository;

class BannerComponent extends Component
{
    /**
     * @author Vu.Tran
     */
    public function zone($controller, $theme, array $params)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'BannerComponent',
            'zone',
            'BannerRepository',
            'getList',
            $params
        )));
        $result = $controller->cache->get($cache_name);

        if (!$result) {
            $banner_repository = new BannerRepository();
            $result = $banner_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/banner/');
        $view->render('zone', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    public function left($controller, $theme, array $params)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'BannerComponent',
            'zone',
            'BannerRepository',
            'getList',
            $params
        )));
        $result = $controller->cache->get($cache_name);

        if (!$result) {
            $banner_repository = new BannerRepository();
            $result = $banner_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/banner/');
        $view->render('left', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    public function right($controller, $theme, array $params)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'BannerComponent',
            'zone',
            'BannerRepository',
            'getList',
            $params
        )));
        $result = $controller->cache->get($cache_name);

        if (!$result) {
            $banner_repository = new BannerRepository();
            $result = $banner_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/banner/');
        $view->render('right', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function trademark($controller, $theme, array $params)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'BannerComponent',
            'trademark',
            'BannerRepository',
            'getList',
            $params
        )));
        $result = $controller->cache->get($cache_name);

        if (!$result) {
            $banner_repository = new BannerRepository();
            $result = $banner_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/banner/');
        $view->render('trademark', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function slideshowHome($controller, $theme, array $params)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'BannerComponent',
            'slideshowHome',
            'BannerRepository',
            'getList',
            $params
        )));
        $result = $controller->cache->get($cache_name);

        if (!$result) {
            $banner_repository = new BannerRepository();
            $result = $banner_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/banner/');
        $view->render('slideshow_home', array(
            'result' => $result
        ));

        return $view->getContent();
    }
}