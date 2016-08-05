<?php
namespace ITECH\Home\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Regex;
use ITECH\Datasource\Model\Location;
use ITECH\Datasource\Lib\Constant;

class RecipientForm extends Form
{
    /**
     * @author Vu.Tran
     */
    public function initialize($model, $options)
    {
        $recipient_name = new Text('recipient_name');
        $recipient_name->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập họ tên người nhận.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Tiêu đề phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $recipient_name->setFilters(array('striptags', 'trim'));
        $this->add($recipient_name);
        
        $recipient_phone = new Text('recipient_phone');
        $recipient_phone->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập số điện thoại người nhận.'
            )),
            new Regex(array(
                'message' => 'Số điện thoại người nhận chỉ nhập số.',
                'pattern' => '/^[[:alnum:]]+$/'    
            ))
        ));
        $recipient_phone->setFilters(array('striptags', 'trim'));
        $this->add($recipient_phone);
        
        $recipient_province_params = array(
            'useEmpty' => true,
            'emptyText' => 'Chọn Tỉnh/Thành phố',
            'emptyValue' => '',
            'using' => array('id', 'title')
        );
        $recipient_province = new Select('recipient_province', Location::find(array(
            'columns' => 'id, title',
            'conditions' => 'parent = :parent:',
            'bind' => array('parent' => 0),
            'order' => 'ordering ASC'
        )), $recipient_province_params);
        $recipient_province->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu chọn Tỉnh/Thành phố.'
            ))
        ));
        $this->add($recipient_province);
        
        $recipient_district_params = array(
            'useEmpty' => true,
            'emptyText' => 'Chọn Quận/Huyện',
            'emptyValue' => '',
            'using' => array('id', 'title')
        );
        $recipient_district = new Select('recipient_district', array(), $recipient_district_params);
        $recipient_district->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu chọn Quận/Huyện.'
            ))
        ));
        $this->add($recipient_district);
        
        $recipient_address = new Text('recipient_address');
        $recipient_address->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập địa chỉ.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Địa chỉ phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $recipient_address->setFilters(array('striptags', 'trim'));
        $this->add($recipient_address); 
        
        $recipient_note = new Textarea('recipient_note');
        $recipient_note->setFilters(array('striptags', 'trim'));
        $this->add($recipient_note);

    }
}