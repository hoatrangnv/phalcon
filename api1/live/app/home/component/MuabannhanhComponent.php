<?php
namespace ITECH\Home\Component;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View\Simple as View;
use ITECH\Datasource\Lib\Util;

class MuabannhanhComponent extends Component{

    private $domain = "https://api.muabannhanh.com/";
    private $urlListCate="category/list";
    private $urltag="tag/list";
    private $urlListPostCate = "article/list";
    private $urlSinglePost = "article/detail";
    private $urlSingleCategory = "category/detail";
    private $urlSingleUser = "user/detail";
    private $name = "muabannhanh";
    private $sessionToken = 'cb2663ce82a9f4ba448ba435091e27bb';
    private $elasticUrl = 'http://125.212.247.153:9220/muabannhanh/product/_search';
    private $elasticUrlPost = 'http://125.212.247.153:9220/muabannhanh/product/';
    private $limitdm = array(170,342,346);

    public  function getListPost($controller, array $params){

        $data = "";
        $params['sort_field'] = 'updated_at';
        $params['session_token'] = $this->sessionToken;

        $cache_name = md5(serialize(array(
            'MuabannhanhComponent',
            'category',
            'getList',
            $params
        )));                    
        foreach ($this->limitdm as $key => $value) {
           $categoryy.= '{ "term": { "category.id":"'.$value.'" } },' ;
        }
        if(isset($categoryy)){
            $len=strlen(trim($categoryy));
            $len=$len-1;
            $limitcategory = substr($categoryy,0,$len);
        }
        
        $data = $controller->cache->get($cache_name);
        if(!$data){
            $util = new Util();

            $res = $this->searchElastic(array("limit" => '20', "page" => $params["page"],
             "filter" => '{"term": {"user.id": '. $params['user_id'] .'}},
                          {"or":
                            [   
                                '.$limitcategory.'
                            ]
                        },
                ' ));
            $data['result'] = $res['hits']['hits'];
            $data['total_item'] = $res['hits']['total'];
            $data['total_pages'] = ceil($res['hits']['total'] / 20);

            if (count($data->result)) {
                $controller->cache->save($cache_name, $data);
            }
        }
        return $data;
    }

    public  function getDetailUser(array $params){

        $data = array();
        $params['session_token'] = $this->sessionToken;
        $cache_name = md5(serialize(array(
            'MuabannhanhComponent',
            'Detail',
            'getDetailUser',
            $params
        )));
        $data = $this->cache->get($cache_name);

        // var_dump($data);exit;

        if(!$data){
            $util = new Util();
            $data =  json_decode( $util->curlGet($this->domain.$this->urlSingleUser , $params));
            if (count($data->result)) {
                $this->cache->save($cache_name, $data);
            }
        }
        if ($data->status == 200) {
            return $data;
        } else {
            return false;
        }
    }

    public  function getDetailPost($controller, array $params){

        $data = false;

        $params['session_token'] = $this->sessionToken;
        $cache_name = md5(serialize(array(
            'MuabannhanhComponent',
            'Detail',
            'getDetail',
            $params
        )));
        $data = $controller->cache->get($cache_name);
        if(!$data){
            $util = new Util();

            $data =  json_decode( $util->curlGet($this->elasticUrlPost . $params['id']) );
            $data = $data->_source;
            $controller->cache->save($cache_name, $data);
        }

        $related = false;
        if ($data) {
            $post = $data;

            $params = array(
                "limit" => "0",
                "aggs" => '
                        "user":{

                            "filter": {
                                
                                "term" : { "user.id" :  "'. $post->user->id .'" }
                            },
                            "aggs": {
                               "top_tag_hits": {
                                   "top_hits": {
                                       "sort": [
                                           {"time_updated_at": { "order": "desc" } }
                                       ],
                                       "size" : 8,
                                       "from": 0
                                   }
                               }
                            }
                        },
                        "related": {
                            "filter": {
                                "term" : { "category.id" : "'. $post->category->id .'" }
                            },
                            "aggs": {
                               "top_tag_hits": {
                                   "top_hits": {
                                       "sort": [
                                           {"time_updated_at": { "order": "desc" } }
                                       ],
                                       "size" : 8,
                                       "from": 0
                                   }
                               }
                            }
                        },
                    ');
            
            $res = $this->searchElastic($params);
            $related['user'] = $res['aggregations']['user']['top_tag_hits']['hits']['hits'];
            $related['category'] = $res['aggregations']['related']['top_tag_hits']['hits']['hits'];
        }

        $util = new Util();
        $token = '89d5c70f11e35817110eeeb7863e3ef0';
        $url = $this->domain . 'article/view-count?id=' . $data->id . '&token=' . $token . '&session_token=' . $this->sessionToken;
        $util->curlGet($url);

        return array('article' => $data, 'related' => $related);
    }

    public  function getCategory($params = array()){
        $data = array();
        $params['session_token'] = $this->sessionToken;

        $cache_name = md5(serialize(array(
            'MuabannhanhComponent',
            'List',
            'getCategory',
            $params
        )));
        $data = $this->cache->get($cache_name);

        if(!$data){
            $util = new Util();
            $data =  json_decode( $util->curlGet($this->domain.$this->urlListCate, $params));

            if (count($data->result) > 0 && $data->message == 200) {
                $this->cache->save($cache_name, $data);
            }
        }

        return $data->result;
    }


    public  function getDetailCategory($params = array()){
        $data = array();
        $params['session_token'] = $this->sessionToken;

        $cache_name = md5(serialize(array(
            'MuabannhanhComponent',
            'Detail',
            'getDetailCategory',
            $params
        )));
        $data = $this->cache->get($cache_name);

        if(!$data){
            $util = new Util();
            $data =  json_decode( $util->curlGet($this->domain.$this->urlSingleCategory, $params));
            if (count($data->result) > 0 && $data->message == 200) {
                $this->cache->save($cache_name, $data);
            }
        }

        return $data->result;
    }

    public function pagination($controller, $theme, array $params){

        $view = new View();
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/muabannhanh/component/');
        $view->render('pagination', array(
            'total_page' => $params['total_page'],
            'current_page' => $params['paged'],
            'url' => $params['url']
        ));

        return $view->getContent();
    }

    public function getNewApi($controller, $theme, $params = array(), $title='true'){
        $r = Util::curlGet($this->domain . $this->urlListCate . '?session_token=cb2663ce82a9f4ba448ba435091e27bb');
        $r = json_decode($r, true);

        $article = array();
        $data = array();
        $args = array(
            'page' => isset($params['page']) && $params['page'] ? $params['page'] : 1,
            'limit' => 8,
            'user_membership' => $this->type_article,
            'sort_field' => 'updated_at',
            'sort_by' => 'DESC',
            'session_token' => $this->sessionToken

        );

        if (isset($params['in_category']) && count($params['in_category']) > 0){
            foreach ($params['in_category'] as $category){
                $paramsCategory['id'] = $category;
                $data[$category]['self'] = $this->getDetailCategory($paramsCategory);

                if ($data[$category]['self']->id) {
                    $_category = $data[$category]['self'];

                    $_args = $args;
                    if ($_category->parent_id == 0) {
                        $_args['parent_category_id'] = $_category->id;
                    } else {
                        $_args['category_id'] = $_category->id;
                    }

                    $cache_name = md5(serialize(array(
                            'MuabannhanhComponent',
                            'category',
                            'getList',
                            $_args
                    )));
                    $article = $controller->cache->get($cache_name);

                    if(!$article){
                        $util = new Util();
                        $article =  json_decode( $util->curlGet($this->domain.$this->urlListPostCate , $_args));
                        if (count($article->result) && $article->status == 200) {
                            $controller->cache->save($cache_name, $article);
                        }
                    }
                    $data[$data[$_category->id]['self']->id]['posts'] = $article;

                    if (isset($params['pagination']) && $params['pagination']) {
                        $pagination = "";
                        if ($article->status == 200) {
                            $argsPagination = array(
                                    'paged' => isset($params['page']) && $params['page'] ? $params['page'] : 1,
                                    'total_page' => $article->total_pages,
                                    'url' => $this->url->get(array(
                                                'for' => 'mbn_list',
                                                'id'=>$_category->id,
                                                'slug'=>$_category->slug)
                                            ));
                            $pagination = $this->pagination($controller, $theme, $argsPagination);
                        }
                    }
                }
            }
        } else if ($this->category_default != 0){
            $paramsCategory['id'] = $this->category_default;
            $data[0]['self'] = $this->getDetailCategory($paramsCategory);

            if ($data[0]['self']->id) {
                $_args = $args;
                if ($data[0]['self']->parent_id == 0) {
                    $_args['parent_category_id'] = $data[0]['self']->id;
                } else {
                    $_args['category_id'] = $data[0]['self']->id;
                }

                $cache_name = md5(serialize(array(
                        'MuabannhanhComponent',
                        'category',
                        'getList',
                        $_args
                )));
                $article = $controller->cache->get($cache_name);

                if(!$article){
                    $util = new Util();
                    $article =  json_decode( $util->curlGet($this->domain.$this->urlListPostCate , $_args));
                    if (count($article->result) && $article->status == 200) {
                        $controller->cache->save($cache_name, $article);
                    }
                }
                $data[0]['posts'] = $article;
            }

        } else {
            $_args = $args;
            $cache_name = md5(serialize(array(
                    'MuabannhanhComponent',
                    'category',
                    'getList',
                    $_args
            )));
            $article = $controller->cache->get($cache_name);

            if(!$article){
                $util = new Util();
                $article =  json_decode( $util->curlGet($this->domain.$this->urlListPostCate , $_args));
                if (count($article->result) && $article->status == 200) {
                    $controller->cache->save($cache_name, $article);
                }
            }
            $data[0]['posts'] = $article;

            if (isset($params['pagination']) && $params['pagination']) {
                $pagination = "";
                if ($article->status == 200) {
                    $argsPagination = array(
                            'paged' => isset($params['page']) && $params['page'] ? $params['page'] : 1,
                            'total_page' => $article->total_pages,
                            'url' => $this->url->get(array(
                                        'for' => 'mbn_home',
                                        'id'=>$_category->id,
                                        'slug'=>$_category->slug)
                                    ));
                    $pagination = $this->pagination($controller, $theme, $argsPagination);
                }
            }
            $data[0]['pagination'] = $pagination;
        }

        $view = new View();
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/muabannhanh/component/');
        $view->render('new_list', array(
            'data' => $data,
            'title'=>$title,
            'theme' => $theme,
            'r' => $r,
            'pagination' => $pagination
        ));

        return $view->getContent();
    }


    public function getByElastic($controller, $theme, $categoriesList)
    {
        $cache_name = md5(serialize(array(
            'MuabannhanhComponent',
            'getAllCategory',
            'getByElastic'
        )));

        $categories = $controller->cache->get($cache_name);
        if (!$categories) {
            $r = Util::curlGet($this->domain . $this->urlListCate . '?session_token=cb2663ce82a9f4ba448ba435091e27bb');
            $r = json_decode($r, true);
            if ($r['status'] == 200) {
                $categories = $r['result'];
                $controller->cache->save($cache_name, $categories);
            }
        }


        $listCompress = array();
        $aggs = '';
        foreach ($categoriesList as $key => $value) {
            foreach ($categories as $_key => $_value) {
                if ($_value['id'] == $value) {
                    $listCompress[$value]['self'] = $_value;
                    $listCompress[$value]['self']['level'] = 1;
                    if ($_value['parent_id'] == 0) {
                        $listCompress[$value]['self']['level'] = 0;
                    }
                }

                foreach ($_value['sub_category'] as $__key => $__value) {
                    if ($__value['id'] == $value) {
                        $listCompress[$value]['self'] = $__value;
                        $listCompress[$value]['self']['level'] = 1;
                        if ($_value['parent_id'] == 0) {
                            $listCompress[$value]['self']['level'] = 0;
                        }
                    }
                }

            }

            $aggs .= '
                "' . $value . '": {
                   "filter" : {
                       "or": {
                           "filters": [
                               {"term": { "parent_category.id": ' . $value . ' } },
                               {"term": { "category.id": ' . $value . ' } }
                           ]
                       }
                   },
                   "aggs": {
                   "top_tag_hits": {
                       "top_hits": {
                           "sort": [
                               {"time_updated_at": { "order": "desc" } }
                           ],
                           "size" : 12,
                           "from": 0
                           }
                       }
                   }
               },';
        }
        $res = $this->searchElastic(array("limit" => '0', "aggs" => $aggs ));
        foreach ($listCompress as $key => $value) {
            $id = $value['self']['id'];
            $listCompress[$id]['posts'] = $res['aggregations'][$id]['top_tag_hits']['hits']['hits'];
            $listCompress[$id]['total_item'] = $res['aggregations'][$id]['top_tag_hits']['hits']['total'];
        }


        $view = new View();
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/muabannhanh/component/');
        $view->render('list_by_category_elastic', array(
            'data' => $listCompress,
            'theme' => $theme,
        ));
        return $view->getContent();
    }

    public function getByElasticCategory($controller, $theme, $id, $page = 1)
    {
        $cache_name = md5(serialize(array(
            'MuabannhanhComponent',
            'getAllCategory',
            'getByElastic'
        )));

        $categories = $controller->cache->get($cache_name);
        if (!$categories) {
            $r = Util::curlGet($this->domain . $this->urlListCate . '?session_token=cb2663ce82a9f4ba448ba435091e27bb');
            $r = json_decode($r, true);
            if ($r['status'] == 200) {
                $categories = $r['result'];
                $controller->cache->save($cache_name, $categories);
            }
        }


        $listCompress = array();

        foreach ($categories as $_key => $_value) {
            if ($_value['id'] == $id) {
                $listCompress[$id]['self'] = $_value;
                $listCompress[$id]['self']['level'] = 0;
            }

            foreach ($_value['sub_category'] as $__key => $__value) {
                if ($__value['id'] == $id) {
                    $__value['sub_category'] = $_value['sub_category'];
                    $listCompress[$id]['self'] = $__value;
                    $listCompress[$id]['self']['level'] = 1;
                }
            }
        }

        $res = $this->searchElastic(array(
            "limit" => '12',
            "page" => $page,
            "filter_or" => '{"term": {"category.id": '. $id .'}},{"term": {"parent_category.id": '. $id .'}}'
        ));

        $listCompress[$id]['posts'] = $res['hits']['hits'];
        $listCompress[$id]['total_item'] = $res['hits']['total'];
        if($listCompress[$id]['self']['slug'] == 'may-in-ky-thuat-so'){
            $phantrang = 'mayinquangcao';
        }else{
             $phantrang = $listCompress[$id]['self']['slug'];
        }
        $pagination = "";
        $argsPagination = array(
            'paged' => isset($page) && $page ? $page : 1,
            'total_page' => ($listCompress[$id]['total_item'] / 12) ,
            'url' => $this->url->get(array(
                'for' => 'mbn_list',
                'id'=>$id,
                'slug'=>str_replace('-','', $phantrang))
            ));
        $pagination = $this->pagination($controller, $theme, $argsPagination);

        $view = new View();
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/muabannhanh/component/');
        $view->render('list_by_category_elastic_pagination', array(
            'data' => $listCompress,
            'theme' => $theme,
            'pagination' => $pagination
        ));
        return $view->getContent();
    }

    /*thien by*/
    public function getByElasticTag($controller, $theme, $id,$page = 1)
    {

        $res = $this->searchElastic(array(
            "limit" => '12',
            "page" => $page,
            "filter" => '{"or":
                [
                    { "term": { "tag.user.id":'.$id.' } },
                    { "term": { "tag.admin.id":'.$id.' } }
                ]
            },',
            "filter_or" => '{"term": {"user.membership_value":"22"}},{"term": {"user.membership_value":"23"}}'
        ));
        // var_dump($res);exit;
        $listCompress[$id]['self']['slug'] = 'xe-oto-kia';
        $listCompress[$id]['posts'] = $res['hits']['hits'];
        $listCompress[$id]['total_item'] = $res['hits']['total'];
        $pagination = "";
        $argsPagination = array(
            'paged' => isset($page) && $page ? $page : 1,
            'total_page' => ($listCompress[$id]['total_item'] / 12) ,
            'url' => $this->url->get(array(
                'for' => 'mbn_listtag',
                'id'=>$id,
                'slug'=>$listCompress[$id]['self']['slug'])
            ));
        $pagination = $this->pagination($controller, $theme, $argsPagination);
        $view = new View();
        $view->setViewsDir(ROOT . '/app/home/view/' . $theme . '/muabannhanh/component/');
        $view->render('list_by_category_elastic_tag', array(
            'data' => $listCompress,
            'theme' => $theme,
            'pagination' => $pagination
        ));
        return $view->getContent();
    }
    /*
     * @Author: Minh.Tran
     *
     * $params
     */
    public function searchElastic($params = array()) {

        $url = $this->elasticUrl;

        $_query = '';
        if (isset($params['request']) && $params['request'] != '') {

            $_content = '';
            if (isset($params['content']) && $params['content'] == 1) {
                $_content = '{ "match_phrase": { "description": "'. $params['request'] .'" } },';
            }

            $_query = '
            "query": {
               "bool": {
                   "should": [
                       { "match_phrase": { "name": "'. $params['request'] .'" } },
                       { "match_phrase": { "address": "'. $params['request'] .'" } },
                       '. $_content .'
                       { "match_phrase": { "gallery": "'. $params['request'] .'" } },
                       { "match_phrase": { "district.name": "'. $params['request'] .'" } },
                       { "match_phrase": { "province.name": "'. $params['request'] .'" } },
                       { "match_phrase": { "user.name": "'. $params['request'] .'" } },
                       { "match_phrase_prefix": { "user.phone_number": "'. $params['request'] .'" } },
                       { "match_phrase": { "tag.admin.name": "'. $params['request'] .'" } },
                       { "match_phrase": { "tag.user.name": "'. $params['request'] .'" } },
                       { "match_phrase": { "category.name": "'. $params['request'] .'" } }
                   ]
               }
            },';
        }

        $sort = '"sort" : [
            { "time_updated_at" : {"order" : "desc"} }
        ],';
        if (isset($params['sort']) && $params['sort'] != '') {
            $sort = $params['sort'];
        }

        $page = 1;
        if (isset($params['page']) && $params['page'] != '') {
            $page = $params['page'];
        }

        $limit = 20;
        if (isset($params['limit']) && $params['limit'] != '') {
            $limit = $params['limit'];
        }

        $filter = '';
        if (isset($params['filter']) && $params['filter'] != '') {
            $filter = $params['filter'];
        }

        $filterOr = '';
        if (isset($params['filter_or']) && $params['filter_or'] != '') {
            $filterOr = $params['filter_or'];
        }

        $aggs = '';
        if (isset($params['aggs']) && $params['aggs'] != '') {
            $aggs = $params['aggs'];
        }

        $queryString = '
        {
            "query": {
                "filtered": {
                    '. $_query .'
                    "filter" : {
                        "bool": {
                            "must": [
                                '. $filter .'
                                {"or":
                                    [
                                        { "term": { "user.membership_value":"22" } },
                                        { "term": { "user.membership_value":"23" } }
                                    ]
                                },
                                {"term" : {"status_value": "1"} },
                                {"term" : {"is_shown": "1"} }
                            ],
                            "should": [
                                 '. $filterOr .'
                            ]
                        }
                    }
                }
            },
            '. $sort .'
            "aggs": {
                '. $aggs .'
                "province": {
                    "terms": {
                        "field": "province.name.raw"
                    },
                    "aggs": {
                        "id": {
                            "terms": {
                                "field": "province.id"
                            }
                        }
                    }
                }
            },
            "size": '. $limit .',
            "from": '. ($limit * $page - $limit) .'
        }';

        $r = Util::curlGetPostJson($url, $queryString);
        $response = json_decode($r, true);
        $response['meta'] = $queryString;
        return $response;
    }
}
