<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\AttributeGroup;

class AttributeGroupRepository extends AttributeGroup
{
    /**
     * @author Vu.Tran
     */
    public function getListPagination(array $params)
    {
        $b = AttributeGroup::getModelsManager()->createBuilder();
        $b->columns('
            ITECH\Datasource\Model\AttributeGroup.id,
            ITECH\Datasource\Model\AttributeGroup.name,
            ITECH\Datasource\Model\AttributeGroup.slug,
            ITECH\Datasource\Model\AttributeGroup.ordering,
            ITECH\Datasource\Model\AttributeGroup.status
        ');

        $b->from(array('ITECH\Datasource\Model\AttributeGroup'));

        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\AttributeGroup.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\AttributeGroup.name LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('ITECH\Datasource\Model\AttributeGroup.slug LIKE :q3:', array('q3' => '%' . $params['conditions']['q'] . '%'));
        }

        if (isset($params['conditions']['status']) && $params['conditions']['status'] !=''){
            $b->andWhere('ITECH\Datasource\Model\AttributeGroup.status = :status:', array('status' => $params['conditions']['status']));
        }       
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\AttributeGroup.id DESC');
        }

        $paginator = new QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}