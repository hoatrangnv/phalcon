<?php
namespace ITECH\Admin\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

class LinkGroupForm extends Form
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
        
        $ordering = new Text('ordering');
        $ordering->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập số thứ tự.'
            )),
            new Regex(array(
                'message' => 'Số thứ tự chỉ nhập.',
                'pattern' => '/^[[:alnum:]]+$/'    
            ))
        ));
        $ordering->setFilters(array('striptags', 'trim'));
        $this->add($ordering);
    }
}