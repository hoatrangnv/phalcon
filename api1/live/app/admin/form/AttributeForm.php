<?php
namespace ITECH\Admin\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use ITECH\Datasource\Model\AttributeGroup;
use ITECH\Datasource\Lib\Constant;

class AttributeForm extends Form
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
                'min' => 3,
                'messageMinimum' => 'Tiêu đề phải lớn hơn hoặc bằng 3 ký tự.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);
        
        
        $type = new Select('type', Constant::elementFormSelect());
        $this->add($type);
        
        $attribute_group_id = new Select('attribute_group_id', AttributeGroup::find(array(
            'columns' => 'id, name'
        )), array('using' => array('id', 'name')));
        $this->add($attribute_group_id);
        
        $status = new Select('status', Constant::statusSelect());
        $this->add($status);
    }
}