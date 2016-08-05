<?php
namespace ITECH\Home\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Regex;
use ITECH\Datasource\Model\Location;
use ITECH\Datasource\Model\Payment;
use ITECH\Datasource\Model\Transport;
use ITECH\Datasource\Lib\Constant;

class DeliveryForm extends Form
{
    /**
     * @author Vu.Tran
     */
    public function initialize($model, $options)
    {
        if (isset($model->name)) {
            $array = array(
                'readonly' => 'readonly'
            );
        } else {
            $array = array();
        }
        $name = new Text('name', $array);
        $name->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập họ tên.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Tiêu đề phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);

        if (isset($model->phone)) {
            $array = array(
                'readonly' => 'readonly'
            );
        } else {
            $array = array();
        }
        $phone = new Text('phone', $array);
        $phone->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập số điện thoại.'
            )),
            new Regex(array(
                'message' => 'Số điện thoại chỉ nhập số.',
                'pattern' => '/^[[:alnum:]]+$/'
            ))
        ));
        $phone->setFilters(array('striptags', 'trim'));
        $this->add($phone);

        if (isset($model->province)) {
            $province_params = array(
                'readonly' => 'readonly',
                'using' => array('id', 'title')
            );
            $province = new Select('province', Location::find(array(
                'columns' => 'id, title',
                'conditions' => 'id = :id:',
                'bind' => array(
                    'id' => $model->province
                    ),
                'order' => 'ordering ASC'
            )), $province_params);
            $province->addValidators(array(
                new PresenceOf(array(
                    'message' => 'Yêu cầu chọn Tỉnh/Thành phố.'
                ))
            ));
            $this->add($province);

        } else {
            $province_params = array(
                'useEmpty' => true,
                'emptyText' => 'Chọn Tỉnh/Thành phố',
                'emptyValue' => '',
                'using' => array('id', 'title')
            );
            $province = new Select('province', Location::find(array(
                'columns' => 'id, title',
                'conditions' => 'parent = :parent:',
                'bind' => array('parent' => 0),
                'order' => 'ordering ASC'
            )), $province_params);
            $province->addValidators(array(
                new PresenceOf(array(
                    'message' => 'Yêu cầu chọn Tỉnh/Thành phố.'
                ))
            ));
            $this->add($province);
        }

        if (isset($model->district)) {
            $district_params = array(
                'readonly' => 'readonly',
                'using' => array('id', 'title')
            );
            $district = new Select('district', Location::find(array(
                'columns' => 'id, title',
                'conditions' => 'id = :id:',
                'bind' => array(
                    'id' => $model->district
                    ),
                'order' => 'ordering ASC'
            )), $district_params);
            $district->addValidators(array(
                new PresenceOf(array(
                    'message' => 'Yêu cầu chọn Quận/Huyện.'
                ))
            ));
            $this->add($district);
        } else {
            $district_params = array(
                'useEmpty' => true,
                'emptyText' => 'Chọn Quận/Huyện',
                'emptyValue' => '',
                'using' => array('id', 'title')
            );
            $district = new Select('district', array(), $district_params);
            $district->addValidators(array(
                new PresenceOf(array(
                    'message' => 'Yêu cầu chọn Quận/Huyện.'
                ))
            ));
            $this->add($district);
        }

        if (isset($model->address)) {
            $array = array(
                'readonly' => 'readonly'
            );
        } else {
            $array = array();
        }
        $address = new Text('address', $array);
        $address->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập địa chỉ.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Địa chỉ phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));

        $address->setFilters(array('striptags', 'trim'));
        $this->add($address);
        
        $delivery_date = new Text('delivery_date');
        $delivery_date->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu chọn ngày giao hàng.'
            ))
        ));

        $delivery_date->setFilters(array('striptags', 'trim'));
        $this->add($delivery_date);
        
    }
}
