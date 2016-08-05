<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Lib\Constant;

class CategoryRepository extends Category
{
    /**
     * @author Vu.Tran
     */
    public function getListPagination(array $params)
    {
        $b = Category::getModelsManager()->createBuilder();
        $b->columns('
            ITECH\Datasource\Model\Category.id,
            ITECH\Datasource\Model\Category.name,
            ITECH\Datasource\Model\Category.slug,
            ITECH\Datasource\Model\Category.ordering,
            ITECH\Datasource\Model\Category.article_count,
            ITECH\Datasource\Model\Category.image,
            ITECH\Datasource\Model\Category.status,
            ITECH\Datasource\Model\Category.created_at,
            ITECH\Datasource\Model\Category.module
        ');

        $b->from('ITECH\Datasource\Model\Category');

        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\Category.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\Category.name LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('ITECH\Datasource\Model\Category.slug LIKE :q3:', array('q3' => '%' . $params['conditions']['q'] . '%'));
        }

        if (isset($params['conditions']['user_id']) && ($params['conditions']['user_id']) != '') {
            $b->innerJoin('ITECH\Datasource\Model\UserCategory', 'ITECH\Datasource\Model\UserCategory.user_id = ITECH\Datasource\Model\Category.id');
        }
        if (isset($params['conditions']['status']) && $params['conditions']['status'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Category.status = :status:', array('status' => $params['conditions']['status']));
        }
        
        if (isset($params['conditions']['module']) && $params['conditions']['module'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Category.module = :module:', array('module' => $params['conditions']['module'])); 
        }
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Category.status DESC, ITECH\Datasource\Model\Category.created_at DESC');
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
        $b = Category::getModelsManager()->createBuilder();
        $b->columns('
            ITECH\Datasource\Model\Category.id,
            ITECH\Datasource\Model\Category.name,
            ITECH\Datasource\Model\Category.slug,
            ITECH\Datasource\Model\Category.ordering,
            ITECH\Datasource\Model\Category.description,
            ITECH\Datasource\Model\Category.article_count,
            ITECH\Datasource\Model\Category.image,
            ITECH\Datasource\Model\Category.status,
            ITECH\Datasource\Model\Category.created_at,
            ITECH\Datasource\Model\Category.module
        ');
        $b->from('ITECH\Datasource\Model\Category');
        $b->where('ITECH\Datasource\Model\Category.status <> :status_deleted:', array('status_deleted' => Constant::CATEGORY_STATUS_DELETED));
        
        if(isset($params['conditions']['id']) && $params['conditions']['id'] != '') {
            $b->andWhere('ITECH\Datasource\Model\Category.id <> :id:', array('id' => $params['conditions']['id']));
        }
        
        if (isset($params['conditions']['idx']) && is_array($params['conditions']['idx'])){
            $b->inWhere('ITECH\Datasource\Model\Category.id', $params['conditions']['idx']);
        }
        
        if(isset($params['conditions']['module']) && $params['conditions']['module'] != '') {
            $b->andWhere('ITECH\Datasource\Model\Category.module = :module:', array('module' => $params['conditions']['module']));
        }
        
        if(isset($params['conditions']['parent_id'])) {
            $b->andWhere('ITECH\Datasource\Model\Category.parent_id = :parent_id:', array('parent_id' => $params['conditions']['parent_id']));
        }
        
        $b->orderBy('ITECH\Datasource\Model\Category.ordering ASC, ITECH\Datasource\Model\Category.name ASC');
        
        return $b->getQuery()->execute();
    }
    
}