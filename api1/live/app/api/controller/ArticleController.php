<?php
namespace ITECH\Api\Controller;

class ArticleController extends \ITECH\Api\Controller\BaseController {
    public function listAction() {
        $response = array(
            'status' => \ITECH\Datasource\Lib\Constant::CODE_SUCCESS,
            'message' => 'Success.',
            'results' => array()
        );

        $filter = $this->request->getQuery('filter', array('striptags', 'trim', 'lower'), '');
        $keywords = $this->request->getQuery('keywords', array('striptags', 'trim'), '');
        $limit = (int)$this->request->getQuery('limit', array('int'), -1);

        
        if ($keywords == '') {
            $keywords= 'F';
        }
        if ($filter == '') {
           $filter = 'type'; 
        }
        if ($limit == -1) {
            $limit = 2;
        }
        
        $params = array(
            'limit' => $limit,
            'order' => 'a.updated_at DESC'

        );

        if ($filter != '' && $keywords != '') {
            switch ($filter) {
                case 'type':
                    $params['conditions']['type'] = $keywords;
                    break;
            }
        }

        $cache_name = md5(serialize(array(
            'ArticleController',
            'listAction',
            'Article',
            'getList',
            $params
        )));

        $articles = $this->cache->get($cache_name);
        
        if (!$articles) {
            $article = new \ITECH\Datasource\Model\Article();
            
            $b = $article->getModelsManager()->createBuilder();
            $b->columns(array(
                'a.id',
                'a.title',
                'a.alias',
                'a.intro',
                'a.image'
            ));
            $b->from(array('a' => 'ITECH\Datasource\Model\Article'));
            $b->andWhere('a.status = :status:', array('status' => \ITECH\Datasource\Lib\Constant::ARTICLE_STATUS_ACTIVED));

            if (isset($params['conditions']['type'])) {
                $b->andWhere('a.type = :type:', array('type' => $params['conditions']['type']));
            }

            if (isset($params['order'])) {
                $b->orderBy($params['order']);
            } else {
                $b->orderBy('a.id DESC');
            }
            
            if (isset($params['limit'])) {
                $b->limit($params['limit']);
            }
            
            $articles = $b->getQuery()->execute();
            
            if ($articles && count($articles) > 0) {
                foreach ($articles as $item) {
                    if ($item->alias == '') {
                        $item->alias = \ITECH\Datasource\Lib\Util::slug($item->title);
                    }
                    $url = $this->config->static->home_url . $item->alias .'/' . $item->id;
                    
                    $response['results'][] = array(
                        'id' => (int)$item->id,
                        'title' => $item->title,
                        'alias' => ($item->alias != '') ? $item->alias : \ITECH\Datasource\Lib\Util::slug($item->title),
                        'intro' => \ITECH\Datasource\Lib\Util::niceWordsByChars($item->intro, 100, '...'),
                        'image' => ($item->image != '') ? $this->config->asset->home_image_url . '500/' . $item->image : '',
                        'url' => $url
                    );
                }
            }
        }

        return parent::outputJSON($response);
    }
    
    public function detailAction() {
        $response = array(
            'status' => \ITECH\Datasource\Lib\Constant::CODE_SUCCESS,
            'message' => 'Success.',
            'results' => array()
        );

        /*$filter = $this->request->getQuery('filter', array('striptags', 'trim', 'lower'), '');
        $keywords = $this->request->getQuery('keywords', array('striptags', 'trim'), '');
        $limit = (int)$this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);
        */
        $filter = 'type';
        $keywords= 'F';
        $limit = 2;
        $params = array(
            'limit' => $limit,
            'order' => 'a.created_at DESC'

        );

        if ($filter != '' && $keywords != '') {
            switch ($filter) {
                case 'type':
                    $params['conditions']['type'] = $keywords;
                    break;
            }
        }

        $cache_name = md5(serialize(array(
            'ArticleController',
            'listAction',
            'Article',
            'getList',
            $params
        )));

        $articles = $this->cache->get($cache_name);
        
        if (!$articles) {
            $article = new \ITECH\Datasource\Model\Article();
            
            $b = $article->getModelsManager()->createBuilder();
            $b->columns(array(
                'a.id',
                'a.title',
                'a.alias',
                'a.intro',
                'a.image'
            ));
            $b->from(array('a' => 'ITECH\Datasource\Model\Article'));
            $b->andWhere('a.status = :status:', array('status' => \ITECH\Datasource\Lib\Constant::ARTICLE_STATUS_ACTIVED));

            if (isset($params['conditions']['type'])) {
                $b->andWhere('a.type = :type:', array('type' => $params['conditions']['type']));
            }

            if (isset($params['order'])) {
                $b->orderBy($params['order']);
            } else {
                $b->orderBy('a.id DESC');
            }
            
            if (isset($params['limit'])) {
                $b->limit($params['limit']);
            }
            
            $articles = $b->getQuery()->execute();
            
            if ($articles && count($articles) > 0) {
                foreach ($articles as $item) {
                    $response['results'][] = array(
                        'id' => (int)$item->id,
                        'title' => $item->title,
                        'alias' => ($item->alias != '') ? $item->alias : \ITECH\Datasource\Lib\Util::slug($item->title),
                        'intro' => $item->intro,
                        'image' => ($item->image != '') ? $this->config->asset->home_image_url . '150/' . $item->image : '',
                    );
                }
            }
        }

        return parent::outputJSON($response);
    }
}