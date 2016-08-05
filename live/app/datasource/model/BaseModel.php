<?php
namespace ITECH\Datasource\Model;

use Phalcon\Mvc\Model;

class BaseModel extends Model
{
    /**
     * @author Cuong.Bui
     */
    public function initialize()
    {
        $this->setWriteConnectionService('db');
        $this->setReadConnectionService('db_slave');
    }
}