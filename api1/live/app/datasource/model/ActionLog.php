<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class ActionLog extends BaseModel
{
    public $id;
    public $type;
    public $item_id;
    public $admin_username;
    public $created_at;

    /**
     * @author Cuong.Bui
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('action_log');

        $this->belongsTo('admin_username', 'TVN\Datasource\Model\Admin', 'username', array(
            'alias' => 'Admin',
            'foreignKey' => true
        ));
    }
}