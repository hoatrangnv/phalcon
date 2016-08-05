<?php
namespace ITECH\Admin\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use ITECH\Datasource\Lib\Constant;
use ITECH\Datasource\Model\LinkGroup;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Model\Article;

class LinkForm extends Form
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
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);

        $url = new Text('url');
        $url->setFilters(array('striptags', 'trim'));
        $this->add($url); 

        $target = new Select('target', Constant::targetSelect());
        $this->add($target);
        
        $group_id = new Select('group_id', LinkGroup::find(), array(
            'using' => array('id', 'name')
        ));
        $this->add($group_id);
        
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
    }
}