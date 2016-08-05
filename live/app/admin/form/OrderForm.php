<?php
namespace ITECH\Admin\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

class OrderForm extends Form
{
    /**
     * @author Vu.Tran
     */
    public function initialize($model, $options)
    {
        $meta_title = new Text('meta_title');
        $meta_title->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập meta title.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Meta title phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $meta_title->setFilters(array('striptags', 'trim'));
        $this->add($meta_title);

        $meta_description = new TextArea('meta_description');
        $meta_description->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập meta description.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Meta description phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $meta_description->setFilters(array('striptags', 'trim'));
        $this->add($meta_description);

        $meta_keywords = new Text('meta_keywords');
        $meta_keywords->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập meta keywords.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Meta keywords phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $meta_keywords->setFilters(array('striptags', 'trim'));
        $this->add($meta_keywords);
    }
}