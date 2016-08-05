<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\ArticleTag;
use ITECH\Datasource\Lib\Constant;

class ArticleTagRepository extends ArticleTag
{   
    /**
     * @author Vu.Tran
     */
    public function getList(array $params)
    {
        $b = ArticleTag::getModelsManager()->createBuilder();
        $b->columns('
            ITECH\Datasource\Model\ArticleTag.article_id,
            ITECH\Datasource\Model\Tag.id as tag_id,
            ITECH\Datasource\Model\Tag.title as tag_title,
            ITECH\Datasource\Model\Tag.slug as tag_slug
        ');
        $b->from('ITECH\Datasource\Model\ArticleTag');
        $b->innerJoin('ITECH\Datasource\Model\Tag', 'ITECH\Datasource\Model\Tag.id = ITECH\Datasource\Model\ArticleTag.tag_id');
        
        if(isset($params['conditions']['id']) && $params['conditions']['id'] != '') {
            $b->andWhere('ITECH\Datasource\Model\ArticleTag.article_id = :article_id:', array('article_id' => $params['conditions']['id']));
        }
        
        $b->orderBy('ITECH\Datasource\Model\Tag.title ASC');
        
        return $b->getQuery()->execute();
    }
    
}