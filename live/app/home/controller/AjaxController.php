<?php
namespace ITECH\Home\Controller;

use Phalcon\Mvc\View;
use ITECH\Home\Controller\BaseController;
use ITECH\Datasource\Model\Location;
use ITECH\Datasource\Model\Payment;
use ITECH\Datasource\Model\Transport;
use ITECH\Home\Component\CartComponent;
use ITECH\Datasource\Model\Comment;
use ITECH\Home\Form\CommentForm;
use ITECH\Datasource\Lib\Constant;

class AjaxController extends BaseController
{
    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * @author Vu.Tran
     */
    public function districtAction()
    {
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                $id = $this->request->getPost('id', array('int'), -1);
                $params = array();
                $cache_name = md5(serialize(array(
                    'AjaxController',
                    'districtAction',
                    'Location',
                    'find',
                    $id,
                    $params
                )));
                $result = $this->cache->get($cache_name);
                if (!$result) {
                    $result = Location::find(array(
                        'conditions' => 'parent = :parent:',
                        'bind' => array('parent' => $id)
                    ));  
                }
            }
        }
        $this->view->setVars(array(
            'result' => $result
        ));
        $this->view->pick(parent::$theme . '/ajax/district');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
    
    /**
     * @author Vu.Tran
     */
    public function paymentAction()
    {
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                $id = $this->request->getPost('id', array('int'), -1);
                $params = array();
                $cache_name = md5(serialize(array(
                    'AjaxController',
                    'paymentAction',
                    'Payment',
                    'findFirst',
                    $id,
                    $params
                )));
                $result = $this->cache->get($cache_name);
                if (!$result) {
                    $result = Payment::findFirst(array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => $id)
                    )); 
                }
            }
        }
        $this->view->setVars(array(
            'result' => $result
        ));
        $this->view->pick(parent::$theme . '/ajax/payment');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
    
    /**
     * @author Vu.Tran
     */
    public function transportAction()
    {
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                $id = $this->request->getPost('id', array('int'), -1);
                $params = array();
                $cache_name = md5(serialize(array(
                    'AjaxController',
                    'transportAction',
                    'Transport',
                    'findFirst',
                    $id,
                    $params
                )));
                $result = $this->cache->get($cache_name);
                if (!$result) {
                    $result = Transport::findFirst(array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => $id)
                    ));  
                }
            }
        }
        $this->view->setVars(array(
            'result' => $result
        ));
        $this->view->pick(parent::$theme . '/ajax/transport');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
    
    /**
     * @author Vu.Tran
     */
    public function cartAction()
    {
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                $id = $this->request->getPost('id', array('int'), -1);
                $params = array();
                $cache_name = md5(serialize(array(
                    'AjaxController',
                    'cartAction',
                    'Location',
                    'findFirst',
                    $id,
                    $params
                )));
                $result = $this->cache->get($cache_name);
                if (!$result) {
                    $result = Location::findFirst(array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => $id)
                    ));  
                }
                
                if ($this->session->has('CUSTOMER')) {
                    $user = $this->session->get('CUSTOMER');
                } else {
                    $user = array();
                }
                
                $user['fee'] = $result->fee;
                $this->session->set("CUSTOMER", $user);
            }
        }
        $cart_component = new CartComponent();
        $cart_mini_layout = $cart_component->mini($this, self::$theme, array());
        
        $this->view->setVars(array(
            'cart_mini_layout' => $cart_mini_layout
        ));
        $this->view->pick(parent::$theme . '/ajax/cart');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
    
    /**
     * @author Vu.Tran
     */
    public function commentAction()
    {
        
        $response = array(
            'status' => Constant::CODE_SUCCESS,
            'message' => 'Success.',
            'results' => array()
        );
        
        $user = $this->session->get('USER');
        
        if (!$user) {
            $response = array(
                'status' => Constant::CODE_ERROR,
                'message' => 'Bạn chưa đăng nhập.',
                'results' => array()
            );
        } else {
            $form = new CommentForm();
            if ($this->request->isAjax()) {
                if ($this->request->isPost()) {
                    if (!$form->isValid($this->request->getPost())) {
                        $response = array(
                            'status' => Constant::CODE_ERROR,
                            'message' => 'Thông tin không hợp lệ.',
                            'results' => array()
                        );
                        $description = $form->getMessagesFor('description');
                        if (isset($description[0])) {
                            $response['results']['description'] = $description[0]->getMessage();
                        }

                    } else {
                       try {
                            $comment = new Comment();
                            $comment->article_id = $this->request->getPost('id');
                            $comment->user_id = $user->result->id;
                            $comment->description = $this->request->getPost('description');
                            $comment->status = Constant::COMMENT_STATUS_INACTIVED;
                            $comment->created_at = date('Y-m-d h:i:s');
                            $comment->updated_at = date('Y-m-d h:i:s');
                            $comment->created_ip = $this->request->getClientAddress();
                            $comment->user_agent = $this->request->getUserAgent();
                            
                            if (!$comment->create()) {
                                $message = $comment->getMessages();
                                if (isset($message[0])) {
                                    $response = array(
                                        'status' => Constant::CODE_ERROR,
                                        'message' => $message[0]->getMessage(),
                                        'results' => array()
                                    );
                                } else {
                                    $response = array(
                                        'status' => Constant::CODE_ERROR,
                                        'message' => 'Error.',
                                        'results' => array()
                                    );
                                }
                            } else {
                                $cache_name = md5(serialize(array(
                                    'AjaxController',
                                    'commentListAction',
                                    'Comment',
                                    'find',
                                    $this->request->getPost('id')
                                )));

                                if ($this->cache->get($cache_name)) {
                                    $this->cache->detele($cache_name); 
                                }
                                $response = array(
                                    'status' => Constant::CODE_SUCCESS,
                                    'message' => 'Success.',
                                    'results' => array()
                                );
                            }
                        } catch (Exception $e) {
                            $this->logger->log('[AjaxController][commentAction] ' . $e->getMessage(), Logger::ERROR);
                            $response = array(
                                'status' => Constant::CODE_SUCCESS,
                                'message' => $e->getMessage(),
                                'results' => array()
                            );
                        }
                    }
                }
            }
        }
        
        parent::outputJSON($response);
    }
    
    /**
     * @author Vu.Tran
     */
    public function commentListAction() {
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                $id = $this->request->getPost('id', array('int'), -1);
                $cache_name = md5(serialize(array(
                    'AjaxController',
                    'commentListAction',
                    'Comment',
                    'find',
                    $id
                )));
                $result = $this->cache->get($cache_name);
                if (!$result) {
                    $result = Comment::find(array(
                        'conditions' => 'article_id = :article_id: and status = :status:',
                        'bind' => array(
                            'article_id' => $id,
                            'status' => Constant::COMMENT_STATUS_ACTIVED
                        ),
                        'limit' => 20,
                        'order' => 'created_at DESC'
                    ));  
                }
            }
        }
        $this->view->setVars(array(
            'result' => $result
        ));
        
        $this->view->pick(parent::$theme . '/ajax/comment_list');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
}