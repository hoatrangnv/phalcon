<?php
namespace ITECH\Admin\Controller;

use Phalcon\Exception;
use Phalcon\Mvc\View;
use ITECH\Admin\Controller\BaseController;
use ITECH\Admin\Form\ArticleForm;
use ITECH\Datasource\Model\Article; 
use ITECH\Datasource\Model\ArticleContent;
use ITECH\Datasource\Model\ArticleCategory;
use ITECH\Datasource\Model\ArticleFulltext;
use ITECH\Datasource\Model\ArticleTag;
use ITECH\Datasource\Model\ArticleAttribute;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Model\Tag;
use ITECH\Datasource\Model\Attribute;
use ITECH\Datasource\Model\File;
use ITECH\Datasource\Model\UserCategory;
use ITECH\Datasource\Repository\ArticleRepository;
use ITECH\Datasource\Repository\CategoryRepository;
use ITECH\Datasource\Repository\TagRepository;
use ITECH\Admin\Component\CategoryComponent;
use ITECH\Admin\Component\ImageComponent;
use ITECH\Datasource\Lib\Util;
use ITECH\Datasource\Lib\Constant; 

class ArticleController extends BaseController
{
    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        parent::authenticate();
        parent::allowRole(array(Constant::ADMIN_TYPE_ROOT, Constant::ADMIN_TYPE_ADMIN, Constant::ADMIN_TYPE_EDITOR));
    }
    
    /**
     * @author Vu.Tran
     */
    public function indexAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $user = $this->session->get('USER');

        if($user['type'] == 'EDITOR')
        {
            $params = array(
                'conditions' => array(
                    'q' => $q,
                    'module' => Constant::MODULE_ARTICLES,
                    //'status' => Constant::STATUS_ACTIVED,
                    'created_by' => $user['id']
                ),
                'page' => (int)$page,
                'limit' => (int)$limit
            );
        } else {
            $params = array(
                'conditions' => array(
                    'q' => $q,
                    'module' => Constant::MODULE_ARTICLES,
                    //'status' => Constant::STATUS_ACTIVED
                ),
                'page' => (int)$page,
                'limit' => (int)$limit
            );
        }

        $cache_name = md5(serialize(array(
            'ArticleController',
            'indexAction',
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
        
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }

            if ($this->request->getPost('ordering')) {
                $ordering = $this->request->getPost('ordering');

                if ($ordering && count($ordering) > 0) {
                    try {
                        foreach ($ordering as $key => $value) {
                            $articles = Article::findFirst($key);
                            if ($articles && count($articles) > 0) {
                                if ($category->ordering != $value) {
                                    $category->ordering = (int)$value;
                                    if (!$category->update()) {
                                        $message = $category->getMessages();
                                        if (isset($message[0])) {
                                            $this->flashSession->error($message[0]->getMessage());
                                        } else {
                                            $this->flashSession->error('Lỗi, không thể cập nhật.');
                                        } 
                                    }
                                }
                            }
                        }
                        
                        $this->cache->delete($cache_name);
                        $query = array(
                        'page' => $page,
                        'q' => $q
                        );
                        
                        $this->flashSession->success('Cập nhật thành công.');
                        return $this->response->redirect(array('for' => 'category_list', 'query' => '?' . http_build_query($query)));
                    } catch (Exception $e) {
                        throw new Exception('Lỗi hệ thống.');
                    }
                }
            }
        }
        //print_r($this->session->get('USER')['id']);die;
        //test session
        $profileVisitor = 0;
        if($this->session->get('action')){
            $profileVisitor = $this->session->get('action');
        }

        //end
        $page_header = 'Danh sách bài viết';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard'.$profileVisitor, 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');
        $search_box = true;
        
        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'search_box' => $search_box,
            'page' => $page,
            'q' => $q,
            'cache_name' => $cache_name,
            'result' => $result,
            'user' => $user
        ));
        $this->view->pick('article/index');
    }

    /**
     * @author Vu.Tran
     */
    public function editAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');
        
        $user = $this->session->get('USER');

        if($user['type'] == 'EDITOR') {
            $user_categories = UserCategory::find(array(
                'conditions' => 'user_id = :id:',
                'bind' => array('id' => $user['id'])
            ));

            $in_array_userCat = array();
            foreach ($user_categories as $item) {
                $in_array_userCat[] = $item->category_id; 
            }
        }

        $article = Article::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id),
        ));
        if (!$article) {
            throw new Exception('Không tồn tại bài viết này.');
        }
        
        $article_attachments = $article->getArticleAttachment();
        
        $article_categories = ArticleCategory::find(array(
            'conditions' => 'article_id = :id:',
            'bind' => array('id' => $article->id)
        ));
        
        $article_tags = ArticleTag::find(array(
            'conditions' => 'article_id = :id:',
            'bind' => array('id' => $article->id)
        ));
        $tags = array();
        foreach ($article_tags as $item) {
            $tags[] = $item->getTag()->title;
        }
        $article->tags = implode(',', $tags);
        $attribute = Attribute::find(array(
          'order' => 'ITECH\Datasource\Model\Attribute.id ASC'  
        ));
        
        $category_component = new CategoryComponent();
        
        if($user['type'] == 'EDITOR') {
            $params = array(
                'conditions' => array(
                    'user_id' => $user['id'],
                    'module' => Constant::MODULE_ARTICLES,
                    'parent_id' => intval(0)
                ),
            );
        } else {
            $params = array(
                'conditions' => array(
                    'module' => Constant::MODULE_ARTICLES,
                    'parent_id' => intval(0)
                ),
            );
        }
        $cache_categories = md5(serialize(array(
            'ArticleController',
            'editAction',
            'CategoryRepository',
            'getList',
             $params
        )));

        $categories = $this->cache->get($cache_categories);
        if (!$categories) {
            $category_repository = new CategoryRepository();
            $categories = $category_repository->getList($params);
            $this->cache->save($cache_categories, $categories);
        }
        
        $in_array = array();
        foreach ($article_categories as $item) {
            $in_array[] = $item->category_id; 
        }
        // print_r($categories);exit();
        $params = array();
        $category_layout_cache_name = md5(serialize(array(
            'ArticleController',
            'editAction',
            $id,
            $user['id'],
            $params
        )));
        
        $category_layout = $this->cache->get($category_layout_cache_name);
        if (!$category_layout) {
            $category_layout = '';
            $level = '';
            foreach ($categories as $item) {

                $active = '';
                if (in_array($item->id, $in_array)) {
                    $active = 'checked="checked"';
                }

                if($user['type'] == 'EDITOR')
                {
                    if (in_array($item->id, $in_array_userCat)) {
                        $category_layout .= '<div class="checkbox">' . '<input type="checkbox" name="category[]" value="' . $item->id . '" class="red" ' . $active . '>' . $item->name . '</div>';
                    } 
                }
                else
                {
                    $category_layout .= '<div class="checkbox">' . '<input type="checkbox" name="category[]" value="' . $item->id . '" class="red" ' . $active . '>' . $item->name . '</div>';
                }
                
                $params = array(
                    'conditions' => array(
                        'module' => Constant::MODULE_ARTICLES,
                        'parent_id' => $item->id,
                    ),
                );
                $sub_category_layout = '';
                $category_layout .= $category_component->sub_checkbox($params, $sub_category_layout, $level, $in_array);

            }
            $this->cache->save($category_layout_cache_name, $category_layout);
        }
        
        $form = new ArticleForm($article, array('edit' => true));
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $article); 
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                if (!$this->request->getPost('category') && empty($this->request->getPost('category'))) {
                   $this->flashSession->error('Vui lòng chọn danh mục.'); 
                } else {                    
                    if ($this->request->hasFiles()) {
                        $file = $this->request->getUploadedFiles();

                        if (isset($file[0])) {
                            $resource = array(
                                'name' => $file[0]->getName(),
                                'type' => $file[0]->getType(),
                                'tmp_name' => $file[0]->getTempName(),
                                'error' => $file[0]->getError(),
                                'size' => $file[0]->getSize()
                            );
                            
                            $response = parent::uploadLocalImage(ROOT . '/web/admin/asset/images/', Util::slug($this->request->getPost('title')), 500, $resource); 
                            if (!empty($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
                                $article->image = $response['result'];
                                parent::uploadRemoteImage(ROOT . '/web/admin/asset/images/', 'default', $article->image);
                                parent::deleteLocalImage(ROOT . '/web/admin/asset/images/', $article->image);
                            }
                            
                            try {
                                $file = new File();
                                $file->title = Util::upperFirstLetter($this->request->getPost('title'));
                                $file->category_id = Constant::CATEGORY_DEFAULT;
                                $file->created_at = time();
                                $file->updated_at = time();
                                $file->created_by = $user['id'];
                                $file->updated_by = $user['id'];
                                $file->created_ip = $this->request->getClientAddress();
                                $file->file_name = isset($response['result']) ? $response['result'] : $article->image;
                                $file->file_type = $resource['type'];
                                $file->file_size = $resource['size'];             
                                if (!$file->create()) {
                                    $message = $file->getMessages();
                                    if (isset($message[0])) {
                                        $this->flashSession->error($message[0]->getMessage());
                                    } else {
                                        $this->flashSession->error('Lỗi, không thể thêm hình ảnh vào quản lý file.');
                                    }
                                }
                            } catch (Exception $e) {
                                $this->logger->log('[ArticleController][editAction] ' . $e->getMessage(), Logger::ERROR);
                                throw new Exception($e->getMessage());
                            }
                        }
                    }
                    
                    $article->title = Util::upperFirstLetter($this->request->getPost('title'));
                    $article->alias = Util::slug($this->request->getPost('title'));
                    $article->intro = $this->request->getPost('intro'); 
                    $article->type = $this->request->getPost('type', array('trim', 'striptags'), '');
                    $article->updated_at = time();  
                    $article->updated_by = Util::numberOnly($user['id']);   
                    $article->created_ip = Util::getRealIpAddress();
                    $article->module = Constant::MODULE_ARTICLES;
                    $this->db->begin();
                    try {
                        if (!$article->update()) {
                            $message = $article->getMessages();
                            if (isset($message[0])) {
                                $this->flashSession->error($message[0]->getMessage());
                            } else {
                                $this->flashSession->error('Lỗi, không thể thêm.');
                            }
                        } else {
                            $article_content = new ArticleContent();
                            $article_content->article_id = $article->id;
                            $article_content->content = Util::filterHtmlTag($this->request->getPost('content'));
                            $article_content->meta_title = $this->request->getPost('meta_title');
                            // 
                            $article_content->title_content_h2 = $this->request->getPost('title_content_h2');
                            $article_content->title_content_h3 = $this->request->getPost('title_content_h3');
                            $article_content->content2_h2 = Util::filterHtmlTag($this->request->getPost('content2_h2'));
                            $article_content->content2_h3 = Util::filterHtmlTag($this->request->getPost('content2_h3'));
                            // 
                            $article_content->meta_description = $this->request->getPost('meta_description');
                            $article_content->meta_keyword = $this->request->getPost('meta_keyword');
                            if (!$article_content->update()) {
                                $message = $article_content->getMessages();
                                if (isset($message[0])) {
                                    $this->flashSession->error($message[0]->getMessage());
                                } else {
                                    $this->flashSession->error('Lỗi, không thể thêm.');
                                }
                            }
                            
                            $article_fulltext = new ArticleFulltext();
                            $article_fulltext->article_id = $article->id;
                            $article_fulltext->title = Util::strClearMark($this->request->getPost('title'));
                            $article_fulltext->content = Util::strClearMark($this->request->getPost('content'));
                            if (!$article_fulltext->update()) {
                                $message = $article_fulltext->getMessages();
                                if (isset($message[0])) {
                                    $this->flashSession->error($message[0]->getMessage());
                                } else {
                                    $this->flashSession->error('Lỗi, không thể cập nhật nội dung bài viết.');
                                }
                            }; 
                              
                            if ($this->request->getPost('tags')) {
                                
                                $tags = explode(',', $this->request->getPost('tags'));
                                $at = array();
                                foreach ($tags as $item) {
                                    $tag_slug = Util::slug($item);
                                    $tag = Tag::findFirst(array(
                                        'conditions' => 'slug = :slug:',
                                        'bind' => array('slug' => $tag_slug)
                                    ));
                                    if ($tag) {
                                        $at[] = $tag->id;
                                    } else {
                                        $tag = new Tag();
                                        $tag->title = $item;
                                        $tag->slug = $tag_slug;
                                        $tag->created_at = time();
                                        $tag->create();

                                        $at[] = $tag->id;
                                    }
                                }
                                
                                foreach ($article_tags as $tag) {
                                    if (!in_array($tag->tag_id, $at)) {
                                            if (!$tag->delete()) {
                                                $message = $tag->getMessages();
                                                if (isset($message[0])) {
                                                    $this->flashSession->error($message[0]->getMessage());
                                                } else {
                                                    $this->flashSession->error('Lỗi, không thể cập nhật tag bài viết.');
                                                }
                                            }
                                        } else {
                                            foreach ($at as $key => $item) {
                                                if ($tag->tag_id == $item) {
                                                    unset($at[$key]);
                                                } 
                                            }
                                        } 
                                }

                                foreach ($at as $item) {
                                    $article_tag = new ArticleTag();
                                    $article_tag->article_id = $article->id;
                                    $article_tag->tag_id = $item;
                                    $article_tag->create();
                                } 
                            }    

                            if ($this->request->getPost('category') && !empty($this->request->getPost('category'))) {
                                $ac = $this->request->getPost('category');
                                if ($article_categories) {
                                    foreach ($article_categories as $article_category) {
                                        if (!in_array($article_category->category_id, $ac)) {
                                            if (!$article_category->delete()) {
                                                $message = $article_category->getMessages();
                                                if (isset($message[0])) {
                                                    $this->flashSession->error($message[0]->getMessage());
                                                } else {
                                                    $this->flashSession->error('Lỗi, không thể cập nhật danh mục bài viết.');
                                                }
                                            }
                                        } else {
                                            foreach ($ac as $key => $item) {
                                                if ($article_category->category_id == $item) {
                                                    unset($ac[$key]);
                                                } 
                                            }
                                        }
                                    }
                                    
                                }
                                
                                
                                
                                foreach ($ac as $item) {
                                    $article_category = new ArticleCategory();
                                    $article_category->article_id = $article->id;
                                    $article_category->category_id = $item;
                                    if (!$article_category->create()) {
                                        $message = $article_category->getMessages();
                                        var_dump($message[0]);
                                        if (isset($message[0])) {
                                            $this->flashSession->error($message[0]->getMessage());
                                        } else {
                                            $this->flashSession->error('Lỗi, không thể cập nhật danh mục bài viết.');
                                        }
                                    }
                                }
                            } 
                            
                            if ($attribute) {
                                foreach ($attribute as $item) {
                                    if($this->request->getPost($item->slug)) {
                                        $article_attribute = new ArticleAttribute();
                                        $article_attribute->article_id = $article->id;
                                        $article_attribute->attribute_id = $item->id;
                                        $article_attribute->attribute_value = $this->request->getPost($item->slug);
                                        $article_attribute->update();
                                    }
                                }
                            }
                            
                            $this->db->commit();
                        }
                        
                        if ($cache_name != '') {
                            $this->cache->delete($cache_name);
                        }
                        
                        if ($category_layout_cache_name != '') {
                            $this->cache->delete($category_layout_cache_name);
                        }
                        
                        $query = array(
                            'id' => $id,
                            'page' => $page,
                            'q' => $q
                        );
                        $this->flashSession->success('Cập nhật thành công.');
                        return $this->response->redirect(array('for' => 'article_edit', 'query' => '?' . http_build_query($query)));
                    } catch (Exception $e) {
                        $this->db->rollback();
                        $this->logger->log('[ArticleController][editAction] ' . $e->getMessage(), Logger::ERROR);
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }
        
        $page_header = 'Sửa bài viết';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách bài viết', 'url' => $this->url->get(array('for' => 'category')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');
        
        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'cache_name' => $cache_name,
            'form' => $form,
            'article' => $article,
            'category_layout' => $category_layout,
            'attribute' => $attribute,
            'article_attachments' => isset($article_attachments) ? $article_attachments : ''
        ));
        $this->view->pick('article/edit');
    }
    
    /**
     * @author Vu.Tran
     */
    public function addAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');
        
        $user = $this->session->get('USER');

        if($user['type'] == 'EDITOR')
        {
            $user_categories = UserCategory::find(array(
                'conditions' => 'user_id = :id:',
                'bind' => array('id' => $user['id'])
            ));

            $in_array_userCat = array();
            foreach ($user_categories as $item) {
                $in_array_userCat[] = $item->category_id; 
            }
        }
        
        $attribute = Attribute::find();
 
        $article = new Article();
        $form = new ArticleForm($article);
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $article); 
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                if (!$this->request->getPost('category') && empty($this->request->getPost('category'))) {
                   $this->flashSession->error('Vui lòng chọn danh mục.'); 
                } else {
                    $article->title = Util::upperFirstLetter($this->request->getPost('title'));
                    $article->alias = Util::slug($this->request->getPost('title'));
                    $article->intro = $this->request->getPost('intro'); 
                    $article->hits = 0;
                    $article->show_comment = 0;
                    $article->type = $this->request->getPost('type', array('trim', 'striptags'), ''); 
                    $article->comment_count = 0;
                    $article->ordering = 0;
                    $article->created_at = time();
                    $article->updated_at = time();  
                    $article->created_by = Util::numberOnly($user['id']);
                    $article->updated_by = Util::numberOnly($user['id']);   
                    $article->created_ip = Util::getRealIpAddress();
                    $article->module = Constant::MODULE_ARTICLES;
                    if ($this->request->hasFiles()) {
                        $file = $this->request->getUploadedFiles();

                        if (isset($file[0])) {
                            $resource = array(
                                'name' => $file[0]->getName(),
                                'type' => $file[0]->getType(),
                                'tmp_name' => $file[0]->getTempName(),
                                'error' => $file[0]->getError(),
                                'size' => $file[0]->getSize()
                            );

                            $response = parent::uploadLocalImage(ROOT . '/web/admin/asset/images/', '', '', $resource);
                            if (!empty($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
                                $article->image = $response['result'];
                                parent::uploadRemoteImage(ROOT . '/web/admin/asset/images/', 'default', $article->image);
                                parent::deleteLocalImage(ROOT . '/web/admin/asset/images/', $article->image);
                            }
                            
                            try {
                                $file = new File();
                                $file->title = Util::upperFirstLetter($this->request->getPost('title'));
                                $file->category_id = Constant::CATEGORY_DEFAULT;
                                $file->created_at = time();
                                $file->updated_at = time();
                                $file->created_by = $user['id'];
                                $file->updated_by = $user['id'];
                                $file->created_ip = $this->request->getClientAddress();
                                $file->file_name = $article->image;
                                $file->file_type = $resource['type'];
                                $file->file_size = $resource['size'];             
                                if (!$file->create()) {
                                    $message = $file->getMessages();
                                    if (isset($message[0])) {
                                        $this->flashSession->error($message[0]->getMessage());
                                    } else {
                                        $this->flashSession->error('Lỗi, không thể thêm hình ảnh vào quản lý file.');
                                    }
                                }
                            } catch (Exception $e) {
                                $this->logger->log('[ArticleController][editAction] ' . $e->getMessage(), Logger::ERROR);
                                throw new Exception($e->getMessage());
                            }
                        }
                    }
                    
                    $this->db->begin();
                    try {
                        if (!$article->create()) {
                            $message = $article->getMessages();
                            if (isset($message[0])) {
                                $this->flashSession->error($message[0]->getMessage());
                            } else {
                                $this->flashSession->error('Lỗi, không thể thêm.');
                            }
                        } else {
                            $article_content = new ArticleContent();
                            $article_content->article_id = $article->id;
                            $article_content->content = Util::filterHtmlTag($this->request->getPost('content'));
                            $article_content->meta_title = $this->request->getPost('meta_title');
                            $article_content->meta_description = $this->request->getPost('meta_description');
                            $article_content->meta_keyword = $this->request->getPost('meta_keyword');
                            // 
                            $article_content->title_content_h2 = $this->request->getPost('title_content_h2');
                            $article_content->title_content_h3 = $this->request->getPost('title_content_h3');
                            $article_content->content2_h2 = Util::filterHtmlTag($this->request->getPost('content2_h2'));
                            $article_content->content2_h3 = Util::filterHtmlTag($this->request->getPost('content2_h3'));
                            // 
                            $article_content->create();

                            $article_fulltext = new ArticleFulltext();
                            $article_fulltext->article_id = $article->id;
                            $article_fulltext->title = Util::strClearMark($this->request->getPost('title'));
                            $article_fulltext->content = Util::strClearMark($this->request->getPost('content'));
                            $article_fulltext->create(); 
                                
                            if ($this->request->getPost('tags')) {
                                $tags = explode(',', $this->request->getPost('tags'));
                                foreach ($tags as $item) {
                                    $tag_slug = Util::slug($item);
                                    $tag = Tag::findFirst(array(
                                        'conditions' => 'slug = :slug:',
                                        'bind' => array('slug' => $tag_slug)
                                    ));
                                    if ($tag) {
                                        $article_tag = new ArticleTag();
                                        $article_tag->article_id = $article->id;
                                        $article_tag->tag_id = $tag->id;
                                        $article_tag->create();
                                    } else {
                                        $tag = new Tag();
                                        $tag->title = $item;
                                        $tag->slug = $tag_slug;
                                        $tag->created_at = time();
                                        $tag->create();

                                        $article_tag = new ArticleTag();
                                        $article_tag->article_id = $article->id;
                                        $article_tag->tag_id = $tag->id;
                                        $article_tag->create();
                                    }
                                } 
                            }
                            
                            if ($this->request->getPost('category')) {
                                $acx = $this->request->getPost('category');
                                foreach ($acx as $item) {
                                    $article_category = new ArticleCategory();
                                    $article_category->article_id = $article->id;
                                    $article_category->category_id = $item;
                                    $article_category->create();
                                }
                            }
                            
                            if ($attribute) {
                                foreach ($attribute as $item):
                                    if($this->request->getPost($item->slug)) {
                                        $article_attribute = new ArticleAttribute();
                                        $article_attribute->article_id = $article->id;
                                        $article_attribute->attribute_id = $item->id;
                                        $article_attribute->attribute_value = $this->request->getPost($item->slug);
                                        $article_attribute->create();
                                    }
                                endforeach;
                            }
                            
                            
                            $this->db->commit();
                            
                            if ($cache_name != '') {
                                $this->cache->delete($cache_name);
                            }
                            
                            apc_clear_cache('user');
                        } 
                        
                        $query = array(
                            'id' => $article->id, 
                            'page' => $page,
                            'q' => $q,
                            'cache_name' => $cache_name
                        );
                        $this->flashSession->success('Thêm bài viết thành công.');
                        return $this->response->redirect(array('for' => 'article_edit', 'query' => '?' . http_build_query($query)));
                    } catch (Exception $e) {
                        $this->db->rollback();
                        $this->logger->log('[ArticleController][addAction] ' . $e->getMessage(), Logger::ERROR);
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }
        
        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_ARTICLES,
                'parent_id' => (int)0
            ),
        );
        $no_categories_cache_name = md5(serialize(array(
            'ArticleController',
            'addAction',
            'CategoryRepository',
            'getList',
             $params
        )));

        $categories = $this->cache->get($no_categories_cache_name);
        if (!$categories) {
            $category_repository = new CategoryRepository();
            $categories = $category_repository->getList($params);
            $this->cache->save($no_categories_cache_name, $categories);
        }
        
        $category_component = new CategoryComponent();
        
        $in_array = array();
        
        $params = array();
        $no_category_layout_cache_name = md5(serialize(array(
            'ArticleController',
            'addAction',
            $params
        )));
        
        $no_category_article_layout = $this->cache->get($no_category_layout_cache_name);
        if (!$no_category_article_layout) {
            $no_category_article_layout = '';
            $level = '';
            foreach ($categories as $item) {

                $active = '';
                if (in_array($item->id, $in_array)) {
                    $active = 'checked="checked"';
                }

                if($user['type'] == 'EDITOR')
                {
                    if (in_array($item->id, $in_array_userCat)) {
                        $no_category_article_layout .= '<div class="checkbox">' . '<input type="checkbox" name="category[]" value="' . $item->id . '" class="red" ' . $active . '>' . $item->name . '</div>';
                    } 
                }
                else
                {
                    $no_category_article_layout .= '<div class="checkbox">' . '<input type="checkbox" name="category[]" value="' . $item->id . '" class="red" ' . $active . '>' . $item->name . '</div>';
                }
                
                $params = array(
                    'conditions' => array(
                        'module' => Constant::MODULE_ARTICLES,
                        'parent_id' => $item->id,
                    ),
                );
                $sub_category_layout = '';
                $no_category_article_layout .= $category_component->sub_checkbox($params, $sub_category_layout, $level, $in_array);

            }
            $this->cache->save($no_category_layout_cache_name, $no_category_article_layout);
        }

        $page_header = 'Thêm bài viết';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách bài viết', 'url' => $this->url->get(array('for' => 'article')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form,
            'article' => $article,
            'categories' => $categories,
            'attribute' => $attribute,
            'category_layout' => $no_category_article_layout,
            'article_attachments' => isset($article_attachments) ? $article_attachments : ''
        ));
        $this->view->pick('article/add');
    }
    
    /**
     * @author Vu.Tran
     */
    public function deleteImageAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1); 
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $article = Article::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$article) {
            throw new Exception('Bài viết này không tồn tại.');
        }

        parent::deleteRemoteImage('default', $article->image);
        $article->image = null;
        $article->created_ip = $this->request->getClientAddress();
        try {
            if (!$article->update()) {
                $message = $article->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể xóa hình ảnh.');
                }
            } else {
                if ($cache_name != '') {
                    $this->cache->delete($cache_name);
                }

                $this->flashSession->success('Xóa hình ảnh thành công.');
            }

            $query = array(
                'id' => $id,
                'page' => $page,
                'q' => $q,
                'cache_name' => $cache_name
            );

            return $this->response->redirect(array('for' => 'article_edit', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @author Vu.Tran
     */
    public function deleteAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');
        
        $article = Article::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$article) {
            throw new Exception('Danh mục này không tồn tại.');
        }

        $article->status = Constant::STATUS_ARCHIVED;

        try {
            if (!$article->update()) {
                $message = $article->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể xóa.');
                }
            } else {
                if ($cache_name != '') {
                    $this->cache->delete($cache_name);
                }

                $this->flashSession->success('Xóa thành công.');
            }

            $query = array(
                'page' => $page,
                'q' => $q
            );

            return $this->response->redirect(array('for' => 'article', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}