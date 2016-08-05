<?php
namespace ITECH\Admin\Component;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View\Simple as View;
use ITECH\Datasource\Model\Link;
use ITECH\Datasource\Lib\Constant;

class LinkComponent extends Component
{
    /**
     * @author Vu.Tran
     */
    public function sub($params, $sub_link_layout, $page, $q, $cache_name, $group_cache_name)
    {
        $sub_links = Link::find(array(
            'conditions' => 'group_id = :group_id: and parent_id = :parent_id:',
            'bind' => array(
                    'group_id' => $params['conditions']['group_id'],
                    'parent_id' => $params['conditions']['parent_id']
                ),
            'order' => 'ordering ASC'
        ));
        
        if (count($sub_links) > 0) {
            $sub_link_layout = '<ol class="dd-list">';
                foreach ($sub_links as $sub_item) {
                    $query = array(
                    'id' => $sub_item->id,
                    'page' => $page,
                    'q' => $q,
                    'cache_name' => $cache_name,
                    'group_cache_name' => $group_cache_name
                );
                $sub_link_layout .= '<li class="dd-item dd3-item" data-id="' . $sub_item->id . '">
                                        <div class="dd-handle dd3-handle"></div>
                                        <div class="dd3-content">
                                            ' . $sub_item->name . '
                                            <div class="visible-md visible-lg hidden-sm hidden-xs float-right">
                                                <a class="btn btn-squared btn-xs btn-default tooltips" data-original-title="Sửa" data-placement="top" href="' 
                                                    . $this->url->get(array('for' => 'link_edit', 'query' =>'?' . http_build_query($query))) . '">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a class="btn btn-squared btn-xs btn-default tooltips" data-original-title="Xóa" data-placement="top" href="' 
                                                    . $this->url->get(array('for' => 'link_delete', 'query' =>'?' . http_build_query($query))) . '">
                                                    <i class="fa fa-times fa fa-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    ';
                $params = array(
                    'conditions' => array(
                        'group_id' => $params['conditions']['group_id'],
                        'parent_id' => $sub_item->id
                    )
                );
                $sub_link_layout .= $this->sub($params, $sub_link_layout, $page, $q, $cache_name, $group_cache_name);
                $sub_link_layout .= '</li>';
            }
            $sub_link_layout .= '</ol>';
            
        }
        else {
            $sub_link_layout = '';
        }
        
        return $sub_link_layout;
    }
}
