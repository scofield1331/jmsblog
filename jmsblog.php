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

if (!defined('_PS_VERSION_')) {
    exit;
}

class JmsBlog extends Module
{
    public function __construct()
    {
        $this->name = 'jmsblog';
        $this->tab = 'front_office_features';
        $this->version = '2.5.6';
        $this->author = 'Joommasters';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Jms Blog');
        $this->description = $this->l('Blog For Prestashop.');
    }

    public function install()
    {
        $res = true;
        $this->addMeta('module-jmsblog-category', 'Jms Blog Category', 'jmsblog-category');
        $this->addMeta('module-jmsblog-post', 'Jms Blog Post', 'jmsblog-post');
        $this->addMeta('module-jmsblog-tag', 'Jms Blog Tag', 'jmsblog-tag');
        $this->addMeta('module-jmsblog-archive', 'Jms Blog Archive', 'jmsblog-archive');
        $this->addMeta('module-jmsblog-categories', 'Jms Blog Categories', 'jmsblog-categories');
        if (parent::install() && $this->registerHook('moduleRoutes') && $this->registerHook('header')) {
            include(dirname(__FILE__).'/install/install.php');
            $id_tab1 = $this->addTab('Jms Blog', 'dashboard');
            $this->addTab('Categories', 'categories', $id_tab1);
            $this->addTab('Post', 'post', $id_tab1);
            $this->addTab('Comments', 'comment', $id_tab1);
            $this->addTab('Setting', 'setting', $id_tab1);
            $res &= Configuration::updateValue('JMSBLOG_INTROTEXT_LIMIT', 300);
            $res &= Configuration::updateValue('JMSBLOG_ITEMS_PER_PAGE', 20);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_CATEGORY', 1);
            $res &= Configuration::updateValue('JMSBLOG_IMAGE_WIDTH', 1000);
            $res &= Configuration::updateValue('JMSBLOG_IMAGE_HEIGHT', 1000);
            $res &= Configuration::updateValue('JMSBLOG_IMAGE_THUMB_WIDTH', 300);
            $res &= Configuration::updateValue('JMSBLOG_IMAGE_THUMB_HEIGHT', 300);
            $res &= Configuration::updateValue('JMSBLOG_COMMENT_ENABLE', 1);
            $res &= Configuration::updateValue('JMSBLOG_FACEBOOK_COMMENT', 0);
            $res &= Configuration::updateValue('JMSBLOG_ALLOW_GUEST_COMMENT', 1);
            $res &= Configuration::updateValue('JMSBLOG_COMMENT_DELAY', 120);
            $res &= Configuration::updateValue('JMSBLOG_AUTO_APPROVE_COMMENT', 0);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_FACEBOOK', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_TWITTER', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_GOOGLEPLUS', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_LINKEDIN', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_PINTEREST', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_EMAIL', 1);
            //mainpage
            $res &= Configuration::updateValue('JMSBLOG_CATEGORIES_BOX', 'cbox-left-right.tpl');
            $res &= Configuration::updateValue('JMSBLOG_TOP_BOX', 'headingpost1.tpl');
            $res &= Configuration::updateValue('JMSBLOG_CATEGORIES_BOX_COLUMN', 2);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_HEADING', 1);
            $res &= Configuration::updateValue('JMSBLOG_MAX_HEADING_POST', 1);
            $res &= Configuration::updateValue('JMSBLOG_MAX_SUB_POST', 4);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_INTRO_SUB', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_CAT_SUB', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_SUB', 1);
            $res &= Configuration::updateValue('JMSBLOG_TOP_DISPLAY', '1,7,2,8');
            $res &= Configuration::updateValue('JMSBLOG_BOX_DISPLAY', '');
            $res &= Configuration::updateValue('JMSBLOG_BOOTSTRAP', 0);
            //category page
            $res &= Configuration::updateValue('JMSBLOG_SHOW_SUBCAT_CATEGORY', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_HEADING_CATEGORY', 1);
            $res &= Configuration::updateValue('JMSBLOG_MAX_HEADINGPOST_CATEGORY', 1);
            $res &= Configuration::updateValue('JMSBLOG_MAX_SUBPOST_CATEGORY', 4);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_INTRO_CATEGORY', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_DATE_CATEGORY', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_VIEWS', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_COMMENTS', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_MEDIA', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_SOCIAL_SHARING', 1);
            //post detail page
            $res &= Configuration::updateValue('JMSBLOG_SHOW_CAT_DETAIL', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_DATE_DETAIL', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_VIEWS_DETAIL', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_COMMENTS_DETAIL', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_IMAGE_DETAIL', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_VIDEO_DETAIL', 1);
            $res &= Configuration::updateValue('JMSBLOG_SOCIAL_SHARING_DETAIL', 1);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_COVER_DETAIL', 1);
            $install_demo = new JmsInstall();
            $install_demo->createTable();
            $install_demo->installSamples();


            return $res;
        }
        return false;
    }
    public function uninstall()
    {
        $res = true;
        /* Deletes Module */
        $this->controllers = array('category','post','archive','tag');
        if (parent::uninstall()) {
            $sql = array();
            include(dirname(__FILE__).'/install/uninstall.php');
            foreach ($sql as $s) {
                Db::getInstance()->execute($s);
            }
            $this->removeTab('categories');
            $this->removeTab('post');
            $this->removeTab('comment');
            $this->removeTab('setting');
            $this->removeTab('dashboard');

            $res &= Configuration::deleteByName('JMSBLOG_INTROTEXT_LIMIT');
            $res &= Configuration::deleteByName('JMSBLOG_ITEMS_PER_PAGE');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_CATEGORY');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_VIEWS');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_COMMENTS');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_MEDIA');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_HEADING_CATEGORY');
            $res &= Configuration::deleteByName('JMSBLOG_IMAGE_WIDTH');
            $res &= Configuration::deleteByName('JMSBLOG_IMAGE_HEIGHT');
            $res &= Configuration::deleteByName('JMSBLOG_IMAGE_THUMB_WIDTH');
            $res &= Configuration::deleteByName('JMSBLOG_IMAGE_THUMB_HEIGHT');
            $res &= Configuration::deleteByName('JMSBLOG_COMMENT_ENABLE');
            $res &= Configuration::deleteByName('JMSBLOG_FACEBOOK_COMMENT');
            $res &= Configuration::deleteByName('JMSBLOG_ALLOW_GUEST_COMMENT');
            $res &= Configuration::deleteByName('JMSBLOG_COMMENT_DELAY');
            $res &= Configuration::deleteByName('JMSBLOG_AUTO_APPROVE_COMMENT');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_SOCIAL_SHARING');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_FACEBOOK');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_TWITTER');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_GOOGLEPLUS');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_LINKEDIN');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_PINTEREST');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_EMAIL');

            $res &= Configuration::deleteByName('JMSBLOG_CATEGORIES_BOX');
            $res &= Configuration::deleteByName('JMSBLOG_TOP_BOX');
            $res &= Configuration::deleteByName('JMSBLOG_CATEGORIES_BOX_COLUMN');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_HEADING');
            $res &= Configuration::deleteByName('JMSBLOG_MAX_HEADING_POST');
            $res &= Configuration::deleteByName('JMSBLOG_MAX_SUB_POST');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_INTRO_SUB');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_CAT_SUB');
            $res &= Configuration::deleteByName('JMSBLOG_TOP_DISPLAY');
            $res &= Configuration::deleteByName('JMSBLOG_BOX_DISPLAY');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_SUB');
            $res &= Configuration::deleteByName('JMSBLOG_BOOTSTRAP');

            $res &= Configuration::deleteByName('JMSBLOG_SHOW_SUBCAT_CATEGORY');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_HEADING_CATEGORY');
            $res &= Configuration::deleteByName('JMSBLOG_MAX_HEADINGPOST_CATEGORY');
            $res &= Configuration::deleteByName('JMSBLOG_MAX_SUBPOST_CATEGORY');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_INTRO_CATEGORY');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_DATE_CATEGORY');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_VIEWS');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_COMMENTS');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_MEDIA');
            //post page
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_CAT_DETAIL');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_DATE_DETAIL');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_VIEWS_DETAIL');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_COMMENTS_DETAIL');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_MEDIA_DETAIL');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_IMAGE_DETAIL');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_VIDEO_DETAIL');
            $res &= Configuration::deleteByName('JMSBLOG_SOCIAL_SHARING_DETAIL');
            $res &= Configuration::deleteByName('JMSBLOG_SHOW_COVER_DETAIL');

            return $res;
        }
        return false;
    }

    private function addTab($title, $class_sfx = '', $parent_id = 0)
    {
        $class = 'Admin'.Tools::ucfirst($this->name).Tools::ucfirst($class_sfx);
        @Tools::copy(_PS_MODULE_DIR_.$this->name.'/logo.gif', _PS_IMG_DIR_.'t/'.$class.'.gif');
        $_tab = new Tab();
        $_tab->class_name = $class;
        $_tab->module = $this->name;
        $_tab->id_parent = $parent_id;
        $langs = Language::getLanguages(false);
        foreach ($langs as $l) {
            $_tab->name[$l['id_lang']] = $title;
        }
        if ($parent_id == -1) {
            $_tab->id_parent = -1;
            $_tab->add();
        } else {
            $_tab->add(true, false);
        }
        return $_tab->id;
    }

    private function removeTab($class_sfx = '')
    {
        $tabClass = 'Admin'.Tools::ucfirst($this->name).Tools::ucfirst($class_sfx);
        $idTab = Tab::getIdFromClassName($tabClass);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $tab->delete();
            return true;
        }
        return false;
    }
    private function addMeta($page, $title, $url_rewrite, $desc = '', $keywords = '')
    {
        $themes = Theme::getThemes();
        $theme_meta_value = array();
        $result = Db::getInstance()->getValue('SELECT * FROM '._DB_PREFIX_.'meta WHERE page="'.pSQL($page).'"');
        if ((int)$result > 0) {
            return true;
        }
        $_meta = new MetaCore();
        $_meta->page = $page;
        $_meta->configurable = 1;
        $langs = Language::getLanguages(false);
        foreach ($langs as $l) {
            $_meta->title[$l['id_lang']] = $title;
            $_meta->description[$l['id_lang']] = $desc;
            $_meta->keywords[$l['id_lang']] = $keywords;
            $_meta->url_rewrite[$l['id_lang']] = $url_rewrite;
        }

        $_meta->add();
        if ((int)$_meta->id > 0) {
            foreach ($themes as $theme) {
                $theme_meta_value[] = array(
                    'id_theme' => $theme->id,
                    'id_meta' => $_meta->id,
                    'left_column' => (int)$theme->default_left_column,
                    'right_column' => (int)$theme->default_right_column
                );
            }
            if (count($theme_meta_value) > 0) {
                return Db::getInstance()->insert('theme_meta', $theme_meta_value);
            }
        } else {
            return false;
        }
    }

    public static function getJmsBlogUrl()
    {
        $ssl_enable = Configuration::get('PS_SSL_ENABLED');
        $id_shop = (int)Context::getContext()->shop->id;
        //$rewrite_set = 1;
        $relative_protocol = false;
        $ssl = null;
        static $force_ssl = null;
        if ($ssl === null) {
            if ($force_ssl === null) {
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            }
            $ssl = $force_ssl;
        }
        if (1 && $id_shop !== null) {
            $shop = new Shop($id_shop);
        } else {
            $shop = Context::getContext()->shop;
        }
        if (!$relative_protocol) {
            $base = '//'.($ssl && $ssl_enable ? $shop->domain_ssl : $shop->domain);
        } else {
            $base = (($ssl && $ssl_enable) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
        }
        return $base.$shop->getBaseURI();
    }

    public static function getPageLink($rewrite = 'jmsblog', $params = null, $id_lang = null)
    {
        $url = jmsblog::getJmsBlogUrl();
        $dispatcher = Dispatcher::getInstance();
        if ($params != null) {
            return str_replace('&', '&amp;', $url.$dispatcher->createUrl($rewrite, $id_lang, $params));
        } else {
            return str_replace('&', '&amp;', $url.$dispatcher->createUrl($rewrite));
        }
    }
    public function hookHeader($params)
    {
        $base_url = '';
        $force_ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        if (isset($force_ssl) && $force_ssl) {
            $base_url = $protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__;
        } else {
            $base_url = _PS_BASE_URL_.__PS_BASE_URI__;
        }
        if (Configuration::get('JMSBLOG_BOOTSTRAP')) {
            $this->context->controller->addCSS($base_url.'modules/jmsblog/views/bootstrap/css/bootstrap.min.css', 'all');
        }
        $this->context->controller->addCSS($base_url.'modules/jmsblog/views/css/style.css', 'all');
        $this->context->controller->addJS($base_url.'modules/jmsblog/views/js/categorymenu.js', 'all');
        $this->context->controller->addJS($base_url.'modules/jmsblog/views/js/main.js', 'all');
    }
    public function hookModuleRoutes($params)
    {
        $html = '.html';
        return array(
            'jmsblog-main' => array(
                'controller' => 'main',
                'rule' => 'jmsblog/main',
                'keywords' => array(
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'jmsblog'
                )
            ),
            'jmsblog-categories' => array(
                'controller' => 'categories',
                'rule' => 'jmsblog/categories'.$html,
                'keywords' => array(
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'jmsblog'
                )
            ),
            'jmsblog-post' => array(
                'controller' => 'post',
                'rule' => 'jmsblog/{category_slug}/{post_id}_{slug}'.$html,
                'keywords' => array(
                    'post_id' => array('regexp' => '[\d]+','param' => 'post_id'),
                    'category_slug' => array('regexp' => '[\w]+','param' => 'category_slug'),
                    'slug' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'jmsblog'
                )
            ),
            'jmsblog-category' => array(
                'controller' => 'category',
                'rule' => 'jmsblog/{category_id}_{slug}{/:start}'.$html,
                'keywords' => array(
                    'category_id' => array('regexp' => '[\w]+','param' => 'category_id'),
                    'slug' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'start'=> array('regexp' => '[\d]+', 'param'=> 'start'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'jmsblog'
                )
            ),
            'jmsblog-archive' => array(
                'controller' => 'archive',
                'rule' => 'jmsblog/archive-month/{archive}{/:start}',
                'keywords' => array(
                    'archive' => array('regexp' => '[_a-zA-Z0-9-\pL]*','param' => 'archive'),
                    'start'=> array('regexp' => '[\d]+$', 'param'=> 'start'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'jmsblog'
                )
            ),
            'jmsblog-tag' => array(
                'controller' => 'tag',
                'rule' => 'jmsblog/tag/{tag}'.$html,
                'keywords' => array(
                    'tag' => array('regexp' => '[\w]+','param' => 'tag')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'jmsblog'
                )
            )
        );
    }
}
