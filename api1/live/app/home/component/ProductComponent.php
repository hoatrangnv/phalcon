<?php
namespace ITECH\Home\Component;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View\Simple as View;
use ITECH\Datasource\Repository\ArticleRepository;
use ITECH\Datasource\Model\Article; 
use ITECH\Datasource\Lib\Constant;

class ProductComponent extends Component
{
    
    public function category($controller, $theme, array $params)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'ProductComponent',
            'category',
            'ArticleRepository',
            'getList',
            $params
        )));
        $result = $controller->cache->get($cache_name);

        if (!$result) {
            $products_by_category_repository = new ArticleRepository();
            $result = $products_by_category_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        
        $attribute_cache_name = md5(serialize(array(
            'ProductComponent',
            'category',
            $params
        )));

        $attributes = $this->cache->get($attribute_cache_name);
        if (!$attributes) {
            $attributes = array();
            foreach ($result as $item) {
                $params = array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => (int)$item->id)
                );
                $cache_name = md5(serialize(array(
                    'ArticleController',
                    'detailAction',
                    'ArticleRepository',
                    'getDetail',
                    $params
                )));
                
                $article = $this->cache->get($cache_name);
                if (!$article) {
                    $article = Article::findFirst($params);
                    $this->cache->save($cache_name, $article);
                }

                $params = array();
                $cache_name = md5(serialize(array(
                    'ProductComponent',
                    'category',
                    'getArticleAttribute',
                    $item->id,
                    $params
                )));
                
                $article_attribute = $this->cache->get($cache_name);
                if (!$article_attribute) {
                    $article_attribute = $article->getArticleAttribute();
                    $this->cache->save($cache_name, $article_attribute);
                } 
                
                foreach ($article_attribute as $attribute) {
                    if (isset($attribute->attribute_id) && $attribute->attribute_id == 1) {
                        $attributes[$item->id]['price'] = $attribute->attribute_value;
                    }

                    if (isset($attribute->attribute_id) && $attribute->attribute_id == 2) {
                        $attributes[$item->id]['component'] = $attribute->attribute_value;
                    }  
                }
            }
            $this->cache->save($attribute_cache_name, $attributes);
        }

        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/product/');
        $view->render('category', array(
            'result' => $result,
            'attributes' => $attributes
        ));

        return $view->getContent();
    }
    
    public function boxCategory($controller, $theme, array $params, $category)
    {
        $view = new View();
        
        $cache_name = md5(serialize(array(
            'ProductComponent',
            'category',
            'ArticleRepository',
            'getList',
            $params
        )));
        $result = $controller->cache->get($cache_name);

        if (!$result) {
            $products_by_category_repository = new ArticleRepository();
            $result = $products_by_category_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        
        $attribute_cache_name = md5(serialize(array(
            'ProductComponent',
            'category',
            $params
        )));

        $attributes = $this->cache->get($attribute_cache_name);
        if (!$attributes) {
            $attributes = array();
            foreach ($result as $item) {
                $params = array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => (int)$item->id)
                );
                $cache_name = md5(serialize(array(
                    'ArticleController',
                    'detailAction',
                    'ArticleRepository',
                    'getDetail',
                    $params
                )));
                
                $article = $this->cache->get($cache_name);
                if (!$article) {
                    $article = Article::findFirst($params);
                    $this->cache->save($cache_name, $article);
                }

                $params = array();
                $cache_name = md5(serialize(array(
                    'ProductComponent',
                    'category',
                    'getArticleAttribute',
                    $item->id,
                    $params
                )));
                
                $article_attribute = $this->cache->get($cache_name);
                if (!$article_attribute) {
                    $article_attribute = $article->getArticleAttribute();
                    $this->cache->save($cache_name, $article_attribute);
                } 
                
                foreach ($article_attribute as $attribute) {
                    if (isset($attribute->attribute_id) && $attribute->attribute_id == 1) {
                        $attributes[$item->id]['price'] = $attribute->attribute_value;
                    }

                    if (isset($attribute->attribute_id) && $attribute->attribute_id == 2) {
                        $attributes[$item->id]['component'] = $attribute->attribute_value;
                    }  
                }
            }
            $this->cache->save($attribute_cache_name, $attributes);
        }

        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/product/');
        $view->render('box_category', array(
            'result' => $result,
            'attributes' => $attributes,
            'category' => $category
        ));

        return $view->getContent();
    }
    /**
     * @author Vu.Tran
     */
    public function slideshowFocus($controller, $theme, array $params)
    {
        $view = new View();
        $cache_name = md5(serialize(array(
            'ProductComponent',
            'slideshowFocus',
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
        
        $attribute_cache_name = md5(serialize(array(
            'ProductComponent',
            'slideshowFocus',
            $params
        )));

        $attributes = $this->cache->get($attribute_cache_name);
        if (!$attributes) {
            $attributes = array();
            foreach ($result as $item) {
                $params = array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => (int)$item->id)
                );
                $cache_name = md5(serialize(array(
                    'ProductComponent',
                    'slideshowFocus',
                    'Article',
                    'findFirst',
                    $params
                )));
                
                $article = $this->cache->get($cache_name);
                if (!$article) {
                    $article = Article::findFirst($params);
                    $this->cache->save($cache_name, $article);
                }

                $params = array();
                $cache_name = md5(serialize(array(
                    'ProductComponent',
                    'slideshowFocus',
                    'getArticleAttribute',
                    $item->id,
                    $params
                )));
                
                $article_attribute = $this->cache->get($cache_name);
                if (!$article_attribute) {
                    $article_attribute = $article->getArticleAttribute();
                    $this->cache->save($cache_name, $article_attribute);
                } 
                
                foreach ($article_attribute as $attribute) {
                    if (isset($attribute->attribute_id) && $attribute->attribute_id == 1) {
                        $attributes[$item->id]['price'] = $attribute->attribute_value;
                    }

                    if (isset($attribute->attribute_id) && $attribute->attribute_id == 2) {
                        $attributes[$item->id]['price_old'] = $attribute->attribute_value;
                    }  
                }
            }
            $this->cache->save($attribute_cache_name, $attributes);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/product/');
        $view->render('slideshow_focus', array(
            'result' => $result,
            'attributes' => $attributes
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function homeTop($controller, $theme, array $params)
    {
        $view = new View();
        $cache_name = md5(serialize(array(
            'ProductComponent',
            'homeTop',
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
        
        $attribute_cache_name = md5(serialize(array(
            'ProductComponent',
            'homeTop',
            $params
        )));

        $attributes = $this->cache->get($attribute_cache_name);
        if (!$attributes) {
            $attributes = array();
            foreach ($result as $item) {
                $params = array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => (int)$item->id)
                );
                $cache_name = md5(serialize(array(
                    'ProductComponent',
                    'homeTop',
                    'Article',
                    'findFirst',
                    $params
                )));
                
                $article = $this->cache->get($cache_name);
                if (!$article) {
                    $article = Article::findFirst($params);
                    $this->cache->save($cache_name, $article);
                }

                $params = array();
                $cache_name = md5(serialize(array(
                    'ProductComponent',
                    'homeTop',
                    'getArticleAttribute',
                    $item->id,
                    $params
                )));
                
                $article_attribute = $this->cache->get($cache_name);
                if (!$article_attribute) {
                    $article_attribute = $article->getArticleAttribute();
                    $this->cache->save($cache_name, $article_attribute);
                } 
                
                foreach ($article_attribute as $attribute) {
                    if (isset($attribute->attribute_id) && $attribute->attribute_id == 1) {
                        $attributes[$item->id]['price'] = $attribute->attribute_value;
                    }

                    if (isset($attribute->attribute_id) && $attribute->attribute_id == 2) {
                        $attributes[$item->id]['price_old'] = $attribute->attribute_value;
                    }  
                }
            }
            $this->cache->save($attribute_cache_name, $attributes);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/product/');
        $view->render('home_top', array(
            'result' => $result,
            'attributes' => $attributes
        ));

        return $view->getContent();
    }

    /**
     * @author Vu.Tran
     */
    public function newer($controller, $theme, array $params, $type = 'sidebar') {
        $view = new View();
        $cache_name = md5(serialize(array(
            'ProductComponent',
            'newer',
            'ArticleRepository',
            'getList',
            $params,
            $type
        )));
        
        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/product/');
        if ($type == 'box') {
        	$view->render('newer_box', array(
	            'result' => $result
	        ));
        } else {
	        $view->render('newer', array(
	            'result' => $result
	        ));
	    }

        return $view->getContent();
    }

    /**
     * @author Vu.Tran
     */
    public function mostViewed($controller, $theme, array $params, $type = 'sidebar')
    {
        $view = new View();
        $cache_name = md5(serialize(array(
            'ProductComponent',
            'mostViewed',
            'ArticleRepository',
            'getList',
            $params,
            $type
        )));
        
        $result = $controller->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getList($params);
            $controller->cache->save($cache_name, $result);
        }
        if ($type == 'box') {
        	$view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/product/');
	        $view->render('most_viewed_box', array(
	            'result' => $result
	        ));
        } else {
	        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/product/');
	        $view->render('most_viewed', array(
	            'result' => $result
	        ));
	    }

        return $view->getContent();
    }
}
