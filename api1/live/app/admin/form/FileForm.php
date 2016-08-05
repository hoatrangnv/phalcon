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

class FileForm extends Form
{
    /**
     * @author Vu.Tran
     */
    public function initialize($model, $options)
    {
        $title = new Text('title');
        $title->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập tiêu đề.'
            )),
            new StringLength(array(
                'min' => 3,
                'messageMinimum' => 'Tiêu đề phải lớn hơn hoặc bằng 3 ký tự.'
            ))
        ));
        $title->setFilters(array('striptags', 'trim'));
        $this->add($title);
        
        $file_name = new File('file_name');
        $file_name->setFilters(array('striptags', 'trim'));
        $this->add($file_name);
        
        $category = new Select('category', Category::find(array(
            'columns' => 'id, name',
            'conditions' => 'module = :module:',
            'bind' => array('module' => Constant::MODULE_FILES)
        )), array('using' => array('id', 'name')));
        $this->add($category);
    }
}