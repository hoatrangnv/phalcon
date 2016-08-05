<?php
namespace ITECH\Admin\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\File;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Lib\Constant;

class BannerZoneForm extends Form
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
        
        $width = new Text('width', array('placeholder' => 'Chỉ nhập số'));
        $width->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập chiều rộng.'
            )),
            new Regex(array(
                'message' => 'Chiều rộng chỉ nhập.',
                'pattern' => '/^[[:alnum:]]+$/'    
            ))
        ));
        $width->setFilters(array('striptags', 'trim'));
        $this->add($width);
        
        $height = new Text('height', array('placeholder' => 'Chỉ nhập số'));
        $height->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập chiều cao.'
            )),
            new Regex(array(
                'message' => 'Chiều cao chỉ nhập số.',
                'pattern' => '/^[[:alnum:]]+$/'    
            ))
        ));
        $height->setFilters(array('striptags', 'trim'));
        $this->add($height); 
        
        $status = new Select('status', Constant::statusSelect());
        $this->add($status);
    }
}