<?php
namespace ITECH\Admin\Controller;

use ITECH\Admin\Controller\BaseController;

class ErrorController extends BaseController
{
    public function error404Action()
    {
        $this->view->setMainView('error');

        $this->response->setStatusCode(404, 'Page not found.');
        $this->view->pick('error/error404');
    }

    public function errorAction($e)
    {
        $this->view->setMainView('error');

        $this->view->setVars(array(
            'message' => $e->getMessage()
        ));
        $this->view->pick('error/error');
    }
}