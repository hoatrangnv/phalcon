<?php
namespace ITECH\Datasource\Repository;

use Phalcon\Paginator\Adapter\QueryBuilder;
use ITECH\Datasource\Model\Article;
use ITECH\Datasource\Lib\Constant;

class ArticleRepository extends Article
{
    /**
     * @author Vu.Tran
     */
    public function getCountAll(array $params) {
        $b = Article::getModelsManager()->createBuilder();

        $b->columns('COUNT(ITECH\Datasource\Model\Article.id) AS count');
        $b->from('ITECH\Datasource\Model\Article');

        if (isset($params['conditions']['status']) && $params['conditions']['status'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Article.status = :status:', array('status' => $params['conditions']['status']));
        }

        if (isset($params['conditions']['module']) && $params['conditions']['module'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Article.module = :module:', array('module' => $params['conditions']['module']));
        }


        if (isset($params['conditions']['is_expired'])) {
            if ($params['conditions']['is_expired'] == false) {
                $b->andWhere('ITECH\Datasource\Model\Article.expired_at >= :expired_at:', array('expired_at' => date('Y-m-d')));
            } else {
                $b->andWhere('ITECH\Datasource\Model\Article.expired_at < :expired_at:', array('expired_at' => date('Y-m-d')));
            }
        }

        if (isset($params['conditions']['today'])) {
            if ($params['conditions']['today'] == true) {
                $b->andWhere('DATE_FORMAT(ITECH\Datasource\Model\Article.updated_at, "%Y-%m-%d") = :updated_at:', array('updated_at' => date('Y-m-d')));
            }
        }

        return $b->getQuery()->execute();
    }

    /**
     * @author Vu.Tran
     */
    public function getListPagination(array $params)
    {
        $b = Article::getModelsManager()->createBuilder();
        $b->columns('
            ITECH\Datasource\Model\Article.id,
            ITECH\Datasource\Model\Article.title,
            ITECH\Datasource\Model\Article.alias,
            ITECH\Datasource\Model\Article.intro,
            ITECH\Datasource\Model\Article.image,
            ITECH\Datasource\Model\Article.hits,
            ITECH\Datasource\Model\Article.created_at,
            ITECH\Datasource\Model\Article.updated_at,
            ITECH\Datasource\Model\Article.type,
            ITECH\Datasource\Model\Article.show_comment,
            ITECH\Datasource\Model\Article.comment_count,
            ITECH\Datasource\Model\Article.status,
            ITECH\Datasource\Model\Article.ordering,
            ITECH\Datasource\Model\Article.created_by,
            ITECH\Datasource\Model\Article.updated_by,
            ITECH\Datasource\Model\Article.created_ip,
            ITECH\Datasource\Model\Admin.name as admin_name
        ');

        $b->from('ITECH\Datasource\Model\Article');
        $b->innerJoin('ITECH\Datasource\Model\Admin', 'ITECH\Datasource\Model\Admin.id = ITECH\Datasource\Model\Article.created_by');
        
        $b->distinct('ITECH\Datasource\Model\Article.id');

        if (isset($params['conditions']['category_id']) && ($params['conditions']['category_id']) != '') {
            $b->innerJoin('ITECH\Datasource\Model\ArticleCategory', 'ITECH\Datasource\Model\ArticleCategory.article_id = ITECH\Datasource\Model\Article.id');
            $b->innerJoin('ITECH\Datasource\Model\Category', 'ITECH\Datasource\Model\Category.id = ITECH\Datasource\Model\ArticleCategory.category_id');
            $b->andWhere('ITECH\Datasource\Model\ArticleCategory.category_id = :category_id:', array('category_id' => $params['conditions']['category_id']));
        }

        if (isset($params['conditions']['tag_id']) && ($params['conditions']['tag_id']) != '') {
            $b->innerJoin('ITECH\Datasource\Model\ArticleTag', 'ITECH\Datasource\Model\ArticleTag.article_id = ITECH\Datasource\Model\Article.id');
            $b->innerJoin('ITECH\Datasource\Model\Tag', 'ITECH\Datasource\Model\Tag.id = ITECH\Datasource\Model\ArticleTag.tag_id');
            $b->andWhere('ITECH\Datasource\Model\ArticleTag.tag_id = :tag_id:', array('tag_id' => $params['conditions']['tag_id']));
        }

        if (isset($params['conditions']['search_q']) && $params['conditions']['search_q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\Article.id = :q1:', array('q1' => $params['conditions']['search_q']));
            $b->andWhere('ITECH\Datasource\Model\Article.title LIKE :q2:', array('q2' => '%' . $params['conditions']['search_q'] . '%'));
            $b->orWhere('ITECH\Datasource\Model\Article.alias LIKE :q3:', array('q3' => '%' . $params['conditions']['search_q'] . '%'));
        }

        if (isset($params['conditions']['q']) && $params['conditions']['q'] != '') {
            $b->orWhere('ITECH\Datasource\Model\Article.id = :q1:', array('q1' => $params['conditions']['q']));
            $b->orWhere('ITECH\Datasource\Model\Article.title LIKE :q2:', array('q2' => '%' . $params['conditions']['q'] . '%'));
            $b->orWhere('ITECH\Datasource\Model\Article.alias LIKE :q3:', array('q3' => '%' . $params['conditions']['q'] . '%'));
        }

        if (isset($params['conditions']['status']) && $params['conditions']['status'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Article.status = :status:', array('status' => $params['conditions']['status']));
        }

        if (isset($params['conditions']['type']) && $params['conditions']['type'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Article.type = :type:', array('module' => $params['conditions']['type']));
        }

        if (isset($params['conditions']['module']) && $params['conditions']['module'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Article.module = :module:', array('module' => $params['conditions']['module']));
        }

        if (isset($params['conditions']['created_by']) && $params['conditions']['created_by'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Article.created_by = :created_by:', array('created_by' => $params['conditions']['created_by']));
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Article.id DESC');
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
        $b = Article::getModelsManager()->createBuilder();

        $b->columns('
            ITECH\Datasource\Model\Article.id,
            ITECH\Datasource\Model\Article.title,
            ITECH\Datasource\Model\Article.alias,
            ITECH\Datasource\Model\Article.intro,
            ITECH\Datasource\Model\Article.image,
            ITECH\Datasource\Model\Article.hits,
            ITECH\Datasource\Model\Article.created_at,
            ITECH\Datasource\Model\Article.updated_at,
            ITECH\Datasource\Model\Article.type,
            ITECH\Datasource\Model\Article.show_comment,
            ITECH\Datasource\Model\Article.comment_count,
            ITECH\Datasource\Model\Article.status,
            ITECH\Datasource\Model\Article.ordering,
            ITECH\Datasource\Model\Article.created_by,
            ITECH\Datasource\Model\Article.updated_by,
            ITECH\Datasource\Model\Article.created_ip,
            ITECH\Datasource\Model\Article.module,
            ITECH\Datasource\Model\Category.id as category_id,
            ITECH\Datasource\Model\Category.name as category_name,
            ITECH\Datasource\Model\Category.slug as category_slug,
            ITECH\Datasource\Model\Admin.name as admin_name,
            ITECH\Datasource\Model\Admin.id as admin_id
        ');


        $b->from('ITECH\Datasource\Model\Article');
        $b->innerJoin('ITECH\Datasource\Model\Admin', 'ITECH\Datasource\Model\Admin.id = ITECH\Datasource\Model\Article.created_by');
        $b->innerJoin('ITECH\Datasource\Model\ArticleCategory', 'ITECH\Datasource\Model\ArticleCategory.article_id = ITECH\Datasource\Model\Article.id');
        $b->join('ITECH\Datasource\Model\Category', 'ITECH\Datasource\Model\Category.id = ITECH\Datasource\Model\ArticleCategory.category_id');


        if (isset($params['conditions']['category_id']) && ($params['conditions']['category_id']) != '') {
            $b->andWhere('ITECH\Datasource\Model\ArticleCategory.category_id = :category_id:', array('category_id' => $params['conditions']['category_id']));
        }
        
        if (isset($params['conditions']['category_idx']) && is_array($params['conditions']['category_idx'])) {
            $b->inWhere('ITECH\Datasource\Model\ArticleCategory.category_id', $params['conditions']['category_idx']);
        }
        
        if (isset($params['conditions']['id']) && $params['conditions']['id'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Article.id <> :id:', array('id' => $params['conditions']['id']));
        }
        
        if (isset($params['conditions']['id_n']) && $params['conditions']['id_n'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Article.id > :id:', array('id' => $params['conditions']['id_n']));
        }
        
        if (isset($params['conditions']['id_o']) && $params['conditions']['id_o'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Article.id < :id:', array('id' => $params['conditions']['id_o']));
        }
        
        if (isset($params['conditions']['module']) && ($params['conditions']['module']) != '') {
            $b->andWhere('ITECH\Datasource\Model\Article.module = :module:', array('module' => $params['conditions']['module']));
            $b->andWhere('ITECH\Datasource\Model\Category.module = :module:', array('module' => $params['conditions']['module']));
        }

        if (isset($params['conditions']['type']) && $params['conditions']['type'] !=''){
            $b->andWhere('ITECH\Datasource\Model\Article.type = :type:', array('type' => $params['conditions']['type']));
        }

        if (isset($params['conditions']['status']) && ($params['conditions']['status']) != '') {
            $b->andWhere('ITECH\Datasource\Model\Article.status = :status:', array('status' => $params['conditions']['status']));
        }
        
        if (!empty($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Article.ordering DESC');
        }

        $b->groupBy(array('ITECH\Datasource\Model\Article.id'));
        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        return $b->getQuery()->execute();
    }

    /**
     * @author Vu.Tran
     */
    public function getListRelated(array $params)
    {
        $b = Article::getModelsManager()->createBuilder();

        $b->from('ITECH\Datasource\Model\Article');
        $b->columns('
            ITECH\Datasource\Model\Article.id,
            ITECH\Datasource\Model\Article.title,
            ITECH\Datasource\Model\Article.alias,
            ITECH\Datasource\Model\Article.intro,
            ITECH\Datasource\Model\Article.image,
            ITECH\Datasource\Model\Article.hits,
            ITECH\Datasource\Model\Article.created_at,
            ITECH\Datasource\Model\Article.updated_at,
            ITECH\Datasource\Model\Article.type,
            ITECH\Datasource\Model\Article.show_comment,
            ITECH\Datasource\Model\Article.comment_count,
            ITECH\Datasource\Model\Article.status,
            ITECH\Datasource\Model\Article.ordering,
            ITECH\Datasource\Model\Article.created_by,
            ITECH\Datasource\Model\Article.updated_by,
            ITECH\Datasource\Model\Article.created_ip,
            ITECH\Datasource\Model\Article.module,
            ITECH\Datasource\Model\Category.id as category_id,
            ITECH\Datasource\Model\Category.name as category_name,
            ITECH\Datasource\Model\Category.slug as category_slug
        ');

        $b->innerJoin('ITECH\Datasource\Model\ArticleCategory', 'ITECH\Datasource\Model\ArticleCategory.article_id = ITECH\Datasource\Model\Article.id');
        $b->innerJoin('ITECH\Datasource\Model\Category', 'ITECH\Datasource\Model\Category.id = ITECH\Datasource\Model\ArticleCategory.category_id');

        if (isset($params['conditions']['category_id']) && ($params['conditions']['category_id']) != '') {
            $b->andWhere('ITECH\Datasource\Model\ArticleCategory.category_id = :category_id:', array('category_id' => $params['conditions']['category_id']));
        }

        if (isset($params['conditions']['id']) && ($params['conditions']['id']) != '') {
            $b->andWhere('ITECH\Datasource\Model\Article.id <> :id:', array('id' => $params['conditions']['id']));
        }

        if (isset($params['conditions']['category_id']) && ($params['conditions']['category_id']) != '') {
            $b->inWhere('ITECH\Datasource\Model\ArticleCategory.category_id', array($params['conditions']['category_id']));
        }

        if (isset($params['conditions']['module']) && ($params['conditions']['module']) != '') {
            $b->andWhere('ITECH\Datasource\Model\Article.module = :module:', array('module' => $params['conditions']['module']));
        }

        if (isset($params['group'])) {
            $b->groupBy($params['group']);
        }

        if (!empty($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Article.ordering DESC');
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        return $b->getQuery()->execute();
    }

    /**
     * @author Vu.Tran
     */
    public function getRssList(array $params)
    {
        $b = Article::getModelsManager()->createBuilder();

        $b->from('ITECH\Datasource\Model\Article');
        $b->columns('
            ITECH\Datasource\Model\Article.id,
            ITECH\Datasource\Model\Article.title,
            ITECH\Datasource\Model\Article.alias,
            ITECH\Datasource\Model\Article.image,
            ITECH\Datasource\Model\Article.updated_at,
            ITECH\Datasource\Model\Article.status,
            ITECH\Datasource\Model\Article.created_by,
            ITECH\Datasource\Model\Article.updated_by,
            ITECH\Datasource\Model\ArticleContent.content
        ');

        $b->innerJoin('ITECH\Datasource\Model\ArticleContent', 'ITECH\Datasource\Model\ArticleContent.article_id = ITECH\Datasource\Model\Article.id');
        $b->innerJoin('ITECH\Datasource\Model\ArticleCategory', 'ITECH\Datasource\Model\ArticleCategory.article_id = ITECH\Datasource\Model\Article.id');

        if (isset($params['conditions']['category_id']) && ($params['conditions']['category_id']) != '') {
            $b->andWhere('ITECH\Datasource\Model\ArticleCategory.category_id = :category_id:', array('category_id' => $params['conditions']['category_id']));
        }

        if (isset($params['conditions']['status']) && ($params['conditions']['status']) != '') {
            $b->andWhere('ITECH\Datasource\Model\Article.status = :status:', array('status' => $params['conditions']['status']));
        }

        if (isset($params['conditions']['module']) && ($params['conditions']['module']) != '') {
            $b->andWhere('ITECH\Datasource\Model\Article.module = :module:', array('module' => $params['conditions']['module']));
        }

        if (!empty($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ITECH\Datasource\Model\Article.ordering DESC');
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        return $b->getQuery()->execute();
    }

    /**
     * @author Vu.Tran
     */
    public function getDetail(array $params)
    {
        $b = Article::getModelsManager()->createBuilder();

        $b->from('ITECH\Datasource\Model\Article');

        $b->andWhere('ITECH\Datasource\Model\Article.status = :status:', array('status' => Constant::STATUS_ACTIVED));

        if (isset($params['conditions']['id']) && $params['conditions']['id'] != '') {
            $b->andWhere('ITECH\Datasource\Model\Article.id = :id:', array('id' => $params['conditions']['id']));
        }

        if (isset($params['conditions']['module']) && ($params['conditions']['module']) != '') {
            $b->andWhere('ITECH\Datasource\Model\Article.module = :module:', array('module' => $params['conditions']['module']));
        }

        return $b->getQuery()->execute();
    }
}
