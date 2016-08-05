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

class ProductForm extends Form
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

        $intro = new TextArea('intro');
        $intro->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập giới thiệu.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Giới thiệu phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $intro->setFilters(array('striptags', 'trim'));
        $this->add($intro);
        
        $content = new TextArea('content');
        $content->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập giới thiệu.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Giới thiệu phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $content->setFilters(array('striptags', 'trim'));
        $this->add($content);
        
        $meta_title = new TextArea('meta_title');
        $meta_title->setFilters(array('striptags', 'trim'));
        $this->add($meta_title);
        
        $meta_description = new TextArea('meta_description');
        $meta_description->setFilters(array('striptags', 'trim'));
        $this->add($meta_description);	
         	
        $meta_keyword = new TextArea('meta_keyword');
        $meta_keyword->setFilters(array('striptags', 'trim'));
        $this->add($meta_keyword);
        
        $tags = new TextArea('tags');
        $tags->setFilters(array('striptags', 'trim'));
        $this->add($tags);       
        
        $type = new Select('type', Constant::productTypeSelect());
        $this->add($type);
        
        $status = new Select('status', Constant::statusArticleSelect());
        $this->add($status);
    }
}