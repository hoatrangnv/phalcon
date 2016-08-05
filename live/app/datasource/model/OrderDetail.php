<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class OrderDetail extends BaseModel
{
    public $product_id;
    public $order_id;	
    public $price;	
    public $quantity;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('order_detail');
    }
}