<?php
namespace TVN\Admin\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Select;
use TVN\Datasource\Lib\Constant;
use TVN\Datasource\Repository\AdminRepository;

class AdminMoveEmployerForm extends Form
{
    /**
     * @author Cuong.Bui
     */
    public function initialize($model, $options)
    {
        $admin_repository = new AdminRepository();
        $admins = $admin_repository->getListByType(Constant::ADMIN_TYPE_EMPLOYER_CUSTOMER_CARE);

        $username_select = array();
        if ($admins && !empty($admins)) {
            foreach ($admins as $a) {
                $username_select[$a->username] = $a->username;
            }
        }

        if (isset($options['current_username'])) {
            unset($username_select[$options['current_username']]);
        }

        $admin_username = new Select('admin_username', $username_select);
        $this->add($admin_username);
    }
}