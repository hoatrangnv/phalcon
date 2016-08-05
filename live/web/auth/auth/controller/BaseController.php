<?php
namespace TVN\Auth\Controller;

use Phalcon\Mvc\Controller;

class BaseController extends Controller
{
    /**
     * @author Cuong.Bui
     */
    public function onConstruct()
    {

    }

    /**
     * @author Cuong.Bui
     */
    public function initialize()
    {
        $this->view->setMainView('admin');

        $title_for_layout = 'Đăng nhập - TimViecNhanh.com';

        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout
        ));
    }

    /**
     * @author Cuong.Bui
     */
    public function outputJSON($response)
    {
        $this->view->disable();

        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setJsonContent($response);
        $this->response->send();
    }
}