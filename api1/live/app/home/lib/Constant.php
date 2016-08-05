<?php
namespace TVN\Home\Lib;

class Constant
{
    /**
     * @author Cuong.Bui
     */
    public static function navigationBar()
    {
        return array(
            'job' => array(
                'title' => 'Người tìm việc',
                'controller' => 'job',
                'action' => 'index',
                'icon_class' => 'icon icon-sm icon-nguoi-tim-viec'
            ),
            'resume' => array(
                'title' => 'Nhà tuyển dụng',
                'controller' => 'resume',
                'action' => 'index',
                'icon_class' => 'icon icon-sm icon-nha-tuyen-dung'
            ),
            'job_province' => array(
                'title' => 'Việc làm theo tỉnh thành',
                'controller' => 'job',
                'action' => 'province',
                'icon_class' => 'icon icon-sm icon-viec-lam-theo-tinh-thanh'
            )
        );
    }
}