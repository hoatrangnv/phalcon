<?php
namespace ITECH\Admin\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Lib\Constant;

class CategoryForm extends Form
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
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);

        $description = new TextArea('description');
        $description->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập mô tả.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Mô tả phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $description->setFilters(array('striptags', 'trim'));
        $this->add($description);
            
        $parent_id = new Select('parent_id', Category::find(array(
            'columns' => 'id, name',
            'conditions' => 'module = :module:',
            'bind' => array('module' => $model->module)
        )), array('using' => array('id', 'name')));
        $this->add($parent_id);

        $status = new Select('status', Constant::statusSelect());
        $this->add($status);

        $title_box = new TextArea('title_box');
        $title_box->setFilters(array('striptags', 'trim'));
        $this->add($title_box);
        
        $category_h2 = new TextArea('category_h2');
        $category_h2->setFilters(array('striptags', 'trim'));
        $this->add($category_h2);

        $category_h3 = new TextArea('category_h3');
        $category_h3->setFilters(array('striptags', 'trim'));
        $this->add($category_h3);

        $category_content_2 = new TextArea('category_content_2');
        $category_content_2->setFilters(array('striptags', 'trim'));
        $this->add($category_content_2);

        $category_content_3 = new TextArea('category_content_3');
        $category_content_3->setFilters(array('striptags', 'trim'));
        $this->add($category_content_3);

    }
}