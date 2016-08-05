<?php
namespace ITECH\Admin\Controller;

use Phalcon\Mvc\View;
use ITECH\Admin\Controller\BaseController;

class HomeController extends BaseController
{
    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        parent::authenticate();
    }

    /**
     * @author Vu.Tran
     */
    public function indexAction()
    {
        $page_header = 'Dashboard';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs
        ));
        $this->view->pick('home/index');
    }

    /**
     * @author Cuong.Bui
     */
    public function apcOpcacheClearAction()
    {
        apc_clear_cache('opcode');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    /**
     * @author Cuong.Bui
     */
    public function apcUsercacheClearAction()
    {
        apc_clear_cache('user');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
}