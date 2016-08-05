<?php
namespace ITECH\Home\Component;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View\Simple as View;
use ITECH\Datasource\Repository\ArticleRepository;
use ITECH\Datasource\Model\Category;

class ArticleComponent extends Component
{
    /**
     * @author Vu.Tran
     */
    public function category($controller, $theme, array $params)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'ArticleComponent',
            'category',
            'ArticleRepository',
            'getList',
            $params
        )));
        $result = $controller->cache->get($cache_name);

        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        if(isset($params['conditions']['category_id']) && $params['conditions']['category_id'] != '') {
            $category = Category::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array('id' => $params['conditions']['category_id'])
            ));
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/article/');
        $view->render('category', array(
            'result' => $result,
            'category' => $category
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function categoryHome($controller, $theme, array $params)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'ArticleComponent',
            'categoryHome',
            'ArticleRepository',
            'getList',
            $params
        )));
        $result = $controller->cache->get($cache_name);

        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        if(isset($params['conditions']['category_id']) && $params['conditions']['category_id'] != '') {
            $category = Category::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array('id' => $params['conditions']['category_id'])
            ));
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/article/');
        $view->render('category_home', array(
            'result' => $result,
            'category' => $category
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function categorySidebar($controller, $theme, array $params)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'ArticleComponent',
            'categorySidebar',
            'ArticleRepository',
            'getList',
            $params
        )));
        $result = $controller->cache->get($cache_name);

        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        if(isset($params['conditions']['category_id']) && $params['conditions']['category_id'] != '') {
            $category = Category::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array('id' => $params['conditions']['category_id'])
            ));
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/article/');
        $view->render('category_sidebar', array(
            'result' => $result,
            'category' => $category
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function categoryFresh($controller, $theme, array $params) {
        $view = new View();
        $cache_name = md5(serialize(array(
            'ArticleComponent',
            'fresh',
            'ArticleRepository',
            'getList',
            $params
        )));
        
        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/article/');
        $view->render('fresh', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function searchForm($controller, $theme, array $params)
    {
        $view = new View();

        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/article/');
        $view->render('search_form', array(
        ));
        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function mostNew($controller, $theme, array $params)
    {
        $view = new View();
        $cache_name = md5(serialize(array(
            'ProductComponent',
            'mostNew',
            'ArticleRepository',
            'getList',
            $params
        )));
        
        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/article/');
        $view->render('most_new', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function focus($controller, $theme, array $params)
    {
        $view = new View();
        $cache_name = md5(serialize(array(
            'ArticleComponent',
            'focus',
            'ArticleRepository',
            'getList',
            $params
        )));
        
        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/article/');
        $view->render('focus', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function hot($controller, $theme, array $params)
    {
        $view = new View();
        $cache_name = md5(serialize(array(
            'ArticleComponent',
            'hot',
            'ArticleRepository',
            'getList',
            $params
        )));
        
        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/article/');
        $view->render('hot', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function mostViewed($controller, $theme, array $params)
    {
        $view = new View();
        $cache_name = md5(serialize(array(
            'ArticleComponent',
            'mostViewed',
            'ArticleRepository',
            'getList',
            $params
        )));
        
        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/article/');
        $view->render('most_viewed', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function fresh($controller, $theme, array $params) {
        $view = new View();
        $cache_name = md5(serialize(array(
            'ArticleComponent',
            'fresh',
            'ArticleRepository',
            'getList',
            $params
        )));
        
        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/article/');
        $view->render('fresh', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function newer($controller, $theme, array $params) {
        $view = new View();
        $cache_name = md5(serialize(array(
            'ArticleComponent',
            'newer',
            'ArticleRepository',
            'getList',
            $params
        )));
        
        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/article/');
        $view->render('newer', array(
            'result' => $result
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function older($controller, $theme, array $params) {
        $view = new View();
        $cache_name = md5(serialize(array(
            'ArticleComponent',
            'older',
            'ArticleRepository',
            'getList',
            $params
        )));
        
        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/article/');
        $view->render('older', array(
            'result' => $result
        ));

        return $view->getContent();
    }
}
