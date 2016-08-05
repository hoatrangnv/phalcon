<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\Order;
use ITECH\Datasource\Lib\Constant;

class OrderRepository extends Order
{
    /**
     * @author Vu.Tran
     */
    public function getListPagination(array $params)
    {
        $b = Order::getModelsManager()->createBuilder();
        $b->columns('
            ITECH\Datasource\Model\Order.id,	
            ITECH\Datasource\Model\Order.user_id,			
            ITECH\Datasource\Model\Order.note,	
            ITECH\Datasource\Model\Order.total,
            ITECH\Datasource\Model\Order.total_amount,	
            ITECH\Datasource\Model\Order.status,	
            ITECH\Datasource\Model\Order.status_note,	
            ITECH\Datasource\Model\Order.payment_method,	
            ITECH\Datasource\Model\Order.payment_status,	
            ITECH\Datasource\Model\Order.delivery_method,
            ITECH\Datasource\Model\Order.delivery_fee,
            ITECH\Datasource\Model\Order.bill,
            ITECH\Datasource\Model\Order.created_at,
            ITECH\Datasource\Model\Order.processed_at,
            ITECH\Datasource\Model\Order.updated_at,		
            ITECH\Datasource\Model\Order.created_by,		
            ITECH\Datasource\Model\Order.updated_by,
            ITECH\Datasource\Model\User.name as order_user_name
        ');	 
    
        $b->from('ITECH\Datasource\Model\Order');
        $b->innerJoin('ITECH\Datasource\Model\User', 'ITECH\Datasource\Model\User.id = ITECH\Datasource\Model\Order.user_id');
        
        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\Article.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\Article.title LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('ITECH\Datasource\Model\Article.alias LIKE :q3:', array('q3' => '%' . $params['conditions']['q'] . '%'));
        }

        if (isset($params['conditions']['status']) && $params['conditions']['status'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Order.status = :status:', array('status' => $params['conditions']['status']));
        } 
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Order.id DESC');
        }

        $paginator = new QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
    
     /**
     * @author Vu.Tran
     */
    public function getList(array $params)
    {
        $b = Article::getModelsManager()->createBuilder();

        $b->from('ITECH\Datasource\Model\Order'); 
        $b->columns('
            ITECH\Datasource\Model\Article.id,
            ITECH\Datasource\Model\Article.title, 	
            ITECH\Datasource\Model\Article.alias, 	
            ITECH\Datasource\Model\Article.intro, 	
            ITECH\Datasource\Model\Article.image, 	
            ITECH\Datasource\Model\Article.hits, 
            ITECH\Datasource\Model\Article.created_at,	
            ITECH\Datasource\Model\Article.updated_at,
            ITECH\Datasource\Model\Article.featured, 	
            ITECH\Datasource\Model\Article.show_comment, 	
            ITECH\Datasource\Model\Article.comment_count, 	
            ITECH\Datasource\Model\Article.status, 
            ITECH\Datasource\Model\Article.ordering,	
            ITECH\Datasource\Model\Article.created_by, 	
            ITECH\Datasource\Model\Article.updated_by, 	
            ITECH\Datasource\Model\Article.created_ip, 	
            ITECH\Datasource\Model\Article.module,
            ITECH\Datasource\Model\Category.id as category_id, 
            ITECH\Datasource\Model\Category.name as category_name,
            ITECH\Datasource\Model\Category.slug as category_slug
        ');
        
        $b->innerJoin('ITECH\Datasource\Model\ArticleCategory', 'ITECH\Datasource\Model\ArticleCategory.article_id = ITECH\Datasource\Model\Article.id');
        $b->innerJoin('ITECH\Datasource\Model\Category', 'ITECH\Datasource\Model\Category.id = ITECH\Datasource\Model\ArticleCategory.category_id');
        
        if (isset($params['conditions']['category_id']) && ($params['conditions']['category_id']) != '') {
            $b->andWhere('ITECH\Datasource\Model\ArticleCategory.category_id = :category_id:', array('category_id' => $params['conditions']['category_id'])); 
        }
        
        if (isset($params['conditions']['module']) && ($params['conditions']['module']) != '') {
            $b->andWhere('ITECH\Datasource\Model\Article.module = :module:', array('module' => $params['conditions']['module'])); 
            $b->andWhere('ITECH\Datasource\Model\Category.module = :module:', array('module' => $params['conditions']['module'])); 
        }
        
        if (!empty($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Article.ordering DESC');
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        return $b->getQuery()->execute();
    }
    
    /**
     * @author Vu.Tran
     */
    public function getListRelated(array $params)
    {
        $b = Article::getModelsManager()->createBuilder();

        $b->from('ITECH\Datasource\Model\Article'); 
        $b->columns('
            ITECH\Datasource\Model\Article.id,
            ITECH\Datasource\Model\Article.title, 	
            ITECH\Datasource\Model\Article.alias, 	
            ITECH\Datasource\Model\Article.intro, 	
            ITECH\Datasource\Model\Article.image, 	
            ITECH\Datasource\Model\Article.hits, 
            ITECH\Datasource\Model\Article.created_at,	
            ITECH\Datasource\Model\Article.updated_at,
            ITECH\Datasource\Model\Article.featured, 	
            ITECH\Datasource\Model\Article.show_comment, 	
            ITECH\Datasource\Model\Article.comment_count, 	
            ITECH\Datasource\Model\Article.status, 
            ITECH\Datasource\Model\Article.ordering,	
            ITECH\Datasource\Model\Article.created_by, 	
            ITECH\Datasource\Model\Article.updated_by, 	
            ITECH\Datasource\Model\Article.created_ip, 	
            ITECH\Datasource\Model\Article.module,
            ITECH\Datasource\Model\Category.id as category_id, 
            ITECH\Datasource\Model\Category.name as category_name,
            ITECH\Datasource\Model\Category.slug as category_slug
        ');
        
        $b->innerJoin('ITECH\Datasource\Model\ArticleCategory', 'ITECH\Datasource\Model\ArticleCategory.article_id = ITECH\Datasource\Model\Article.id');
        $b->innerJoin('ITECH\Datasource\Model\Category', 'ITECH\Datasource\Model\Category.id = ITECH\Datasource\Model\ArticleCategory.category_id');
        
        if (isset($params['conditions']['category_id']) && ($params['conditions']['category_id']) != '') {
            $b->andWhere('ITECH\Datasource\Model\ArticleCategory.category_id = :category_id:', array('category_id' => $params['conditions']['category_id'])); 
        }
        
        if (isset($params['conditions']['id']) && ($params['conditions']['id']) != '') {
            //$b->andWhere('ITECH\Datasource\Model\ArticleCategory.article_id = :article_id:', array('article_id' => $params['conditions']['id'])); 
            $b->andWhere('ITECH\Datasource\Model\Article.id <> :id:', array('id' => $params['conditions']['id']));
        }
        
        if (isset($params['conditions']['category_id']) && ($params['conditions']['category_id']) != '') {
            $b->inWhere('ITECH\Datasource\Model\ArticleCategory.category_id', array($params['conditions']['category_id'])); 
        }
        
        if (isset($params['conditions']['module']) && ($params['conditions']['module']) != '') {
            $b->andWhere('ITECH\Datasource\Model\Article.module = :module:', array('module' => $params['conditions']['module'])); 
        }
        
        if (isset($params['group'])) {
            $b->groupBy($params['group']);
        }
        
        if (!empty($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Article.ordering DESC');
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        return $b->getQuery()->execute();
    }
    
    /**
     * @author Vu.Tran
     */
    public function getDetail(array $params)
    {
        $b = Article::getModelsManager()->createBuilder();

        $b->from('ITECH\Datasource\Model\Article');
        
        $b->andWhere('ITECH\Datasource\Model\Article.status = :status:', array('status' => Constant::STATUS_ACTIVED));

        if (isset($params['conditions']['id']) && $params['conditions']['id'] != '') {
            $b->andWhere('ITECH\Datasource\Model\Article.id = :id:', array('id' => $params['conditions']['id']));
        }
        
        if (isset($params['conditions']['module']) && ($params['conditions']['module']) != '') {
            $b->andWhere('ITECH\Datasource\Model\Article.module = :module:', array('module' => $params['conditions']['module'])); 
        }

        return $b->getQuery()->execute();
    }
}