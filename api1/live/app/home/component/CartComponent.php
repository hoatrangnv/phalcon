<?php
namespace ITECH\Home\Component;

use Phalcon\Mvc\User\Component;
use ITECH\Datasource\Model\Location;
use Phalcon\Mvc\View\Simple as View;
use ITECH\Datasource\Repository\ArticleRepository;

class CartComponent extends Component
{
    /**
     * @author Vu.Tran
     */
    public function mini($controller, $theme, array $params)
    {
        $view = new View();
        
        if ($this->session->has('CART')) {
            $carts = $this->session->get('CART');
        } else {
            $carts = array();
        }
        
        if ($this->session->has('CUSTOMER')) {
            $user = $this->session->get('CUSTOMER');
        } else {
            $user = array();
        }
        
        $image_url = $this->config->asset->home_image_url . $this->config->drive->channel_name . $this->config->asset->home_image_product_url;
        $image_default_url = $this->config->asset->home_image_article_url . $this->config->drive->channel_name;
        
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/cart/');
        $view->render('mini', array(
            'carts' => $carts,
            'user' => $user,
            'image_url' => $image_url,
            'image_default_url' => $image_default_url
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function top($controller, $theme, array $params)
    {
        $view = new View();
        
        if ($this->session->has('CART')) {
            $carts = $this->session->get('CART');
        } else {
            $carts = array();
        }

        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/cart/');
        $view->render('top', array(
            'carts' => $carts
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function bill($controller, $theme, array $params)
    {
        $view = new View();
        
        if ($this->session->has('CART')) {
            $carts = $this->session->get('CART');
        } else {
            $carts = array();
        }
        
        if ($this->session->has('CUSTOMER')) {
            $user = $this->session->get('CUSTOMER');
        } else {
            $user = array();
        }

        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/cart/');
        $view->render('bill', array(
            'carts' => $carts,
            'user' => $user
        ));

        return $view->getContent();
    }
    
    /**
     * @author Vu.Tran
     */
    public function orderBill($controller, $theme, array $params)
    {
        $view = new View();
        
        if ($this->session->has('CART')) {
            $carts = $this->session->get('CART');
        } else {
            $carts = array();
        }
        
        if ($this->session->has('CUSTOMER')) {
            $user = $this->session->get('CUSTOMER');
        } else {
            $user = array();
        }

        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/component/cart/');
        $view->render('order_bill', array(
            'carts' => $carts,
            'user' => $user
        ));

        return $view->getContent();
    }
}
