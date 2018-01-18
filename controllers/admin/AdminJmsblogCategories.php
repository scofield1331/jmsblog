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
include_once(_PS_MODULE_DIR_.'jmsblog/JmsCategory.php');
class AdminJmsblogCategoriesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->name = 'jmsblog';
        $this->tab = 'front_office_features';
        $this->bootstrap = true;
        $this->root_url = JmsBlogHelper::getUrl();
        $this->lang = true;
        $this->context = Context::getContext();
        $this->secure_key = Tools::encrypt($this->name);
        $this->child    = array();
        $this->tree = array();
        parent::__construct();
    }

    public function renderList()
    {

        $this->_html = $this->headerHTML();
        /* Validate & process */
        if (Tools::isSubmit('CancelAddForm')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmsblogCategories', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
        } elseif (Tools::isSubmit('submitCategory') || Tools::isSubmit('duplicate') || Tools::isSubmit('submitCategoryAndStay') || Tools::isSubmit('delete_id_category') || Tools::isSubmit('changeCategoryStatus')) {
            if (Tools::isSubmit('duplicate')) {
                $this->duplicate();
            }
            if ($this->_postValidation()) {
                $this->_postProcess();
                $this->_html .= $this->renderNavigation();
                $this->_html .= $this->renderListCategories();
            } else {
                $this->_html .= $this->renderNavigation();
                $this->_html .= $this->renderExtraTabs();
            }
        } elseif (Tools::isSubmit('addCategory') || ((Tools::isSubmit('id_category') && $this->categoryExists((int)Tools::getValue('id_category'))))) {
            $this->_html .= $this->renderNavigation();
            $this->_html .= $this->renderExtraTabs();
        } else {
            $this->_html .= $this->renderNavigation();
            $this->_html .= $this->renderListCategories();
        }
        return $this->_html;
    }
    private function duplicate()
    {
        $_POST['submitCategory'] = 1;
        $_POST['active'] = 0;
        unset($_POST['id_category']);
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $_POST['title_'.$language['id_lang']] .= ' - Copy';
            if (isset($_FILES['image_'.$language['id_lang']]['tmp_name']) && $_FILES['image_'.$language['id_lang']]['tmp_name'] != '') {
            p($language['id_lang']);
                unset($_POST['image_old_'.$language['id_lang']]);
            }
        }
    }
    private function _postValidation()
    {
        $errors = array();

        /* Validation for configuration */
        if (Tools::isSubmit('changeCategoryStatus')) {
            if (!Validate::isInt(Tools::getValue('status_id_category'))) {
                $errors[] = $this->l('Invalid Category');
            }
        } elseif (Tools::isSubmit('delete_id_category')) {
            if ((!Validate::isInt(Tools::getValue('delete_id_category')) || !$this->categoryExists((int)Tools::getValue('delete_id_category')))) {
                $errors[] = $this->l('Invalid id_category');
            }
        } elseif (Tools::isSubmit('submitCategory')  || Tools::isSubmit('submitCategoryAndStay')) {
        /* Checks position */
            if (!Validate::isInt(Tools::getValue('ordering')) || (Tools::getValue('ordering') < 0)) {
                $errors[] = $this->l('Invalid Category ordering');
            }
            /* If edit : checks post_id */
            if (Tools::isSubmit('id_category')) {
                if (!Validate::isInt(Tools::getValue('id_category')) && !$this->itemExists(Tools::getValue('id_category'))) {
                    $errors[] = $this->l('Invalid id_category');
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
                if (Tools::strlen(Tools::getValue('description_'.$language['id_lang'])) > 4000) {
                    $errors[] = $this->l('The description is too long.');
                }
                if (Tools::getValue('image_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_'.$language['id_lang']))) {
                    $errors[] = $this->l('Invalid filename');
                }
                if (Tools::getValue('image_old_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_old_'.$language['id_lang']))) {
                    $errors[] = $this->l('Invalid filename');
                }

            }
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            if (Tools::strlen(Tools::getValue('title_'.$id_lang_default)) == 0) {
                $errors[] = $this->l('The title is not set.');
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
    private function _postProcess()
    {
        $errors = array();
        $jmsblog_setting = JmsBlogHelper::getSettingFieldsValues();
        if (Tools::isSubmit('submitCategory') || Tools::isSubmit('submitCategoryAndStay')) {
            if (Tools::getValue('id_category')) {
                $item = new JmsCategory((int)Tools::getValue('id_category'));
                if (!Validate::isLoadedObject($item)) {
                    $this->_html .= $this->displayError($this->l('Invalid id_category'));
                    return;
                }
            } else {
                $item = new JmsCategory();
                $item->ordering = 0;
            }
            if (Tools::isSubmit('duplicate')) {
                $base_url = _PS_MODULE_DIR_.'/jmsblog/views/img/';
                $languages = Language::getLanguages(false);
                foreach ($languages as $language) {
                    $image = Tools::getValue('image_old_'.$language['id_lang'], false);
                    $type = strrchr($image, '.');
                    $newImage = sha1(microtime()).$type;
                    if ($image && file_exists($base_url.$image)) {
                        copy($base_url.$image, $base_url.$newImage);
                        copy($base_url.'resized_'.$image, $base_url.'resized_'.$newImage);
                        copy($base_url.'thumb_'.$image, $base_url.'thumb_'.$newImage);
                        $item->image[$language['id_lang']] = $newImage;
                    }
                    unset($_POST['image_old_'.$language['id_lang']]);
                }
                $this->confirmations[] = $this->l('Duplicated');
            }
            /* Sets ordering */
            $item->parent = Tools::getValue('parent');
            /* Sets active */
            $item->active = (int)Tools::getValue('active');
            /* Sets each langue fields */
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $item->title[$language['id_lang']] = Tools::getValue('title_'.$language['id_lang']);
                $item->alias[$language['id_lang']] = Tools::getValue('alias_'.$language['id_lang']);
                if (!$item->alias[$language['id_lang']]) {
                    $item->alias[$language['id_lang']] = JmsBlogHelper::makeAlias($item->title[$language['id_lang']]);
                } else {
                    $item->alias[$language['id_lang']] = JmsBlogHelper::makeAlias($item->alias[$language['id_lang']]);
                }
                $item->description[$language['id_lang']] = Tools::getValue('description_'.$language['id_lang']);
                $item->meta_desc[$language['id_lang']] = Tools::getValue('meta_desc_'.$language['id_lang']);
                $item->meta_key[$language['id_lang']] = Tools::getValue('meta_key_'.$language['id_lang']);

                /* Uploads image and sets item */
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES['image_'.$language['id_lang']]['name'], '.'), 1));
                $imagesize = array();
                $imagesize = @getimagesize($_FILES['image_'.$language['id_lang']]['tmp_name']);
                //echo "aaa"; exit;
                if (isset($_FILES['image_'.$language['id_lang']]) && isset($_FILES['image_'.$language['id_lang']]['tmp_name']) && !empty($_FILES['image_'.$language['id_lang']]['tmp_name']) && !empty($imagesize) && in_array(Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)), array('jpg', 'gif', 'jpeg', 'png')) && in_array($type, array('jpg', 'gif', 'jpeg', 'png'))) {

                    $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                    $salt = sha1(microtime());
                    if ($error = ImageManager::validateUpload($_FILES['image_'.$language['id_lang']])) {
                        $errors[] = $error;
                    } elseif (!$temp_name || !move_uploaded_file($_FILES['image_'.$language['id_lang']]['tmp_name'], $temp_name)) {
                        return false;
                    } elseif (!ImageManager::resize($temp_name, _PS_MODULE_DIR_.'/jmsblog/views/img/'.Tools::encrypt($_FILES['image_'.$language['id_lang']]['name'].$salt).'.'.$type, null, null, $type)) {
                        $errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
                    }
                    if (isset($temp_name)) {
                        @unlink($temp_name);
                    }
                    $item->image[$language['id_lang']] = Tools::encrypt($_FILES['image_'.($language['id_lang'])]['name'].$salt).'.'.$type;
                    JmsBlogHelper::createThumb(_PS_MODULE_DIR_.'/jmsblog/views/img/', Tools::encrypt($_FILES['image_'.($language['id_lang'])]['name'].$salt).'.'.$type, $jmsblog_setting['JMSBLOG_IMAGE_WIDTH'], $jmsblog_setting['JMSBLOG_IMAGE_HEIGHT'], 'resized_', 0);
                    JmsBlogHelper::createThumb(_PS_MODULE_DIR_.'/jmsblog/views/img/', Tools::encrypt($_FILES['image_'.($language['id_lang'])]['name'].$salt).'.'.$type, $jmsblog_setting['JMSBLOG_IMAGE_THUMB_WIDTH'], $jmsblog_setting['JMSBLOG_IMAGE_THUMB_HEIGHT'], 'thumb_', 1);
                    //delete old img
                    $old_img = Tools::getValue('image_old_'.$language['id_lang']);
                    if ($old_img && file_exists(_PS_MODULE_DIR_.'/jmsblog/views/img/'.$old_img)) {
                        @unlink(_PS_MODULE_DIR_.'/jmsblog/views/img/'.$old_img);
                        @unlink(_PS_MODULE_DIR_.'/jmsblog/views/img/resized_'.$old_img);
                        @unlink(_PS_MODULE_DIR_.'/jmsblog/views/img/thumb_'.$old_img);
                    }
                } elseif (Tools::getValue('image_old_'.$language['id_lang']) != '') {
                    $item->image[$language['id_lang']] = Tools::getValue('image_old_'.$language['id_lang']);
                }
            }

            /* Processes if no errors  */
            if (!$errors) {
                /* Adds */
                if (!Tools::getValue('id_category')) {
                    if (!$item->add()) {
                        $errors[] = $this->displayError($this->l('The item could not be added.'));
                    } elseif (Tools::isSubmit('submitCategoryAndStay')) {
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmsblogCategories').'&conf=4&&id_category='.$item->id.'&category_tab='.Tools::getValue('post_tab',0));
                    }
                } elseif (!$item->update()) {
                /* Update */
                    $errors[] = $this->displayError($this->l('The item could not be updated.'));
                } elseif (Tools::isSubmit('submitCategoryAndStay')) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmsblogCategories').'&conf=4&&id_category='.$item->id.'&category_tab='.Tools::getValue('post_tab',0));
                }
            }
        } elseif (Tools::isSubmit('delete_id_category')) {
            $item = new JmsCategory((int)Tools::getValue('delete_id_category'));
            $res = $item->delete();
            if (!$res) {
                $this->_html .= Tools::displayError('Could not delete');
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmsblogCategories', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&start='.Tools::getValue('start',0).'&limit='.Tools::getValue('limit',0));
            }
        } elseif (Tools::isSubmit('changeCategoryStatus') && Tools::isSubmit('status_id_category')) {
            $item = new JmsCategory((int)Tools::getValue('status_id_category'));
            if ($item->active == 0) {
                $item->active = 1;
            } else {
                $item->active = 0;
            }
            $res = $item->update();
            if (!$res) {
                $this->_html .= Tools::displayError('The status could not be updated.');
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmsblogCategories', true).'&conf=5&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&start='.Tools::getValue('start',0).'&limit='.Tools::getValue('limit',0));
            }
        }

        if (count($errors)) {
            $this->_html .= Tools::displayError(implode('<br />', $errors));
        } elseif (Tools::isSubmit('submitCategory') && Tools::getValue('id_category')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmsblogCategories', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
        }
    }

    public function categoryExists($id_category)
    {
        $req = 'SELECT hs.`category_id`
                FROM `'._DB_PREFIX_.'jmsblog_categories` hs
                WHERE hs.`category_id` = '.(int)$id_category;
        $_category = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);
        return ($_category);
    }

    public function treeCats($parent = 0, $lvl = 0, $limit = 0, $offset = 0)
    {
        $lvl++;
        $this->context = Context::getContext();
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $sql = '
            SELECT hss.`category_id` as category_id, hssl.`image`, hss.`ordering`, hss.`active`, hssl.`title`
            FROM '._DB_PREFIX_.'jmsblog_categories hss
            LEFT JOIN '._DB_PREFIX_.'jmsblog_categories_lang hssl ON (hss.`category_id` = hssl.`category_id`)
            LEFT JOIN '._DB_PREFIX_.'jmsblog_shop js ON (js.category_id = hss.category_id)
            WHERE hssl.`id_lang` = '.(int)$id_lang.' AND js.`id_shop` = '.(int)$id_shop.
            ' AND hss.`parent` = '.$parent.'
            ORDER BY hss.ordering, hss.`category_id` ASC ';
        if ($lvl==1 && $limit) {
            $sql.= "LIMIT $offset, $limit";
        }
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (count($items)) {
            while ($element = current($items)) {
                $items[key($items)]['lvl'] = $lvl;
                $items[key($items)]['item_count'] = $this->getItemCount($element['category_id']);
                $items[key($items)]['titlelv'] = $this->makeTitleLevel($element['title'], $lvl);
                $this->child[] = $items[key($items)];
                $items[key($items)]['childs']=$this->treeCats($element['category_id'], $lvl);
                next($items);
            }
        }
        return $items;
    }
    public function getItemCount($category_id)
    {
        $sql = '
            SELECT COUNT(hss.`post_id`)
            FROM '._DB_PREFIX_.'jmsblog_posts hss
            WHERE hss.`category_id` = '.(int)$category_id;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
    public function makeTitleLevel($title, $level)
    {
        $result_title = '';
        for ($i = 1; $i < $level; $i++) {
            $result_title .= '----';
        }
        $result_title .= "  ".$title;
        return $result_title;
    }
    private function getCategoriesCount()
    {
        $sql = new DbQuery();
        $sql->select('COUNT(*)');
        $sql->from('jmsblog_categories');
        $sql->where('parent = 0');
        return Db::getInstance()->getValue($sql);
    }
    public function renderListCategories()
    {
        $this->context->controller->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin_style.css', 'all');
        $this->context->controller->addJqueryUI('ui.draggable');
        $start = Tools::getValue('start', 0);
        $limit = Configuration::get('JMSBLOG_ITEMS_PER_PAGE');
        $total = $this->getCategoriesCount();
        if ($total % $limit) {
            $pages = (int)($total / $limit) + 1;
        } else {
            $pages = $total / $limit;
        }
        $this->tree = $this->treeCats(0, 0, $limit, $start);
        $items = $this->child;
        $tpl = $this->createTemplate('listcategories.tpl');
        $tpl->assign(array(
            'link' => $this->context->link,
            'items' => $items,
            'tree' => $this->tree,
            'start'=>$start,
            'limit'=>$limit,
            'pages'=>$pages,
            'link' => $this->context->link,
            'total'=>$total
        ));
        return $tpl->fetch();
    }
    public function renderNavigation()
    {
        $html = '<div class="navigation">';
        $html .= '<a class="btn btn-default" href="'.AdminController::$currentIndex.
            '&configure='.$this->name.'
                &token='.Tools::getAdminTokenLite('AdminJmsblogCategories').'" title="Back to Dashboard"><i class="icon-home"></i>Back to Dashboard</a>';
        $html .= '</div>';
        return $html;
    }
    public function getParentOptions($category_id = 0)
    {
        $this->treeCats(0,0);
        return $this->child;
        // $this->context = Context::getContext();
        // $id_lang = $this->context->language->id;
        // $cat_arr = array();
        // $sql = '
        //     SELECT hss.`category_id` as category_id, hssl.`title` as title
        //     FROM '._DB_PREFIX_.'jmsblog_categories hss
        //     LEFT JOIN '._DB_PREFIX_.'jmsblog_categories_lang hssl ON (hss.`category_id` = hssl.`category_id`)
        //     WHERE hssl.`id_lang` = '.(int)$id_lang.'
        //     ORDER BY hss.`ordering`';
        // $cats = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        // $total_cats = count($cats);
        // for ($i = 0; $i < $total_cats; $i++) {
        //     $check = $this->isChild($category_id, $cats[$i]['category_id']);
        //     if (!$check) {
        //         $cat_arr[] = $cats[$i];
        //     }
        // }
        // return $cat_arr;
    }
    public function isChild($parent_id, $test_id)
    {
        $isChild = 0;
        if ($parent_id == $test_id) {
            $isChild = 1;
        } else {
            $sql = '
                SELECT hss.`category_id` as category_id, hss.`parent` as parent
                FROM '._DB_PREFIX_.'jmsblog_categories hss
                ORDER BY hss.`ordering`';
            $cat_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $cats = array();
            $total_catlist = count($cat_list);
            for ($i = 0; $i < $total_catlist; $i ++) {
                $cats[$cat_list[$i]['category_id']] = $cat_list[$i]['parent'];
            }
            while ($cats[$test_id] != 0) {
                if ($cats[$test_id] == $parent_id) {
                    $isChild = 1;
                }
                $test_id = $cats[$test_id];
            }
        }
        return $isChild;
    }
    public function renderAddCategory()
    {
        $category_id    = (int)Tools::getValue('id_category', 0);
        $categories     = $this->getParentOptions($category_id);
        if (!count($categories)) {
            $categories = array();
        }
        array_unshift($categories, array ( 'category_id' => 0,'titlelv' => 'Root Category' ));
        $this->fields_form = array(
            'legend' => array(
                    'title' => $this->l('Category informations'),
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
                        'type' => 'select',
                        'lang' => true,
                        'label' => $this->l('Parent Category'),
                        'name' => 'parent',
                        'desc' => $this->l('Please Select parent category'),
                        'options' => array('query' => $categories,'id' => 'category_id','name' => 'titlelv')
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Description'),
                        'name' => 'description',
                        'autoload_rte' => true,
                        'lang' => true,
                    ),
                    array(
                        'type' => 'file_lang',
                        'label' => $this->l('Image'),
                        'name' => 'image',
                        'lang' => true,
                        'desc' => $this->l(sprintf('Max image size %s', ini_get('upload_max_filesize')))
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
                ),
        );
        if (Tools::isSubmit('id_category')) {
            $item = new JmsCategory((int)Tools::getValue('id_category'));
            $this->fields_form['input'][] = array('type' => 'hidden', 'name' => 'id_category');

            $has_picture = false;
            foreach (Language::getLanguages(false) as $lang) {
                if (isset($item->image[$lang['id_lang']]) && Tools::strlen($item->image[$lang['id_lang']]) > 0) {
                    $has_picture = true;
                }
            }

            if ($has_picture) {
                $this->fields_form['input']['has_picture'] = array('type' => 'hidden', 'name' => 'has_picture');
            }
            if ($has_picture) {
                $this->fields_form['images'] = $item->image;
            }

        }
        $this->fields_value = $this->getCategoryFieldsValues();
        $this->tpl_folder = 'jmsblogform/';
        $this->tpl_form_vars = array(
            'base_url' => $this->root_url,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => $this->root_url.'modules/jmsblog/views/img/'
        );
        return adminController::renderForm();
    }
    public function getCategoryFieldsValues()
    {
        $fields = array();
        if ((int)Tools::getValue('id_category')) {
            $item = new JmsCategory((int)Tools::getValue('id_category'));
            $fields['id_category']  = (int)Tools::getValue('id_category', $item->id);
        } else {
            $item = new JmsCategory();
        }
        $fields['parent']   = (int)Tools::getValue('parent', $item->parent);
        $fields['active'] = Tools::getValue('active', $item->active);

        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $fields['image'][$lang['id_lang']] = Tools::getValue('image_'.(int)$lang['id_lang']);
            $fields['title'][$lang['id_lang']] = Tools::getValue('title_'.(int)$lang['id_lang'], $item->title[$lang['id_lang']]);
            $fields['description'][$lang['id_lang']] = Tools::getValue('description_'.(int)$lang['id_lang'], $item->description[$lang['id_lang']]);
        }
        return $fields;
    }


    public function headerHTML()
    {
        if (Tools::getValue('controller') != 'AdminJmsblogCategories' && Tools::getValue('configure') != $this->name) {
            return;
        }
        $this->context->controller->addJqueryUI('ui.sortable');
        $html = '<script type="text/javascript">
                    $(function() {
                    $("#categories").hover(function() {
                        $(this).css("cursor","move");
                        },
                        function() {
                        $(this).css("cursor","auto");
                    });';
        for ($i=0; $i <= 3; $i++) {
            $html.=$this->headerScript($i);
        }
        $html .= '});</script>';

        return $html;
    }
    private function headerScript($lvl)
    {
        if($lvl) {
            $selector = '.submenu'.$lvl;
        }
        else {
            $selector = '#categories';
        }
        $script = '
                    $("'.$selector.'").sortable({
                        opacity: 0.6,
                        cursor: "move",'.(($lvl)?'containment: "parent",':'').'
                        update: function() {
                            var order = $(this).sortable("serialize") + "&action=updateCategoryOrdering&start='.Tools::getValue('start',0).'";
                            $.post("'.$this->root_url.'modules/'.$this->name.'/ajax_'.$this->name.'.php?secure_key='.$this->secure_key.'", order);
                        },
                        stop: function( event, ui ) {
                            showSuccessMessage("Saved!");
                        }
                    })';
        return $script;
    }
    private function renderExtraTabs()
    {
        $this->context->controller->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin_style.css', 'all');
        $this->context->controller->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin.js', 'all');
        $this->context->controller->addJqueryUI('ui.draggable');
        $this->addJqueryUI('ui.tabs');
        $this->buttons = array(
            'save' => array(
                'title' => $this->l('Save'),
                'name' => 'submitCategory',
                'class' => 'btn btn-default pull-right submitCategory',
                'icon' => 'process-icon-save',
            ),
            'save-and-stay' => array(
                'title' => $this->l('Save and Stay'),
                'name' => 'submitCategoryAndStay',
                'class' => 'btn btn-default pull-right submitCategoryAndStay',
                'icon' => 'process-icon-save',
            ),
        );
        if (Tools::isSubmit('id_category')) {
            $this->buttons['duplicate'] = array(
                'title' => $this->l('Duplicate Category'),
                'name' => 'duplicate',
                'class' => 'btn btn-default pull-right duplicate',
                'icon' => 'process-icon- icon-copy',
            );
        }
        $form = array(
                'Informations'   => $this->renderAddCategory(),
                'SEO'   => $this->renderFormSEO(),
        );
        $tpl = $this->createTemplate('extraTabs.tpl');
        $tab = Tools::getValue('category_tab',0);
        $tabindex = Validate::isUnsignedInt($tab)?$tab:array_search($tab,array_keys($form));
        $tpl->assign(array(
            'form' => $form,
            'post_tab'   => $tabindex
        ));
        return $tpl->fetch();
    }
    private function renderFormSEO()
    {
        $default_lang = $this->default_form_language;
        $route_id = 'jmsblog-category';
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
                        'rule' => str_replace('{/:start}', '', $default_routes['rule']),
                        'keywords' => json_encode(array_keys($default_routes['keywords'])),
                        'url' => jmsblog::getJmsBlogUrl(),
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
        if (Tools::isSubmit('id_category')) {
            $item = new JmsCategory((int)Tools::getValue('id_category'));
        } else {
            $item = new JmsCategory();
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
            $helper->fields_value['generate'][$lang['id_lang']] = array(
                'category_id' => $item->id,
                'slug' => $item->alias[$lang['id_lang']],
                'start' => '',
            );
        }
        return $helper->generateForm($fields_form);
    }
}
