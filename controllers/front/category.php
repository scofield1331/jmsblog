<?php
/**
* 2007-2017 PrestaShop
*
* Jms Blog
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2017 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

include_once(_PS_MODULE_DIR_.'jmsblog/class/JmsBlogHelper.php');
include_once(_PS_MODULE_DIR_.'jmsblog/class/JmsPostHelper.php');
include_once(_PS_MODULE_DIR_.'jmsblog/JmsImage.php');
include_once(_PS_MODULE_DIR_.'jmsblog/JmsVideo.php');
class JmsblogCategoryModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;

    /**
     * @see FrontController::initContent()
     */
    // public function __construct()
    // {
    //     parent::__construct();
    // }
    public function initContent()
    {
        parent::initContent();
        $category_id = (int)Tools::getValue('category_id');
        $category   = JmsBlogHelper::getCategory($category_id);
        if ($category == null) {
            return;
        }
        $force_ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        $use_https = false;
        if (isset($force_ssl) && $force_ssl) {
            $use_https = true;
        }
        $this->display_column_left = false;
        $this->display_column_right = false;
        $this->jmsblog_setting = Configuration::getMultiple(
            array(
                'JMSBLOG_INTROTEXT_LIMIT',
                'JMSBLOG_ITEMS_PER_PAGE',
                'JMSBLOG_SHOW_CATEGORY',
                'JMSBLOG_SHOW_MEDIA',
                'JMSBLOG_SHOW_SUBCAT_CATEGORY',
                'JMSBLOG_SHOW_HEADING_CATEGORY',
                'JMSBLOG_MAX_HEADINGPOST_CATEGORY',
                'JMSBLOG_MAX_SUBPOST_CATEGORY',
                'JMSBLOG_SHOW_VIEWS',
                'JMSBLOG_SHOW_COMMENTS',
                'JMSBLOG_SHOW_SOCIAL_SHARING',
                'JMSBLOG_SHOW_FACEBOOK',
                'JMSBLOG_SHOW_TWITTER',
                'JMSBLOG_SHOW_GOOGLEPLUS',
                'JMSBLOG_SHOW_LINKEDIN',
                'JMSBLOG_SHOW_PINTEREST',
                'JMSBLOG_SHOW_EMAIL',
            )
        );
        $this->jmsblog_setting['JMSBLOG_SHOW_DATE'] = Configuration::get('JMSBLOG_SHOW_DATE_CATEGORY');
        $this->jmsblog_setting['JMSBLOG_SHOW_INTRO'] = Configuration::get('JMSBLOG_SHOW_INTRO_CATEGORY');
        if ($this->jmsblog_setting['JMSBLOG_SHOW_SUBCAT_CATEGORY']) {
            $category['childs'] = $this->getChildCategories($category_id);
        }
        $category['heading'] = $this->getTopPosts($category_id,1,$this->jmsblog_setting['JMSBLOG_MAX_HEADINGPOST_CATEGORY']);
        $category['common'] = $this->getTopPosts($category_id,0,$this->jmsblog_setting['JMSBLOG_MAX_SUBPOST_CATEGORY']);
        $start = Tools::getValue('start', 1);
        $limit = Tools::getValue('limit', $this->jmsblog_setting['JMSBLOG_ITEMS_PER_PAGE']);
        if (!Validate::isUnsignedInt($start)) {
            $start = 0;
        }
        if ($start) {
            $start = ($start-1)*$limit;
        }
        $link = $this->context->link->getModuleLink($this->module->name, 'category').'&category_id='.$category_id;
        $total = JmsPostHelper::getPostCount($category_id, 1);
        if ($total % $limit) {
            $pages = (int)($total / $limit) + 1;
        } else {
            $pages = $total / $limit;
        }
        $posts  = $this->getPosts($category_id, $start, $limit);
        for ($i = 0; $i < count($posts); $i++) {
            $posts[$i]['introtext'] = JmsBlogHelper::genIntrotext($posts[$i]['introtext'], $this->jmsblog_setting['JMSBLOG_INTROTEXT_LIMIT']);
            $posts[$i]['comment_count'] = JmsBlogHelper::getCommentCount($posts[$i]['post_id']);
        }
        $this->context->controller->addCSS($this->module->getPathUri().'views/css/category.css', 'all');
        $catparam = array('category_id' => '', 'slug' => '', 'start' => '');
        $postparam = array('category_slug' => '', 'post_id' => '', 'slug' => '');
        // $this->context->smarty->assign(array('meta_title' => $category['title']));
        $this->context->smarty->assign(array(
            'posts' => $posts,
            'current_category' => $category,
            'config' => $this->jmsblog_setting,
            'image_baseurl' => $this->module->getPathUri().'views/img/',
            'start'=>Tools::getValue('start', 1),
            'limit'=>$limit,
            'pages'=>$pages,
            'current_uri'=>$link,
            'total'=>$total,
            'catparams' => $catparam,
            'postparams' => $postparam,
            'use_https' => $use_https
        ));
        $category_layout = 'category.tpl';
		if(Tools::getValue('layout') != '') {
			$category_layout = Tools::getValue('layout').'.tpl';
		} elseif (Configuration::get('JMSBLOG_CATEGORY_LAYOUT')) {
			$category_layout = Configuration::get('JMSBLOG_CATEGORY_LAYOUT');
		}
        $this->setTemplate($category_layout);
    }

    public function getPosts($category_id = 0, $start = 0, $limit)
    {
        $this->context = Context::getContext();
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $sql = '
            SELECT hss.`post_id`,hss.`category_id`, hss.`ordering`, hss.`active`, hssl.`title`, hss.`created`, hss.`modified`, hss.`views`,
            CASE WHEN im.image IS NULL THEN "default.jpg" ELSE im.image END AS image,
            hssl.`alias`, hssl.`fulltext`, hssl.`introtext`,hssl.`meta_desc`, hssl.`meta_key`, hssl.`key_ref`, catsl.`title` AS category_title, catsl.`alias` AS category_alias
            FROM '._DB_PREFIX_.'jmsblog_posts hss
            LEFT JOIN '._DB_PREFIX_.'jmsblog_posts_lang hssl ON (hss.`post_id` = hssl.`post_id`)
            LEFT JOIN '._DB_PREFIX_.'jmsblog_categories_lang catsl ON (catsl.`category_id` = hss.`category_id`)
            LEFT JOIN '._DB_PREFIX_.'jmsblog_shop js ON (js.category_id = hss.category_id)
            LEFT JOIN '._DB_PREFIX_.'jmsblog_posts_images im ON (hss.post_id = im.post_id) AND (im.cover = 1)
            WHERE hss.`active` = 1 AND hssl.`id_lang` = '.(int)$id_lang.' AND catsl.`id_lang` = '.(int)$id_lang.' AND js.`id_shop` = '.(int)$id_shop.
            ' AND hss.`category_id` = '.$category_id.'
            GROUP BY hss.`post_id`
            ORDER BY hss.`created` DESC
            LIMIT '.$start.','.$limit;
        $posts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        // foreach ($posts as &$post) {
        //     $videoCol = new PrestashopCollection('JmsVideo');
        //     $videoCol->where('post_id','=', $post['post_id']);
        //     $post['link_video'] = $videoCol->getResults();
        // }
        return $posts;
    }

    private function getChildCategories($parent_id)
    {
        $id_lang = $this->context->language->id;
        $sql = new DbQuery();
        $sql->select('cl.category_id, cl.title, cl.alias');
        $sql->from('jmsblog_categories', 'c');
        $sql->innerJoin('jmsblog_categories_lang', 'cl', 'cl.category_id=c.category_id');
        $sql->where('c.active = 1');
        $sql->where('c.parent = '.$parent_id);
        $sql->where('cl.id_lang = '.$id_lang);
        $sql->orderBy('c.ordering');
        return Db::getInstance()->executeS($sql);
    }
    private function getTopPosts($category_id,$heading,$limit)
    {
        $id_lang = $this->context->language->id;
        $sql = new DbQuery();
        $sql->select('SUBSTRING(pl.introtext, 1, '.$this->jmsblog_setting['JMSBLOG_INTROTEXT_LIMIT'].') AS introtext');
        $sql->select('p.created,p.views, p.post_id, p.category_id, pl.title, pl.alias, cl.title AS category_title, cl.alias AS category_alias, COUNT(cmt.comment_id) AS comment_count');
        $sql->select('CASE WHEN im.image IS NULL THEN "default.jpg" ELSE im.image END AS image');
        $sql->from('jmsblog_posts', 'p');
        $sql->innerJoin('jmsblog_posts_lang', 'pl', 'p.post_id=pl.post_id');
        $sql->innerJoin('jmsblog_categories_lang', 'cl', 'cl.category_id='.$category_id);
        $sql->leftJoin('jmsblog_posts_images', 'im', 'p.post_id = im.post_id AND im.cover = 1');
        $sql->leftJoin('jmsblog_posts_comments', 'cmt', 'p.post_id = cmt.post_id AND cmt.status = 1');
        $sql->where('p.active = 1 AND p.heading ='.$heading);
        $sql->where('p.category_id = '.$category_id);
        $sql->where('pl.id_lang = cl.id_lang');
        $sql->where('cl.id_lang = '.$id_lang);
        $sql->orderBy('p.created DESC');
        $sql->groupBy('p.post_id');
        $sql->limit($limit);
        return Db::getInstance()->executeS($sql);
    }
}
