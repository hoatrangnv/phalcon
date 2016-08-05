<?php
namespace ITECH\Home\Controller;

use Phalcon\Exception;
use Phalcon\Mvc\View;
use ITECH\Home\Controller\BaseController;
use ITECH\Datasource\Model\ArticleContent;
use ITECH\Datasource\Model\User;
use ITECH\Datasource\Model\Admin;
use ITECH\Home\Form\ForgetPasswordForm;
use ITECH\Home\Form\ForgetPasswordResetForm;
use ITECH\Home\Form\RegisterForm;
use ITECH\Home\Form\ContactForm;
use ITECH\Home\Form\LoginForm;
use ITECH\Home\Component\ArticleComponent;
use ITECH\Home\Component\ProductComponent;
use ITECH\Datasource\Lib\Constant;
use ITECH\Datasource\Lib\Util;
use ITECH\Datasource\Lib\Mailer;
use ITECH\Datasource\Model\AuthToken;
use ITECH\Home\Lib\Config as LocalConfig;
use ITECH\Home\Component\MuabannhanhComponent;

class HomeController extends BaseController
{
    private $limitByCategory = array(170,342,346); /* Id hien thi */

        public function initialize()
    {
        parent::initialize();
    }
        public function apcOpcacheClearAction()
    {
        apc_clear_cache('opcode');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

        public function apcUsercacheClearAction()
    {
        apc_clear_cache('user');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
    
    public function seo(){
        
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
        public function indexAction()
    {
        $article_component = new ArticleComponent();
        $product_component = new ProductComponent();
        $setting = LocalConfig::setting();
        $ads = isset($setting['ads']) ? $setting['ads'] : '';
        $tabs_for_layout = isset($setting['tabs']) ? $setting['tabs'] : '';
        $noi_bat_layout = isset($setting['noi_bat']) ? $setting['noi_bat'] : '';
        $tin_hot_layout = isset($setting['tin_hot']) ? $setting['tin_hot'] : '';
        $tieu_diem_layout = isset($setting['tieu_diem']) ? $setting['tieu_diem'] : '';
        $moi_nhat_layout = isset($setting['moi_nhat']) ? $setting['moi_nhat'] : '';
        $nhieu_nhat_layout = isset($setting['nhieu_nhat']) ? $setting['nhieu_nhat'] : '';
        $hien_thi_layout = isset($setting['hien_thi']) ? $setting['hien_thi'] : '';
        
        $params = array(
            'conditions' => array( 
                'type' => Constant::ARTICLES_HOT,
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_ARTICLES
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => $tin_hot_layout
        );
        
        $hot_layout = $article_component->hot($this, self::$theme, $params);
        
        $params = array(
            'conditions' => array( 
                'type' => Constant::ARTICLES_NEW,
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_ARTICLES
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => $noi_bat_layout
        );
        
        $fresh_layout = $article_component->fresh($this, self::$theme, $params);
        
        $params = array(
            'conditions' => array( 
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_ARTICLES
            ),
            'order' => 'ITECH\Datasource\Model\Article.hits DESC',
            'limit' => $moi_nhat_layout
        );
        
        $most_viewed_layout = $article_component->mostViewed($this, self::$theme, $params);

        $params = array(
            'conditions' => array( 
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_PRODUCTS
            ),
            'order' => 'ITECH\Datasource\Model\Article.hits DESC',
            'limit' => $hien_thi_layout
        );
        
        $most_viewed_box_layout = $product_component->mostViewed($this, self::$theme, $params, $type = 'box');
        
        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_ARTICLES,
                'status' => Constant::STATUS_ACTIVED,
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => $nhieu_nhat_layout
        );
        $most_new_layout = $article_component->mostNew($this, self::$theme, $params);
        
        $params = array(
            'conditions' => array( 
                'type' => Constant::ARTICLES_FOCUS,
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_ARTICLES
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => $tieu_diem_layout
        );
        
        $focus_layout = $article_component->focus($this, self::$theme, $params);

        $arr = explode(',', $tabs_for_layout);
        $cache_name = md5(serialize(array(
            'HomeController',
            'indexAction',
            'Category',
            'find',
            $arr
        )));

        $categories = $this->cache->get($cache_name);
        if (!$categories) {
            $categories = $this->modelsManager->createBuilder()
            ->from('ITECH\Datasource\Model\Category')
            ->inWhere('ITECH\Datasource\Model\Category.id', $arr)
            ->orderBy('ITECH\Datasource\Model\Category.ordering ASC')
            ->getQuery()
            ->execute();
            $this->cache->save($cache_name, $categories);
        }
        
        $cache_name = md5(serialize(array(
            'HomeController',
            'indexAction',
            'box_layout',
            $arr
        )));

        $box_layout = $this->cache->get($cache_name);
        if (!$box_layout) {
            $box_layout = array();
            foreach ($categories as $item) {
                $params = array(
                    'conditions' => array(
                        'category_id' => $item->id,
                        'module' => Constant::MODULE_ARTICLES,
                        'status' => Constant::STATUS_ACTIVED
                    ),
                    'order' => 'ITECH\Datasource\Model\Article.id DESC',
                    'limit' => $hien_thi_layout
                );

                $box_layout[$item->id] = $article_component->categoryHome($this, parent::$theme, $params);
            }
            $this->cache->save($cache_name, $box_layout);
            
        }

        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_PRODUCTS,
                'status' => Constant::STATUS_ACTIVED,
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => 5
        );
        $product_newer_layout = $product_component->newer($this, self::$theme, $params);

        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_PRODUCTS,
                'status' => Constant::STATUS_ACTIVED,
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => 4
        );
        $product_newer_box_layout = $product_component->newer($this, self::$theme, $params , $type = 'box');

	    $page_header = '';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '', 'url' => '');
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $muabanhanhComponent = new MuabannhanhComponent();
        $listPostApi = $muabanhanhComponent->getByElastic($this, parent::$theme,$this->limitByCategory);

        $this->view->setVars(array(
            'listPostApi' => $listPostApi,
            'breadcrumbs' => $breadcrumbs,
            'focus_layout' => $focus_layout,
            'hot_layout' => $hot_layout,
            'fresh_layout' => $fresh_layout,
            'product_newer_layout' => $product_newer_layout,
            'product_newer_box_layout' => $product_newer_box_layout,
            'most_viewed_box_layout' => $most_viewed_box_layout,
            'most_viewed_layout' => $most_viewed_layout,
            'most_new_layout' => $most_new_layout,
            'box_layout' => $box_layout,
            'categories' => $categories
        ));
        $this->view->pick(parent::$theme . '/home/index');
    }

    private function userLogin($email, $password)
    {
        $user = Admin::findFirst(array(
            'conditions' => 'email = :email: AND password = :password:',
            'bind' => array(
                'email' => $email,
                'password' => $password
            )
        )); 

        return $user;
    }

    private function userLoginFb($email,$tokenFacebook)
    {
        $user = Admin::findFirst(array(
            'conditions' => 'email = :email: AND tokenFacebook = :tokenFacebook:',
            'bind' => array(
                'email' => $email,
                'tokenFacebook' => $tokenFacebook
            )
        ));

        return $user;
    }

        public function registerMbnAction() {
        
        $response = array(
            'status' => Constant::CODE_SUCCESS,
            'message' => 'Success.',
            'results' => array()
        );
        
        $form = new RegisterForm();
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                if (!$form->isValid($this->request->getPost())) {
                    $response = array(
                        'status' => Constant::CODE_ERROR,
                        'message' => 'Thông tin không hợp lệ.',
                        'results' => array()
                    );
                    $phone = $form->getMessagesFor('phone');
                    $password = $form->getMessagesFor('password');
                    $name = $form->getMessagesFor('name');
                    $email = $form->getMessagesFor('email');
                    $confirm_password = $form->getMessagesFor('confirm_password');


                    if (isset($phone[0])) {
                        $response['results']['phone'] = $phone[0]->getMessage();
                    }

                    if (isset($password[0])) {
                        $response['results']['password'] = $password[0]->getMessage();
                    }
                    if (isset($name[0])) {
                        $response['results']['name'] = $name[0]->getMessage();
                    }

                    if (isset($email[0])) {
                        $response['results']['email'] = $email[0]->getMessage();
                    }

                    if (isset($confirm_password[0])) {
                        $response['results']['confirm_password'] = $confirm_password[0]->getMessage();
                    }

                } else {
                    $url = 'http://muabannhanh.com/api/user/register';
                    $post = array(
                        'phone' => $this->request->getPost('phone'),
                        'password' => $this->request->getPost('password'),
                        'confirm_password' => $this->request->getPost('confirm_password'),
                        'name' => $this->request->getPost('name'),
                        'email' => $this->request->getPost('email')
                    );

                    $response = json_decode(Util::curlPostJson($url, $post));
                    if (isset($response->status) &&  $response->status == Constant::CODE_SUCCESS) {
                        $response = array(
                            'status' => Constant::CODE_SUCCESS,
                            'message' => 'Đăng ký thành viên thành công.',
                            'results' => array()
                        );
                    } else {
                        $response = array(
                            'status' => Constant::CODE_ERROR,
                            'message' => 'Số điện thoại bạn đăng ký đã tồn tại.',
                            'results' => array()
                        );
                        $response['results']['name'] = '';
                        $response['results']['phone'] = '';
                        $response['results']['password'] = '';
                        $response['results']['confirm_password'] = '';
                        $response['results']['email'] = '';
                    }
                }
            }
        }
        
        parent::outputJSON($response);
    }

    /**
    * @author Vu.Tran
    */
    public function loginMbnAction()
    {
        $response_n = array(
            'status' => Constant::CODE_SUCCESS,
            'message' => 'Success.',
            'results' => array()
        );
        
        $form = new LoginForm();
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                if (!$form->isValid($this->request->getPost())) {
                    $response_n = array(
                        'status' => Constant::CODE_ERROR,
                        'message' => 'Thông tin không hợp lệ.',
                        'results' => array()
                    );
                    $phone = $form->getMessagesFor('phone');
                    $password = $form->getMessagesFor('password');


                    if (isset($phone[0])) {
                        $response_n['results']['phone'] = $phone[0]->getMessage();
                    }

                    if (isset($password[0])) {
                        $response_n['results']['password'] = $password[0]->getMessage();
                    }

                } else {
                    $url = 'http://muabannhanh.com/api/user/login';
                    $post = array(
                        'phone' => $this->request->getPost('phone'),
                        'password' => $this->request->getPost('password'),
                        'user_agent' => $this->request->getUserAgent(),
                        'ip' => $this->request->getClientAddress()
                    );
                    $r = json_decode(Util::curlPostJson($url, $post));
                    if(isset($r->status) &&  $r->status == Constant::CODE_SUCCESS) {
                        $this->session->set('USER', $r);
                        $response_n = array(
                            'status' => Constant::CODE_SUCCESS,
                            'message' => 'Success.',
                            'results' => array()
                        );

                        $response_n['results']['referral_url'] = $this->request->getPost('referral_url');
                    } else {
                        $response_n = array(
                            'status' => Constant::CODE_ERROR,
                            'message' => 'Lỗi đăng nhập. Số điện thoại hoặc mật khẩu chưa đúng.',
                            'results' => array()
                        );
                        $response_n['results']['phone'] = '';
                        $response_n['results']['password'] = '';
                    }
                }
            }
        }
        
        parent::outputJSON($response_n);
        $this->view->disable();
    }
    
        public function loginAction()
    {

        if ($this->request->isPost()) 
        {
            $tokenFacebook = $this->request->getPost('tokenfb');
            $email = $this->request->getPost('email');
            $userID = $this->request->getPost('userID');
            $membertype = $this->request->getPost('membertype');

            if($membertype == 'facebookuser'){
                if(empty($email))
                {
                    $email = $userID;
                }

                $seeker = self::userLoginFb($email,$tokenFacebook);
            }else {

                $email = $this->request->getPost('email');
                $password = md5($this->request->getPost('password'));
                $channel_id = 1;
                $token = $this->security->getToken();

                $auth_token = AuthToken::findFirst(array(
                    'conditions' => 'auth_channel_id = :channel_id: AND token = :token: AND status = :status:',
                    'bind' => array(
                        'channel_id' => $channel_id,
                        'token' => $token,
                        'status' => Constant::AUTH_TOKEN_STATUS_REQUEST
                    )
                ));

                $seeker = self::userLogin($email, $password);
            }
            if (!$seeker) {
                echo('Tài khoản đăng nhập không chính xác.');
            }
            else
            {

                $seeker->logined_at = date('Y-m-d H:i:s');
                $seeker->logined_ip = $this->request->getClientAddress();

                $this->db->begin();

                try {

                    $seeker->update();
                    $this->db->commit();

                    if (!$this->session->has('USER'))
                    {
                        $this->session->set('USER', $seeker);
                        echo 0;
                    }else {
                        echo 1;
                    }
                    
                } catch (Exception $e) {
                    $this->db->rollback();

                    $this->logger->log('[AuthController][loginAction] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception($e->getMessage());
                }
            }
            $title_for_layout_web =  'Blog.MuaBanNhanh.com';

            $this->view->setVars(array(
            'title_for_layout_web' => $title_for_layout_web
            ));
        }

        $this->view->disable();       
    }
    
        public function logoutAction()
    {
        $this->session->remove('USER');
        $cookie = $this->cookies->get('USER');
        $cookie->delete();
        return $this->response->redirect(array('for' => 'home'));
    }

    public function generateRandomString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function registerAction()
    {
        $email = $this->request->getPost('email');
        $token = $this->request->getPost('tokenfb');
        $userId = $this->request->getPost('userId');
        $membertype = $this->request->getPost('membertype');
        
        if($membertype == 'facebookuser')
		{
			$email = $userId;
		}

        if ($this->request->isPost()) {
            $user_check = Admin::findFirst(array(
                'conditions' => 'email = :email:',
                'bind' => array(
                    'email' => $email
                )
            ));

            if(!$user_check)
            {
            	if($membertype == 'webuser')
            	{
            		if(filter_var($email, FILTER_VALIDATE_EMAIL))
                	{
                        $username_array = explode('@', $email);
                        $username = $username_array[0];
                        $register = 1;
                	}
                	else
                	{
                		$notify = "Email không hợp lệ"; 
                	}

            	}
            	
        		if($membertype == 'facebookuser')
        		{
                    $username = $userId;
                    $register = 1;
        		}
            	
                if($register == 1):
                    $password = md5(self::generateRandomString(5));

                    $user = new Admin();
                    $user->username = $username;
                    $user->name = $username;
                    $user->password = md5($password);
                    $user->email = $email;
                    $user->type = Constant::ADMIN_TYPE_EDITOR;

                    if($token)
                    {
                        $user->tokenFacebook = $token;
                    }
                    else
                    {
                        $user->tokenFacebook ='thegioicongso!@#';
                    }

                    $user->created_at = date('Y-m-d H:i:s');
                    $user->updated_at = date('Y-m-d H:i:s');

                    try {
                        if (!$user->create()) {
                            echo 'Lỗi, không thể đăng ký.';
                            
                        } else {
                            
                            if(filter_var($email, FILTER_VALIDATE_EMAIL))
                            {
                                $mailer = Mailer::instance(array(
                                    'delivery' => $this->config->mailer_private->delivery,
                                    'ssl' => $this->config->mailer_private->ssl,
                                    'port' => $this->config->mailer_private->port,
                                    'host' => $this->config->mailer_private->host,
                                    'username' => $this->config->mailer_private->username,
                                    'password' => $this->config->mailer_private->password
                                ));

                                $to = $email;
                                $name = "Blog.MuaBanNhanh.com";

                                $mailer->send(
                                    array('quangbinh@vinadesign.vn' => 'Blog.MuaBanNhanh.com'),
                                    array($to => $name),
                                    array(),
                                    array(),
                                    'Thông báo đăng ký thành công tài khoản trên ' . $name,
                                    file_get_contents(ROOT . '/app/home/view/email/user_registered.tpl'),
                                    array($to => array(
                                        '{user_email}' => $email,
                                        '{user_password}' => $password
                                    ))
                                );
                            }

                            $seeker = Admin::findFirst(array(
                                            'conditions' => 'email = :email:',
                                            'bind' => array(
                                                'email' => $email
                                            )
                                        )); 
                            $this->session->set('USER', $seeker);
                            echo 0;
                        }
                    } catch (Exception $e) {
                        $this->logger->log('[HomeController][registerSeekerAction] ' . $e->getMessage(), Logger::ERROR);
                        throw new Exception('Internal server error.');
                    }
                else:
                    echo $notify;
                endif;
            
            }
            else
            {
                if($membertype == 'facebookuser')
                {
                    $seeker = Admin::findFirst(array(
                                    'conditions' => 'email = :email: AND tokenFacebook = :tokenFacebook:',
                                    'bind' => array(
                                        'email' => $email,
                                        'tokenFacebook' => $token
                                    )
                                )); 

                    $this->session->set('USER', $seeker);
                    echo 0;
                }
                else
                {
                    echo "Email đã tồn tại";
                }
                
            }
            
        }

        $this->view->disable();
    }

       public function registerAction1()
    {
        if ($this->session->has('USER')) {
            throw new Exception('Bạn đang ở trạng thái đăng nhập.');
        }
        
        $cache_name = md5(serialize(array(
            'HomeController',
            'registerAction',
            'ArticleContent',
            'findFirst',
            1
        )));

        $policy_detail = $this->cache->get($cache_name);
        if (!$policy_detail) {
            $policy_detail = ArticleContent::findFirst(array(
                'conditions' => 'article_id = :article_id:',
                'bind' => array('article_id' => 2082)
            ));
            $this->cache->save($cache_name, $policy_detail);
        }
        
        $user = new User();
        $form = new RegisterForm($user);

        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }

            $form->bind($this->request->getPost(), $user);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $user->name = Util::upperFirstLetters($this->request->getPost('name'));
                $user->slug = Util::slug($this->request->getPost('name'));
                $user->password = md5($this->request->getPost('password'));
                $user->status = Constant::USER_STATUS_ACTIVED;
                $user->mobile = Util::numberOnly($this->request->getPost('mobile'));
                $user->birthday = $this->request->getPost('year'). '-' . $this->request->getPost('month') . '-' . $this->request->getPost('day');
                $user->token = Util::token();
                $user->created_at = date('Y-m-d H:i:s');
                $user->updated_at = date('Y-m-d H:i:s');

                $user->is_premium = Constant::USER_IS_STANDARD;
                $user->is_mail_birthday_received = Constant::USER_IS_MAIL_BIRTHDAY_RECEIVED_NOT;
                $user->is_mail_notify_received = Constant::USER_IS_MAIL_NOTIFY_RECEIVED_NOT;
                $user->cover_status = Constant::COVER_STATUS_INACTIVED;

                try {
                    if (!$user->create()) {
                        $message = $user->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể đăng ký.');
                        }
                    } else {

                        $query = array(
                            'id' => $user->id,
                            'token' => $user->token
                        );
                        return $this->response->redirect(array('for' => 'register_user_done', 'query' => '?' . http_build_query($query)));
                    }
                } catch (Exception $e) {
                    $this->logger->log('[HomeController][registerSeekerAction] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception('Internal server error.');
                }
            }
        }

        $title_for_layout = 'Đăng ký người tìm việc';
                
        $description_for_layout = 'Tim viec lam nhanh 24H - Trang tìm việc làm nhanh tuyển dụng uy tín, Tìm kiếm việc làm Online nổi tiếng tại Việt Nam với hàng chục ngàn việc làm lương cao';
        $keywords_for_layout = 'tim viec, viec lam, tuyen dung, viec lam 24h, tuyển dụng, việc làm, tim viec nhanh';

        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'policy_detail' => $policy_detail,
            'form' => $form
        ));

        $this->view->pick(parent::$theme . '/home/register');
    }

        public function registerDoneAction()
    {
        if ($this->session->has('USER')) {
            throw new Exception('Bạn đang ở trạng thái đăng nhập.');
        }

        $id = $this->request->getQuery('id', array('int'), -1);
        $token = $this->request->getQuery('token', array('striptags', 'trim'), '');

        $user = User::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));
        if (!$user) {
            throw new Exception('Không tồn tại người tìm việc này.');
        }

        if ($user->status == Constant::USER_STATUS_ACTIVED && $user->token != $token) {
            throw new Exception('Token không hợp lệ.');
        }

        $user->status = Constant::USER_STATUS_ACTIVED;
        $user->token = Util::token();
        $user->updated_at = date('Y-m-d H:i:s');
        $user->update();

        $title_for_layout = 'Đăng ký người tìm việc thành công';
        $description_for_layout = 'Tim viec lam nhanh 24H - Trang tìm việc làm nhanh tuyển dụng uy tín, Tìm kiếm việc làm Online nổi tiếng tại Việt Nam với hàng chục ngàn việc làm lương cao';
        $keywords_for_layout = 'tim viec, viec lam, tuyen dung, viec lam 24h, tuyển dụng, việc làm, tim viec nhanh';

        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout
        ));
        $this->view->pick(parent::$theme . '/home/register_done');
    }

        public function forgetPasswordAction()
    {
        if ($this->session->has('USER')) {
            throw new Exception('Bạn đang ở trạng thái đăng nhập.');
        }

        $form = new ForgetPasswordForm();

        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }

            if (!$form->isValid($this->request->getPost())) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $seeker = Seeker::findFirst(array(
                    'conditions' => 'email = :email:',
                    'bind' => array('email' => $this->request->getPost('email'))
                ));

                if (!$seeker) {
                    $this->flashSession->error('Không tồn tại người tìm việc này.');
                } else {
                    $token = Util::token();

                    $seeker->status = Constant::SEEKER_STATUS_ACTIVED;
                    $seeker->token = $token;
                    $seeker->updated_at = date('Y-m-d H:i:s');

                    try {
                        if (!$seeker->update()) {
                            $message = $seeker->getMessages();
                            if (isset($message[0])) {
                                $this->flashSession->error($message[0]->getMessage());
                            } else {
                                $this->flashSession->error('Lỗi, không thể gửi mail.');
                            }
                        } else {
                            $mailer = Mailer::instance(array(
                                'delivery' => $this->config->mailer_private->delivery,
                                'ssl' => $this->config->mailer_private->ssl,
                                'port' => $this->config->mailer_private->port,
                                'host' => $this->config->mailer_private->host,
                                'username' => $this->config->mailer_private->username,
                                'password' => $this->config->mailer_private->password
                            ));

                            $to = $seeker->email;
                            //$to = 'phucuong44th1@gmail.com';
                            $name = $seeker->name;

                            $query = array(
                                'id' => $seeker->id,
                                'token' => $token
                            );
                            $reset_password_url = $this->url->get(array('for' => 'forget_password_seeker_reset', 'query' => '?' . http_build_query($query)));

                            $mailer->send(
                                array('lienhe@timviecnhanh.com' => 'TimViecNhanh.com'),
                                array($to => $name),
                                array(),
                                array(),
                                'Đổi mật khẩu tài khoản người tìm việc ' . $name,
                                file_get_contents(ROOT . '/app/home/view/email/seeker_reset_password.tpl'),
                                array($to => array(
                                    '{logo_image}' => $this->config->asset->home_image_url . 'logo_tvn_mail.png',
                                    '{seeker_name}' => $name,
                                    '{reset_password_url}' => $reset_password_url
                                ))
                            );

                            return $this->response->redirect(array('for' => 'reset_password_seeker_done'));
                        }
                    } catch (Exception $e) {
                        $this->logger->log('[HomeController][forgetPasswordSeekerAction] ' . $e->getMessage(), Logger::ERROR);
                        throw new Exception('Internal server error.');
                    }
                }
            }
        }

        $title_for_layout = 'Quên mật khẩu người tìm việc';
        $description_for_layout = 'Tim viec lam nhanh 24H - Trang tìm việc làm nhanh tuyển dụng uy tín, Tìm kiếm việc làm Online nổi tiếng tại Việt Nam với hàng chục ngàn việc làm lương cao';
        $keywords_for_layout = 'tim viec, viec lam, tuyen dung, viec lam 24h, tuyển dụng, việc làm, tim viec nhanh';

        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'form' => $form
        ));
        $this->view->pick(parent::$theme . '/home/forget_password');
    }

    /**
     * @author Cuong.Bui
     */
    public function forgetPasswordResetAction()
    {
        if ($this->session->has('USER')) {
            throw new Exception('Bạn đang ở trạng thái đăng nhập.');
        }

        $id = $this->request->getQuery('id', array('int'), -1);
        $token = $this->request->getQuery('token', array('striptags', 'trim'), '');

        $seeker = Seeker::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));
        if (!$seeker) {
            throw new Exception('Không tồn tại người tìm việc này.');
        }

        if ($seeker->status == Constant::SEEKER_STATUS_ACTIVED && $seeker->token != $token) {
            throw new Exception('Token không hợp lệ.');
        }

        $form = new ForgetPasswordResetForm();

        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }

            $form->bind($this->request->getPost(), $seeker);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $token = Util::token();

                $seeker->password = md5($this->request->getPost('password'));
                $seeker->status = Constant::SEEKER_STATUS_ACTIVED;
                $seeker->token = $token;
                $seeker->updated_at = date('Y-m-d H:i:s');

                try {
                    if (!$seeker->update()) {
                        $message = $seeker->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể đổi mật khẩu.');
                        }
                    } else {
                        $this->flashSession->success('Đổi mật khẩu thành công.');

                        $query = array(
                            'id' => $id,
                            'token' => $token
                        );
                        return $this->response->redirect(array('for' => 'forget_password_seeker_reset', 'query' => '?' . http_build_query($query)));
                    }
                } catch (Exception $e) {
                    $this->logger->log('[HomeController][forgetPasswordSeekerResetAction] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception('Internal server error.');
                }
            }
        }

        $title_for_layout = 'Đổi mật khẩu người tìm việc';
        $description_for_layout = 'Tim viec lam nhanh 24H - Trang tìm việc làm nhanh tuyển dụng uy tín, Tìm kiếm việc làm Online nổi tiếng tại Việt Nam với hàng chục ngàn việc làm lương cao';
        $keywords_for_layout = 'tim viec, viec lam, tuyen dung, viec lam 24h, tuyển dụng, việc làm, tim viec nhanh';

        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'form' => $form
        ));
        $this->view->pick(parent::$theme . '/home/forget_password_reset');
    }

    /**
     * @author Cuong.Bui
     */
    public function resetPasswordDoneAction()
    {
        if ($this->session->has('USER')) {
            throw new Exception('Bạn đang ở trạng thái đăng nhập.');
        }

        $title_for_layout = 'Hướng dẫn lấy lại mật khẩu';
        $description_for_layout = 'Tim viec lam nhanh 24H - Trang tìm việc làm nhanh tuyển dụng uy tín, Tìm kiếm việc làm Online nổi tiếng tại Việt Nam với hàng chục ngàn việc làm lương cao';
        $keywords_for_layout = 'tim viec, viec lam, tuyen dung, viec lam 24h, tuyển dụng, việc làm, tim viec nhanh';

        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
        ));
        $this->view->pick(parent::$theme . '/home/reset_password_seeker_done');
    }
    
        public function contactAction()
    {
        $form = new ContactForm();

        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            
            if (!$form->isValid($this->request->getPost())) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
               
                

                try {
                    $mailer = Mailer::instance(array(
                        'delivery' => $this->config->mailer_private->delivery,
                        'ssl' => $this->config->mailer_private->ssl,
                        'port' => $this->config->mailer_private->port,
                        'host' => $this->config->mailer_private->host,
                        'username' => $this->config->mailer_private->username,
                        'password' => $this->config->mailer_private->password
                    ));

                    $to = $this->request->getPost('email');
                    $name = Util::upperFirstLetters($this->request->getPost('name'));

                    $mailer->send(
                        array( 'sendemailvina@gmail.com' => 'Liên Hệ' ),
                        array($to => $name, 'vu@vinadesign.vn' => 'Vu Tran'),
                        array(),
                        array(),
                        $this->request->getPost('title') . ' - ' . $this->request->getPost('email'),
                        file_get_contents(ROOT . '/app/home/view/email/contact.tpl'),
                        array($to => array(
                            '{logo_image}' => $this->config->asset->home_image_url . 'logo_tvn.png',
                            '{name}' => Util::upperFirstLetters($this->request->getPost('name')),
                            '{email}' => $this->request->getPost('email'),
                            '{phone}' => Util::numberOnly($this->request->getPost('phone')),
                            '{title}' => $this->request->getPost('title'),
                            '{description}' => nl2br($this->request->getPost('description'))
                        ))
                    );

                    $this->flashSession->success('Gửi liên hệ thành công.');
                    return $this->response->redirect(array('for' => 'home_contact'));
                } catch (Exception $e) {
                    $this->logger->log('[HomeController][contactAction] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception('Internal server error.');
                }
            }
        }

        $page_header = 'Liên hệ';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Trang chủ', 'url' => $this->url->get(array('for' => 'home_contact')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');
        
        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'form' => $form
        ));
        $this->view->pick(parent::$theme . '/home/contact');
    }
    public function createSiteMapAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][createSiteMapAction]' . "\n";

        $category = new \ITECH\Datasource\Model\Category();
        $b = $category->getModelsManager()->createBuilder();
        $b->columns(array(
            'c.id',
            'c.parent_id',
            'c.name',
            'c.slug',
            'c.article_count'
        ));
        $b->from(array('c' => 'ITECH\Datasource\Model\Category'));
        $b->andWhere('c.parent_id <> 0');
        $b->andWhere('c.status = :status:', array('status' => \ITECH\Datasource\Lib\Constant::CATEGORY_STATUS_ACTIVED));
        $categories = $b->getQuery()->execute();

        $doc = new DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('sitemapindex');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        $doc->appendChild($root);

        $u_site_map = $doc->createElement('sitemap');
        $u_loc = $u_site_map->appendChild($doc->createElement('loc'));
        $u_loc_node = $doc->createTextNode($this->config->application->frontend_url . 'sitemap-user.xml');
        $u_loc->appendChild($u_loc_node);
        $doc->appendChild($u_site_map);

        $u_lastmod = $u_site_map->appendChild($doc->createElement('lastmod'));
        $u_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $u_lastmod->appendChild($u_last_mod_node);

        $doc->appendChild($u_site_map);
        $root->appendChild($u_site_map);

        $p_site_map = $doc->createElement('sitemap');
        $p_loc = $p_site_map->appendChild($doc->createElement('loc'));
        $p_loc_node = $doc->createTextNode($this->config->application->frontend_url . 'sitemap-province.xml');
        $p_loc->appendChild($p_loc_node);
        $doc->appendChild($p_site_map);

        $p_lastmod = $p_site_map->appendChild($doc->createElement('lastmod'));
        $p_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $p_lastmod->appendChild($p_last_mod_node);

        $doc->appendChild($p_site_map);
        $root->appendChild($p_site_map);

        $c_site_map = $doc->createElement('sitemap');
        $c_loc = $c_site_map->appendChild($doc->createElement('loc'));
        $c_loc_node = $doc->createTextNode($this->config->application->frontend_url . 'sitemap-category.xml');
        $c_loc->appendChild($c_loc_node);
        $doc->appendChild($c_site_map);

        $c_lastmod = $c_site_map->appendChild($doc->createElement('lastmod'));
        $c_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $c_lastmod->appendChild($c_last_mod_node);
        $doc->appendChild($c_site_map);
        $root->appendChild($c_site_map);

        $t_site_map = $doc->createElement('sitemap');
        $t_loc = $t_site_map->appendChild($doc->createElement('loc'));
        $t_loc_node = $doc->createTextNode($this->config->application->frontend_url . 'sitemap-tag.xml');
        $t_loc->appendChild($t_loc_node);
        $doc->appendChild($t_site_map);

        $t_lastmod = $t_site_map->appendChild($doc->createElement('lastmod'));
        $t_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $t_lastmod->appendChild($t_last_mod_node);
        $doc->appendChild($t_site_map);
        $root->appendChild($t_site_map);

        $d_site_map = $doc->createElement('sitemap');
        $d_loc = $d_site_map->appendChild($doc->createElement('loc'));
        $d_loc_node = $doc->createTextNode($this->config->application->frontend_url . 'sitemap-district.xml');
        $d_loc->appendChild($d_loc_node);
        $doc->appendChild($d_site_map);

        $d_lastmod = $d_site_map->appendChild($doc->createElement('lastmod'));
        $d_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $d_lastmod->appendChild($d_last_mod_node);
        $doc->appendChild($d_site_map);
        $root->appendChild($d_site_map);

        foreach ($categories as $item) {
            if ((int)$item->article_count > 0) {
                $site_map = $doc->createElement('sitemap');
                $loc = $site_map->appendChild($doc->createElement('loc'));
                $loc_node = $doc->createTextNode($this->config->application->frontend_url . 'sitemap-category-' . $item->slug . '.xml');
                $loc->appendChild($loc_node);
                $doc->appendChild($site_map);

                $lastmod = $site_map->appendChild($doc->createElement('lastmod'));
                $last_mod_node = $doc->createTextNode(date('Y-m-d'));
                $lastmod->appendChild($last_mod_node);

                $doc->appendChild($site_map);
                $root->appendChild($site_map);
            }
        }


        $file = ROOT . '/web/home/sitemap.xml';
        if (!$doc->save($file)) {
            die('Error, cannot create XML file');
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }
    public function categoryAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][categoryAction]' . "\n";

        $category = new \ITECH\Datasource\Model\Category();
        $b = $category->getModelsManager()->createBuilder();
        $b->columns(array(
            'c.id',
            'c.parent_id',
            'c.name',
            'c.slug',
            'c.article_count'
        ));
        $b->from(array('c' => 'ITECH\Datasource\Model\Category'));
        $b->andWhere('c.parent_id <> 0');
        $b->andWhere('c.status = :status:', array('status' => \ITECH\Datasource\Lib\Constant::CATEGORY_STATUS_ACTIVED));
        $categories = $b->getQuery()->execute();

        if ($categories && count($categories)) {
            foreach ($categories as $item) {
                $count = \ITECH\Datasource\Model\Article::count(array(
                    'conditions' => 'category_id = :category_id:
                        AND is_shown = :is_shown:
                        AND status = :status:',
                    'bind' => array(
                        'category_id' => $item->id,
                        'is_shown' => \ITECH\Datasource\Lib\Constant::ARTICLE_IS_SHOWN_YES,
                        'status' => \ITECH\Datasource\Lib\Constant::ARTICLE_STATUS_ACTIVE
                    )
                ));

                $q = 'UPDATE ITECH\Datasource\Model\Category
                    SET ITECH\Datasource\Model\Category.article_count = :article_count:
                    WHERE ITECH\Datasource\Model\Category.id = :id:';
                $b = $category->getModelsManager()->createQuery($q);
                $b->execute(array(
                    'article_count' => $count,
                    'id' => $item->id
                ));
            }

            $b = $category->getModelsManager()->createBuilder();
            $b->columns(array(
                'ITECH\Datasource\Model\Category.id',
                'ITECH\Datasource\Model\Category.parent_id',
                'ITECH\Datasource\Model\Category.article_count'
            ));
            $b->from('ITECH\Datasource\Model\Category');
            $b->andWhere('ITECH\Datasource\Model\Category.parent_id <> 0');
            $b->andWhere('ITECH\Datasource\Model\Category.status = :status:', array('status' => \ITECH\Datasource\Lib\Constant::CATEGORY_STATUS_ACTIVED));
            $categories = $b->getQuery()->execute();

            $b = $category->getModelsManager()->createBuilder();
            $b->columns(array('ITECH\Datasource\Model\Category.id'));
            $b->from('ITECH\Datasource\Model\Category');
            $b->andWhere('ITECH\Datasource\Model\Category.parent_id = 0');
            $b->andWhere('ITECH\Datasource\Model\Category.status = :status:', array('status' => \ITECH\Datasource\Lib\Constant::CATEGORY_STATUS_ACTIVED));
            $parent_categories = $b->getQuery()->execute();

            if ($parent_categories && count($parent_categories)) {
                foreach ($parent_categories as $item) {
                    $count = 0;

                    foreach ($categories as $cat) {
                        if ($cat->parent_id == $item->id) {
                            $count += $cat->article_count;
                        }
                    }

                    $q = 'UPDATE ITECH\Datasource\Model\Category
                        SET ITECH\Datasource\Model\Category.article_count = :article_count:
                        WHERE ITECH\Datasource\Model\Category.id = :id:';
                    $b = $category->getModelsManager()->createQuery($q);
                    $b->execute(array(
                        'article_count' => $count,
                        'id' => $item->id
                    ));
                }
            }
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }

}
