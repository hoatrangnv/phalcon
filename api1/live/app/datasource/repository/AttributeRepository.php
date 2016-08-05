<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\Attribute;

class AttributeRepository extends Attribute
{
    /**
     * @author Vu.Tran
     */
    public function getListPagination(array $params)
    {
        $b = Attribute::getModelsManager()->createBuilder();
        $b->columns('
            ITECH\Datasource\Model\Attribute.id,
            ITECH\Datasource\Model\Attribute.name,
            ITECH\Datasource\Model\Attribute.slug,
            ITECH\Datasource\Model\Attribute.ordering,
            ITECH\Datasource\Model\Attribute.status
        ');

        $b->from(array('ITECH\Datasource\Model\Attribute'));

        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\Attribute.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\Attribute.name LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('ITECH\Datasource\Model\Attribute.slug LIKE :q3:', array('q3' => '%' . $params['conditions']['q'] . '%'));
        }

        if (isset($params['conditions']['status']) && $params['conditions']['status'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Attribute.status = :status:', array('status' => $params['conditions']['status']));
        }       
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Attribute.id DESC');
        }

        $paginator = new QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}