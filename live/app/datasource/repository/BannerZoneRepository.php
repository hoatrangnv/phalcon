<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\BannerZone;
use ITECH\Datasource\Lib\Constant;

class BannerZoneRepository extends BannerZone
{
    /**
     * @author Vu.Tran
     */
    public function getListPagination(array $params)
    {
        $b = BannerZone::getModelsManager()->createBuilder();
        $b->columns(' 	
            ITECH\Datasource\Model\BannerZone.id, 	
            ITECH\Datasource\Model\BannerZone.name, 	
            ITECH\Datasource\Model\BannerZone.slug, 
            ITECH\Datasource\Model\BannerZone.width,	
            ITECH\Datasource\Model\BannerZone.height,
            ITECH\Datasource\Model\BannerZone.status
        ');	 
    
        $b->from('ITECH\Datasource\Model\BannerZone');
        
        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\BannerZone.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\BannerZone.name LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('ITECH\Datasource\Model\BannerZone.slug LIKE :q3:', array('q3' => '%' . $params['conditions']['q'] . '%'));
        } 
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\BannerZone.name DESC');
        }
        
        $paginator = new QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
        
    }
}