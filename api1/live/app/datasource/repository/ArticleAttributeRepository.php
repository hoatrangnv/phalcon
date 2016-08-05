<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;

class ArticleAttributeRepository extends ArticleAttribute
{
    /**
     * @author Vu.Tran
     */
    public function getListPagination(array $params)
    {
        $b = ArticleAttribute::getModelsManager()->createBuilder();
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
            ITECH\Datasource\Model\Article.module
        ');	 
    
        $b->from('ITECH\Datasource\Model\ArticleAttribute');

        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\ArticleAttribute.article_id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\ArticleAttribute.attribute_id LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('ITECH\Datasource\Model\ArticleAttribute.attribute_value LIKE :q3:', array('q3' => '%' . $params['conditions']['q'] . '%'));
        } 
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\ArticleAttribute.id DESC');
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
        $b = ArticleAttribute::getModelsManager()->createBuilder();

        $b->from('ITECH\Datasource\Model\ArticleAttribute'); 
        $b->columns('
            ITECH\Datasource\Model\ArticleAttribute.article_id,
            ITECH\Datasource\Model\ArticleAttribute.attribute_id, 	
            ITECH\Datasource\Model\ArticleAttribute.attribute_value
        ');
        
        $b->innerJoin('ITECH\Datasource\Model\Article', 'ITECH\Datasource\Model\Article.id = ITECH\Datasource\Model\ArticleAttribute.article_id');
        
        if (isset($params['conditions']['article_id']) && ($params['conditions']['article_id']) != '') {
            $b->andWhere('ITECH\Datasource\Model\ArticleAttribute.article_id = :article_id:', array('article_id' => $params['conditions']['article_id'])); 
        }
        
        if (!empty($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\ArticleAttribute.id DESC');
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        return $b->getQuery()->execute();
    }
}