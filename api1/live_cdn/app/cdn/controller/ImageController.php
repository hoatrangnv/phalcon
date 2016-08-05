<?php
namespace ITECH\Cdn\Controller;

use ITECH\Cdn\Controller\BaseController;
use ITECH\Cdn\Lib\Constant;
use ITECH\Cdn\Lib\Util;

class ImageController extends BaseController
{
    /**
     * @author Cuong.Bui
     */
    public function deleteImageAction()
    {
        $response = array();

        $response['status'] = Constant::CODE_SUCCESS;
        $response['message'] = 'Success.';

        $folder = $this->request->getQuery('folder', array('striptags', 'trim'), '');
        $channel_name = $this->request->getQuery('channel_name', array('striptags', 'trim'), '');
        $filename = $this->request->getQuery('filename', array('striptags', 'trim'), '');

        if ($folder == '' || $filename == '') {
            $response['status'] = Constant::CODE_ERROR;
            $response['message'] = 'Parameter is invalid.';
        } else {
            switch ($folder) {
                case 'category':
                    $file_150 = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/category/' . $filename;
                    $file_250 = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/category/' . $filename;
                    $file_500 = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/category/' . $filename;
                    break;

                case 'articles':
                    $file_150 = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/articles/' . $filename;
                    $file_250 = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/articles/' . $filename;
                    $file_500 = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/articles/' . $filename;
                    break;
                case 'products':
                    $file_150 = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/products/' . $filename;
                    $file_250 = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/products/' . $filename;
                    $file_500 = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/products/' . $filename;
                    break;
                case 'pages':
                    $file_150 = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/pages/' . $filename;
                    $file_250 = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/pages/' . $filename;
                    $file_500 = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/pages/' . $filename;
                    break;
                case 'default':
                    $file_150 = ROOT . '/web/cdn/asset/home/img/150/' . $filename;
                    $file_250 = ROOT . '/web/cdn/asset/home/img/250/' . $filename;
                    $file_500 = ROOT . '/web/cdn/asset/home/img/500/' . $filename;
                    break;
            }

            if (isset($file_150) && isset($file_250) && isset($file_500)) {
                if (!file_exists($file_150) || !file_exists($file_250) || !file_exists($file_500)) {
                    $response['status'] = Constant::CODE_ERROR;
                    $response['message'] = 'File is not exsited.';
                } else {
                    if (!@unlink($file_150) || !@unlink($file_250) || !@unlink($file_500)) {
                        $response['status'] = Constant::CODE_ERROR;
                        $response['message'] = 'Error, cannot delete file.';
                    } else {
                        $response['status'] = Constant::CODE_SUCCESS;
                        $response['message'] = 'Delete file successfully.';
                    }
                }
            } else {
                $response['status'] = Constant::CODE_ERROR;
                $response['message'] = 'File is not exsited.';
            }
        }

        parent::outputJSON($response);
    }

    /**
     * @author Cuong.Bui
     */
    public function uploadImageAction()
    {
        $response = array();

        $response['status'] = Constant::CODE_SUCCESS;
        $response['message'] = 'Success.';

        if (!$this->request->isPost()) {
            $response['status'] = Constant::CODE_ERROR;
            $response['message'] = 'Invalid POST method.';
        } else {
            $content = $this->request->getPost('content');
            $folder = $this->request->getPost('folder', array('striptags', 'trim'), '');
            $filename = $this->request->getPost('filename', array('striptags', 'trim'), '');

            if ($content == '' || $folder == '' || $filename == '') {
                $response['status'] = Constant::CODE_ERROR;
                $response['message'] = 'Parameter is invalid.';
            } else {
                switch ($folder) {
                    case 'category':
                        $file = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/category/' . $filename;
                        break;
                    case 'articles':
                        $file = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/articles/' . $filename;
                        break;
                    case 'products':
                        $file = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/products/' . $filename;
                        break;
                    case 'banner':
                        $file = ROOT . '/web/cdn/asset/home/img/' . $channel_name . '/banner/' . $filename;
                        break;
                    case 'content':
                        $file = ROOT . '/web/cdn/asset/home/img/' . $filename;
                        break;
                    case 'default':
                        $file = ROOT . '/web/cdn/asset/home/img/' . $filename;
                        break;
                }

                if (isset($file)) {

                    $h = fopen($file, 'w');
                    if (!$h) {
                        $response['status'] = Constant::CODE_ERROR;
                        $response['message'] = 'Error, cannot create file.';
                    } else {
                        if (!fwrite($h, $content)) {
                            $response['status'] = Constant::CODE_ERROR;
                            $response['message'] = 'Error, cannot create file.';
                        } else {
                            if ($folder == 'default') {
                                parent::getUploadThumbnail($file, ROOT . '/web/cdn/asset/home/img/', $filename);
                                $response['status'] = Constant::CODE_SUCCESS;
	                            $response['message'] = 'Create file successfully.';
                                @unlink($file);
                                fclose($h);
                            } else {

                                if ($folder == 'content') {
                                    parent::getUploadContentThumbnail($file, ROOT . '/web/cdn/asset/home/img/', $filename);
                                    $response['status'] = Constant::CODE_SUCCESS;
                                    $response['message'] = 'Create file successfully.';
                                    @unlink($file);
                                    fclose($h);
                                }
                                else
                                {   
                                	parent::getUploadThumbnail($file, ROOT . '/web/cdn/asset/home/img/', $filename);
                                    $response['status'] = Constant::CODE_SUCCESS;
    	                            $response['message'] = 'Create file successfully.';
    	                            fclose($h);
                                }
                            }

                            


                        }
                    }
                } else {
                    $response['status'] = Constant::CODE_ERROR;
                    $response['message'] = 'Error, cannot create file.';
                }
            }
        }

        parent::outputJSON($response);
    }

    /**
     * @author Vu.Tran
     */
    public function listImageAction()
    {
        $response = array();
        $folder = $this->request->getPost('folder', array('striptags', 'trim'), '');
        $channel_name = $this->request->getPost('channel_name', array('striptags', 'trim'), '');
        if ($channel_name == '' ) {
            $response['status'] = Constant::CODE_ERROR;
            $response['message'] = 'Parameter is invalid.';
            parent::outputJSON($response);
        } else {
            $file = ROOT . '/web/cdn/asset/home/img/' . $channel_name;
            if (isset($file)) {
                if (!file_exists($file)) {
                    $response['status'] = Constant::CODE_ERROR;
                    $response['message'] = 'File is not exsited.';
                    parent::outputJSON($response);
                } else {
                    if ($folder == '') {
                        $path = $file . '/';
                    } else {
                        $path = $file . '/' . $folder . '/';
                    }

                    if (!file_exists($path)) {
                        $response['status'] = Constant::CODE_ERROR;
                        $response['message'] = 'File is not exsited.';
                        parent::outputJSON($response);
                    } else {
                        $response['folders'] = Util::dirArray($path);
                        $response['files'] = Util::fileArray($path);
                        parent::outputJSON($response);
                    }
                }
            }
        }
    }
}