<?php

class CronjobTask extends \Phalcon\CLI\Task {
    public function cleanupAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][cleanupAction]' . "\n";

        $files = glob(ROOT . '/web/frontend/uploads/image/cache/*');
        if (count($files)) {
            foreach ($files as $file) {
                if (file_exists($file) && basename($file) != 'index.html' && filemtime($file) < strtotime('-1 hour')) {
                    @chmod($file, 0777);
                    @unlink($file);
                }
            }
        }

        $files = glob(ROOT . '/web/frontend/uploads/image/uploads/*');
        if (count($files)) {
            foreach ($files as $file) {
                if (file_exists($file) && basename($file) != '.gitkeep' && filemtime($file) < strtotime('-1 hour')) {
                    @chmod($file, 0777);
                    @unlink($file);
                }
            }
        }

        $files = glob(ROOT . '/web/backend/uploads/*');
        if (count($files)) {
            foreach ($files as $file) {
                if (file_exists($file) && basename($file) != '.gitkeep' && filemtime($file) < strtotime('-1 hour')) {
                    @chmod($file, 0777);
                    @unlink($file);
                }
            }
        }

        $files = glob(ROOT . '/web/api/uploads/*');
        if (count($files)) {
            foreach ($files as $file) {
                if (file_exists($file) && basename($file) != '.gitkeep' && filemtime($file) < strtotime('-1 hour')) {
                    @chmod($file, 0777);
                    @unlink($file);
                }
            }
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }

    public function vipUserExpireAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][vipUserExpire]' . "\n";

        $user = new \Fast\Data\Model\User();

        $q = 'UPDATE user
            SET user.type = "' . \Fast\Data\Lib\Constant::USER_TYPE_BASIC . '"
            WHERE user.type = "' . \Fast\Data\Lib\Constant::USER_TYPE_VIP . '"
                AND user.vip_expired_at <= "' . date('Y-m-d H:i:s') . '"';

        $user->getWriteConnection()->query($q);

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }

    public function categoryAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][categoryAction]' . "\n";

        $category = new \Fast\Data\Model\Category();
        $b = $category->getModelsManager()->createBuilder();
        $b->columns(array(
            'c.id',
            'c.parent_id',
            'c.name',
            'c.slug',
            'c.article_count'
        ));
        $b->from(array('c' => 'Fast\Data\Model\Category'));
        $b->andWhere('c.parent_id <> 0');
        $b->andWhere('c.status = :status:', array('status' => \Fast\Data\Lib\Constant::CATEGORY_STATUS_ACTIVE));
        $categories = $b->getQuery()->execute();

        if ($categories && count($categories)) {
            foreach ($categories as $item) {
                $count = \Fast\Data\Model\Article::count(array(
                    'conditions' => 'category_id = :category_id:
                        AND is_shown = :is_shown:
                        AND status = :status:',
                    'bind' => array(
                        'category_id' => $item->id,
                        'is_shown' => \Fast\Data\Lib\Constant::ARTICLE_IS_SHOWN_YES,
                        'status' => \Fast\Data\Lib\Constant::ARTICLE_STATUS_ACTIVE
                    )
                ));

                $q = 'UPDATE Fast\Data\Model\Category
                    SET Fast\Data\Model\Category.article_count = :article_count:
                    WHERE Fast\Data\Model\Category.id = :id:';
                $b = $category->getModelsManager()->createQuery($q);
                $b->execute(array(
                    'article_count' => $count,
                    'id' => $item->id
                ));
            }

            $b = $category->getModelsManager()->createBuilder();
            $b->columns(array(
                'Fast\Data\Model\Category.id',
                'Fast\Data\Model\Category.parent_id',
                'Fast\Data\Model\Category.article_count'
            ));
            $b->from('Fast\Data\Model\Category');
            $b->andWhere('Fast\Data\Model\Category.parent_id <> 0');
            $b->andWhere('Fast\Data\Model\Category.status = :status:', array('status' => \Fast\Data\Lib\Constant::CATEGORY_STATUS_ACTIVE));
            $categories = $b->getQuery()->execute();

            $b = $category->getModelsManager()->createBuilder();
            $b->columns(array('Fast\Data\Model\Category.id'));
            $b->from('Fast\Data\Model\Category');
            $b->andWhere('Fast\Data\Model\Category.parent_id = 0');
            $b->andWhere('Fast\Data\Model\Category.status = :status:', array('status' => \Fast\Data\Lib\Constant::CATEGORY_STATUS_ACTIVE));
            $parent_categories = $b->getQuery()->execute();

            if ($parent_categories && count($parent_categories)) {
                foreach ($parent_categories as $item) {
                    $count = 0;

                    foreach ($categories as $cat) {
                        if ($cat->parent_id == $item->id) {
                            $count += $cat->article_count;
                        }
                    }

                    $q = 'UPDATE Fast\Data\Model\Category
                        SET Fast\Data\Model\Category.article_count = :article_count:
                        WHERE Fast\Data\Model\Category.id = :id:';
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

    public function categoryTagAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][categoryTagAction]' . "\n";

        $category_tag = new \Fast\Data\Model\CategoryTag();

        $b = $category_tag->getModelsManager()->createBuilder();
        $b->columns(array(
            'ct1.id',
            'ct1.category_id',
            'ct1.name',
            'ct1.slug'
        ));
        $b->from(array('ct1' => 'Fast\Data\Model\CategoryTag'));
        $b->innerJoin('Fast\Data\Model\Category', 'c1.id = ct1.category_id', 'c1');
        $b->andWhere('c1.status = :category_status:', array('category_status' => \Fast\Data\Lib\Constant::CATEGORY_STATUS_ACTIVE));

        $category_tags = $b->getQuery()->execute();
        if (count($category_tags)) {
            $article = new \Fast\Data\Model\Article();

            foreach ($category_tags as $tag) {
                $b = $article->getModelsManager()->createBuilder();
                $b->columns(array('COUNT(a1.id) AS count'));
                $b->from(array('a1' => 'Fast\Data\Model\Article'));
                $b->leftJoin('Fast\Data\Model\ArticleTag', 'at1.id = a1.id', 'at1');

                $query = array();
                $query[] = 'at1.user_tag_slug LIKE :q1:';
                $query[] = 'at1.admin_tag_slug LIKE :q2:';

                $b->andWhere(trim(implode(' OR ', $query)), array(
                    'q1' => '%' . $tag->slug . '%',
                    'q2' => '%' . $tag->slug . '%'
                ));

                $b->andWhere('a1.is_shown = :is_shown:', array('is_shown' => \Fast\Data\Lib\Constant::ARTICLE_IS_SHOWN_YES));
                $b->andWhere('a1.status = :status:', array('status' => \Fast\Data\Lib\Constant::ARTICLE_STATUS_ACTIVE));
                $r = $b->getQuery()->execute();

                if (isset($r[0]->count)) {
                    $category_tag = \Fast\Data\Model\CategoryTag::findFirst(array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => $tag->id)
                    ));
                    if ($category_tag) {
                        $category_tag->article_count = $r[0]->count;
                        $category_tag->update();
                    }
                }
            }
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }

    public function typeAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][typeAction]' . "\n";

        $type = new \Fast\Data\Model\Type();

        $b = $type->getModelsManager()->createBuilder();
        $b->columns(array('Fast\Data\Model\Type.id'));
        $b->from('Fast\Data\Model\Type');
        $types = $b->getQuery()->execute();

        if ($types && count($types)) {
            foreach ($types as $item) {
                $count = \Fast\Data\Model\Article::count(array(
                    'conditions' => 'type_id = :type_id:
                        AND is_shown = :is_shown:
                        AND status = :status:',
                    'bind' => array(
                        'type_id' => $item->id,
                        'is_shown' => \Fast\Data\Lib\Constant::ARTICLE_IS_SHOWN_YES,
                        'status' => \Fast\Data\Lib\Constant::ARTICLE_STATUS_ACTIVE
                    )
                ));

                $q = 'UPDATE Fast\Data\Model\Type
                    SET Fast\Data\Model\Type.article_count = :article_count:
                    WHERE Fast\Data\Model\Type.id = :id:';
                $b = $type->getModelsManager()->createQuery($q);
                $b->execute(array(
                    'article_count' => $count,
                    'id' => $item->id
                ));
            }
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }

    public function provinceAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][provinceAction]' . "\n";

        $province = new \Fast\Data\Model\Province();
        $b = $province->getModelsManager()->createBuilder();
        $b->columns(array('Fast\Data\Model\Province.id'));
        $b->from('Fast\Data\Model\Province');
        $provinces = $b->getQuery()->execute();

        if ($provinces && count($provinces)) {
            foreach ($provinces as $item) {
                $count = \Fast\Data\Model\Article::count(array(
                    'conditions' => 'province_id = :province_id:
                        AND is_shown = :is_shown:
                        AND status = :status:',
                    'bind' => array(
                        'province_id' => $item->id,
                        'is_shown' => \Fast\Data\Lib\Constant::ARTICLE_IS_SHOWN_YES,
                        'status' => \Fast\Data\Lib\Constant::ARTICLE_STATUS_ACTIVE
                    )
                ));

                $q = 'UPDATE Fast\Data\Model\Province
                    SET Fast\Data\Model\Province.article_count = :article_count:
                    WHERE Fast\Data\Model\Province.id = :id:';
                $b = $province->getModelsManager()->createQuery($q);
                $b->execute(array(
                    'article_count' => $count,
                    'id' => $item->id
                ));
            }
        }

        $district = new \Fast\Data\Model\District();
        $b = $district->getModelsManager()->createBuilder();
        $b->columns(array("d1.id"));
        $b->from(array("d1" => "Fast\Data\Model\District"));
        $districts = $b->getQuery()->execute();

        if ($districts && count($districts)) {
            foreach ($districts as $item) {
                $count = \Fast\Data\Model\Article::count(array(
                    "conditions" => "district_id = :district_id:
                        AND is_shown = :is_shown:
                        AND status = :status:",
                    "bind" => array(
                        "district_id" => $item->id,
                        'is_shown' => \Fast\Data\Lib\Constant::ARTICLE_IS_SHOWN_YES,
                        'status' => \Fast\Data\Lib\Constant::ARTICLE_STATUS_ACTIVE
                    )
                ));

                $q = 'UPDATE Fast\Data\Model\District
                    SET Fast\Data\Model\District.article_count = :article_count:
                    WHERE Fast\Data\Model\District.id = :id:';
                $b = $province->getModelsManager()->createQuery($q);
                $b->execute(array(
                    'article_count' => $count,
                    'id' => $item->id
                ));
            }
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }

    public function createSiteMapAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][createSiteMapAction]' . "\n";

        $category = new \Fast\Data\Model\Category();
        $b = $category->getModelsManager()->createBuilder();
        $b->columns(array(
            'c.id',
            'c.parent_id',
            'c.name',
            'c.slug',
            'c.article_count'
        ));
        $b->from(array('c' => 'Fast\Data\Model\Category'));
        $b->andWhere('c.parent_id <> 0');
        $b->andWhere('c.status = :status:', array('status' => \Fast\Data\Lib\Constant::CATEGORY_STATUS_ACTIVE));
        $categories = $b->getQuery()->execute();

        $doc = new DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('sitemapindex');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        $doc->appendChild($root);

        $u_site_map = $doc->createElement('sitemap');
        $u_loc = $u_site_map->appendChild($doc->createElement('loc'));
        $u_loc_node = $doc->createTextNode($this->config->application->frontend_url . 'sitemap-user.xml');
        $u_loc->appendChild($u_loc_node);
        $doc->appendChild($u_site_map);

        $u_lastmod = $u_site_map->appendChild($doc->createElement('lastmod'));
        $u_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $u_lastmod->appendChild($u_last_mod_node);

        $doc->appendChild($u_site_map);
        $root->appendChild($u_site_map);

        $p_site_map = $doc->createElement('sitemap');
        $p_loc = $p_site_map->appendChild($doc->createElement('loc'));
        $p_loc_node = $doc->createTextNode($this->config->application->frontend_url . 'sitemap-province.xml');
        $p_loc->appendChild($p_loc_node);
        $doc->appendChild($p_site_map);

        $p_lastmod = $p_site_map->appendChild($doc->createElement('lastmod'));
        $p_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $p_lastmod->appendChild($p_last_mod_node);

        $doc->appendChild($p_site_map);
        $root->appendChild($p_site_map);

        $c_site_map = $doc->createElement('sitemap');
        $c_loc = $c_site_map->appendChild($doc->createElement('loc'));
        $c_loc_node = $doc->createTextNode($this->config->application->frontend_url . 'sitemap-category.xml');
        $c_loc->appendChild($c_loc_node);
        $doc->appendChild($c_site_map);

        $c_lastmod = $c_site_map->appendChild($doc->createElement('lastmod'));
        $c_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $c_lastmod->appendChild($c_last_mod_node);
        $doc->appendChild($c_site_map);
        $root->appendChild($c_site_map);

        $t_site_map = $doc->createElement('sitemap');
        $t_loc = $t_site_map->appendChild($doc->createElement('loc'));
        $t_loc_node = $doc->createTextNode($this->config->application->frontend_url . 'sitemap-tag.xml');
        $t_loc->appendChild($t_loc_node);
        $doc->appendChild($t_site_map);

        $t_lastmod = $t_site_map->appendChild($doc->createElement('lastmod'));
        $t_last_mod_node = $doc->createTextNode(date('Y-m-d'));
        $t_lastmod->appendChild($t_last_mod_node);
        $doc->appendChild($t_site_map);
        $root->appendChild($t_site_map);

        $d_site_map = $doc->createElement('sitemap');
        $d_loc = $d_site_map->appendChild($doc->createElement('loc'));
        $d_loc_node = $doc->createTextNode($this->config->application->frontend_url . 'sitemap-district.xml');
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
                $loc_node = $doc->createTextNode($this->config->application->frontend_url . 'sitemap-category-' . $item->slug . '.xml');
                $loc->appendChild($loc_node);
                $doc->appendChild($site_map);

                $lastmod = $site_map->appendChild($doc->createElement('lastmod'));
                $last_mod_node = $doc->createTextNode(date('Y-m-d'));
                $lastmod->appendChild($last_mod_node);

                $doc->appendChild($site_map);
                $root->appendChild($site_map);
            }
        }


        $file = ROOT . '/web/frontend/sitemap.xml';
        if (!$doc->save($file)) {
            die('Error, cannot create XML file');
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }

    public function createCategorySiteMapAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][createCategorySiteMapAction]' . "\n";

        $category = new \Fast\Data\Model\Category();
        $b = $category->getModelsManager()->createBuilder();
        $b->columns(array(
            'c.id',
            'c.parent_id',
            'c.name',
            'c.slug',
            'c.article_count'
        ));
        $b->from(array('c' => 'Fast\Data\Model\Category'));
        $b->andWhere('c.status = :status:', array('status' => \Fast\Data\Lib\Constant::CATEGORY_STATUS_ACTIVE));
        $categories = $b->getQuery()->execute();

        $doc = new DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('urlset');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        $doc->appendChild($root);

        foreach ($categories as $item) {
            $url = $doc->createElement('url');

            $loc = $url->appendChild($doc->createElement('loc'));
            $loc_node = $doc->createTextNode($this->config->application->frontend_url . $item->slug);
            $loc->appendChild($loc_node);
            $doc->appendChild($url);

            $lastmod = $url->appendChild($doc->createElement('lastmod'));
            $last_mod_node = $doc->createTextNode(date('Y-m-d'));
            $lastmod->appendChild($last_mod_node);
            $doc->appendChild($url);

            $changefreq = $url->appendChild($doc->createElement('changefreq'));
            $changefreq_node = $doc->createTextNode('hourly');
            $changefreq->appendChild($changefreq_node);
            $doc->appendChild($url);

            $priority = $url->appendChild($doc->createElement('priority'));
            $priority_node = $doc->createTextNode('0.9');
            $priority->appendChild($priority_node);
            $doc->appendChild($url);

            $root->appendChild($url);
        }


        $file = ROOT . '/web/frontend/sitemap-category.xml';
        if (!$doc->save($file)) {
            die('Error, cannot create XML file');
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }

    public function createUserSiteMapAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][createUserSiteMapAction]' . "\n";

        $user = new \Fast\Data\Model\User();
        $b = $user->getModelsManager()->createBuilder();
        $b->columns(array(
            'u.phone_number'
        ));
        $b->from(array('u' => 'Fast\Data\Model\User'));
        $b->andWhere('u.status = :status:', array('status' => \Fast\Data\Lib\Constant::USER_STATUS_ACTIVE));
        $b->orderBy('u.updated_at DESC');
        $b->limit(200);
        $users = $b->getQuery()->execute();

        $doc = new DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('urlset');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        $doc->appendChild($root);

        foreach ($users as $item) {
            $url = $doc->createElement('url');

            $loc = $url->appendChild($doc->createElement('loc'));
            $loc_node = $doc->createTextNode($this->config->application->frontend_url . $item->phone_number);
            $loc->appendChild($loc_node);
            $doc->appendChild($url);

            $lastmod = $url->appendChild($doc->createElement('lastmod'));
            $last_mod_node = $doc->createTextNode(date('Y-m-d'));
            $lastmod->appendChild($last_mod_node);
            $doc->appendChild($url);

            $changefreq = $url->appendChild($doc->createElement('changefreq'));
            $changefreq_node = $doc->createTextNode('hourly');
            $changefreq->appendChild($changefreq_node);
            $doc->appendChild($url);

            $priority = $url->appendChild($doc->createElement('priority'));
            $priority_node = $doc->createTextNode('0.9');
            $priority->appendChild($priority_node);
            $doc->appendChild($url);

            $root->appendChild($url);
        }

        $file = ROOT . '/web/frontend/sitemap-user.xml';
        if (!$doc->save($file)) {
            die('Error, cannot create XML file');
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }

    public function createProvinceSiteMapAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][createProvinceSiteMapAction]' . "\n";

        $province = new \Fast\Data\Model\Province();
        $b = $province->getModelsManager()->createBuilder();
        $b->columns(array(
            'p.id',
            'p.slug'
        ));
        $b->from(array('p' => 'Fast\Data\Model\Province'));
        $provinces = $b->getQuery()->execute();

        $doc = new DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('urlset');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        $doc->appendChild($root);

        foreach ($provinces as $item) {
            $url = $doc->createElement('url');

            $loc = $url->appendChild($doc->createElement('loc'));
            $loc_node = $doc->createTextNode($this->config->application->frontend_url . 'tinh-thanh/' . $item->slug . '/id-' . \Fast\Data\Lib\Util::hashId($item->id));
            $loc->appendChild($loc_node);
            $doc->appendChild($url);

            $lastmod = $url->appendChild($doc->createElement('lastmod'));
            $last_mod_node = $doc->createTextNode(date('Y-m-d'));
            $lastmod->appendChild($last_mod_node);
            $doc->appendChild($url);

            $changefreq = $url->appendChild($doc->createElement('changefreq'));
            $changefreq_node = $doc->createTextNode('hourly');
            $changefreq->appendChild($changefreq_node);
            $doc->appendChild($url);

            $priority = $url->appendChild($doc->createElement('priority'));
            $priority_node = $doc->createTextNode('0.9');
            $priority->appendChild($priority_node);
            $doc->appendChild($url);

            $root->appendChild($url);
        }

        $file = ROOT . '/web/frontend/sitemap-province.xml';
        if (!$doc->save($file)) {
            die('Error, cannot create XML file');
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }

    public function createDistrictSiteMapAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][createDistrictSiteMapAction]' . "\n";

        $district = new \Fast\Data\Model\District();
        $b = $district->getModelsManager()->createBuilder();
        $b->columns(array(
            'd.id',
            'd.slug'
        ));
        $b->from(array('d' => 'Fast\Data\Model\District'));
        $districts = $b->getQuery()->execute();

        $doc = new DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('urlset');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        $doc->appendChild($root);

        foreach ($districts as $item) {
            $url = $doc->createElement('url');

            $loc = $url->appendChild($doc->createElement('loc'));
            $loc_node = $doc->createTextNode($this->config->application->frontend_url . 'quan-huyen/' . $item->slug . '/id-' . \Fast\Data\Lib\Util::hashId($item->id));
            $loc->appendChild($loc_node);
            $doc->appendChild($url);

            $lastmod = $url->appendChild($doc->createElement('lastmod'));
            $last_mod_node = $doc->createTextNode(date('Y-m-d'));
            $lastmod->appendChild($last_mod_node);
            $doc->appendChild($url);

            $changefreq = $url->appendChild($doc->createElement('changefreq'));
            $changefreq_node = $doc->createTextNode('hourly');
            $changefreq->appendChild($changefreq_node);
            $doc->appendChild($url);

            $priority = $url->appendChild($doc->createElement('priority'));
            $priority_node = $doc->createTextNode('0.9');
            $priority->appendChild($priority_node);
            $doc->appendChild($url);

            $root->appendChild($url);
        }

        $file = ROOT . '/web/frontend/sitemap-district.xml';
        if (!$doc->save($file)) {
            die('Error, cannot create XML file');
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }

    public function createTagSiteMapAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][createTagSiteMapAction]' . "\n";

        $tag = new \Fast\Data\Model\Tag();
        $b = $tag->getModelsManager()->createBuilder();
        $b->columns(array(
            't.id',
            't.slug'
        ));
        $b->from(array('t' => 'Fast\Data\Model\Tag'));
        $b->orderBy('t.id DESC');
        $b->limit(200);
        $tags = $b->getQuery()->execute();

        $doc = new DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('urlset');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        $doc->appendChild($root);

        foreach ($tags as $item) {
            $url = $doc->createElement('url');

            $loc = $url->appendChild($doc->createElement('loc'));
            $loc_node = $doc->createTextNode($this->config->application->frontend_url . 'tag/' . $item->slug);
            $loc->appendChild($loc_node);
            $doc->appendChild($url);

            $lastmod = $url->appendChild($doc->createElement('lastmod'));
            $last_mod_node = $doc->createTextNode(date('Y-m-d'));
            $lastmod->appendChild($last_mod_node);
            $doc->appendChild($url);

            $changefreq = $url->appendChild($doc->createElement('changefreq'));
            $changefreq_node = $doc->createTextNode('hourly');
            $changefreq->appendChild($changefreq_node);
            $doc->appendChild($url);

            $priority = $url->appendChild($doc->createElement('priority'));
            $priority_node = $doc->createTextNode('0.9');
            $priority->appendChild($priority_node);
            $doc->appendChild($url);

            $root->appendChild($url);
        }

        $file = ROOT . '/web/frontend/sitemap-tag.xml';
        if (!$doc->save($file)) {
            die('Error, cannot create XML file');
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }

    public function createArticleByCategorySiteMapAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][createArticleByCategorySiteMapAction]' . "\n";

        $category = new \Fast\Data\Model\Category();
        $b = $category->getModelsManager()->createBuilder();
        $b->columns(array(
            'c.id',
            'c.parent_id',
            'c.name',
            'c.slug',
            'c.article_count'
        ));
        $b->from(array('c' => 'Fast\Data\Model\Category'));
        $b->andWhere('c.parent_id <> 0');
        $b->andWhere('c.status = :status:', array('status' => \Fast\Data\Lib\Constant::CATEGORY_STATUS_ACTIVE));
        $categories = $b->getQuery()->execute();

        foreach ($categories as $item) {
            $article = new \Fast\Data\Model\Article();
            $b = $article->getModelsManager()->createBuilder();
            $b->columns(array(
                'a.id',
                'a.name',
                'a.user_id',
                'u.phone_number'
            ));
            $b->from(array('a' => 'Fast\Data\Model\Article'));
            $b->innerJoin('Fast\Data\Model\User', 'u.id = a.user_id', 'u');
            $b->andWhere('a.status = :status:', array('status' => \Fast\Data\Lib\Constant::ARTICLE_STATUS_ACTIVE));
            $b->andWhere('a.category_id = :category_id:', array('category_id' => $item->id));
            $b->orderBy('a.updated_at DESC');
            $b->limit(200);
            $articles = $b->getQuery()->execute();

            if ($articles && count($articles) > 0) {
                $doc = new DOMDocument('1.0', 'UTF-8');
                $root = $doc->createElement('urlset');
                $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
                $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
                $root->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
                $doc->appendChild($root);
                foreach ($articles as $a_item) {
                    $url = $doc->createElement('url');

                    $loc = $url->appendChild($doc->createElement('loc'));
                    $loc_node = $doc->createTextNode($this->config->application->frontend_url . $a_item->phone_number .'/id-' . \Fast\Data\Lib\Util::hashId($a_item->id));
                    $loc->appendChild($loc_node);
                    $doc->appendChild($url);

                    $lastmod = $url->appendChild($doc->createElement('lastmod'));
                    $last_mod_node = $doc->createTextNode(date('Y-m-d'));
                    $lastmod->appendChild($last_mod_node);
                    $doc->appendChild($url);

                    $changefreq = $url->appendChild($doc->createElement('changefreq'));
                    $changefreq_node = $doc->createTextNode('daily');
                    $changefreq->appendChild($changefreq_node);
                    $doc->appendChild($url);

                    $priority = $url->appendChild($doc->createElement('priority'));
                    $priority_node = $doc->createTextNode('0.9');
                    $priority->appendChild($priority_node);
                    $doc->appendChild($url);

                    $root->appendChild($url);
                }

                $file = ROOT . '/web/frontend/sitemap-category'. '-' . $item->slug . '.xml';
                if (!$doc->save($file)) {
                    die('Error, cannot create XML file');
                }
            }
        }

        echo '--------- ' . date('Y-m-d H:i:s') . "\n\n";
    }

    public function createQrcodeAction() {
        echo "\n" . '--------- ' . date('Y-m-d H:i:s') . "\n";
        echo '[CronjobTask][createQrcode]' . "\n";

        $params = array(
            'conditions' => 'status = :status:
                AND phone_number_certified = :phone_number_certified:
                AND qrcode IS NULL',
            'bind' => array(
                'status' => \Fast\Data\Lib\Constant::TYPE_STATUS_ACTIVE,
                'phone_number_certified' => \Fast\Data\Lib\Constant::USER_PHONE_NUMBER_CERTIFIED_YES
            )
        );

        $users = \Fast\Data\Model\User::find($params);
        if (count($users)) {
            foreach ($users as $item) {
                $link = $this->config->application->frontend_url . $item->phone_number;
                $file_name = \Fast\Data\Lib\Qrcode::create(ROOT . '/web/backend/uploads/', $link);
                $file = ROOT . '/web/backend/uploads/' . $file_name;

                if (file_exists($file)) {
                    $content = file_get_contents($file);
                    $url = $this->config->cdn->upload_image_url;
                    $post = array(
                        'content' => $content,
                        'folder' => 'qrcode',
                        'filename' => $file_name
                    );

                    $r = \Fast\Data\Lib\Util::curlPost($url, $post);
                    $r = json_decode($r, true);

                    if (isset($r['status']) && $r['status'] == \Fast\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
                        $user = new \Fast\Data\Model\User();
                        $q = 'UPDATE user
                            SET user.qrcode = "' . $file_name . '"
                            WHERE user.id = "' . $item->id . '"';
                        $user->getWriteConnection()->query($q);
                    }

                    @chmod($file, 0777);
                    @unlink($file);
                }
            }
        }

        echo '---------' . date('Y-m-d H:i:s') . "\n\n";
    }
}