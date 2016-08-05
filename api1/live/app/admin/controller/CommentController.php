<?php
namespace ITECH\Admin\Controller;

use Phalcon\Exception;
use ITECH\Admin\Controller\BaseController;
use ITECH\Datasource\Model\Comment;
use ITECH\Datasource\Repository\CommentRepository;
use ITECH\Datasource\Lib\Util;
use ITECH\Datasource\Lib\Constant;

class CommentController extends BaseController
{
    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        parent::authenticate();
        parent::allowRole(array(Constant::ADMIN_TYPE_ROOT, Constant::ADMIN_TYPE_ADMIN));
    }

    /**
     * @author Vu.Tran
     */
    public function indexAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');

        $params = array(
            'conditions' => array('q' => $q),
            'page' => (int)$page,
            'limit' => (int)$limit
        );

        $comment_repository = new CommentRepository();
        $result = $comment_repository->getListPagination($params);

        $page_header = 'Thành viên';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');
        $search_box = true;

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'search_box' => $search_box,
            'page' => $page,
            'q' => $q,
            'result' => $result
        ));
        $this->view->pick('comment/index');
    }
    
    /**
     * @author Vu.Tran
     */
    public function deleteAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');

        $comment = Comment::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$comment) {
            throw new Exception('Bình luận  này không tồn tại.');
        }

        $comment->status = Constant::COMMENT_STATUS_DELETED;
        $comment->updated_at = date('Y-m-d H:i:s');

        $this->db->begin();
        try {
            if (!$comment->update()) {
                $message = $comment->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể xóa.');
                }
            } else {
                $this->db->commit();
                $this->flashSession->success('Xóa thành công.');
            }

            $query = array(
                'page' => $page,
                'q' => $q
            );
            return $this->response->redirect(array('for' => 'comment', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @author Vu.Tran
     */
    public function inactivedAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');

        $comment = Comment::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$comment) {
            throw new Exception('Bình luận  này không tồn tại.');
        }

        $comment->status = Constant::COMMENT_STATUS_INACTIVED;
        $comment->updated_at = date('Y-m-d H:i:s');

        $this->db->begin();
        try {
            if (!$comment->update()) {
                $message = $comment->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể xóa.');
                }
            } else {
                $this->db->commit();
                $this->flashSession->success('Ẩn thành công.');
            }

            $query = array(
                'page' => $page,
                'q' => $q
            );
            return $this->response->redirect(array('for' => 'comment', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @author Vu.Tran
     */
    public function activedAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');

        $comment = Comment::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$comment) {
            throw new Exception('Bình luận  này không tồn tại.');
        }

        $comment->status = Constant::COMMENT_STATUS_ACTIVED;
        $comment->updated_at = date('Y-m-d H:i:s');

        $this->db->begin();
        try {
            if (!$comment->update()) {
                $message = $comment->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể xóa.');
                }
            } else {
                $this->db->commit();
                $this->flashSession->success('Duyệt thành công.');
            }

            $query = array(
                'page' => $page,
                'q' => $q
            );
            return $this->response->redirect(array('for' => 'comment', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception($e->getMessage());
        }
    }
}