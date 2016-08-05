<?php
namespace ITECH\Home\Controller;

use Phalcon\Exception;
use ITECH\Home\Controller\BaseController;
use ITECH\Datasource\Model\Article;
use ITECH\Datasource\Model\Admin;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Model\Tag;
use ITECH\Datasource\Model\ArticleCategory;
use ITECH\Datasource\Model\Comment;
use ITECH\Datasource\Repository\ArticleRepository;
use ITECH\Datasource\Repository\ArticleCategoryRepository;
use ITECH\Datasource\Repository\ArticleTagRepository;
use ITECH\Home\Component\MuabannhanhComponent;
use ITECH\Home\Component\ArticleComponent;
use ITECH\Home\Component\LinkComponent;
use ITECH\Datasource\Lib\Constant;
use ITECH\Datasource\Lib\Util;
use ITECH\Home\Form\CommentForm;
use ITECH\Home\Lib\Config as LocalConfig;

class ArticleController extends BaseController
{
    private $limitByCategory = array(170,342,346);

    public function initialize()
    {
        parent::initialize();
    }

    public function detailAction()
    {
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

        $article_component = new ArticleComponent();
         // var_dump($nhieu_nhat_layout);exit();
        $id = $this->dispatcher->getParam('id', array('int'), -1);

        // --------- Article
        $params = array(
            'conditions' => array(
                'id' => (int)$id,
                'status' => Constant::STATUS_ACTIVED
            )
        );
        $cache_name = md5(serialize(array(
            'ArticleController',
            'detailAction',
            'ArticleRepository',
            'getDetail',
            $params
        )));

        $article = $this->cache->get($cache_name);
        if (!$article) {
            $article_repository = new ArticleRepository();
            $article = $article_repository->getDetail($params);
            $this->cache->save($cache_name, $article);
        }

        if (!count($article)) {
            throw new Exception('Thông tin chưa được duyệt hoặc không tồn tại.');
        }

        $article = $article[0];
        $name_admin = $article->getAdmin(array('columns' => 'id, name'));

        $cache_name = md5(serialize(array(
            'ArticleController',
            'detailAction',
            'getDetail',
            'getArticleContent',
            $id
        )));


        $article_content = $this->cache->get($cache_name);
        if (!$article_content) {
            $article_content = $article->getArticleContent();
        }

        if (!$article_content) {
            throw new Exception('Thông tin chưa được duyệt hoặc không tồn tại.');
        }

        // Categories ------
        $params = array(
            'conditions' => array(
                'id' => (int)$id,
                'status' => Constant::STATUS_ACTIVED,
                'module' => $article->module
            )
        );
        $cache_name = md5(serialize(array(
            'ArticleController',
            'detailAction',
            'ArticleCategoryRepository',
            'getList',
            $params
        )));
        $categories = $this->cache->get($cache_name);
        if (!$categories) {
            $article_category_respository = new ArticleCategoryRepository();
            $categories = $article_category_respository->getList($params);
            $this->cache->save($cache_name, $categories);
        }
        // Tags ----------

        $params = array(
            'conditions' => array(
                'id' => (int)$id
            )
        );
        $cache_name = md5(serialize(array(
            'ArticleController',
            'detailAction',
            'ArticleTagRepository',
            'getList',
            $params
        )));
        $tags = $this->cache->get($cache_name);
        if (!$tags) {
            $article_tag_respository = new ArticleTagRepository();
            $tags = $article_tag_respository->getList($params);
            $this->cache->save($cache_name, $tags);
        }

        $hits = $article->hits;
        if ($hits == 0) {
            $hits += 1;
        }

        if (!$this->session->has('ARTICLE_VIEWED')) {
            $article = Article::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array('id' => $article->id)
            ));

            $article->hits += 1;
            $article->save();
            $this->session->set('ARTICLE_VIEWED', $article->id);
        } else {
            $article_viewed = $this->session->get('ARTICLE_VIEWED');

            if ($article_viewed != $article->id) {
                $article = Article::findFirst(array(
                    'conditions' => 'id = :id:',
                    'bind' => array('id' => $article->id)
                ));

                $article->hits += 1;
                $article->save();
                $this->session->set('ARTICLE_VIEWED', $article->id);
            }
        }

        $cache_name = md5(serialize(array(
            'ArticleController',
            'detailAction',
            'ArticleCategory',
            $article->id,
        )));
        $article_category = ArticleCategory::find(array(
            'columns' => 'category_id',
            'conditions' => 'article_id = :article_id:',
            'bind' => array('article_id' => $article->id),
            'cache' => array('key' => $cache_name)
        ));
        $array = array();
        foreach ($article_category as $item) {
            $array[] = $item->category_id;
        }

        $params = array(
            'conditions' => array(
                'id' => $article->id,
                'category_idx' => $array,
                'type' => Constant::ARTICLES_DEFAULT,
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_ARTICLES
            ),
            'order' => 'ITECH\Datasource\Model\Article.hits DESC',
            'limit' => $nhieu_nhat_layout
        );

        $most_viewed_layout = $article_component->mostViewed($this, self::$theme, $params);

        $params = array(
            'conditions' => array(
                'id' => $article->id,
                'category_idx' => $array,
                'module' => Constant::MODULE_ARTICLES,
                'status' => Constant::STATUS_ACTIVED,
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => $moi_nhat_layout
        );
        $most_new_layout = $article_component->mostNew($this, self::$theme, $params);

        $params = array(
            'conditions' => array(
                'id_n' => $article->id,
                'category_idx' => $array,
                'module' => Constant::MODULE_ARTICLES,
                'status' => Constant::STATUS_ACTIVED,
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => $moi_hon_layout
        );
        $newer_layout = $article_component->newer($this, self::$theme, $params);

        $params = array(
            'conditions' => array(
                'id_o' => $article->id,
                'category_idx' => $array,
                'module' => Constant::MODULE_ARTICLES,
                'status' => Constant::STATUS_ACTIVED,
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => $cu_hon_layout
        );
        $older_layout = $article_component->older($this, self::$theme, $params);

        $params = array(
            'conditions' => array(
                'type' => Constant::ARTICLES_NEW,
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_ARTICLES
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => $noi_bat_layout
        );

        $category_fresh_layout = $article_component->categoryFresh($this, self::$theme, $params);

        $arr = explode(',', $tabs_for_layout);
        $cache_name = md5(serialize(array(
            'HomeController',
            'detailAction',
            'Category',
            'find',
            $arr
        )));
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
            'ArticleController',
            'detailAction',
            'box_layout',
            $arr
        )));

        $box_layout = $this->cache->get($cache_name);
        if (!$box_layout) {
            $box_layout = array();
            foreach ($categories_detail as $item) {
                $params = array(
                    'conditions' => array(
                        'category_id' => $item->id,
                        'module' => Constant::MODULE_ARTICLES,
                        'status' => Constant::STATUS_ACTIVED
                    ),
                    'order' => 'ITECH\Datasource\Model\Article.id DESC',
                    'limit' => $hien_thi_layout
                );

                $box_layout[$item->id] = $article_component->categoryHome($this, parent::$theme, $params);
            }
            $this->cache->save($cache_name, $box_layout);

        }

        $form = new CommentForm();
        $cache_name = md5(serialize(array(
            'AjaxController',
            'commentListAction',
            'Comment',
            'find',
            $article->id
        )));
        $comments = $this->cache->get($cache_name);
        if (!$comments) {
            $comments = Comment::find(array(
                'conditions' => 'article_id = :article_id: and status = :status:',
                'bind' => array(
                    'article_id' => $id,
                    'status' => Constant::COMMENT_STATUS_ACTIVED
                ),
                'limit' => 20,
                'order' => 'created_at DESC'
            ));
            $this->cache->save($cache_name, $comments);
        }

        $breadcrumbs = array();

        $breadcrumbs[] = array('title' => 'Trang chủ', 'url' => $this->url->get(array('for' => 'home')));
        foreach($categories as $item) {
            $breadcrumbs[] = array('title' => $item->category_name, 'url' => $this->url->get(array('for' => 'article_list', 'slug' => $item->category_slug)));
        }

        $tag = array();
        $category_name = array();
        foreach ($tags as $item):
            $tag[] = $item->tag_title;
        endforeach;
        
        foreach ($categories as $item) {
            $category_name[] = $item->category_name;
        }

        $link_component = new LinkComponent();
        $params = array(
            'conditions' => array(
                'group_id' => 8,
                'ordering' => 'ITECH\Datasource\Model\Link.ordering DESC'
        ));
        $link_box_four_layout = $link_component->custom($this, self::$theme, $params, 'box_four');

        $muabanhanhComponent = new MuabannhanhComponent();
        $listPostApi = $muabanhanhComponent->getByElastic($this, parent::$theme,$this->limitByCategory);

        $title_for_layout =  $article->title . ', ' . $article->id . ', ' . $name_admin->name . ', ' . Constant::SEO_WEB_SITE . ', ' . date('d/m/Y H:i:s', $article->updated_at);
        $description_for_layout = $article->title . ', ' . $article->id . ', ' . Constant::SEO_WEB_SITE_DESCRIPTION . ', ' . $name_admin->name . ', ' . Constant::SEO_WEB_SITE . ', ' . date('d/m/Y H:i:s', $article->updated_at);
        if (!empty($tag)) {
            $keywords_for_layout = implode(', ', $tag);
        } else {
            $keywords_for_layout = implode(', ', $tag);
        }
        
        $og_title = $article->title;
        $og_site_name = Constant::SEO_WEB_SITE;
        $og_url = $this->config->application->base_url;
        $og_description = $article->intro;
        $og_image = $this->config->asset->home_image_url . '500/' . $article->image;
        
        $canonical_layout = $this->url->get(array('for' => 'article_detail', 'slug' => $article->alias, 'id' => $article->id));
        
        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'listPostApi' => $listPostApi,
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'canonical_layout' => $canonical_layout,
            'og_title' => $og_title,
            'og_site_name' => $og_site_name,
            'og_url' => $og_url,
            'og_description' => $og_description,
            'og_image' => $og_image,
            'article' => $article,
            'comments' => $comments,
            'most_new_layout' => $most_new_layout,
            'article_content' => $article_content,
            'hits' => $hits,
            'categories' => $categories,
            'tags' => $tags,
            'category_fresh_layout' => $category_fresh_layout,
            'newer_layout' => $newer_layout,
            'older_layout' => $older_layout,
            'most_viewed_layout' => $most_viewed_layout,
            'box_layout' => $box_layout,
            'categories_detail' => $categories_detail,
            'form' => $form,
            'link_box_four_layout' => $link_box_four_layout,
            // 'article_mbn' => $article_mbn,
            'name_admin' => $name_admin
        ));
        if ($article->module == Constant::MODULE_ARTICLES) {
            $this->view->pick(parent::$theme . '/article/detail');
        }

        if ($article->module == Constant::MODULE_PRODUCTS) {
            $this->view->pick(parent::$theme . '/product/detail');
        }

    }

    public function categoryListAction()
    {
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

        $slug = $this->dispatcher->getParam('slug', array('striptags', 'trim', 'lower'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $article_component = new ArticleComponent();

        $cache_name = md5(serialize(array(
            'ProductController',
            'listAction',
            'Category',
            'findFirst',
            $slug
        )));

        $category = $this->cache->get($cache_name);
        if (!$category) {
            $category = Category::findFirst(array(
                'conditions' => 'slug = :slug:',
                'bind' => array('slug' => $slug)
            ));
            $this->cache->save($cache_name, $category);
        }

        if (!$category || $category->slug != $slug) {
            throw new Exception('Không ton tai danh muc này !');
        }

        $params = array(
            'conditions' => array(
                'category_id' => $category->id,
                'status' => Constant::STATUS_ACTIVED
            ),
            'order' => 'ITECH\Datasource\Model\Article.ordering DESC, ITECH\Datasource\Model\Article.updated_at DESC',
            'page' => (int)$page,
            'limit' => (int)$limit
        );

        $cache_name = md5(serialize(array(
            'ProductController',
            'searchResultListAction',
            'ArticleRepository',
            'getListPagination',
            $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);

            if ($page == 1) {
                $category->article_count = $result->total_items;
                $category->update();
            }
        }

        $arr = explode(',', $tabs_for_layout);
        $cache_name = md5(serialize(array(
            'ArticleController',
            'categoryListAction',
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
            'ArticleController',
            'categoryListAction',
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
                    'limit' => $hien_thi_layout
                );

                $box_layout[$item->id] = $article_component->category($this, parent::$theme, $params);
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

        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_ARTICLES,
                'status' => Constant::STATUS_ACTIVED,
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => $moi_nhat_layout
        );
        $most_new_layout = $article_component->mostNew($this, self::$theme, $params);
        
        $params = array(
            'conditions' => array(
                'id' => $category->id,
                'category_id' => $category->id,
                'type' => Constant::ARTICLES_DEFAULT,
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_ARTICLES
            ),
            'order' => 'ITECH\Datasource\Model\Article.hits DESC',
            'limit' => $nhieu_nhat_layout
        );

        $most_viewed_layout = $article_component->mostViewed($this, self::$theme, $params);

        $link_component = new LinkComponent();
        $params = array(
            'conditions' => array(
                'group_id' => 8,
                'ordering' => 'ITECH\Datasource\Model\Link.ordering DESC'
        ));
        $title_for_layout = $category->name;     
        $title_for_layout = $category->name . ', ' . Constant::SEO_WEB_SITE_CATEGORY_TITLE . ', Trang ' . $page;
        $description_for_layout = $category->name . ', ' . Constant::SEO_WEB_SITE_CATEGORY_TITLE . ', chuyên trang ' . $category->name . ', Trang ' . $page;
        $keywords_for_layout = $category->name ;
        
        $canonical_layout = $this->url->get(array('for' => 'article_list', 'slug' => $category->slug));
        $image_layout = $this->config->asset->home_image_url . 'logo.png';
        
        $og_title = $category->name;
        $og_site_name = Constant::SEO_WEB_SITE;
        $og_url = $this->config->application->base_url;
        $og_description = $category->name;
        $og_image = $this->config->asset->home_image_url . 'logo_fb.png';
        
        $page_header = $category->name;
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Trang chủ', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $muabanhanhComponent = new MuabannhanhComponent();
        $listPostApi = $muabanhanhComponent->getByElastic($this, parent::$theme,$this->limitByCategory);
        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'canonical_layout' => $canonical_layout,
            'og_title' => $og_title,
            'most_viewed_layout'=>$most_viewed_layout,
            'og_site_name' => $og_site_name,
            'og_url' => $og_url,
            'og_description' => $og_description,
            'og_image' => $og_image,
            'result' => $result,
            'category' => $category,
            'image_layout' => $image_layout,
            'box_layout' => $box_layout,
            'categories' => $categories,
            'fresh_layout' => $fresh_layout,
            'listPostApi' => $listPostApi,
            'most_new_layout' => $most_new_layout,
            // 'link_box_four_layout' => $link_box_four_layout
        ));
        if ($category->module == Constant::MODULE_ARTICLES) {
            $this->view->pick(parent::$theme . '/article/list');
        }
        if ($category->module == Constant::MODULE_PRODUCTS) {
            $this->view->pick(parent::$theme . '/product/list');
        }
    }
    // tag rss
    public function rsscategoryAction()
    {
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

        $response = array(
            'status' => \ITECH\Datasource\Lib\Constant::CODE_SUCCESS,
            'message' => 'Success.',
            'results' => array()
        );

        $slug = $this->dispatcher->getParam('slug', array('striptags', 'trim', 'lower'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $article_component = new ArticleComponent();

        $cache_name = md5(serialize(array(
            'ProductController',
            'listAction',
            'category',
            'findFirst',
            $slug
        )));

        $category = $this->cache->get($cache_name);
        if (!$category) {
            $category = Category::findFirst(array(
                'conditions' => 'slug = :slug:',
                'bind' => array('slug' => $slug)
            ));
            $this->cache->save($cache_name, $category);
        }

        if (!$category || $category->slug != $slug) {
            throw new Exception('Không t?n t?i danh m?c này.');
        }
        $params = array(
            'conditions' => array(
                'category_id' => $category->id,
                'status' => Constant::STATUS_ACTIVED
            ),
            'order' => 'ITECH\Datasource\Model\Article.ordering DESC, ITECH\Datasource\Model\Article.updated_at DESC',
            'page' => (int)$page,
            'limit' => (int)$limit
        );

        $cache_name = md5(serialize(array(
            'ProductController',
            'searchResultListAction',
            'ArticleRepository',
            'getListPagination',
            $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);

            if ($page == 1) {
                $category->article_count = $result->total_items;
                $category->update();
            }
        }

        if ($result && count($result) > 0) {

            foreach ($result->items as $item) {
                if ($item->alias == '') {
                    $item->alias = \ITECH\Datasource\Lib\Util::slug($item->title);
                }
                
                $url = $this->config->application->base_url . $item->alias .'-' . $item->id.'.html';
                
                $response['results'][] = array(
                    'id' => (int)$item->id,
                    'title' => $item->title,
                    'alias' => ($item->alias != '') ? $item->alias : \ITECH\Datasource\Lib\Util::slug($item->title),
                    'intro' => \ITECH\Datasource\Lib\Util::niceWordsByChars($item->intro, 100, '...'),
                    'image' => ($item->image != '') ? $this->config->asset->home_image_url . '500/' . $item->image : '',
                    'url' => $url
                );
            }
        }
        return parent::outputJSON($response);
        
    }
    // 

    public function tagListAction()
    {
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

        $slug = $this->dispatcher->getParam('slug', array('striptags', 'trim', 'lower'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $cache_name = md5(serialize(array(
            'ProductController',
            'listAction',
            'Tag',
            'findFirst',
            $slug
        )));

        $tag = $this->cache->get($cache_name);
        if (!$tag) {
            $tag = Tag::findFirst(array(
                'conditions' => 'slug = :slug:',
                'bind' => array('slug' => $slug)
            ));
            $this->cache->save($cache_name, $tag);
        }

        if (!$tag || $tag->slug != $slug) {
            throw new Exception('Không tồn tại tag này.');
        }

        $params = array(
            'conditions' => array(
                'tag_id' => $tag->id,
                'status' => Constant::STATUS_ACTIVED
            ),
            'order' => 'ITECH\Datasource\Model\Article.ordering DESC, ITECH\Datasource\Model\Article.updated_at DESC',
            'page' => (int)$page,
            'limit' => (int)$limit
        );

        $cache_name = md5(serialize(array(
            'ArticleController',
            'tagListAction',
            'ArticleRepository',
            'getListPagination',
            $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);

            if ($page == 1) {
                $tag->article_count = $result->total_items;
                $tag->update();
            }
        }

        $article_component = new ArticleComponent();

        $params = array(
            'conditions' => array(
                'type' => Constant::ARTICLES_DEFAULT,
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_ARTICLES
            ),
            'order' => 'ITECH\Datasource\Model\Article. hits ASC',
            'limit' => $nhieu_nhat_layout
        );

        $most_viewed_layout = $article_component->mostViewed($this, self::$theme, $params);

        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_ARTICLES,
                'status' => Constant::STATUS_ACTIVED,
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => $moi_nhat_layout
        );
        $most_new_layout = $article_component->mostNew($this, self::$theme, $params);

        $arr = explode(',', $tabs_for_layout);
        $cache_name = md5(serialize(array(
            'ArticleController',
            'tagListAction',
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
            'ArticleController',
            'tagListAction',
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
                    'limit' => $hien_thi_layout
                );

                $box_layout[$item->id] = $article_component->category($this, parent::$theme, $params);
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

        $link_component = new LinkComponent();
        $params = array(
            'conditions' => array(
                'group_id' => 8,
                'ordering' => 'ITECH\Datasource\Model\Link.ordering DESC'
        ));
        // $link_box_four_layout = $link_component->custom($this, self::$theme, $params, 'box_four');

        $muabanhanhComponent = new MuabannhanhComponent();
        $listPostApi = $muabanhanhComponent->getByElastic($this, parent::$theme,$this->limitByCategory);
        
        $title_for_layout = $tag->title . ', tag của ' . Constant::SEO_WEB_SITE_TAG . ', Trang ' . $page;
        $description_for_layout = $tag->title . ', tag của '  . Constant::SEO_WEB_SITE_TAG . ', nội dung mới nhất về '. $tag->title .', Trang ' . $page;
        $keywords_for_layout = $tag->title;
        
        if ($page > 1) {
            $canonical_layout = $this->url->get(array('for' => 'article_list_tag', 'slug' => $tag->slug, 'query' => '?' . http_build_query(array('page' => $page))));
        } else {
            $canonical_layout = $this->url->get(array('for' => 'article_list_tag', 'slug' => $tag->slug));
        }

        $image_layout = $this->config->asset->home_image_url . 'logo.png';
        
        $og_title = $tag->title;
        $og_site_name = Constant::SEO_WEB_SITE;
        $og_url = $this->config->application->base_url;
        $og_description = $tag->title;
        $og_image = $this->config->asset->home_image_url . 'logo_fb.png';
        
        $page_header = $tag->title;
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Trang chủ', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => $description_for_layout,
            'keywords_for_layout' => $keywords_for_layout,
            'og_title' => $og_title,
            'og_site_name' => $og_site_name,
            'og_url' => $og_url,
            'og_description' => $og_description,
            'og_image' => $og_image,
            'result' => $result,
            'tag' => $tag,
            'most_viewed_layout' => $most_viewed_layout,
            'most_new_layout' => $most_new_layout,
            'image_layout' => $image_layout,
            'box_layout' => $box_layout,
            'categories' => $categories,
            'fresh_layout' => $fresh_layout,
            'listPostApi' => $listPostApi,
            // 'link_box_four_layout' => $link_box_four_layout
        ));
        $this->view->pick(parent::$theme . '/article/tag_list');
    }

    // //////////////////////
    public function tagrssAction()
    {
        
        $response = array(
            'status' => \ITECH\Datasource\Lib\Constant::CODE_SUCCESS,
            'message' => 'Success.',
            'results' => array()
        );

        $slug = $this->dispatcher->getParam('slug', array('striptags', 'trim', 'lower'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;


        $cache_name = md5(serialize(array(
            'ProductController',
            'listAction',
            'Tag',
            'findFirst',
            $slug
        )));

        $tag = $this->cache->get($cache_name);
        if (!$tag) {
            $tag = Tag::findFirst(array(
                'conditions' => 'slug = :slug:',
                'bind' => array('slug' => $slug)
            ));
            $this->cache->save($cache_name, $tag);
        }

        if (!$tag || $tag->slug != $slug) {
            throw new Exception('Không tồn tại tag này.');
        }

        $params = array(
            'conditions' => array(
                'tag_id' => $tag->id,
                'status' => Constant::STATUS_ACTIVED
            ),
            'order' => 'ITECH\Datasource\Model\Article.ordering DESC, ITECH\Datasource\Model\Article.updated_at DESC',
            'page' => (int)$page,
            'limit' => (int)$limit
        );

        $cache_name = md5(serialize(array(
            'ArticleController',
            'tagListAction',
            'ArticleRepository',
            'getListPagination',
            $params
        )));

        $result = $this->cache->get($cache_name);

        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);

            if ($page == 1) {
                $tag->article_count = $result->total_items;
                $tag->update();
            }

        }
            
        if ($result && count($result) > 0) {

            foreach ($result->items as $item) {
                if ($item->alias == '') {
                    $item->alias = \ITECH\Datasource\Lib\Util::slug($item->title);
                }
                
                $url = $this->config->application->base_url . $item->alias .'-' . $item->id.'.html';
                
                $response['results'][] = array(
                    'id' => (int)$item->id,
                    'title' => $item->title,
                    'alias' => ($item->alias != '') ? $item->alias : \ITECH\Datasource\Lib\Util::slug($item->title),
                    'intro' => \ITECH\Datasource\Lib\Util::niceWordsByChars($item->intro, 100, '...'),
                    'image' => ($item->image != '') ? $this->config->asset->home_image_url . '500/' . $item->image : '',
                    'url' => $url
                );
            }
        }
        return parent::outputJSON($response);
    }

    public function memberListAction()
    {
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
        
        $id = $this->dispatcher->getParam('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $cache_name = md5(serialize(array(
            'ArticleController',
            'memberListAction',
            'Admin',
            'findFirst',
            $id
        )));

        $admin = $this->cache->get($cache_name);
        if (!$admin) {
            $admin = Admin::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array('id' => $id)
            ));
            $this->cache->save($cache_name, $admin);
        }

        if (!$admin) {
            throw new Exception('Không tồn tại thành viên này.');
        }

        $params = array(
            'conditions' => array(
                'created_by' => $admin->id,
                'status' => Constant::STATUS_ACTIVED
            ),
            'order' => 'ITECH\Datasource\Model\Article.ordering DESC, ITECH\Datasource\Model\Article.updated_at DESC',
            'page' => (int)$page,
            'limit' => (int)$limit
        );

        $cache_name = md5(serialize(array(
            'ArticleController',
            'memberListAction',
            'ArticleRepository',
            'getListPagination',
            $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);
        }

        $article_component = new ArticleComponent();

        $params = array(
            'conditions' => array(
                'type' => Constant::ARTICLES_DEFAULT,
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_ARTICLES
            ),
            'order' => 'ITECH\Datasource\Model\Article. hits ASC',
            'limit' => $nhieu_nhat_layout
        );

        $most_viewed_layout = $article_component->mostViewed($this, self::$theme, $params);

        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_ARTICLES,
                'status' => Constant::STATUS_ACTIVED,
            ),
            'order' => 'ITECH\Datasource\Model\Article.updated_at DESC',
            'limit' => $moi_nhat_layout
        );
        $most_new_layout = $article_component->mostNew($this, self::$theme, $params);

        $arr = explode(',', $tabs_for_layout);
        $cache_name = md5(serialize(array(
            'ArticleController',
            'tagListAction',
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
            'ArticleController',
            'tagListAction',
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
                    'limit' => $hien_thi_layout
                );

                $box_layout[$item->id] = $article_component->category($this, parent::$theme, $params);
            }
            $this->cache->save($cache_name, $box_layout);

        }

        $title_for_layout = $category->name . ', ' . Constant::SEO_WEB_SITE_CATEGORY_TITLE . ', Trang ' . $page;
        $description_for_layout = $category->name . ', ' . Constant::SEO_WEB_SITE_CATEGORY_TITLE . ', chuyên trang ' . $category->name . ', Trang ' . $page;
        $keywords_for_layout = $category->name ;
        
        if ($page > 1) {
            $canonical_layout = $this->url->get(array('for' => 'article_list_member', 'slug' => Util::slug($admin->name), 'query' => '?' . http_build_query(array('page' => $page))));
        } else {
            $canonical_layout = $this->url->get(array('for' => 'article_list_member', 'slug' => Util::slug($admin->name)));
        }

        $image_layout = $this->config->asset->home_image_url . 'logo.png';
        $page_header = $admin->name;
        $breadcrumbs = array();

        $breadcrumbs[] = array('title' => 'Trang chủ', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => Util::setNiceWords($description_for_layout,160),
            'keywords_for_layout' => Util::setNiceWords($description_for_layout,147),
            'result' => $result,
            'admin' => $admin,
            'most_viewed_layout' => $most_viewed_layout,
            'most_new_layout' => $most_new_layout,
            'image_layout' => $image_layout,
            'box_layout' => $box_layout,
            'categories' => $categories
        ));
        $this->view->pick(parent::$theme . '/article/member_list');
    }
    
    public function newListAction()
    {
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

        $slug = $this->dispatcher->getParam('slug', array('striptags', 'trim', 'lower'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $article_component = new ArticleComponent();

        $params = array(
            'conditions' => array(
                'type' => Constant::ARTICLES_NEW,
                'status' => Constant::STATUS_ACTIVED
            ),
            'order' => 'ITECH\Datasource\Model\Article.ordering DESC, ITECH\Datasource\Model\Article.updated_at DESC',
            'page' => (int)$page,
            'limit' => (int)$limit
        );

        $cache_name = md5(serialize(array(
            'ArticleController',
            'newListAction',
            'ArticleRepository',
            'getListPagination',
            $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);
        }

        $arr = explode(',', $tabs_for_layout);
        $cache_name = md5(serialize(array(
            'ArticleController',
            'categoryListAction',
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
            'ArticleController',
            'categoryListAction',
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
                    'limit' => $hien_thi_layout
                );

                $box_layout[$item->id] = $article_component->category($this, parent::$theme, $params);
            }
            $this->cache->save($cache_name, $box_layout);

        }
                
        $title_for_layout = 'Tin nổi bật' . ', Blog tin nổi bật' . ', '. Constant::SEO_WEB_SITE_CATEGORY_TITLE . ', ' . Constant::SEO_WEB_SITE_CATEGORY_TITLE . ', Trang ' . $page;
        $description_for_layout = 'Tin nổi bật' . ', Blog tin nổi bật' . ', ' . Constant::SEO_WEB_SITE_CATEGORY_TITLE . ', ' . Constant::SEO_WEB_SITE_DESCRIPTION . ', Trang ' . $page;
        $keywords_for_layout = 'Tin nổi bật' . ', Blog tin nổi bật' . ', ' . Constant::SEO_WEB_SITE_CATEGORY_TITLE;
        
        $canonical_layout = $this->url->get(array('for' => 'article_list'));
        $image_layout = $this->config->asset->home_image_url . 'logo.png';
        
        $og_title = 'Tin nổi bật';
        $og_site_name = Constant::SEO_WEB_SITE;
        $og_url = $this->config->application->base_url;
        $og_description = Constant::SEO_WEB_SITE_CATEGORY_TITLE . ', ' . Constant::SEO_WEB_SITE_DESCRIPTION;
        $og_image = $this->config->asset->home_image_url . 'logo_fb.png';
        
        $page_header = 'Tin nổi bật';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Trang chủ', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'title_for_layout' => Util::setNiceWords($title_for_layout,70),
            'description_for_layout' => Util::setNiceWords($description_for_layout,160),
            'keywords_for_layout' => Util::setNiceWords($description_for_layout,147),
            'canonical_layout' => $canonical_layout,
            'og_title' => $og_title,
            'og_site_name' => $og_site_name,
            'og_url' => $og_url,
            'og_description' => $og_description,
            'og_image' => $og_image,
            'result' => $result,
            'category' => $category,
            'image_layout' => $image_layout,
            'box_layout' => $box_layout,
            'categories' => $categories
        ));
        $this->view->pick(parent::$theme . '/article/list_new');
    }
    // site map

        public function creatsitemapAction()
        {
        $setting = LocalConfig::setting();
        echo "\n Thien By " . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][createSiteMapAction]' . "\n";

        $category = new \ITECH\Datasource\Model\Category();
        $b = $category->getModelsManager()->createBuilder();
        $b->columns(array(
            'c.id',
            'c.parent_id',
            'c.name',
            'c.slug',
            'c.article_count'
        ));
        $b->from(array('c' => 'ITECH\Datasource\Model\Category'));
        $b->andWhere('c.parent_id <> 0');
        $b->andWhere('c.status = :status:', array('status' => \ITECH\Datasource\Lib\Constant::CATEGORY_STATUS_ACTIVED));
        
        $categories = $b->getQuery()->execute();

        if ($categories && count($categories)) {
            foreach ($categories as $item) {
                $count = \ITECH\Datasource\Model\Article::count(array(
                    'conditions' => 'category_id = :category_id:
                        AND is_shown = :is_shown:
                        AND status = :status:',
                    'bind' => array(
                        'category_id' => $item->id,
                        'status' => \ITECH\Datasource\Lib\Constant::ARTICLE_STATUS_ACTIVED
                    )
                ));

                $q = 'UPDATE ITECH\Datasource\Model\Category
                    SET ITECH\Datasource\Model\Category.article_count = :article_count:
                    WHERE ITECH\Datasource\Model\Category.id = :id:';
                $b = $category->getModelsManager()->createQuery($q);
                $b->execute(array(
                    'article_count' => $count,
                    'id' => $item->id
                ));
            }

            $b = $category->getModelsManager()->createBuilder();
            $b->columns(array(
                'ITECH\Datasource\Model\Category.id',
                'ITECH\Datasource\Model\Category.parent_id',
                'ITECH\Datasource\Model\Category.article_count'
            ));
            $b->from('ITECH\Datasource\Model\Category');
            $b->andWhere('ITECH\Datasource\Model\Category.parent_id <> 0');
            $b->andWhere('ITECH\Datasource\Model\Category.status = :status:', array('status' => ITECH\Datasource\Lib\Constant::CATEGORY_STATUS_ACTIVE));
            $categories = $b->getQuery()->execute();

            $b = $category->getModelsManager()->createBuilder();
            $b->columns(array('ITECH\Datasource\Model\Category.id'));
            $b->from('ITECH\Datasource\Model\Category');
            $b->andWhere('ITECH\Datasource\Model\Category.parent_id = 0');
            $b->andWhere('ITECH\Datasource\Model\Category.status = :status:', array('status' => ITECH\Datasource\Lib\Constant::CATEGORY_STATUS_ACTIVE));
            $parent_categories = $b->getQuery()->execute();

            if ($parent_categories && count($parent_categories)) {
                foreach ($parent_categories as $item) {
                    $count = 0;

                    foreach ($categories as $cat) {
                        if ($cat->parent_id == $item->id) {
                            $count += $cat->article_count;
                        }
                    }

                    $q = 'UPDATE ITECH\Datasource\Model\Category
                        SET ITECH\Datasource\Model\Category.article_count = :article_count:
                        WHERE ITECH\Datasource\Model\Category.id = :id:';
                    $b = $category->getModelsManager()->createQuery($q);
                    $b->execute(array(
                        'article_count' => $count,
                        'id' => $item->id
                    ));
                }
            }
        }
        
        $doc = new \ DOMDocument('1.0', 'UTF-8');

        $root = $doc->createElement('sitemapindex');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        $doc->appendChild($root);

        $u_site_map = $doc->createElement('sitemap');
        $u_loc = $u_site_map->appendChild($doc->createElement('loc'));
        $u_loc_node = $doc->createTextNode($this->config->application->base_url . 'sitemap-user.xml');
        $u_loc->appendChild($u_loc_node);
        $doc->appendChild($u_site_map);

        $u_lastmod = $u_site_map->appendChild($doc->createElement('lastmod'));
        $u_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $u_lastmod->appendChild($u_last_mod_node);

        $doc->appendChild($u_site_map);
        $root->appendChild($u_site_map);

        $p_site_map = $doc->createElement('sitemap');
        $p_loc = $p_site_map->appendChild($doc->createElement('loc'));
        $p_loc_node = $doc->createTextNode($this->config->application->base_url . 'sitemap-province.xml');
        $p_loc->appendChild($p_loc_node);
        $doc->appendChild($p_site_map);

        $p_lastmod = $p_site_map->appendChild($doc->createElement('lastmod'));
        $p_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $p_lastmod->appendChild($p_last_mod_node);

        $doc->appendChild($p_site_map);
        $root->appendChild($p_site_map);

        $c_site_map = $doc->createElement('sitemap');
        $c_loc = $c_site_map->appendChild($doc->createElement('loc'));
        $c_loc_node = $doc->createTextNode($this->config->application->base_url . 'sitemap-category.xml');
        $c_loc->appendChild($c_loc_node);
        $doc->appendChild($c_site_map);

        $c_lastmod = $c_site_map->appendChild($doc->createElement('lastmod'));
        $c_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $c_lastmod->appendChild($c_last_mod_node);
        $doc->appendChild($c_site_map);
        $root->appendChild($c_site_map);

        $t_site_map = $doc->createElement('sitemap');
        $t_loc = $t_site_map->appendChild($doc->createElement('loc'));
        $t_loc_node = $doc->createTextNode($this->config->application->base_url . 'sitemap-tag.xml');
        $t_loc->appendChild($t_loc_node);
        $doc->appendChild($t_site_map);

        $t_lastmod = $t_site_map->appendChild($doc->createElement('lastmod'));
        $t_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $t_lastmod->appendChild($t_last_mod_node);
        $doc->appendChild($t_site_map);
        $root->appendChild($t_site_map);

        $d_site_map = $doc->createElement('sitemap');
        $d_loc = $d_site_map->appendChild($doc->createElement('loc'));
        $d_loc_node = $doc->createTextNode($this->config->application->base_url . 'sitemap-district.xml');
        $d_loc->appendChild($d_loc_node);
        $doc->appendChild($d_site_map);

        $d_lastmod = $d_site_map->appendChild($doc->createElement('lastmod'));
        $d_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $d_lastmod->appendChild($d_last_mod_node);
        $doc->appendChild($d_site_map);
        $root->appendChild($d_site_map);

        foreach ($categories as $item) {
            if ((int)$item->article_count > 0) {
                $site_map = $doc->createElement('sitemap');
                $loc = $site_map->appendChild($doc->createElement('loc'));
                $loc_node = $doc->createTextNode($this->config->application->base_url . 'sitemap-category-' . $item->slug . '.xml');
                $loc->appendChild($loc_node);
                $doc->appendChild($site_map);

                $lastmod = $site_map->appendChild($doc->createElement('lastmod'));
                $last_mod_node = $doc->createTextNode(date('Y-m-d'));
                $lastmod->appendChild($last_mod_node);

                $doc->appendChild($site_map);
                $root->appendChild($site_map);
            }
        }


        $file = ROOT . '/web/home/sitemap.xml';
        if (!$doc->save($file)) {
            die('Error, cannot create XML file');
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }
    // 
        public function createcategoryAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][categoryAction]' . "\n";

        $category = new \ITECH\Datasource\Model\Category();
        $b = $category->getModelsManager()->createBuilder();
        $b->columns(array(
            'c.id',
            'c.parent_id',
            'c.name',
            'c.slug',
            'c.article_count'
        ));
        $b->from(array('c' => 'ITECH\Datasource\Model\Category'));
        $b->andWhere('c.parent_id <> 0');
        $b->andWhere('c.status = :status:', array('status' => \ITECH\Datasource\Lib\Constant::CATEGORY_STATUS_ACTIVED));
        $categories = $b->getQuery()->execute();
        
        if ($categories && count($categories)) {
            foreach ($categories as $item) {
                $count = \ITECH\Datasource\Model\Article::count(array(
                    'conditions' => 'category_id = :category_id:
                        AND is_shown = :is_shown:
                        AND status = :status:',
                    'bind' => array(
                        'category_id' => $item->id,
                        'status' => \ITECH\Datasource\Lib\Constant::ARTICLE_STATUS_ACTIVED
                    )
                ));

                $q = 'UPDATE ITECH\Datasource\Model\Category
                    SET ITECH\Datasource\Model\Category.article_count = :article_count:
                    WHERE ITECH\Datasource\Model\Category.id = :id:';
                $b = $category->getModelsManager()->createQuery($q);
                $b->execute(array(
                    'article_count' => $count,
                    'id' => $item->id
                ));
            }

            $b = $category->getModelsManager()->createBuilder();
            $b->columns(array(
                'ITECH\Datasource\Model\Category.id',
                'ITECH\Datasource\Model\Category.parent_id',
                'ITECH\Datasource\Model\Category.article_count'
            ));
            $b->from('ITECH\Datasource\Model\Category');
            $b->andWhere('ITECH\Datasource\Model\Category.parent_id <> 0');
            $b->andWhere('ITECH\Datasource\Model\Category.status = :status:', array('status' => ITECH\Datasource\Lib\Constant::CATEGORY_STATUS_ACTIVE));
            $categories = $b->getQuery()->execute();

            $b = $category->getModelsManager()->createBuilder();
            $b->columns(array('ITECH\Datasource\Model\Category.id'));
            $b->from('ITECH\Datasource\Model\Category');
            $b->andWhere('ITECH\Datasource\Model\Category.parent_id = 0');
            $b->andWhere('ITECH\Datasource\Model\Category.status = :status:', array('status' => ITECH\Datasource\Lib\Constant::CATEGORY_STATUS_ACTIVE));
            $parent_categories = $b->getQuery()->execute();

            if ($parent_categories && count($parent_categories)) {
                foreach ($parent_categories as $item) {
                    $count = 0;

                    foreach ($categories as $cat) {
                        if ($cat->parent_id == $item->id) {
                            $count += $cat->article_count;
                        }
                    }

                    $q = 'UPDATE ITECH\Datasource\Model\Category
                        SET ITECH\Datasource\Model\Category.article_count = :article_count:
                        WHERE ITECH\Datasource\Model\Category.id = :id:';
                    $b = $category->getModelsManager()->createQuery($q);
                    $b->execute(array(
                        'article_count' => $count,
                        'id' => $item->id
                    ));
                }
            }
        }
        
        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }



    // 
    
    public function searchResultListAction() {
        $search_q = $q = $this->request->getQuery('tu_khoa', array('striptags', 'trim'), '');
        $search_category = $this->request->getQuery('danh_muc', array('striptags', 'trim'), '');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $query_array = array();

        if ($q != '') {
            $query_array[] = 'tu_khoa=' . $q;
        }

        if ($search_category != '') {
            $query_array[] = 'danh_muc=' . $search_category;

            $cache_name = md5(serialize(array(
                'ProductController',
                'searchResultListAction',
                'Category',
                'findFirst',
                $search_category
            )));

            $category = $this->cache->get($cache_name);
            if (!$category) {
                $category = Category::findFirst(array(
                    'conditions' => 'id = :id:',
                    'bind' => array('id' => $search_category)
                ));
                $this->cache->save($cache_name, $category);
            }
        }


        $params = array(
            'conditions' => array(
                'search_q' => $search_q,
                'category_id' => $search_category,
                'status' => Constant::STATUS_ACTIVED,
                'module' => Constant::MODULE_ARTICLES,
            ),
            'order' => 'ITECH\Datasource\Model\Article.ordering DESC, ITECH\Datasource\Model\Article.updated_at DESC',
            'page' => (int)$page,
            'limit' => (int)$limit
        );

        $cache_name = md5(serialize(array(
            'ArticleController',
            'searchResultListAction',
            'ArticleRepository',
            'getListPagination',
            $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $article_repository = new ArticleRepository();
            $result = $article_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);
        }

        $query_array[] = 'page=' . $page;

         $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_ARTICLES,
                'status' => Constant::STATUS_ACTIVED
            ),
            'order' => 'ITECH\Datasource\Model\Article.id DESC',
            'limit' => 5
        );
        $cache_name = md5(serialize(array(
            'HomeController',
            'indexAction',
            'ArticleRepository',
            'getList',
            $params
        )));

        $latest_news = $this->cache->get($cache_name);
        if (!$latest_news) {
            $article_repository = new ArticleRepository();
            $latest_news = $article_repository->getListPagination($params);
            $this->cache->save($cache_name, $latest_news);
        }

        //-------------------------------------------------latest news

        // hit article -------------------------------------------------
        $params = array(
            'conditions' => array(
                'status' => Constant::STATUS_ACTIVED
            ),
            'order' => 'ITECH\Datasource\Model\Article.hits DESC',
            'limit' => 5
        );
        $cache_name = md5(serialize(array(
            'HomeController',
            'indexAction',
            'ArticleRepository',
            'getList',
            $params
        )));

        $hit_news = $this->cache->get($cache_name);
        if (!$hit_news) {
            $article_repository = new ArticleRepository();
            $hit_news = $article_repository->getList($params);
            $this->cache->save($cache_name, $hit_news);
        }
        //---------------------------------------------------hit article

        $title_for_layout = 'Kết quả tìm kiếm - Trang ' . $page;
        $description_for_layout = 'Kết quả tìm kiếm';
        $keywords_for_layout = 'Kết quả tìm kiếm';

        $canonical_layout = $this->url->get(array('for' => 'article_search_result_list', 'slug' => $tag->slug, 'query' => '?' . http_build_query($query_array)));

        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'description_for_layout' => Util::setNiceWords($description_for_layout,160),
            'keywords_for_layout' => Util::setNiceWords($description_for_layout,147),
            'canonical_layout' => $canonical_layout,
            'result' => $result,
            'query_array' => $query_array,
            'search_q' => $search_q,
            'category' => isset($category) ? $category : '',
            'search_category' => $search_category,
            'latest_news' => $latest_news,
            'hit_news' => $hit_news,
        ));
        $this->view->pick(parent::$theme . '/product/search_result_list');
    }
}
