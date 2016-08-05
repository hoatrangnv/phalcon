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


class SitemapController extends \Phalcon\Mvc\Controller
{

    public function initialize()
    {
        
        set_time_limit(0); 

    }

    // public function indexAction()
    // {
    //     $domain = $this->config->application->base_url;

    //     $response = new Response();

    //     $expireDate = new \DateTime();
    //     $expireDate->modify('+1 day');

    //     $response->setExpires($expireDate);

    //     $response->setHeader('Content-Type', "application/xml; charset=UTF-8");

    //     $sitemap = new \DOMDocument("1.0", "UTF-8");

    //     $urlset = $sitemap->createElement('urlset');
    //     $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    //     $urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    //     $urlset->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');


    //     $links = array();


    //     $modifiedAt = new \DateTime();
    //     $modifiedAt->setTimezone(new \DateTimeZone('UTC'));
        
    //     $comment = $sitemap->createComment(' Last update of sitemap ' . date("Y-m-d H:i:s").' ');
        
    //     $urlset->appendChild($comment);

    //     foreach ($links as $link) {

    //         $url = $sitemap->createElement('url');
    //         $href = $this->domain.$link;
    //         $url->appendChild($sitemap->createElement('loc', $href));
    //         $url->appendChild($sitemap->createElement('changefreq', 'daily')); //Hourly, daily, weekly etc.
    //         $url->appendChild($sitemap->createElement('priority', '0.5'));     //1, 0.7, 0.5 ...

    //         $urlset->appendChild($url);
    //     }

    //     $sitemap->appendChild($urlset);

    //     $response->setContent($sitemap->saveXML());
    //     return $response;
    // }

    public function indexAction()
        {
            
            $response = new \Phalcon\Http\Response();
            $expireDate = new \DateTime();
            $expireDate->modify('+1 day');

            $response->setExpires($expireDate);

            $response->setHeader('Content-Type', "application/xml; charset=UTF-8");

            $sitemap = new \DOMDocument("1.0", "UTF-8");

            $urlset = $sitemap->createElement('urlset');
            $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

            $baseUrl = $this->config->application->base_url;

            $url = $sitemap->createElement('url');
            $url->appendChild($sitemap->createElement('loc', $baseUrl));
            $url->appendChild($sitemap->createElement('changefreq', 'daily'));
            $url->appendChild($sitemap->createElement('priority', '1.0'));
            $urlset->appendChild($url);

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

            var_dump($categories);exit;
            // $posts = Posts::find(["order" => "modified_at DESC","limit"=>20]);

            $modifiedAt = new \DateTime();
            $modifiedAt->setTimezone(new \DateTimeZone('UTC'));

            foreach ($posts as $post) {

                $modifiedAt->setTimestamp($post->modified_at);

                $url = $sitemap->createElement('url');
                $href = $baseUrl . '/discussion/' . $post->id . '/' . $post->slug;
                $url->appendChild(
                    $sitemap->createElement('loc', $href)
                );

                $url->appendChild($sitemap->createElement('lastmod', $modifiedAt->format('Y-m-d\TH:i:s\Z')));
                $urlset->appendChild($url);
            }

            $sitemap->appendChild($urlset);

            $response->setContent($sitemap->saveXML());
            return $response;
        }
}