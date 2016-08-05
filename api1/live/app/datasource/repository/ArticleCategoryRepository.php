<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\ArticleCategory;
use ITECH\Datasource\Lib\Constant;

class ArticleCategoryRepository extends ArticleCategory
{   
    /**
     * @author Vu.Tran
     */
    public function getList(array $params)
    {
        $b = ArticleCategory::getModelsManager()->createBuilder();
        $b->columns('
            ITECH\Datasource\Model\ArticleCategory.article_id,
            ITECH\Datasource\Model\Category.id as category_id,
            ITECH\Datasource\Model\Category.name as category_name,
            ITECH\Datasource\Model\Category.slug as category_slug,
            ITECH\Datasource\Model\Category.article_count as category_article_count,
            ITECH\Datasource\Model\Category.image as category_image,
            ITECH\Datasource\Model\Category.status as category_status,
            ITECH\Datasource\Model\Category.created_at as category_created_at,
            ITECH\Datasource\Model\Category.module as category_module
        ');
        $b->from('ITECH\Datasource\Model\ArticleCategory');
        $b->innerJoin('ITECH\Datasource\Model\Category', 'ITECH\Datasource\Model\Category.id = ITECH\Datasource\Model\ArticleCategory.category_id');
        
        $b->where('ITECH\Datasource\Model\Category.status <> :status_deleted:', array('status_deleted' => Constant::CATEGORY_STATUS_DELETED));
        
        if(isset($params['conditions']['id']) && $params['conditions']['id'] != '') {
            $b->andWhere('ITECH\Datasource\Model\ArticleCategory.article_id = :article_id:', array('article_id' => $params['conditions']['id']));
        }
        
        if(isset($params['conditions']['module']) && $params['conditions']['module'] != '') {
            $b->andWhere('ITECH\Datasource\Model\Category.module = :module:', array('module' => $params['conditions']['module']));
        }
        
        $b->orderBy('ITECH\Datasource\Model\Category.ordering ASC, ITECH\Datasource\Model\Category.name ASC');
        
        return $b->getQuery()->execute();
    }
    
}