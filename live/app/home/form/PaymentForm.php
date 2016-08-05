<?php
namespace ITECH\Home\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Regex;
use ITECH\Datasource\Model\Payment;
use ITECH\Datasource\Model\Transport;
use ITECH\Datasource\Lib\Constant;

class PaymentForm extends Form
{
    /**
     * @author Vu.Tran
     */
    public function initialize($model, $options)
    {        
        $payment = new Select('payment', Payment::find(array(
            'columns' => 'id, title',
            'order' => 'ordering ASC'
        )), array('using' => array('id', 'title')));
        $this->add($payment);
        
        $transport = new Select('transport', Transport::find(array(
            'columns' => 'id, title',
            'order' => 'ordering ASC'
        )), array('using' => array('id', 'title')));
        $this->add($transport);    
    }
}