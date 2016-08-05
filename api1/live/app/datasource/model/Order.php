<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class Order extends BaseModel
{
    public $id;	
    public $user_id;			
    public $note;	
    public $total;
    public $total_amount;	
    public $status;	
    public $status_note;	
    public $payment_method;	
    public $payment_status;	
    public $delivery_method;
    public $delivery_fee;
    public $bill;
    public $delivery_date;
    public $created_at;
    public $processed_at;
    public $updated_at;		
    public $created_by;		
    public $updated_by;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('order');
    }
}