<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\File;
use ITECH\Datasource\Lib\Constant;

class FileRepository extends File
{
    /**
     * @author Vu.Tran
     */
    public function getListPagination(array $params)
    {
        $b = File::getModelsManager()->createBuilder();
        $b->columns(' 	
            ITECH\Datasource\Model\File.id, 
            ITECH\Datasource\Model\File.category_id,
            ITECH\Datasource\Model\File.title, 	
            ITECH\Datasource\Model\File.file_name,
            ITECH\Datasource\Model\File.file_type, 	
            ITECH\Datasource\Model\File.file_size, 	
            ITECH\Datasource\Model\File.created_at, 	
            ITECH\Datasource\Model\File.updated_at, 
            ITECH\Datasource\Model\File.created_by,	
            ITECH\Datasource\Model\File.updated_by, 	
            ITECH\Datasource\Model\File.created_ip,
            ITECH\Datasource\Model\Category.id as category_id
        ');	 
    
        $b->from('ITECH\Datasource\Model\File');
        $b->leftJoin('ITECH\Datasource\Model\Category', 'ITECH\Datasource\Model\Category.id = ITECH\Datasource\Model\File.category_id');
        
        if (isset($params['conditions']['category_id']) && ($params['conditions']['category_id']) != '') {
            $b->andWhere('ITECH\Datasource\Model\File.category_id = :category_id:', array('category_id' => $params['conditions']['category_id'])); 
        }
        
        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\File.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\File.title LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
        } 
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\File.id DESC');
        }
        
        $paginator = new QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
        
    }
}