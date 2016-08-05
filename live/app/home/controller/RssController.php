<?php
namespace ITECH\Home\Controller;

use Phalcon\Mvc\View;
use ITECH\Home\Controller\BaseController;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Repository\ArticleRepository;
use ITECH\Datasource\Lib\Constant;

class RssController extends BaseController
{
    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * @author Vu.Tran
     */
    public function indexAction()
    {
        $title_for_layout = 'RSS HoaTuoiDep.Com';
        $description_for_layout = 'Tìm Việc Nhanh - Hệ thống Việc làm, Tuyển dụng, Tìm việc hàng đầu tại Việt Nam với hàng chục ngàn Việc làm mới. Nơi nhà tuyển dụng và ứng viên lựa chọn';
        $keywords_for_layout = 'Việc làm, tuyển dụng, tìm việc, tim viec lam, tuyen dung, viec lam, công việc, việc làm bán thời gian, tuyển dụng nhanh, tìm việc nhanh, việc làm sinh viên, đăng tuyển dụng';
        
        $params = array(
            'conditions' => 'module = :module:',
            'bind' => array('module' => Constant::MODULE_ARTICLES),
            'order' => 'ITECH\Datasource\Model\Category.ordering ASC' 
        );
        $cache_name = md5(serialize(array(
            'RssController',
            'indexAction',
            'Category',
            'find',
            $params
        )));
        
        $categories = $this->cache->get($cache_name);
        if (!$categories) {
            $categories = Category::find($params);
            $this->cache->save($cache_name, $categories);
        }
        
        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'categories' => $categories
        ));
        $this->view->pick(parent::$theme . '/rss/index');
    }

    /**
     * @author Vu.Tran
     */
    public function articleAction()
    {
        $limit = $this->config->application->pagination_limit;

        $params = array(
            'conditions' => array(
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_ARTICLES
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => (int)$limit
        );

        $cache_name = md5(serialize(array(
            'RssController',
            'articleAction',
            'ArticleRepository',
            'getRssList',
            $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getRssList($params);
            $this->cache->save($cache_name, $result);
        }

        $rss_title = '';
        $rss_link = $this->url->get(array('for' => 'rss'));

        $this->view->setVars(array(
            'result' => $result,
            'rss_title' => $rss_title,
            'rss_link' => $rss_link
        ));
        $this->view->pick(parent::$theme . '/rss/detail');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
    
    /**
     * @author Vu.Tran
     */
    public function categoryAction()
    {
        $id = $this->dispatcher->getParam('id', array('int'), -1);
        $limit = $this->config->application->pagination_limit;
        
        $params = array(
            'conditions' => array(
                'category_id' => $id,
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_ARTICLES
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => (int)$limit
        );

        $cache_name = md5(serialize(array(
            'RssController',
            'categoryAction',
            'ArticleRepository',
            'getRssList',
            $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getRssList($params);
            $this->cache->save($cache_name, $result);
        }
        
        $cache_name = md5(serialize(array(
            'RssController',
            'categoryAction',
            'Category',
            'findFirst',
            $id
        )));

        $category = $this->cache->get($cache_name);
        if (!$category) {
            $category = Category::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array('id' => $id)
            ));
            $this->cache->save($cache_name, $category);
        }

        if (!$category) {
            throw new Exception('Không tồn tại danh mục này.');
        }

        $this->view->setVars(array(
            'result' => $result,
            'category' => $category
        ));
        $this->view->pick(parent::$theme . '/rss/category');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
}