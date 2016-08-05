<?php
namespace TVN\Auth\Controller;

use TVN\Auth\Controller\BaseController;
use TVN\Datasource\Repository\JobRepository;
use TVN\Datasource\Lib\Constant;

class ErrorController extends BaseController
{
    public static $theme;

    /**
     * @author Cuong.Bui
     */
    public function initialize()
    {
        $channel_id = $this->request->getQuery('channel_id');

        switch ($channel_id) {
            default:
            case 1:
                $this->view->setMainView('admin_error');
                self::$theme = 'admin';
                break;

            case 2:
                $this->view->setMainView('user_error');
                self::$theme = 'user';
                break;

            case 3:
                $this->view->setMainView('user_error');
                self::$theme = 'user';
                break;
        }
    }

    /**
     * @author Cuong.Bui
     */
    public function error404Action()
    {
        $this->response->setStatusCode(404, 'Page not found.');
        $this->view->pick(self::$theme . '/error/error404');
    }

    /**
     * @author Cuong.Bui
     */
    public function errorAction($e)
    {
        $this->view->setVars(array(
            'message' => $e->getMessage()
        ));
        $this->view->pick(self::$theme . '/error/error');
    }
}