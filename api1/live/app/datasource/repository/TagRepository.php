<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\Tag;

class TagRepository extends Tag
{
    /**
     * @author Vu.Tran
     */
    public function getListPagination(array $params)
    {
        $b = Tag::getModelsManager()->createBuilder();
        $b->columns('
            ITECH\Datasource\Model\Tag.id,
            ITECH\Datasource\Model\Tag.title,
            ITECH\Datasource\Model\Tag.slug
        ');

        $b->from(array('ITECH\Datasource\Model\Tag'));

        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\Tag.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\Tag.title LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('ITECH\Datasource\Model\Tag.slug LIKE :q3:', array('q3' => '%' . $params['conditions']['q'] . '%'));
        }

        if (isset($params['conditions']['status']) && $params['conditions']['status'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Tag.status = :status:', array('status' => $params['conditions']['status']));
        }       
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Tag.id DESC');
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
        $b = Tag::getModelsManager()->createBuilder();
        $b->from('ITECH\Datasource\Model\Tag');

        if (isset($params['conditions']['idx'])) {
            $b->inWhere('ITECH\Datasource\Model\Tag.id', $params['conditions']['idx']);
        }

        return $b->getQuery()->execute();
    }
}