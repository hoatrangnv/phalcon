<?php
namespace ITECH\Admin\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\File;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use ITECH\Admin\Form\Validator\ExpireDate;
use ITECH\Datasource\Lib\Constant;
use ITECH\Datasource\Model\BannerZone;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Model\Article;

class BannerForm extends Form
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
            )),
            new StringLength(array(
                'min' => 3,
                'messageMinimum' => 'Tiêu đề phải lớn hơn hoặc bằng 3 ký tự.'
            ))
        ));
        // 
        $content = new TextArea('content');
        $content->setFilters(array('striptags', 'trim'));
        $this->add($content);
        // 
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);

        $url = new Text('url');
        $url->setFilters(array('striptags', 'trim'));
        $this->add($url);
        
        $expired_at = new Text('expired_at', array('value' => date('d-m-Y')));
        $expired_at->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập ngày hết hạn.'
            )),
            new ExpireDate(array(
                'message' => 'Ngày hết hạn không hợp lệ.'
            ))
        ));
        $expired_at->setFilters(array('striptags', 'trim'));
        $this->add($expired_at);
        
        $image = new File('image');
        $image->setFilters(array('striptags', 'trim'));
        $this->add($image);
        
        $banner_zone_id = new Select('banner_zone_id', BannerZone::find(), array(
            'using' => array('id', 'name')
        ));
        $this->add($banner_zone_id);
        
        $category_product_params = array(
            'useEmpty' => true,
            'emptyText' => 'Chọn liên kết',
            'emptyValue' => '',
            'using' => array('id', 'name', 'module')
        );
        
        $category_product = new Select('category_product', Category::find(array(
            'conditions' => 'module = :module:',
            'bind' => array('module' => Constant::MODULE_PRODUCTS)
        )), $category_product_params);
        $this->add($category_product);
        
        $category_product_ajax = new Select('category_product_ajax', Category::find(array(
            'conditions' => 'module = :module:',
            'bind' => array('module' => Constant::MODULE_PRODUCTS)
        )), $category_product_params);
        $this->add($category_product_ajax);
        
        $category_article_params = array(
            'useEmpty' => true,
            'emptyText' => 'Chọn liên kết',
            'emptyValue' => '',
            'using' => array('id', 'name', 'module')
        );
        
        $category_article = new Select('category_article', Category::find(array(
            'conditions' => 'module = :module:',
            'bind' => array('module' => Constant::MODULE_ARTICLES)
        )), $category_article_params);
        $this->add($category_article);
        
        $category_article_ajax = new Select('category_article_ajax', Category::find(array(
            'conditions' => 'module = :module:',
            'bind' => array('module' => Constant::MODULE_ARTICLES)
        )), $category_article_params);
        $this->add($category_article_ajax);
        
        $article_select = new Select('article_select', array(), array(
            'useEmpty' => true,
            'emptyText' => 'Chọn bài viết',
            'emptyValue' => '')
        );
        $this->add($article_select);
        
        $product_select = new Select('product_select', array(), array(
            'useEmpty' => true,
            'emptyText' => 'Chọn sản phẩm',
            'emptyValue' => '')
        );
        $this->add($product_select);
        
        $page_params = array(
            'useEmpty' => true,
            'emptyText' => 'Chọn liên kết tĩnh',
            'emptyValue' => '',
            'using' => array('id', 'title')
        );
        
        $page = new Select('page', Article::find(array(
            'conditions' => 'module = :module:',
            'bind' => array('module' => Constant::MODULE_PAGES)
        )), $page_params);
        $this->add($page);
        
        $status = new Select('status', Constant::statusSelect());
        $this->add($status);
    }
}