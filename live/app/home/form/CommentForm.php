<?php
namespace ITECH\Home\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\PresenceOf;

class CommentForm extends Form
{
    /**
     * @author Vu.Tran
     */
    public function initialize($model, $options)
    {
        $description = new TextArea('description');
        $description->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập nội dung bình luận.'
            ))
        ));
        $description->setFilters(array('striptags', 'trim'));
        $this->add($description);
    }
}