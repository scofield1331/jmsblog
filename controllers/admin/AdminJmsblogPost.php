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

include_once(_PS_MODULE_DIR_.'jmsblog/class/JmsBlogHelper.php');
include_once(_PS_MODULE_DIR_.'jmsblog/JmsPost.php');
include_once(_PS_MODULE_DIR_.'jmsblog/JmsImage.php');
include_once(_PS_MODULE_DIR_.'jmsblog/JmsVideo.php');
class AdminJmsblogPostController extends ModuleAdminController
{
    public $linkVideos;
    public $buttons;
    public function __construct()
    {
        $this->name = 'jmsblog';
        $this->tab = 'front_office_features';
        $this->bootstrap = true;
        $this->lang = true;
        $this->root_url = JmsBlogHelper::getUrl();
        $this->context = Context::getContext();
        $this->secure_key = Tools::encrypt($this->name);
        $this->catselect = array();
        parent::__construct();
    }
    // update code
    private function renderExtraTabs()
    {
        $this->context->controller->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin_style.css', 'all');
        $this->context->controller->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin.js', 'all');
        $this->context->controller->addJqueryUI('ui.draggable');
        $this->addJqueryUI('ui.tabs');
        $this->buttons = array(
            'save' => array(
                'title' => $this->l('Save'),
                'name' => 'submitPost',
                'class' => 'btn btn-default pull-right submitPost',
                'icon' => 'process-icon-save',
            ),
            'save-and-stay' => array(
                'title' => $this->l('Save and Stay'),
                'name' => 'submitPostAndStay',
                'class' => 'btn btn-default pull-right submitPostAndStay',
                'icon' => 'process-icon-save',
            ),
        );
        if (Tools::isSubmit('id_post')) {
            $this->buttons['duplicate'] = array(
                'title' => $this->l('Duplicate Post'),
                'name' => 'duplicate',
                'class' => 'btn btn-default pull-right duplicate',
                'icon' => 'process-icon- icon-copy',
            );
        }
        $form = array(
                'Informations'   => $this->renderAddPost(),
                'Media'   => $this->renderFormImages().$this->renderFormVideo(),
                'SEO'   => $this->renderFormSEO(),
        );
        $tpl = $this->createTemplate('extraTabs.tpl');
        $tab = Tools::getValue('post_tab',0);
        $tabindex = Validate::isUnsignedInt($tab)?$tab:array_search($tab,array_keys($form));
        $tpl->assign(array(
            'form' => $form,
            'post_tab'   => $tabindex
        ));
        return $tpl->fetch();
    }
    public function ajaxProcessaddImages()
    {
        $image_uploader = new HelperImageUploader('file');
        $image_uploader->setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg'))->setMaxSize(8*1024*1024);
        $files = $image_uploader->process();
        foreach ($files as &$file) {
            if (!$file['error']) {
                $img = new jmsImage();
                $img->file = $file;
                $img->post_id = Tools::getValue('id_post_ajax');
                $img->ordering = 0;
                $img->cover = false;
                $res = $img->add();
                if (!$res['status']) {
                    $file['error'] = $res['message'];
                }
                else {
                    $file['status'] = 'ok';
                    $file['image'] = $img->image;
                    $file['image_id'] = $img->id;
                }
                unset($file['save_path']);
            }

        }
        die(Tools::jsonEncode(array($image_uploader->getName() => $files)));
    }
    private function renderFormVideo()
    {
        $this->linkVideos = $this->getVideos();
        if (isset($this->linkVideos['new'])) {
            foreach ($this->linkVideos['new'] as $key => $link) {
                if (Tools::strlen(trim($link)) == 0) {
                    unset($this->linkVideos['new'][$key]);
                }
            }
        }
        $default_lang = $this->default_form_language;
        $fields_form = array();
        $fields_form[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Video'),
                    'icon' => 'icon-cogs'
                ),
                'buttons' => $this->buttons,
                'input' => array(
                    array(
                        'type' => 'multitext',
                        'col' => 7,
                        'label' => $this->l('Link Video'),
                        'name' => 'link_video',
                        'mdesc' => $this->l('Add share link video that you want to show. Example : <iframe title="Victoria Secret" src="//player.vimeo.com/video/43115415"></iframe>.'),
                        'videos' => $this->linkVideos,
                    ),
                ),
            ),
        );
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        if (Tools::isSubmit('id_post')) {
            $item = new JmsPost((int)Tools::getValue('id_post'));
        } else {
            $item = new JmsPost();
        }
        $helper->tpl_vars = array(
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        );
        return $helper->generateForm($fields_form);

    }
    private function renderFormSEO()
    {
        $default_lang = $this->default_form_language;
        $route_id = 'jmsblog-post';
        $default_routes = Dispatcher::getInstance()->default_routes[$route_id];
        $fields_form = array();
        $fields_form[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('SEO & URLs'),
                    'icon' => 'icon-cogs'
                ),
                'buttons' => $this->buttons,
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Alias'),
                        'name' => 'alias',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'generate',
                        'name' => 'generate',
                        'col' => 7,
                        'route_id' => $route_id,
                        'rule' => $default_routes['rule'],
                        'keywords' => json_encode(array_keys($default_routes['keywords'])),
                        'url' => jmsblog::getJmsBlogUrl(),
                        'cat_data' => json_encode($this->catselect),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Description'),
                        'name' => 'meta_desc',
                        'lang'  => true,
                        'desc' => $this->l('An optional paragraph to be used as the description of the page in the HTML output. This will generally display in the results of search engines.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Keywords'),
                        'name' => 'meta_key',
                        'lang' => true,
                        'desc' => $this->l('An optional comma-separated list of keywords and/or phrases to be used in the HTML output.')
                    ),
                ),
            ),
        );
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        if (Tools::isSubmit('id_post')) {
            $item = new JmsPost((int)Tools::getValue('id_post'));
        } else {
            $item = new JmsPost();
        }
        $helper->tpl_vars = array(
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        );
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $helper->fields_value['alias'][$lang['id_lang']]  = Tools::getValue('alias_'.(int)$lang['id_lang'], $item->alias[$lang['id_lang']]);
            $helper->fields_value['meta_desc'][$lang['id_lang']] = Tools::getValue('meta_desc_'.(int)$lang['id_lang'], $item->meta_desc[$lang['id_lang']]);
            $helper->fields_value['meta_key'][$lang['id_lang']] = Tools::getValue('meta_key_'.(int)$lang['id_lang'], $item->meta_key[$lang['id_lang']]);
            if ($item->category_id) {
                $category_alias = $this->catselect[array_search($item->category_id, array_column($this->catselect, 'category_id'))]['alias'];
            } else {
                $category_alias = '';
            }
            $helper->fields_value['generate'][$lang['id_lang']] = array(
                'post_id' => $item->id,
                'category_slug' => $category_alias,
                'slug' => $item->alias[$lang['id_lang']],
            );
        }
        return $helper->generateForm($fields_form);
    }
    private function renderFormImages()
    {
        $data = $this->createTemplate('images.tpl');
        if (Tools::isSubmit('id_post') && $this->postExists(Tools::getValue('id_post'))) {
            $id_post = Tools::getValue('id_post');
            $image_uploader = new HelperImageUploader('file');
            $image_uploader->setMultiple(!(Tools::getUserBrowser() == 'Apple Safari' && Tools::getUserPlatform() == 'Windows'))
                ->setUseAjax(true)->setUrl(
                    Context::getContext()->link->getAdminLink($this->controller_name).'&ajax=1&id_post_ajax='.$id_post
                    .'&action=addImages');
            $ajaxpath = __PS_BASE_URI__.'modules/'.$this->module->name.'/ajax_'.$this->module->name.'.php';
            $total = JmsImage::countImages($id_post);
            // d(JmsImage::getImages($id_post));
            $data->assign(array(
                    'countImages' => $total,
                    'ajaxpath' => $ajaxpath,
                    'id_post'   => $id_post,
                    'Images' => JmsImage::getImages($id_post),
                    'baseDir'   => $this->module->getPathUri().'views/img/',
                    'max_image_size' => 8,
                    'image_uploader' => $image_uploader->render(),
            ));

            return $data->fetch();
        } else {
            return $this->warning($this->l('You must save this post before adding images.'));
        }

    }
    private function warning($str, $id = 0)
    {
        $html = '<div class="bootstrap">
                    <div class="alert alert-warning">
                        <p>'.$str.'</p>
                        <p '.(($id)?"id=\"$id\"":'').' class="list-unstyled"></p>
                    </div>
                </div>';
        return $html;
    }
    public function getVideos()
    {
        $res = array();
        if (Tools::isSubmit('link_video')) {
            $res = Tools::getValue('link_video');
            if (isset($res['old'])) {
                $arr = array();
                foreach ($res['old'] as $id => $link) {
                    if (Tools::strlen(trim($link)) == 0) {
                        $res['remove'][] = $id;
                        continue;
                    }
                    $arr[] = array(
                        'id' => $id,
                        'link_video' => $link,
                        'post_id' => Tools::getValue('id_post')
                    );
                }
                $res['old'] = ObjectModel::hydrateCollection('JmsVideo', $arr);
            }
            if (isset($res['remove'])) {
                $arr = array();
                foreach ($res['remove'] as $id) {
                    $arr[] = array(
                        'id' => $id,
                    );
                }
                $res['remove'] = ObjectModel::hydrateCollection('JmsVideo', $arr);
            }
        } elseif (Tools::isSubmit('id_post')) {
            $post_id = Tools::getValue('id_post');
            $videos = new PrestashopCollection('JmsVideo');
            $videos->where('post_id','=', (int)$post_id);
            $arr = $videos->getResults();
            if (count($arr) != 0) $res['old'] = $arr;
        }
        return $res;
    }

    // end update code
    public function renderList()
    {
        if(Tools::isSubmit('id_post_ajax')){
            $this->ajaxProcessaddImages();
        }
        $limit = Configuration::get('JMSBLOG_ITEMS_PER_PAGE');
        $this->_html = $this->headerHTML();
        /* Validate & process */
        if (Tools::isSubmit('CancelAddForm')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmsblogPost', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
        } elseif (Tools::isSubmit('submitPost') ||
            Tools::isSubmit('submitPostAndStay') ||
            Tools::isSubmit('duplicate') ||
            Tools::isSubmit('delete_id_post') ||
            Tools::isSubmit('changePostStatus')) {
            if (Tools::isSubmit('duplicate')) {
                $this->duplicate();
                // d($_POST);
            }
            if ($this->_postValidation()) {
                $this->_postProcess();
                $this->_html .= $this->renderFilter();
                $this->_html .= $this->renderListPost($this->context->cookie->filter_category_id, $this->context->cookie->filter_state, $this->context->cookie->filter_start, $limit);
                $this->_html .= $this->renderPagination();
            } else {
                $this->_html .= $this->renderNavigation();
                $this->_html .= $this->renderExtraTabs();
            }
        } elseif (Tools::isSubmit('addPost') || ((Tools::isSubmit('id_post') && $this->postExists((int)Tools::getValue('id_post'))))) {
            $this->_html .= $this->renderNavigation();
            $this->_html .= $this->renderExtraTabs();
            // renderAddPost() is called inside renderExtraTabs();
        } else {
            if (Tools::isSubmit('filter_category_id')) {
                $this->context->cookie->filter_category_id = (int)Tools::getValue('filter_category_id', 0);
            }
            if (Tools::isSubmit('filter_state')) {
                $this->context->cookie->filter_state        = (int)Tools::getValue('filter_state', -1);
            } else {
                $this->context->cookie->filter_state        = -1;
            }
            if (Tools::isSubmit('start')) {
                $this->context->cookie->filter_start        = (int)Tools::getValue('start', 0);
            } else {
                $this->context->cookie->filter_start        = 0;
            }
            if (Tools::isSubmit('limit')) {
                $limit        = (int)Tools::getValue('limit', $limit);
            }
            $this->_html .= $this->renderFilter();
            $this->_html .= $this->renderListPost($this->context->cookie->filter_category_id, $this->context->cookie->filter_state, $this->context->cookie->filter_start, $limit);
            $this->_html .= $this->renderPagination();
        }
        return $this->_html;
    }

    private function duplicate()
    {
        $_POST['id_duplicate'] = $_POST['id_post'];
        $_POST['active'] = 0;
        $_POST['heading'] = 0;
        $_POST['submitPost'] = 1;
        $_POST['link_video']['new'] = array_merge(
            isset($_POST['link_video']['old'])?$_POST['link_video']['old']:array(),
            isset($_POST['link_video']['new'])?$_POST['link_video']['new']:array()
        );
        unset($_POST['id_post']);
        unset($_POST['created']);
        unset($_POST['modified']);
        unset($_POST['link_video']['old']);
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $_POST['title_'.$language['id_lang']] .= ' - Copy';
        }
        $this->confirmations[] = $this->l('Duplicated');
    }
    private function _postValidation()
    {
        $errors = array();
        /* Validation for configuration */
        if (Tools::isSubmit('changePostStatus')) {
            if (!Validate::isInt(Tools::getValue('status_id_post'))) {
                $errors[] = $this->l('Invalid Post');
            }
        } elseif (Tools::isSubmit('delete_id_post')) {
            if ((!Validate::isInt(Tools::getValue('delete_id_post')) || !$this->postExists((int)Tools::getValue('delete_id_post')))) {
                $errors[] = $this->l('Invalid id_post');
            }
        } elseif (Tools::isSubmit('submitPost') || Tools::isSubmit('submitPostAndStay')) {
            /* Checks position */
            if (!Validate::isInt(Tools::getValue('ordering')) || (Tools::getValue('ordering') < 0)) {
                $errors[] = $this->l('Invalid Post ordering');
            }
            /* If edit : checks post_id */
            if (Tools::isSubmit('id_post')) {
                if (!Validate::isInt(Tools::getValue('id_post')) && !$this->postExists(Tools::getValue('id_post'))) {
                    $errors[] = $this->l('Invalid id_post');
                }
            }
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                if (Tools::strlen(Tools::getValue('title_'.$language['id_lang'])) > 255) {
                    $errors[] = $this->l('The title is too long.');
                }
                if (Tools::strlen(Tools::getValue('alias_'.$language['id_lang'])) > 255) {
                    $errors[] = $this->l('The URL is too long.');
                }
                if (Tools::strlen(Tools::getValue('introtext_'.$language['id_lang'])) > 4000) {
                    $errors[] = $this->l('The introtext is too long.');
                }
                if (Tools::strlen(Tools::getValue('fulltext_'.$language['id_lang'])) > 40000) {
                    $errors[] = $this->l('The fulltext is too long.');
                }

            }
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            if (Tools::strlen(Tools::getValue('title_'.$id_lang_default)) == 0) {
                $errors[] = $this->l('The title is not set.');
            }
            $this->linkVideos = $this->getVideos();
            if (isset($this->linkVideos['new'])) {
                foreach ($this->linkVideos['new'] as $key => $link) {
                    if (Tools::strlen(trim($link)) == 0) {
                        unset($this->linkVideos['new'][$key]);
                        continue;
                    }
                    if (!preg_match('/^<iframe(.)*<\/iframe>$/', $link)) {
                        $errors[] = $this->l('Embed video must use <iframe> tag');
                        break;
                    }
                }
            }
            if (!count($errors) && isset($this->linkVideos['old'])) {
                foreach ($this->linkVideos['old'] as $key => $video) {
                    if (!preg_match('/^<iframe(.)*<\/iframe>$/', $video->link_video)) {
                        $errors[] = $this->l('Embed video must use <iframe> tag');
                        break;
                    }
                }
            }
        }
        /* Display errors if needed */
        if (count($errors)) {
            $this->_html .= $this->module->displayError(implode('<br />', $errors));
            return false;
        }
        /* Returns if validation is ok */
        return true;
    }
    //remove image and old image process
    private function _postProcess()
    {
        $errors = array();
        $jmsblog_setting = JmsBlogHelper::getSettingFieldsValues();
        if (Tools::isSubmit('submitPost') || Tools::isSubmit('submitPostAndStay')) {
            /* Sets ID if needed */
            if (Tools::getValue('id_post')) {
                $item = new JmsPost((int)Tools::getValue('id_post'));
                if (Tools::getValue('created') == '') {
                    $item->created = date('Y-m-d H:i:s');
                } else {
                    $item->created = Tools::getValue('created');
                }

                if (Tools::getValue('modified') == '') {
                    $item->modified = date('Y-m-d H:i:s');
                } else {
                    $item->modified = Tools::getValue('modified');
                }

                if (!Validate::isLoadedObject($item)) {
                    $this->_html .= $this->displayError($this->l('Invalid id_post'));
                    return;
                }
            } else {
                $item = new JmsPost();
                if (Tools::getValue('created') == '') {
                    $item->created = date('Y-m-d H:i:s');
                } else {
                    $item->created = Tools::getValue('created');
                }
                if (Tools::getValue('modified') == '') {
                    $item->modified = date('Y-m-d H:i:s');
                } else {
                    $item->modified = Tools::getValue('modified');
                }
                $item->views = 0;
                $item->ordering = (int)Tools::getValue('ordering');
            }
            $item->link_video = $this->linkVideos;
            $item->category_id = Tools::getValue('category_id');

            /* Sets active */
            $item->active = (int)Tools::getValue('active');
            $item->heading = (int)Tools::getValue('heading');
            /* Sets each langue fields */
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $item->title[$language['id_lang']] = Tools::getValue('title_'.$language['id_lang']);
                $item->alias[$language['id_lang']] = Tools::getValue('alias_'.$language['id_lang']);
                $item->tags[$language['id_lang']] = Tools::getValue('tags_'.$language['id_lang']);
                if (!$item->alias[$language['id_lang']]) {
                    $item->alias[$language['id_lang']] = JmsBlogHelper::makeAlias($item->title[$language['id_lang']]);
                } else {
                    $item->alias[$language['id_lang']] = JmsBlogHelper::makeAlias($item->alias[$language['id_lang']]);
                }
                $item->introtext[$language['id_lang']] = Tools::getValue('introtext_'.$language['id_lang']);
                $item->fulltext[$language['id_lang']] = Tools::getValue('fulltext_'.$language['id_lang']);
                $item->meta_desc[$language['id_lang']] = Tools::getValue('meta_desc_'.$language['id_lang']);
                $item->meta_key[$language['id_lang']] = Tools::getValue('meta_key_'.$language['id_lang']);
                $item->key_ref[$language['id_lang']] = Tools::getValue('key_ref_'.$language['id_lang']);
                // uploads image was moved to ajaxProcessAddImages()

            }
            /* Processes if no errors  */
            if (!$errors) {
                /* Adds */
                if (!Tools::getValue('id_post')) {
                    if (!$item->add(Tools::getValue('id_duplicate', 0))) {
                        $errors[] = $this->module->displayError($this->l('The post could not be added.'));
                    } elseif (Tools::isSubmit('submitPostAndStay')) {
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmsblogPost').'&conf=4&&id_post='.$item->id.'&post_tab='.Tools::getValue('post_tab',0));
                    }
                } elseif (!$item->update()) {
                /* Update */
                    $errors[] = $this->module->displayError($this->l('The post could not be updated.'));
                } elseif (Tools::isSubmit('submitPostAndStay')) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmsblogPost').'&conf=4&&id_post='.$item->id.'&post_tab='.Tools::getValue('post_tab',0));
                }
            }
        } elseif (Tools::isSubmit('delete_id_post')) {
            $item = new JmsPost((int)Tools::getValue('delete_id_post'));
            $res = $item->delete();
            if (!$res) {
                $this->_html .= Tools::displayError('Could not delete');
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmsblogPost', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&start='.Tools::getValue('start',0).'&limit='.Tools::getValue('limit',0));
            }
        } elseif (Tools::isSubmit('changePostStatus') && Tools::isSubmit('status_id_post')) {
            $item = new JmsPost((int)Tools::getValue('status_id_post'));
            if ($item->active == 0) {
                $item->active = 1;
            } else {
                $item->active = 0;
            }
            $res = $item->update();
            if (!$res) {
                $this->_html .= Tools::displayError('The status could not be updated.');
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmsblogPost', true).'&conf=5&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&start='.Tools::getValue('start',0).'&limit='.Tools::getValue('limit',0));
            }
        }
        if (count($errors)) {
            $this->_html .= Tools::displayError(implode('<br />', $errors));
        } elseif (Tools::isSubmit('submitPost') && Tools::getValue('id_post')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmsblogPost', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
        }
    }

    public function postExists($id_post)
    {
        $req = 'SELECT hs.`post_id`
                FROM `'._DB_PREFIX_.'jmsblog_posts` hs
                WHERE hs.`post_id` = '.(int)$id_post;
        $post = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);
        return ($post);
    }

    public function getPosts($category_id = 0, $state = -1, $start = 0, $limit = 20)
    {
        $this->context = Context::getContext();
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $filter = '';
        if ($state != -1) {
            $filter = ' AND hss.`active` = '.$state;
        }
        $sql = '
            SELECT hss.`post_id` as post_id, hss.`category_id`, hss.`ordering`, hss.`active`, hssl.`title`, hssll.`title` as category_title,
            hssl.`alias`,hssl.`fulltext`,hssl.`introtext`,hssl.`meta_desc`,hssl.`meta_key`,hssl.`key_ref`
            FROM '._DB_PREFIX_.'jmsblog_posts hss
            LEFT JOIN '._DB_PREFIX_.'jmsblog_posts_lang hssl ON (hss.`post_id` = hssl.`post_id`)
            LEFT JOIN '._DB_PREFIX_.'jmsblog_categories_lang hssll ON (hss.`category_id` = hssll.`category_id`)
            LEFT JOIN '._DB_PREFIX_.'jmsblog_shop js ON (js.category_id = hss.category_id)
            WHERE hssl.`id_lang` = '.(int)$id_lang.' AND js.`id_shop` = '.(int)$id_shop.
            ' AND hssll.`id_lang` = '.(int)$id_lang.
            $filter.
            ($category_id ? ' AND hss.`category_id` = '.$category_id : ' ').'
            ORDER BY hss.`ordering`, hss.post_id DESC
            LIMIT '.$start.','.$limit;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function getPostCount($category_id = 0, $state = -1)
    {
        $this->context = Context::getContext();
        $id_lang = $this->context->language->id;
        $filter = '';
        if ($state != -1) {
            $filter = ' AND hss.`active` = '.$state;
        }
        $sql = '
            SELECT COUNT(hss.`post_id`)
            FROM '._DB_PREFIX_.'jmsblog_posts hss
            LEFT JOIN '._DB_PREFIX_.'jmsblog_posts_lang hssl ON (hss.`post_id` = hssl.`post_id`)
            WHERE hssl.`id_lang` = '.(int)$id_lang.
            $filter.
            ($category_id ? ' AND hss.`category_id` = '.$category_id : ' ').'
            ORDER BY hss.`post_id`';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
    public function renderFilter()
    {
        $filter_category_id = $this->context->cookie->filter_category_id;
        $filter_state = $this->context->cookie->filter_state;
        $this->getCategorySelect(0, 0);
        $categories = $this->catselect;
        $tpl = $this->createTemplate('filter.tpl');
        $tpl->assign(array(
            'categories'=>$categories,
            'filter_state'=>$filter_state,
            'link' => $this->context->link,
            'filter_category_id' => $filter_category_id
        ));
        return $tpl->fetch();
    }

    public function renderPagination()
    {
        $start = (int)Tools::getValue('start', 0);
        $limit = (int)Tools::getValue('limit', Configuration::get('JMSBLOG_ITEMS_PER_PAGE'));
        $total = $this->getPostCount($this->context->cookie->filter_category_id, $this->context->cookie->filter_state);
        if ($total % $limit) {
            $pages = (int)($total / $limit) + 1;
        } else {
            $pages = $total / $limit;
        }
        $tpl = $this->createTemplate('pagination.tpl');
        $tpl->assign(array(
            'start'=>$start,
            'limit'=>$limit,
            'pages'=>$pages,
            'total'=>$total,
            'link' => $this->context->link
        ));

        return $tpl->fetch();
    }

    public function renderListPost($category_id = 0, $state = -1, $start = 0, $limit = 20)
    {
        $this->context->controller->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin_style.css', 'all');
        $this->context->controller->addJqueryUI('ui.draggable');
        if (!$category_id) {
            $category_id = (int)Tools::getValue('filter_category_id', 0);
        }
        $items = $this->getPosts($category_id, $state, $start, $limit);
        $tpl = $this->createTemplate('listposts.tpl');
        $tpl->assign(array(
            'category_id'=>$category_id,
            'state'=>$state,
            'start'=>$start,
            'limit'=>$limit,
            'link' => $this->context->link,
            'items' => $items,
        ));
        return $tpl->fetch();
    }
    public function renderNavigation()
    {
        $html = '<div class="navigation">';
        $html .= '<a class="btn btn-default" href="'.AdminController::$currentIndex.
            '&configure='.$this->name.'
                &token='.Tools::getAdminTokenLite('AdminJmsblogPost').'" title="Back to Dashboard"><i class="icon-home"></i>Back to Dashboard</a>';
        $html .= '</div>';
        return $html;
    }
    public function getCategorySelect($parent = 0, $lvl = 0)
    {
        $lvl ++;
        $str = '';
        for ($i = 1; $i <= $lvl; $i++) {
            $str .= '- ';
        }
        $this->context = Context::getContext();
        $id_lang = $this->context->language->id;
        $sql = '
            SELECT hss.`category_id` as category_id,hssl.`title`, hssl.`alias`
            FROM '._DB_PREFIX_.'jmsblog_categories hss
            LEFT JOIN '._DB_PREFIX_.'jmsblog_categories_lang hssl ON (hss.`category_id` = hssl.`category_id`)
            WHERE hssl.`id_lang` = '.(int)$id_lang.
            ' AND hss.`parent` = '.$parent.'
            ORDER BY hss.`category_id` ASC';
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (count($items)) {
            while ($element = current($items)) {
                $items[key($items)]['lvl'] = $lvl;
                $items[key($items)]['title'] = $str.$items[key($items)]['title'];
                $this->catselect[] = $items[key($items)];
                $this->getCategorySelect($element['category_id'], $lvl);
                next($items);
            }
        }
    }
    public function renderAddPost()
    {
        // set media functions was moved to renderExtraTabs()
        $this->getCategorySelect(0, 0);
        $categories = $this->catselect;
        array_unshift($categories, array('category_id' => 0,'title' => 'Root Category'));
        $this->fields_form = array(
            'legend' => array(
                    'title' => $this->l('Post informations'),
                    'icon' => 'icon-cogs'
                ),
            'buttons' => $this->buttons,
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'lang' => true,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Heading'),
                    'name' => 'heading',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'heading_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'heading_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Category'),
                    'name' => 'category_id',
                    'desc' => $this->l('Please Select a category'),
                    'options' => array('query' => $categories,'id' => 'category_id','name' => 'title')
                ),
                array(
                    'type' => 'datetime',
                    'label' => $this->l('Created'),
                    'name' => 'created',
                    'desc' => $this->l('Created Time')
                ),
                array(
                    'type' => 'datetime',
                    'label' => $this->l('Modified'),
                    'name' => 'modified',
                    'desc' => $this->l('Modified Time')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Introtext'),
                    'name' => 'introtext',
                    'autoload_rte' => true,
                    'lang' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Fulltext'),
                    'name' => 'fulltext',
                    'autoload_rte' => true,
                    'lang' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Key Reference'),
                    'name' => 'key_ref',
                    'lang' => true,
                    'desc' => $this->l('Used to store information referring to an external resource .')
                ),
                array(
                    'type' => 'file_tags',
                    'label' => $this->l('Tags'),
                    'name' => 'tags',
                    'lang' => true,
                ),
            ),
        );
        if (Tools::isSubmit('id_post')) {
            $item = new JmsPost((int)Tools::getValue('id_post'));
            $this->fields_form['input'][] = array('type' => 'hidden', 'name' => 'id_post');
            $this->fields_form['tags'] = $item->tags;
        }

        $this->fields_value = $this->getPostFieldsValues();
        $this->tpl_folder = 'jmsblogform/';
        $this->tpl_form_vars = array(
            'base_url' => $this->root_url,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return adminController::renderForm();
    }
    public function getPostFieldsValues()
    {
        $fields = array();
        if (Tools::isSubmit('id_post') && $this->postExists((int)Tools::getValue('id_post'))) {
            $item = new JmsPost((int)Tools::getValue('id_post'));
            $fields['id_post']      = (int)Tools::getValue('id_post', $item->id);
            $fields['category_id']  = (int)Tools::getValue('category_id', $item->category_id);
            $fields['created']      = Tools::getValue('created', $item->created);
            $fields['modified']     = Tools::getValue('modified', $item->modified);
        } else {
            $item = new JmsPost();
        }
        $fields['active'] = Tools::getValue('active', $item->active);
        $fields['heading'] = Tools::getValue('heading', $item->heading);
        $fields['category_id'] = (int)Tools::getValue('category_id', $item->category_id);
        $fields['has_picture'] = true;
        $fields['cat_id_current'] = (int)Tools::getValue('cat_id_current', 0);
        $fields['state'] = (int)Tools::getValue('state', -1);
        $fields['start'] = (int)Tools::getValue('start', 0);
        $fields['limit'] = (int)Tools::getValue('limit', 20);

        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $fields['title'][$lang['id_lang']]  = Tools::getValue('title_'.(int)$lang['id_lang'], $item->title[$lang['id_lang']]);
            $fields['introtext'][$lang['id_lang']] = Tools::getValue('introtext_'.(int)$lang['id_lang'], $item->introtext[$lang['id_lang']]);
            $fields['fulltext'][$lang['id_lang']] = Tools::getValue('fulltext_'.(int)$lang['id_lang'], $item->fulltext[$lang['id_lang']]);
            $fields['key_ref'][$lang['id_lang']] = Tools::getValue('key_ref_'.(int)$lang['id_lang'], $item->key_ref[$lang['id_lang']]);
            $fields['tags'][$lang['id_lang']] = Tools::getValue('tags_'.$lang['id_lang'], $item->tags[$lang['id_lang']]);
        }
        return $fields;
    }


    public function headerHTML()
    {
        if (Tools::getValue('controller') != 'AdminJmsblogPost' && Tools::getValue('configure') != $this->name) {
            return;
        }
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->addJqueryPlugin('tagify');
        $html = '<script type="text/javascript">
            $(function() {
                var $posts = $("#posts");
                $posts.sortable({
                    opacity: 0.6,
                    cursor: "move",
                    update: function() {
                        var order = $(this).sortable("serialize") + "&action=updatePostOrdering&start='.Tools::getValue('start',0).'";
                        $.post("'.$this->root_url.'modules/'.$this->name.'/ajax_'.$this->name.'.php?secure_key='.$this->secure_key.'", order, function(data) {
                            console.log(data);
                        });
                    },
                    stop: function( event, ui ) {
                        showSuccessMessage("Saved!");
                    }
                });
                $posts.hover(function() {
                    $(this).css("cursor","move");
                    },
                    function() {
                    $(this).css("cursor","auto");
                });
            });
        </script>';

        return $html;
    }
}
