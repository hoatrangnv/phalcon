<?php
namespace ITECH\Admin\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\File;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

class GalleryForm extends Form
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
        
        $description = new TextArea('description');
        $description->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập mô tả về hình ảnh.'
            )),
            new StringLength(array(
                'min' => 3,
                'messageMinimum' => 'Mô tả phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $description->setFilters(array('striptags', 'trim'));
        $this->add($description);
        
        $alternative = new Text('alternative');
        $alternative->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập alt cho hình ảnh.'
            )),
            new StringLength(array(
                'min' => 3,
                'messageMinimum' => 'Alt đề phải lớn hơn hoặc bằng 3 ký tự.'
            ))
        ));
        $alternative->setFilters(array('striptags', 'trim'));
        $this->add($alternative);

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