<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\Comment;

class CommentRepository extends Comment
{
    /**
     * @author Vu.Tran
     */
    public function getListPagination(array $params)
    {
        $b = Comment::getModelsManager()->createBuilder();
        $b->columns('
            ITECH\Datasource\Model\Comment.id,
            ITECH\Datasource\Model\Comment.article_id,
            ITECH\Datasource\Model\Comment.description,
            ITECH\Datasource\Model\Comment.status,
            ITECH\Datasource\Model\Comment.created_at,
            ITECH\Datasource\Model\Comment.created_ip,
            ITECH\Datasource\Model\Article.title as article_title,
            ITECH\Datasource\Model\Article.alias as article_alias
        ');

        $b->from(array('ITECH\Datasource\Model\Comment'));
        $b->innerJoin('ITECH\Datasource\Model\Article', 'ITECH\Datasource\Model\Article.id = ITECH\Datasource\Model\Comment.article_id');
        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\Comment.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\Comment.description LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('ITECH\Datasource\Model\Comment.article_id LIKE :q3:', array('q3' => '%' . $params['conditions']['q'] . '%'));
        }

        if (isset($params['conditions']['status']) && $params['conditions']['status'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Comment.status = :status:', array('status' => $params['conditions']['status']));
        }       
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Comment.created_at DESC');
        }

        $paginator = new QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}