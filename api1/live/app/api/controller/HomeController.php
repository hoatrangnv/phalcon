<?php
namespace ITECH\Api\Controller;

use Phalcon\Mvc\View;

class HomeController extends BaseController
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
    public function apcOpcacheClearAction()
    {
        apc_clear_cache('opcode');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    /**
     * @author Vu.Tran
     */
    public function apcUsercacheClearAction()
    {
        apc_clear_cache('user');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
}