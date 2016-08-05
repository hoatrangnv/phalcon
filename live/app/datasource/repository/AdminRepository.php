<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\Admin;

class AdminRepository extends Admin
{
    /**
     * @author Cuong.Bui
     */
    public function getListPagination(array $params)
    {
        $b = Admin::getModelsManager()->createBuilder();
        $b->columns('
            a.id,
            a.username,
            a.name,
            a.email,
            a.phone,
            a.type,
            a.logined_at
        ');
        $b->from(array('a' => 'ITECH\Datasource\Model\Admin'));

        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('a.username LIKE :q1:', array('q1' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('a.name LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
        }

        $b->orderBy('
            a.logined_at DESC,
            a.username ASC
        ');

        $paginator = new QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }

    /**
     * @author Cuong.Bui
     */
    public function getListByType($type)
    {
        $b = Admin::getModelsManager()->createBuilder();

        $b->from(array('a' => 'ITECH\Datasource\Model\Admin'));
        $b->where('a.type = :type:', array('type' => $type));
        $b->orderBy('a.username ASC');

        return $b->getQuery()->execute();
    }
}