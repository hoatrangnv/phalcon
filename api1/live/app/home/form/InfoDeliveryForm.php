<?php
namespace ITECH\Home\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Regex;
use ITECH\Datasource\Model\Location;
use ITECH\Datasource\Lib\Constant;

class InfoDeliveryForm extends Form
{
    /**
     * @author Vu.Tran
     */
    public function initialize($model, $options)
    {
        $name = new Text('name');
        $name->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập tiêu đề.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Tiêu đề phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);
        
        $phone = new Text('phone');
        $phone->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập số điện thoại.'
            )),
            new Regex(array(
                'message' => 'Số điện thoại chỉ nhập số.',
                'pattern' => '/^[[:alnum:]]+$/'    
            ))
        ));
        $phone->setFilters(array('striptags', 'trim'));
        $this->add($phone);
        
        $province = new Select('province', Location::find(array(
            'columns' => 'id, title',
            'conditions' => 'parent = :parent:',
            'bind' => array('parent' => 0),
            'order' => 'ordering ASC'
        )), array('using' => array('id', 'title')));
        $this->add($province);
        
        $address = new Text('address');
        $address->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập địa chỉ.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Địa chỉ phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $address->setFilters(array('striptags', 'trim'));
        $this->add($address);
    }
}