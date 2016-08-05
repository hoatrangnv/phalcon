<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\Banner;
use ITECH\Datasource\Lib\Constant;

class BannerRepository extends Banner
{
    /**
     * @author Vu.Tran
     */
    public function getListPagination(array $params)
    {
        $b = Banner::getModelsManager()->createBuilder();
        $b->columns(' 	
            ITECH\Datasource\Model\Banner.id, 	
            ITECH\Datasource\Model\Banner.name, 	
            ITECH\Datasource\Model\Banner.image, 
            ITECH\Datasource\Model\Banner.url,	
            ITECH\Datasource\Model\Banner.expired_at,
            ITECH\Datasource\Model\Banner.click,
            ITECH\Datasource\Model\Banner.ordering,
            ITECH\Datasource\Model\Banner.banner_zone_id,
            ITECH\Datasource\Model\Banner.status
        ');	 
    
        $b->from('ITECH\Datasource\Model\Banner');
        
        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\Banner.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\Banner.name LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
        } 
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Banner.name DESC');
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
        $b = Banner::getModelsManager()->createBuilder();
        $b->columns('
            ITECH\Datasource\Model\Banner.id,
            ITECH\Datasource\Model\Banner.name, 	
            ITECH\Datasource\Model\Banner.image, 	
            ITECH\Datasource\Model\Banner.url, 	
            ITECH\Datasource\Model\Banner.click,
            ITECH\Datasource\Model\Banner.ordering,
            ITECH\Datasource\Model\BannerZone.width as banner_width,
            ITECH\Datasource\Model\BannerZone.height as banner_height	
        ');
        
        $b->from('ITECH\Datasource\Model\Banner');
        $b->innerJoin('ITECH\Datasource\Model\BannerZone', 'ITECH\Datasource\Model\BannerZone.id = ITECH\Datasource\Model\Banner.banner_zone_id');
        
        if (isset($params['conditions']['status']) && ($params['conditions']['status']) != '') {
            $b->andWhere('ITECH\Datasource\Model\Banner.status = :status:', array('status' => $params['conditions']['status'])); 
        }
        
        if (isset($params['conditions']['banner_zone_id']) && ($params['conditions']['banner_zone_id']) != '') {
            $b->andWhere('ITECH\Datasource\Model\Banner.banner_zone_id = :banner_zone_id:', array('banner_zone_id' => $params['conditions']['banner_zone_id'])); 
        }
        
        if (!empty($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Banner.ordering DESC');
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        return $b->getQuery()->execute();
    }
}