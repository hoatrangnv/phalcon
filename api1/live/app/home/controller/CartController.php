<?php
namespace ITECH\Home\Controller;

use Phalcon\Logger;
use Phalcon\Exception;
use ITECH\Home\Controller\BaseController;
use ITECH\Home\Component\CartComponent;
use ITECH\Datasource\Model\Location;
use ITECH\Datasource\Model\Article;
use ITECH\Datasource\Model\User;
use ITECH\Datasource\Model\Order;
use ITECH\Datasource\Model\OrderDetail;
use ITECH\Datasource\Model\OrderUser;
use ITECH\Datasource\Model\Payment;
use ITECH\Datasource\Model\Transport;
use ITECH\Home\Form\CheckOutForm;
use ITECH\Home\Form\CheckOutLoginForm;
use ITECH\Home\Form\RecipientForm;
use ITECH\Home\Form\DeliveryForm;
use ITECH\Home\Form\PaymentForm;
use ITECH\Datasource\Lib\Constant;
use ITECH\Datasource\Lib\Mailer;
use ITECH\Datasource\Lib\Util;
use ITECH\Datasource\Lib\Captcha;

class CartController extends BaseController
{
    public $maxQuantity = 100;
    public $minQuantity = 1;

    /**
     * @author Vu.Tran
     */
    public function indexAction()
    {
        $carts = $this->session->get('CART');

        $image_url = $this->config->asset->home_image_url;
        $image_default_url = $this->config->asset->home_image_article_url . $this->config->drive->channel_name;

        $title_for_layout = 'Giỏ hàng của bạn';
        $description_for_layout = 'Giỏ hàng của bạn';
        $keywords_for_layout = 'Giỏ hàng của bạn';

        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'carts' => $carts,
            'image_url' => $image_url,
            'image_default_url' => $image_default_url
        ));
        $this->view->pick(parent::$theme . '/cart/index');
    }

    /**
     * @author Vu.Tran
     */
    public function addAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $total = $this->request->getPost('total', array('int'), -1);
        $article = Article::findFirst(array(
            'conditions' => 'id = :id: AND status = :status: AND module = :module:',
            'bind' => array(
                'id' => $id,
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_PRODUCTS,
            ))
        );

        if (!$article) {
            throw new Exception('Sản phẩm không tồn tại.');
        }

        $params = array();
        $cache_name = md5(serialize(array(
            'ProductController',
            'detailAction',
            'getDetail',
            'getArticleAttribute',
            $article->id,
            $params
        )));
        $article_attribute = $this->cache->get($cache_name);
        if (!$article_attribute) {
            $article_attribute = $article->getArticleAttribute();
        }

        if ($article_attribute) {
            foreach ($article_attribute as $item) {
                if ($item->attribute_id == 1) {
                    $price = $item->attribute_value;
                }
            }
        }


        if ($this->session->has('CART')) {
            $cart = $this->session->get('CART');
            $check = false;
            foreach ($cart as &$item) {
                if (in_array($id,$item)) {
                    if ($item['quantity'] > $this->maxQuantity) {
                        $this->flashSession->error('Sản phẩm ' . $article->title . ' có số lượng lớn hơn ' . $this->maxQuantity . '. Không thể thêm vào giỏ hàng!');
                        break;
                    } else {
                        if($total > 1){
                            $item['quantity'] = $total;
                            $item['total'] = $item['quantity'] * $item['price'];
                        } else {
                            $item['quantity'] += 1;
                            $item['total'] = $item['quantity'] * $item['price'];
                        }
                        $this->flashSession->success('Sản phẩm ' . $article->title . 'đã được thêm vào giỏ hàng của bạn!');
                    }

                    $check = true;

                    break;
                }
            }
            $this->session->set('CART', $cart);

            if(!$check){
                if ($total > 1) {
                    $quantity = $total;
                } else {
                    $quantity = $this->minQuantity;
                }
                $cart[]= array(
                    'id' => $article->id,
                    'title' => $article->title,
                    'image' => $article->image,
                    'price' => $price,
                    'quantity' => $quantity,
                    'total' => $quantity * $price
                );

                $this->session->set('CART', $cart);
                $this->flashSession->success('Sản phẩm ' . $article->title . ' đã được thêm vào giỏ hàng của bạn!');
            }
        } else {
            if ($total > 1) {
                $quantity = $total;
            } else {
                $quantity = $this->minQuantity;
            }

            $cart[]= array(
                'id' => $article->id,
                'title' => $article->title,
                'image' => $article->image,
                'price' => $price,
                'quantity' => $quantity,
                'total' => $price
            );

            $this->session->set('CART', $cart);
            $this->flashSession->success('Sản phẩm ' . $article->title . ' đã được thêm vào giỏ hàng của bạn!');
        }

        $this->response->redirect(array('for' => 'cart'));

    }

    /**
     * @author Vu.Tran
     */
    public function updateAction()
    {
        if ($this->session->has("CART")) {
            $cart = $this->session->get("CART");
        } else {
            $this->flashSession->error('Bạn không có sản phẩm trong giỏ hàng!');
            $this->response->redirect(array('for' => 'cart'));
        }

        if ($this->request->isPost()) {
            $quantity = $this->request->getPost('quantity', array('int'), -1);

            if (!empty($quantity)){
                foreach ($quantity as $key => $item) {
                    $cart[$key]['quantity'] = (int)$item;
                    $cart[$key]['total']= $item * $cart[$key]['price'];
                }
                $this->session->set("CART", $cart);
            }
        }

        $this->flashSession->success('Cập nhật giỏ hàng thành công!');
        $this->response->redirect(array('for' => 'cart'));
    }

    /**
     * @author Vu.Tran
     */
    public function deleteAction()
    {
        if ($this->session->has('CART')) {
            $carts = $this->session->get('CART');
        } else {
            $this->flashSession->error('Bạn không có sản phẩm nào trong giỏ hàng!');
            $this->response->redirect(array('for' => 'cart'));
        }

        $id = $this->request->getQuery('id', array('int'), -1);

        if ($this->session->has('CART')) {
            $cart = $this->session->get('CART');
        } else {
            $this->flashSession->error('Bạn không có sản phẩm trong giỏ hàng!');
            $this->response->redirect(array('for' => 'cart'));
        }

        foreach ($cart as $key => $item) {
            if($id == $item['id']){
                $this->flashSession->success('Xóa sản phẩm ' . $item['title'] . ' khỏi giỏ hàng!');
                unset($cart[$key]);
                break;
            }
        }

        $this->session->set('CART', $cart);
        $this->response->redirect(array('for' => 'cart'));

    }

    public function deleteAllAction()
    {
        if ($this->session->has('CART')) {
            $carts = $this->session->get('CART');
        } else {
            $this->flashSession->error('Bạn không có sản phẩm nào trong giỏ hàng!');
            $this->response->redirect(array('for' => 'cart'));
        }

        $this->session->remove('CART');
        $this->flashSession->success('Xóa giỏ hàng thành công!');

        $this->response->redirect(array('for' => 'cart'));
    }

    public function checkOutAction()
    {
        if ($this->session->has('CART')) {
            $carts = $this->session->get('CART');
        } else {
            $this->flashSession->error('Bạn không có sản phẩm nào trong giỏ hàng!');
            $this->response->redirect(array('for' => 'cart'));
        }

        if ($this->session->has('USER')) {
            $this->response->redirect(array('for' => 'cart_info_delivery'));
        }

        $image_url = $this->config->asset->home_image_url . $this->config->drive->channel_name . $this->config->asset->home_image_product_url;
        $image_default_url = $this->config->asset->home_image_article_url . $this->config->drive->channel_name;

        $form = new CheckOutForm();
        $has_account = false;
        if ($this->request->isPost()) {
            $status_check_out = $this->request->getPost('account', array('striptags', 'trim'), '');
            if ($status_check_out == 'no-account') {
                if (!$form->isValid($this->request->getPost())) {
                    $this->flashSession->error('Thông tin không hợp lệ.');
                } else {
                    $user = array();
                    $user['email'] = $this->request->getPost('email', array('striptags', 'trim'), '');
                    $this->session->set("CUSTOMER", $user);
                    $this->response->redirect(array('for' => 'cart_info_delivery'));
                }
            } else {
                $has_account = true;
                if (!$form->isValid($this->request->getPost()) || ($this->request->getPost('password', array('striptags', 'trim'), '')) == '') {
                    $this->flashSession->error('Thông tin không hợp lệ.');
                    $mess_password = 'Yêu cầu nhập mật khẩu.';
                } else {
                    $url = $this->url->get(array('for' => 'login_user_ajax'));
                    $post = array(
                        'email' => $this->request->getPost('email', array('striptags', 'trim'), ''),
                        'password' => md5($this->request->getPost('password', array('striptags', 'trim'), ''))
                    );

                    $response = json_decode(Util::curlPost($url, $post), true);
                    if ($response && isset($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
                        $this->session->set('USER', $response['result']);
                        $this->cookies->set('USER', serialize($response['result']), strtotime('+4 hours'));
                        $this->response->redirect(array('for' => 'cart_info_delivery'));
                    } else {
                        $this->flashSession->error('Thông tin đăng nhập không chính xác.');
                    }
                }
            }
        }

        $cart_component = new CartComponent();
        $cart_mini_layout = $cart_component->mini($this, self::$theme, array());

        $title_for_layout = 'Thanh toán đơn hàng';
        $description_for_layout = 'Thanh toán đơn hàng';
        $keywords_for_layout = 'Thanh toán đơn hàng';

        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'carts' => $carts,
            'image_url' => $image_url,
            'image_default_url' => $image_default_url,
            'cart_mini_layout' => $cart_mini_layout,
            'form' => $form, 
            'has_account' => $has_account,
            'mess_password' => isset($mess_password) ? $mess_password : '',
        ));
        $this->view->pick(parent::$theme . '/cart/check_out');
    }

    public function infoDeliveryAction()
    {
        if ($this->session->has('CART')) {
            $carts = $this->session->get('CART');
        } else {
            $this->flashSession->error('Bạn không có sản phẩm nào trong giỏ hàng!');
            $this->response->redirect(array('for' => 'cart'));
        }

        if ($this->session->has('USER')) {
            $user = $this->session->get('USER');
            $account = User::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array('id' => $user['id'])
            ));
        } else {
            if ($this->session->has('CUSTOMER')) {
                $user = $this->session->get('CUSTOMER');
            } else {
                $this->flashSession->error('Thông tin không hợp lệ.');
                $this->response->redirect(array('for' => 'cart_check_out'));
            }
        }

        $image_url = $this->config->asset->home_image_url . $this->config->drive->channel_name . $this->config->asset->home_image_product_url;
        $image_default_url = $this->config->asset->home_image_article_url . $this->config->drive->channel_name;

        $transport = Transport::findFirst(array(
            'order' => 'ordering ASC'
        ));

        $transports = Transport::find(array(
            'order' => 'ordering ASC'
        ));

        $payment = Payment::findFirst(array(
            'order' => 'ordering ASC'
        ));

        $payments = Payment::find(array(
            'order' => 'ordering ASC'
        ));
        if (isset($account)) {
            $form = new DeliveryForm($account);
            $account_district = Location::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array('id' => $account->district)
            ));

            if ($this->session->has('CUSTOMER')) {
                $user = $this->session->get('CUSTOMER');
            } else {
                $user = array();
            }
            $user['fee'] = $account_district->fee;
            $this->session->set("CUSTOMER", $user);
        } else {
            $form = new DeliveryForm();
        }

        $form_r = new RecipientForm();
        if ($this->request->isPost()) {

            $other_address = $this->request->getPost('other-address', array('striptags', 'trim'), '');
            if (isset($account) && $account) {
                if ($other_address == 'on') {
                    if (!$form_r->isValid($this->request->getPost())) {
                        $this->flashSession->error('Thông tin không hợp lệ.');
                    } else {
                        $user = array();
                        $user['name'] = $this->request->getPost('name', array('striptags', 'trim'), '');
                        $user['email'] = $account->email;
                        $user['phone'] = $this->request->getPost('phone', array('striptags', 'trim'), '');
                        $user['province'] = $this->request->getPost('province', array('striptags', 'trim'), '');
                        $user['district'] = $this->request->getPost('district', array('striptags', 'trim'), '');
                        $province = Location::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $user['province'])
                        ));
                        $user['province_title'] = $province->title;

                        $district = Location::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $user['district'])
                        ));
                        $user['district_title'] = $district->title;
                        $user['address'] = $this->request->getPost('address', array('striptags', 'trim'), '');
                        $user['delivery_date'] = $this->request->getPost('delivery_date', array('striptags', 'trim'), ''); 
                        $user['payment'] = $this->request->getPost('payment', array('striptags', 'trim'), '');
                        $user['transport'] = $this->request->getPost('transport', array('striptags', 'trim'), '');
                        $user['recipient_name'] = $this->request->getPost('recipient_name', array('striptags', 'trim'), '');
                        $user['recipient_phone'] = $this->request->getPost('recipient_phone', array('striptags', 'trim'), '');

                        $recipient_province = Location::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $this->request->getPost('recipient_province', array('striptags', 'trim'), ''))
                        ));

                        $user['recipient_province'] = $recipient_province->title;

                        $recipient_district = Location::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $this->request->getPost('recipient_district', array('striptags', 'trim'), ''))
                        ));

                        $user['recipient_district'] = $recipient_district->title;
                        $user['fee'] = $recipient_district->fee;
                        $user['recipient_address'] = $this->request->getPost('recipient_address', array('striptags', 'trim'), '');
                        $user['recipient_note'] = $this->request->getPost('recipient_note', array('striptags', 'trim'), '');
                        $this->session->set('CUSTOMER', $user);
                        $this->response->redirect(array('for' => 'cart_bill'));
                    }
                } else {
                    $user = array();
                    $user['name'] = $this->request->getPost('name', array('striptags', 'trim'), '');
                    $user['email'] = $account->email;
                    $user['phone'] = $this->request->getPost('phone', array('striptags', 'trim'), '');
                    $user['province'] = $this->request->getPost('province', array('striptags', 'trim'), '');
                    
                    $province = Location::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $user['province'])
                        ));
                    $user['province_title'] = $province->title;
                    
                    $district = Location::findFirst(array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => $account->district)
                    ));
                    $user['district'] = $district->id;
                    $user['district_title'] = $district->title;
                    $user['fee'] = $district->fee;
                    $user['address'] = $this->request->getPost('address', array('striptags', 'trim'), '');
                    $user['delivery_date'] = $this->request->getPost('delivery_date', array('striptags', 'trim'), '');
                    $user['payment'] = $this->request->getPost('payment', array('striptags', 'trim'), '');
                    $user['transport'] = $this->request->getPost('transport', array('striptags', 'trim'), '');
                    $this->session->set('CUSTOMER', $user);
                    $this->response->redirect(array('for' => 'cart_bill'));
                }
            } else {
                if ($other_address == 'on') {
                    if (!$form->isValid($this->request->getPost()) && !$form_r->isValid($this->request->getPost())) {
                        $this->flashSession->error('Thông tin không hợp lệ.');
                    } else {
                        if ($this->session->has('CUSTOMER')) {
                            $user = $this->session->get('CUSTOMER');
                        } else {
                            $user = array();
                        }
                        $user['name'] = $this->request->getPost('name', array('striptags', 'trim'), '');
                        $user['phone'] = $this->request->getPost('phone', array('striptags', 'trim'), '');
                        $user['province'] = $this->request->getPost('province', array('striptags', 'trim'), '');
                        $user['district'] = $this->request->getPost('district', array('striptags', 'trim'), '');
                        
                        $province = Location::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $user['province'])
                        ));
                        $user['province_title'] = $province->title;

                        $district = Location::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $user['district'])
                        ));
                        $user['district_title'] = $district->title;
                        $user['address'] = $this->request->getPost('address', array('striptags', 'trim'), '');
                        $user['delivery_date'] = $this->request->getPost('delivery_date', array('striptags', 'trim'), '');
                        $user['payment'] = $this->request->getPost('payment', array('striptags', 'trim'), '');
                        $user['transport'] = $this->request->getPost('transport', array('striptags', 'trim'), '');
                        $user['recipient_name'] = $this->request->getPost('recipient_name', array('striptags', 'trim'), '');
                        $user['recipient_phone'] = $this->request->getPost('recipient_phone', array('striptags', 'trim'), '');

                        $recipient_province = Location::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $this->request->getPost('recipient_province', array('striptags', 'trim'), ''))
                        ));

                        $user['recipient_province'] = $recipient_province->title;

                        $recipient_district = Location::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $this->request->getPost('recipient_district', array('striptags', 'trim'), ''))
                        ));

                        $user['recipient_district'] = $recipient_district->title;
                        $user['fee'] = $recipient_district->fee;
                        $user['recipient_address'] = $this->request->getPost('recipient_address', array('striptags', 'trim'), '');
                        $user['recipient_note'] = $this->request->getPost('recipient_note', array('striptags', 'trim'), '');
                        $this->session->set('CUSTOMER', $user);
                        $this->response->redirect(array('for' => 'cart_bill'));
                    }
                } else {
                    if (!$form->isValid($this->request->getPost())) {
                        $this->flashSession->error('Thông tin không hợp lệ.');
                    } else {
                        if ($this->session->has('CUSTOMER')) {
                            $user = $this->session->get('CUSTOMER');
                        } else {
                            $user = array();
                        }
                        $user['name'] = $this->request->getPost('name', array('striptags', 'trim'), '');
                        $user['phone'] = $this->request->getPost('phone', array('striptags', 'trim'), '');
                        $user['province'] = $this->request->getPost('province', array('striptags', 'trim'), '');
                        $user['district'] = $this->request->getPost('district', array('striptags', 'trim'), '');
                        $province = Location::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $user['province'])
                        ));
                        $user['province_title'] = $province->title;
                        
                        
                        $district = Location::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $user['district'])
                        ));
                        $user['district_title'] = $district->title;
                        $user['fee'] = $district->fee;
                        $user['address'] = $this->request->getPost('address', array('striptags', 'trim'), '');
                        $user['delivery_date'] = $this->request->getPost('delivery_date', array('striptags', 'trim'), '');
                        $user['payment'] = $this->request->getPost('payment', array('striptags', 'trim'), '');
                        $user['transport'] = $this->request->getPost('transport', array('striptags', 'trim'), '');
                        $this->session->set('CUSTOMER', $user);
                        $this->response->redirect(array('for' => 'cart_bill'));
                    }
                }
            }
        }

        $cart_component = new CartComponent();
        $cart_mini_layout = $cart_component->mini($this, self::$theme, array());

        $title_for_layout = 'Thông tin thanh toán đơn hàng';
        $description_for_layout = 'Thông tin thanh toán đơn hàng';
        $keywords_for_layout = 'Thông tin thanh toán đơn hàng';

        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'carts' => $carts,
            'image_url' => $image_url,
            'image_default_url' => $image_default_url,
            'cart_mini_layout' => $cart_mini_layout,
            'transport' => $transport,
            'payment' => $payment,
            'transports' => $transports,
            'payments' => $payments,
            'form' => $form,
            'form_r' => $form_r,
            'other_address' => isset($other_address) ? $other_address : '',
        ));

        $this->view->pick(parent::$theme . '/cart/info_delivery');
    }

    public function billAction()
    {
        if (!$this->session->has('CART')) {
            $this->flashSession->error('Bạn không có sản phẩm nào trong giỏ hàng!');
            $this->response->redirect(array('for' => 'cart'));
        }

        if (!$this->session->has('CUSTOMER')) {
            $this->flashSession->error('Vui lòng điền đầy đủ thông tin trước khi thanh toán!');
            $this->response->redirect(array('for' => 'cart_check_out'));
        }

        $image_url = $this->config->asset->home_image_url . $this->config->drive->channel_name . $this->config->asset->home_image_product_url;
        $image_default_url = $this->config->asset->home_image_article_url . $this->config->drive->channel_name;

        if ($this->request->isPost()) {
            $this->response->redirect(array('for' => 'cart_process'));
        }

        $cart_component = new CartComponent();
        $cart_bill_layout = $cart_component->bill($this, self::$theme, array());

        $title_for_layout = 'Thông tin thanh toán đơn hàng';
        $description_for_layout = 'Thông tin thanh toán đơn hàng';
        $keywords_for_layout = 'Thông tin thanh toán đơn hàng';

        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'image_url' => $image_url,
            'image_default_url' => $image_default_url,
            'cart_bill_layout' => $cart_bill_layout
        ));

        $this->view->pick(parent::$theme . '/cart/bill');
    }


    public function processAction()
    {

        if ($this->session->has('CART')) {
            $carts = $this->session->get('CART');
        } else {
            $this->flashSession->error('Bạn không có sản phẩm nào trong giỏ hàng!');
            $this->response->redirect(array('for' => 'cart'));
        }

        if ($this->session->has('CUSTOMER')) {
            $customer = $this->session->get('CUSTOMER');
        } else {
            $this->flashSession->error('Vui lòng điền đầy đủ thông tin trước khi thanh toán!');
            $this->response->redirect(array('for' => 'cart_check_out'));
        }
        $cart_component = new CartComponent();
        $cart_bill_layout = $cart_component->bill($this, self::$theme, array());
        $order_bill = $cart_component->orderBill($this, self::$theme, array());

        $user = User::findFirst(array(
            'conditions' => 'email = :email:',
            'bind' => array('email' => $customer['email'])
        ));
        try {
            if(!$user) {
                $genpass = Captcha::genpass();
                $user = new User(); 
                $user->email = $customer['email'];
                $user->name = Util::upperFirstLetters($customer['name']);
                $user->slug = Util::slug($customer['name']);
                $user->phone = Util::numberOnly($customer['phone']);
                $user->province = Util::numberOnly($customer['province']);
                $user->district = Util::numberOnly($customer['district']);
                $user->address = Util::niceWordsByChars($customer['address']);
                $user->password = md5($genpass);
                $user->created_at = time();
                $user->status = Constant::STATUS_ACTIVED;
                $user->create();
                $mailer = Mailer::instance(array(
                    'delivery' => $this->config->mailer_private->delivery,
                    'ssl' => $this->config->mailer_private->ssl,
                    'port' => $this->config->mailer_private->port,
                    'host' => $this->config->mailer_private->host,
                    'username' => $this->config->mailer_private->username,
                    'password' => $this->config->mailer_private->password
                ));

                $to = $user->email;
                //$to = 'vu@vinadesign.vn';
                $name = $user->name;

                $mailer->send(
                        array('vu@vinadesign.vn' => 'HoaTuoiDep.Com'), array($to => $name), array(), array(), 'Thông tin tài khoản ' . $name, file_get_contents(ROOT . '/app/home/view/email/user_registered.tpl'), array($to => array(
                        '{logo_image}' => $this->config->asset->home_image_url . 'logo_tvn_mail.png',
                        '{user_name}' => $user->name,
                        '{user_email}' => $user->email,
                        '{user_password}' => $genpass
                    ))
                );
            }
        } catch (Exception $e) {
            $this->logger->log('[HomeController][registerEmployerAction] ' . $e->getMessage(), Logger::ERROR);
            throw new Exception($e->getMessage());
        }

        $order = new Order();
        $order->user_id = $user->id;
        $total = 0;
        $total_amount = 0;
        foreach ($carts as $item) {
            $total += $item['quantity'];
            $total_amount += $item['total'];
        }

        $order->total = $total;
        $order->total_amount = $total_amount;
        $order->status = Constant::ORDER_STATUS_NEW;
        $order->payment_method = $customer['payment'];
        $order->payment_status = Constant::PAYMENT_STATUS_PAID;
        $order->delivery_method = $customer['transport'];
        $order->delivery_fee = $customer['fee'];
        $order->bill = $order_bill;
        $order->delivery_date = strtotime(str_replace('/', '-', $customer['delivery_date']));
        $order->created_at = time();
        $order->create(); 

        foreach ($carts as $item) {
            $order_detail = new OrderDetail();
            $order_detail->product_id = $item['id'];
            $order_detail->order_id = $order->id;
            $order_detail->price = $item['price'];
            $order_detail->quantity = $item['quantity'];
            $order_detail->create();
        }

        $order_user = new OrderUser();
        $order_user->user_id = $user->id;
        $order_user->order_id = $order->id;
        $order_user->create();

        $customer['order_id'] = $order->id;
        $this->session->set('CUSTOMER', $customer);
        
        $mailer = Mailer::instance(array(
            'delivery' => $this->config->mailer_private->delivery,
            'ssl' => $this->config->mailer_private->ssl,
            'port' => $this->config->mailer_private->port,
            'host' => $this->config->mailer_private->host,
            'username' => $this->config->mailer_private->username,
            'password' => $this->config->mailer_private->password
        ));

        $to = $user->email;
        //$to = 'vu@vinadesign.vn';
        $name = $user->name;
        $mailer->send(
                array('vu@vinadesign.vn' => 'HoaTuoiDep.Com'), array($to => $name), array(), array(), 'Đơn đặt hàng ' . $name, file_get_contents(ROOT . '/app/home/view/email/order_bill.tpl'), array($to => array(
                '{logo_image}' => $this->config->asset->home_image_url . 'logo_tvn_mail.png',
                '{order_bill}' => $order_bill
            ))
        ); 
        

        if (!$this->session->has('USER')) {
            $url = $this->url->get(array('for' => 'login_user_ajax'));
            $post = array(
                'email' => $user->email,
                'password' => $user->password 
            );
            $response = json_decode(Util::curlPost($url, $post), true);
            
            if ($response && isset($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
                $this->session->set('USER', $response['result']);
                $this->cookies->set('USER', serialize($response['result']), strtotime('+4 hours'));
            } 
        }
        
        
        $this->session->remove('CART');
        $this->response->redirect(array('for' => 'cart_complete'));
    }
    
    public function completeAction()
    {
        $title_for_layout = 'Thông tin thanh toán đơn hàng';
        $description_for_layout = 'Thông tin thanh toán đơn hàng';
        $keywords_for_layout = 'Thông tin thanh toán đơn hàng';
        
        

        if ($this->session->has('CUSTOMER')) {
            $customer = $this->session->get('CUSTOMER');
        } else {
            $this->flashSession->error('Bạn đã đặt hàng thành công trong hệ thống!');
            $this->response->redirect(array('for' => 'cart'));
        }
        
        
        $order = Order::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $customer['order_id'])
        ));
        
        $user = User::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $order->user_id)
        ));
        
        $transport = Transport::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $order->delivery_method)
        ));
        
        $payment = Transport::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $order->payment_method)
        ));
        
        $this->session->remove('CUSTOMER');
        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'order' => $order,
            'transport' => $transport,
            'payment' => $payment,
            'user' => $user
        ));
        
        $this->view->pick(parent::$theme . '/cart/complete');
    }
}

