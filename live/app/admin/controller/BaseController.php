<?php
namespace ITECH\Admin\Controller;

use Phalcon\Mvc\Controller;
use Phalcon\Exception;
use ITECH\Datasource\Model\Admin;
use ITECH\Datasource\Lib\Constant;
use ITECH\Datasource\Lib\Util;
use ITECH\Datasource\Lib\Upload;

class BaseController extends Controller
{
    /**
     * @author Vu.Tran
     */
    public function onConstruct()
    {

    }

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        $this->view->setMainView('layout');
    }

    /**
     * @author Vu.Tran
     */
    public function outputJSON($response)
    {
        $this->view->disable();

        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setJsonContent($response);
        $this->response->send();
    }

    /**
     * @author Cuong.Bui
     */
    public function authenticate()
    {
        if (!$this->session->has('USER')) {
            if (!$this->cookies->has('USER')) {
                $host = 'http://' . $this->request->getHttpHost();
                $request_uri = $host . $this->request->getServer('REQUEST_URI');
                $auth_process_url = $this->url->get(array('for' => 'auth_process'));

                if ($request_uri != $this->url->get(array('for' => 'home')) && $request_uri != $this->url->get(array('for' => 'login'))) {
                    $referral_url = $request_uri;
                } else {
                    $referral_url = $this->url->get(array('for' => 'home'));
                }

                $query = array(
                    'referral_url' => $referral_url,
                    'auth_process_url' => $auth_process_url
                );

                return $this->response->redirect(array('for' => 'login', 'query' => '?' . http_build_query($query)));
            } else {
                $user = unserialize($this->cookies->get('USER'));
                $this->session->set('USER', $user);
            }
        } else {
            $user = $this->session->get('USER');

            $cache_name = md5(serialize(array(
                'BaseController',
                'authenticate',
                Constant::ADMIN_TYPE_ADMIN,
                'findFirst',
                $user['id']
            )));

            $admin = $this->cache->get($cache_name);
            if (!$admin) {
                $admin = Admin::findFirst(array(
                    'conditions' => 'id = :id: AND type <> :inactived_type:',
                    'bind' => array(
                        'id' => $user['id'],
                        'inactived_type' => Constant::ADMIN_TYPE_INACTIVED
                    )
                ));

                if (!$admin) {
                    return $this->response->redirect(array('for' => 'logout'));
                }

                $session = array(
                    'id' => $admin->id,
                    'username' => $admin->username,
                    'email' => $admin->email,
                    'name' => $admin->name,
                    'type' => $admin->type
                );
                $this->session->set('USER', $session);
                $this->cookies->set('USER', serialize($session), strtotime('+4 hours'));
                $this->cache->save($cache_name, $admin);
            }

            if (!$this->cookies->has('USER')) {
                $this->cookies->set('USER', serialize($user), strtotime('+4 hours'));
            }
        }
    }

    /**
     * @author Cuong.Bui
     */
    public function allowRole($roles = array())
    {
        $user = $this->session->get('USER');

        if (!$user) {
            $this->authenticate();
        }

        if (!isset($user['type'])) {
            throw new Exception('Tài khoản này chưa được phân quyền.');
        }

        if ($roles && count($roles)) {
            if (!in_array($user['type'], $roles)) {
                throw new Exception('Tài khoản này không có quyền truy cập.');
            }
        }
    }

    public function uploadLocalImage($dir, $file_name, $scale_x, $resource)
    {
        $response = array();

        if (is_dir($dir)) {
            if (strlen($file_name) > 255 || $file_name == '') {
                $file_name = uniqid() . time();
            } else {
                $file_name = $file_name . '_' . uniqid() . time();
            }
            
            if ($resource && !empty($resource)) {
                $u = new Upload($resource);
                $u->allowed = array('image/*');
                $u->forbidden = array('application/*');
                $u->png_compression = 9;
                try {
                    if (!$u->uploaded) {
                        $response = array(
                            'status' => Constant::CODE_ERROR,
                            'message' => 'Lỗi, không thể upload.'
                        );
                    } else {
                        if ($u->file_is_image) {
                            if ($scale_x > 0) {
                                $u->image_resize = true;
                                $u->image_x = $scale_x;
                                $u->image_ratio_y = true;

                                if ($u->image_src_y > ($scale_x * 1.5)) {
                                    $u->image_y = $scale_x;
                                }
                            }

                            /*
                            $u->image_text = 'TimViecNhanh.com';
                            $u->image_text_position = 'BR';
                            $u->image_text_font = 1;
                            $u->image_text_color = '#000000';
                            $u->image_text_opacity = 30;
                            $u->image_text_background = '#FFFFFF';
                            $u->image_text_background_opacity = 0;
                            */

                            $u->file_new_name_body = $file_name;
                            $u->process($dir);

                            if ($u->processed) {
                                $file_name .= '.' . $u->file_src_name_ext;

                                $response = array(
                                    'status' => Constant::CODE_SUCCESS,
                                    'message' => 'Upload thành công.',
                                    'result' => $file_name
                                );
                            } else {
                                $response = array(
                                    'status' => Constant::CODE_ERROR,
                                    'message' => 'Lỗi, không thể xử lý hình ảnh.'
                                );
                            }
                        } else {
                            $response = array(
                                'status' => Constant::CODE_ERROR,
                                'message' => 'Lỗi, không đúng định dạng hình ảnh.'
                            );
                        }
 
                        $u->clean();
                    }
                } catch (Exception $e) {
                    $this->logger->log('[BaseController][uploadLocalImage] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception('Internal server error.');
                }
            }
        } else {
            $response = array(
                'status' => Constant::CODE_ERROR,
                'message' => 'Không tìm thấy thư mục hình ảnh.'
            );
        }

        return $response;
    }

    public function deleteLocalImage($dir, $file_name)
    {
        $response = array();

        if (is_dir($dir)) {
            if (file_exists($dir . $file_name)) {
                $file = $dir . $file_name;

                chmod($file, 0777);
                if (@unlink($file)) {
                    $response = array(
                        'status' => Constant::CODE_SUCCESS,
                        'message' => 'Xóa hình ảnh thành công.'
                    );
                } else {
                    $response = array(
                        'status' => Constant::CODE_ERROR,
                        'message' => 'Lỗi, không thể xóa hình ảnh.'
                    );
                }
            } else {
                $response = array(
                    'status' => Constant::CODE_ERROR,
                    'message' => 'Không tồn tại hình ảnh.'
                );
            }
        } else {
            $response = array(
                'status' => Constant::CODE_ERROR,
                'message' => 'Không tìm thấy thư mục hình ảnh.'
            );
        }

        return $response;
    }

    public function uploadRemoteImage($local_dir, $remote_folder, $file_name)
    {
        $response = array();

        if (is_dir($local_dir)) {
            $content = file_get_contents($local_dir . $file_name);

            $url = $this->config->cdn->upload_image_url;
            $post = array(
                'content' => $content,
                'folder' => $remote_folder,
                'filename' => $file_name

            );

            $r = Util::curlPost($url, $post);
            if (!empty($r['status']) && $r['status'] == Constant::CODE_SUCCESS) {
                $response = array(
                    'status' => Constant::CODE_SUCCESS,
                    'message' => 'Upload thành công.'
                );
            } else {
                $response = array(
                    'status' => Constant::CODE_ERROR,
                    'message' => 'Lỗi, không thể upload.'
                );
            }
        } else {
            $response = array(
                'status' => Constant::CODE_ERROR,
                'message' => 'Không tìm thấy thư mục hình ảnh.'
            );
        }
        return $response;
    }

    public function deleteRemoteImage($remote_folder, $file_name)
    {
        $response = array();

        $url = $this->config->cdn->delete_image_url;
        $channel_name = $this->config->drive->channel_name;
        $get = array(
            'folder' => $remote_folder,
            'filename' => $file_name,
            'channel_name' => $channel_name
        );

        $r = Util::curlGet($url, $get);
        if (!empty($r['status']) && $r['status'] == Constant::CODE_SUCCESS) {
            $response = array(
                'status' => Constant::CODE_SUCCESS,
                'message' => 'Xóa thành công.'
            );
        } else {
            $response = array(
                'status' => Constant::CODE_ERROR,
                'message' => 'Lỗi, không thể xóa.'
            );
        }

        return $response;
    }
}