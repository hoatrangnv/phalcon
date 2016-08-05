<?php
namespace ITECH\Admin\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

class TagForm extends Form
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
                'min' => 1,
                'messageMinimum' => 'Tiêu đề phải lớn hơn hoặc bằng 1 ký tự.'
            ))
        ));
        $title->setFilters(array('striptags', 'trim'));
        $this->add($title);
        
        $meta_title = new TextArea('meta_title');
        $meta_title->setFilters(array('striptags', 'trim'));
        $this->add($meta_title);
        
        $meta_description = new TextArea('meta_description');
        $meta_description->setFilters(array('striptags', 'trim'));
        $this->add($meta_description);	
         	
        $meta_keyword = new TextArea('meta_keyword');
        $meta_keyword->setFilters(array('striptags', 'trim'));
        $this->add($meta_keyword);

        $tags_h2 = new TextArea('tags_h2');
        $tags_h2->setFilters(array('striptags', 'trim'));
        $this->add($tags_h2);

        $tags_h3 = new TextArea('tags_h3');
        $tags_h3->setFilters(array('striptags', 'trim'));
        $this->add($tags_h3);

        $tags_content_2 = new TextArea('tags_content_2');
        $tags_content_2->setFilters(array('striptags', 'trim'));
        $this->add($tags_content_2);

        $tags_content_3 = new TextArea('tags_content_3');
        $tags_content_3->setFilters(array('striptags', 'trim'));
        $this->add($tags_content_3);
    }
}