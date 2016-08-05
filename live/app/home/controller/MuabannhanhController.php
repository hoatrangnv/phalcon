<?php
namespace ITECH\Home\Controller;

use Phalcon\Exception;
use Phalcon\Mvc\View;
use ITECH\Home\Controller\BaseController;
use ITECH\Home\Component\MuabannhanhComponent;
use ITECH\Home\Component\ArticleComponent;
use ITECH\Home\Component\LinkComponent;
use ITECH\Datasource\Lib\Constant;
use ITECH\Datasource\Lib\Util;
use ITECH\Home\Lib\Config as LocalConfig;

class MuabannhanhController extends BaseController
{

    private $limitByCategory = array(170,342,346); /* Id các chuyên m?c ???c ch?p nh?n hi?n th? */

    private $limitdm = array(170,342,346);

    public function initialize()
    {
        parent::initialize();
    }

    public function indexAction(){
        $setting = LocalConfig::setting();
        $tabs_for_layout = isset($setting['tabs']) ? $setting['tabs'] : '';
        $noi_bat_layout = isset($setting['noi_bat']) ? $setting['noi_bat'] : '';
        $moi_nhat_layout = isset($setting['moi_nhat']) ? $setting['moi_nhat'] : '';
        $nhieu_nhat_layout = isset($setting['nhieu_nhat']) ? $setting['nhieu_nhat'] : '';

        $muabanhanhComponent = new MuabannhanhComponent();
        $page = $this->request->getQuery('page', array('int'), 1);

        
        $title_for_layout = 'Shop '.Constant::SEO_WEB_SITE.' - chuyên mục mua bán nhanh của trang '.Constant::SEO_WEB_SITE.' trang '.$page;

        $description_for_layout = 'Shop '.Constant::SEO_WEB_SITE.' - chuyên mục mua bán nhanh của trang '.Constant::SEO_WEB_SITE.' - mua hàng giá rẻ, bán hàng tức thì trang '.$page;

        $keywords_for_layout = 'Shop '.Constant::SEO_WEB_SITE.', mua bán '.Constant::SEO_WEB_SITE.', giá '.Constant::SEO_WEB_SITE.', '.Constant::SEO_WEB_SITE.' giá rẻ';

        $article_component = new ArticleComponent();
        $arr = explode(',', $tabs_for_layout);
        $cache_name = md5(serialize(array(
            'HomeController',
            'indexAction',
            'Category',
            'find',
            $arr
        )));
        $categories = $this->cache->get($cache_name);
        if (!$categories) {
            $categories = $this->modelsManager->createBuilder()
            ->from('ITECH\Datasource\Model\Category')
            ->inWhere('ITECH\Datasource\Model\Category.id', $arr)
            ->orderBy('ITECH\Datasource\Model\Category.ordering ASC')
            ->getQuery()
            ->execute();
            $this->cache->save($cache_name, $categories);
        }

        $categories_detail = $this->cache->get($cache_name);
        if (!$categories_detail) {
            $categories_detail = $this->modelsManager->createBuilder()
            ->from('ITECH\Datasource\Model\Category')
            ->inWhere('ITECH\Datasource\Model\Category.id', $arr)
            ->orderBy('ITECH\Datasource\Model\Category.ordering ASC')
            ->getQuery()
            ->execute();
            $this->cache->save($cache_name, $categories_detail);
        }

        $cache_name = md5(serialize(array(
            'HomeController',
            'indexAction',
            'box_layout',
            $arr
        )));

        $box_layout = $this->cache->get($cache_name);
        if (!$box_layout) {
            $box_layout = array();
            foreach ($categories as $item) {
                $params = array(
                    'conditions' => array(
                        'category_id' => $item->id,
                        'module' => Constant::MODULE_ARTICLES,
                        'status' => Constant::STATUS_ACTIVED
                    ),
                    'order' => 'ITECH\Datasource\Model\Article.id DESC',
                    'limit' => $moi_nhat_layout
                );

                $box_layout[$item->id] = $article_component->categoryHome($this, parent::$theme, $params);
            }
            $this->cache->save($cache_name, $box_layout);
        }

        $params = array(
           'conditions' => array(
               'type' => Constant::ARTICLES_NEW,
               'status' => Constant::STATUS_ACTIVED,
               'module' => Constant::MODULE_ARTICLES
           ),
           'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
           'limit' => $noi_bat_layout
        );
        $fresh_layout = $article_component->fresh($this, self::$theme, $params);
        $category_fresh_layout = $article_component->categoryFresh($this, self::$theme, $params);

        $listPostApi = $muabanhanhComponent->getByElastic($this, parent::$theme,$this->limitByCategory);

        $this->view->setVars(array(
                'listPostApi' => $listPostApi,
                'box_layout' => $box_layout,
                'categories' => $categories,
                'fresh_layout' => $fresh_layout,
                'title_for_layout' => $title_for_layout,
                'description_for_layout' => $description_for_layout,
                'keywords_for_layout' => $keywords_for_layout,
                'categories_detail' => $categories_detail,
                'category_fresh_layout' => $category_fresh_layout
        ));
        $this->view->pick(parent::$theme . '/muabannhanh/index');
    }

    public function listAction(){
        $muabanhanhComponent = new MuabannhanhComponent();

        $id = $this->dispatcher->getParam('id', array('int'), 0);
        $slug = $this->dispatcher->getParam('slug', array('striptags', 'trim', 'lower'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        if($slug == 'inanquangcao'){
            $id = 170;
        }elseif($slug == 'thicongquangcao') {
            $id = 342;
        }elseif ($slug == 'mayinquangcao') {
            $id = 346;
        }
        $detailCategory = $muabanhanhComponent->getDetailCategory(array('id' => $id));
        // $mbnver = trim($detailCategory->slug);
        // $mbnver = str_replace('-','', $mbnver);
        //var_dump($mbnver);exit;
        // var_dump($detailCategory->slug);exit;
        if($slug == 'mayinquangcao'){

        }elseif (!$detailCategory || str_replace('-','', $detailCategory->slug) != $slug) {
            throw new Exception('Không tồn tại danh muc này !');
        }
        foreach ($this->limitdm as $key => $value) {
            if($detailCategory->id == $value){
                $limitapi = $detailCategory->id;
            }
        }
        if (!($detailCategory->id == $limitapi )) {
            throw new Exception('Không ton tai danh muc này !',2);
        }
        $title_for_layout = 'Shop '.$detailCategory->name . ' - Chuyên mục mua bán ' . $detailCategory->name .' của '.Constant::SEO_WEB_SITE.' - Trang '.$page;
        $description_for_layout = 'Shop '.$detailCategory->name . ' - Chuyên mục mua bán ' . $detailCategory->name . ' của '.Constant::SEO_WEB_SITE.' - mua hàng giá rẻ, bán hàng tức thì - Trang '.$page;
        $keywords_for_layout = $detailCategory->name;
        

        $this->view->setVars(array(
            'data' => $muabanhanhComponent->getByElasticCategory($this, parent::$theme, $id, $page),
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout
        ));
        $this->view->pick(parent::$theme . '/muabannhanh/category');
    }

    public function listtagAction(){
        $muabanhanhComponent = new MuabannhanhComponent();

        $id = $this->dispatcher->getParam('id', array('int'), 0);
        $slug = $this->dispatcher->getParam('slug', array('striptags', 'trim', 'lower'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $detailCategory = $muabanhanhComponent->getDetailCategory(array('id' => $id));
        

        $title_for_layout = 'Shop '.$detailCategory->name . ' - Chuyên mục mua bán ' . $detailCategory->name .' của '.Constant::SEO_WEB_SITE.' - Trang '.$page;
        $description_for_layout = 'Shop '.$detailCategory->name . ' - Chuyên mục mua bán ' . $detailCategory->name . ' của '.Constant::SEO_WEB_SITE.' - mua hàng giá rẻ, bán hàng tức thì - Trang '.$page;
        $keywords_for_layout = $detailCategory->name;
        

        $this->view->setVars(array(
            'data' => $muabanhanhComponent->getByElasticTag($this, parent::$theme, $id, $page),
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout
        ));
        $this->view->pick(parent::$theme . '/muabannhanh/category');
    }

    public function listByPhoneAction(){
        $phone = $this->dispatcher->getParam('phone', array('int'), 0);
        $page = $this->request->getQuery('page', array('int'), 1);
        $mbnComponent = new MuabannhanhComponent();

        $userDetail = $mbnComponent->getDetailUser(array('phone'=>$phone));
        if (!$userDetail) {
            $article = false;
            $pagination = false;
        } else {
            $userDetail = $userDetail->result;
            $params['user_id'] = $userDetail->id;
            $params['page'] = $page;
            $params['pagination'] = true;
            $article = $mbnComponent->getListPost($this, $params);
            $pagination = "";
            $argsPagination = array(
                'paged' => isset($params['page']) && $params['page'] ? $params['page'] : 1,
                'total_page' => $article['total_pages'],
                'phone' => $phone,
                'url' => $this->url->get(array(
                    'for' => 'mbn_list_by_phone',
                    'phone' => $phone)
                ));
            $pagination = $mbnComponent->pagination($this, parent::$theme, $argsPagination);

            $title_for_layout = 'Tin đăng bán hàng của ' . $userDetail->name . ' - '. $userDetail->phone .' - tại '.Constant::SEO_WEB_SITE.' - Trang ' .$page;
            $description_for_layout = 'Tin đăng bán hàng của ' . $userDetail->name . ' '.Constant::SEO_WEB_SITE .' - liên hệ '. $userDetail->phone . ' Thành viên Vip/Partner tại mạng xã hội MuaBanNhanh - Trang '.$page;
            $keywords_for_layout = $userDetail->name . ', ' . $userDetail->phone;
        }

        $this->view->setVars(array(
            'article' => $article,
            'user' => $userDetail,
            'pagination' => $pagination,
            'title_for_layout' => $title_for_layout,
                'description_for_layout' => $description_for_layout,
                'keywords_for_layout' => $keywords_for_layout,
        ));
        $this->view->pick(parent::$theme . '/muabannhanh/phone');
    }

    public function detailAction(){

        $setting = LocalConfig::setting();
        $ads = isset($setting['ads']) ? $setting['ads'] : '';
        $tabs_for_layout = isset($setting['tabs']) ? $setting['tabs'] : '';
        $noi_bat_layout = isset($setting['noi_bat']) ? $setting['noi_bat'] : '';
        $tin_hot_layout = isset($setting['tin_hot']) ? $setting['tin_hot'] : '';
        $tieu_diem_layout = isset($setting['tieu_diem']) ? $setting['tieu_diem'] : '';
        $moi_nhat_layout = isset($setting['moi_nhat']) ? $setting['moi_nhat'] : '';
        $nhieu_nhat_layout = isset($setting['nhieu_nhat']) ? $setting['nhieu_nhat'] : '';
        $moi_hon_layout = isset($setting['moi_hon']) ? $setting['moi_hon'] : '';
        $cu_hon_layout = isset($setting['cu_hon']) ? $setting['cu_hon'] : '';
        $hien_thi_layout = isset($setting['hien_thi']) ? $setting['hien_thi'] : '';

        $muabanhanhComponent = new MuabannhanhComponent();
        $id = $this->dispatcher->getParam('id', array('int'), 0);
        $slug = $this->dispatcher->getParam('slug', array('striptags', 'trim', 'lower'), '');
        $mbnver = $this->dispatcher->getParam('mbnver', array('striptags', 'trim', 'lower'), '');
        $article = $muabanhanhComponent->getDetailPost($this, array('id'=>$id));
        $data = $article['article'];
       if($mbnver == 'mayinquangcao'){

        }elseif(!$data || $data->slug != $slug || $mbnver != str_replace('-','', $data->category->slug)) {
            throw new Exception('Không tồn tại tin đăng này !');
        }
        foreach ($this->limitdm as $key => $value) {
            if($data->category->id == $value){
                $limitapi = $data->category->id;
            }
        }
        
        if(!($data->category->id == $limitapi ) || !isset($article)){
            throw new Exception("Error Processing Request", 2);
        }
        
        if(!($data->user->membership_value == 22 || $data->user->membership_value == 23)){
            throw new Exception("Error Processing Request", 2);
        }
        $limitca = $this->limitdm;
        // POST -----------
        $article_component = new ArticleComponent();
        $arr = explode(',', $tabs_for_layout);
        $cache_name = md5(serialize(array(
            'HomeController',
            'indexAction',
            'Category',
            'find',
            $arr
        )));
        $categories = $this->cache->get($cache_name);
        if (!$categories) {
            $categories = $this->modelsManager->createBuilder()
            ->from('ITECH\Datasource\Model\Category')
            ->inWhere('ITECH\Datasource\Model\Category.id', $arr)
            ->orderBy('ITECH\Datasource\Model\Category.ordering ASC')
            ->getQuery()
            ->execute();
            $this->cache->save($cache_name, $categories);
        }

        $cache_name = md5(serialize(array(
            'HomeController',
            'indexAction',
            'box_layout',
            $arr
        )));

        $box_layout = $this->cache->get($cache_name);
        if (!$box_layout) {
            $box_layout = array();
            foreach ($categories as $item) {
                $params = array(
                    'conditions' => array(
                        'category_id' => $item->id,
                        'module' => Constant::MODULE_ARTICLES,
                        'status' => Constant::STATUS_ACTIVED
                    ),
                    'order' => 'ITECH\Datasource\Model\Article.id DESC',
                    'limit' => 6
                );

                $box_layout[$item->id] = $article_component->categoryHome($this, parent::$theme, $params);
            }
            $this->cache->save($cache_name, $box_layout);
        }

        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_ARTICLES,
                'status' => Constant::STATUS_ACTIVED,
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => 3
        );
        $most_new_layout = $article_component->mostNew($this, self::$theme, $params);

        $params = array(
           'conditions' => array(
               'type' => Constant::ARTICLES_NEW,
               'status' => Constant::STATUS_ACTIVED,
               'module' => Constant::MODULE_ARTICLES
           ),
           'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
           'limit' => $noi_bat_layout
        );
        $fresh_layout = $article_component->fresh($this, self::$theme, $params);

        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_ARTICLES,
                'status' => Constant::STATUS_ACTIVED,
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => 3
        );
        $most_new_layout = $article_component->mostNew($this, self::$theme, $params);

        $link_component = new LinkComponent();
        $params = array(
            'conditions' => array(
                'group_id' => 8,
                'ordering' => 'ITECH\Datasource\Model\Link.ordering DESC'
            ));

        $link_box_four_layout = $link_component->custom($this, self::$theme, $params, 'box_four');

        if ( $article && $data->slug == $slug) :
            //sub mbn
            
            // $title_for_layout = $data->name . ' - '.$data->user->name .' - '. $data->id .' - Mua Bán tại chuyên trang '.Constant::SEO_WEB_SITE .' của Mua Bán Nhanh';
            // $description_for_layout = $data->name . ' - '.$data->user->name .' - '. $data->id .' - Mua Bán tại chuyên trang '.Constant::SEO_WEB_SITE .' của Mua Bán Nhanh - '.$data->created_at;
            //sub mbn-end

            // ve tinh 
            $title_for_layout = $data->name . ' - '.$data->user->phone_number .' - '. $data->id .' - '.Constant::SEO_WEB_SITE;
            $description_for_layout = $data->name . ' - ' .'liên hệ shop '.$data->user->name . ' - điện thoại '.$data->user->phone_number . ' - ' . $data->id . ' - Mua bán tại '.Constant::SEO_WEB_SITE.' - '.$data->created_at;
            // ve tinh-end
            $keywords_for_layout = $data->name;
            if (count($data->tag->user) > 0){
                foreach ($data->tag->user as $key => $tag) {
                    $keywords_for_layout .= $tag->name .',';
                }
            }

            $canonical_layout = $this->url->get(array('for' => 'mbn_detail', 'slug' => $data->slug, 'id' => $data->id ));
            $og_title = $data->name;
            $og_site_name = Constant::SEO_WEB_SITE;
            $og_url = $canonical_layout;
            $og_description = $data->name;
            $og_image = $this->config->asset->home_image_url . 'logo_fb.png';


        endif;
        
        $this->view->setVars(array(
            'article' => $article['article'],
            'related' => $article['related'],
            'pagination' => $pagination,
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'canonical_layout' => $canonical_layout,
            'og_title' => $og_title,
            'og_site_name' => $og_site_name,
            'og_url' => $og_url,
            'limitca' =>$limitca,
            'og_description' => $og_description,
            'og_image' => $og_image,
            'box_layout' => $box_layout,
            'categories' => $categories,
            'fresh_layout' => $fresh_layout,
            'link_box_four_layout' => $link_box_four_layout,
            'most_new_layout' => $most_new_layout
        ));

        $this->view->pick(parent::$theme . '/muabannhanh/detail');
    }

    // public function riderectAction(){

    //     $muabanhanhComponent = new MuabannhanhComponent();
    //     $id = $this->dispatcher->getParam('id', array('int'), 0);
    //     $slug = $this->dispatcher->getParam('slug', array('striptags', 'trim', 'lower'), '');
    //     $article = $muabanhanhComponent->getDetailPost($this, array('id'=>$id));
    //     $data = $article['article'];

    //     header("Location: http://final.dev/muabannhanh/$slug-$id.cu");
    // }
}
