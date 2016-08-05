<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\LinkGroup;
use ITECH\Datasource\Lib\Constant;

class LinkGroupRepository extends LinkGroup
{
    /**
     * @author Vu.Tran
     */
    public function getListPagination(array $params)
    {
        $b = LinkGroup::getModelsManager()->createBuilder();
        $b->columns('
            ITECH\Datasource\Model\LinkGroup.id, 
            ITECH\Datasource\Model\LinkGroup.name, 	
            ITECH\Datasource\Model\LinkGroup.slug, 	
            ITECH\Datasource\Model\LinkGroup.ordering 
        ');

        $b->from(array('ITECH\Datasource\Model\LinkGroup'));

        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\LinkGroup.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\LinkGroup.name LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('ITECH\Datasource\Model\LinkGroup.alias LIKE :q3:', array('q3' => '%' . $params['conditions']['q'] . '%'));
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\LinkGroup.id DESC'); 
        }

        $paginator = new QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}