<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\Link;
use ITECH\Datasource\Lib\Constant;

class LinkRepository extends Link
{
    /**
     * @author Vu.Tran
     */
    public function getListPagination(array $params)
    {
        $b = Link::getModelsManager()->createBuilder(); 
        $b->columns('
            ITECH\Datasource\Model\Link.id, 	
            ITECH\Datasource\Model\Link.parent_id,  	
            ITECH\Datasource\Model\Link.name, 	
            ITECH\Datasource\Model\Link.title, 	
            ITECH\Datasource\Model\Link.url, 	
            ITECH\Datasource\Model\Link.target, 	
            ITECH\Datasource\Model\Link.rel, 	
            ITECH\Datasource\Model\Link.ordering, 	
            ITECH\Datasource\Model\Link.group_id
        ');

        $b->from('ITECH\Datasource\Model\Link');

        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\Link.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\Link.name LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('ITECH\Datasource\Model\Link.alias LIKE :q3:', array('q3' => '%' . $params['conditions']['q'] . '%'));
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Link.id DESC');
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
    public function getAdminList(array $params)
    {
        $b = Link::getModelsManager()->createBuilder();
        $b->columns('
            ITECH\Datasource\Model\Link.id,	
            ITECH\Datasource\Model\Link.parent_id, 	
            ITECH\Datasource\Model\Link.name, 	
            ITECH\Datasource\Model\Link.title, 	
            ITECH\Datasource\Model\Link.url, 	
            ITECH\Datasource\Model\Link.target, 	
            ITECH\Datasource\Model\Link.rel, 	
            ITECH\Datasource\Model\Link.ordering, 	
            ITECH\Datasource\Model\Link.group_id
        ');

        $b->from('ITECH\Datasource\Model\Link');

        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\Link.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\Link.name LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('ITECH\Datasource\Model\Link.alias LIKE :q3:', array('q3' => '%' . $params['conditions']['q'] . '%'));
        }
        
        if (isset($params['conditions']['group_id']) && $params['conditions']['group_id'] != '') {
            $b->andWhere('ITECH\Datasource\Model\Link.group_id = :group_id:', array('group_id' => $params['conditions']['group_id']));
        }
        
        if (isset($params['conditions']['parent_id']) && $params['conditions']['parent_id'] != '') {
            $b->andWhere('ITECH\Datasource\Model\Link.parent_id = :parent_id:', array('parent_id' => $params['conditions']['parent_id']));
        }
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Link.id ASC');
        }
        
        return $b->getQuery()->execute();
    }
    
    /**
     * @author Vu.Tran
     */
    public function getList(array $params)
    {
        $b = Link::getModelsManager()->createBuilder();
        $b->columns('
            l.id,	
            l.parent_id, 	
            l.name, 	
            l.title, 	
            l.url, 	
            l.target, 	
            l.rel, 	
            l.ordering, 	
            l.group_id
        ');

        $b->from(array('l' => 'ITECH\Datasource\Model\Link'));

        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('l.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('l.name LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('l.alias LIKE :q3:', array('q3' => '%' . $params['conditions']['q'] . '%'));
        }
        
        if (isset($params['conditions']['group_id']) && $params['conditions']['group_id'] != '') {
            $b->andWhere('l.group_id = :group_id:', array('group_id' => $params['conditions']['group_id']));
        }
        
        $b->andWhere('l.parent_id = :parent_id:', array('parent_id' => 0));
        
        if (isset($params['ordering'])) {
            $b->orderBy($params['ordering']);
        } else {
            $b->orderBy('l.id ASC');
        }

        $categories['main'] = $b->getQuery()->execute();
        foreach ($categories['main'] as $item) {
            $b = Link::getModelsManager()->createBuilder();
            $b->from(array('l' => 'ITECH\Datasource\Model\Link'));
            $b->where('l.parent_id = :parent_id:', array('parent_id' => $item->id));
            $b->orderBy('l.ordering ASC, l.name ASC');
            $categories['sub'][$item->id] = $b->getQuery()->execute();
        }
        
        return $categories;
    }
}