<?php
namespace ITECH\Home\Component;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View\Simple as View;
use ITECH\Datasource\Repository\CategoryRepository;

class CategoryComponent extends Component
{
    /**
     * @author Vu.Tran
     */
    public function index($controller, $theme, array $params)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'CategoryComponent',
            'index',
            'CategoryRepository',
            'getList',
            $params
        )));
                
        $result = $controller->cache->get($cache_name);

        if (!$result) {
            $category_repository = new CategoryRepository();
            $result = $category_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/category/');
        $view->render('index', array(
            'result' => $result
        ));

        return $view->getContent();
    }
}