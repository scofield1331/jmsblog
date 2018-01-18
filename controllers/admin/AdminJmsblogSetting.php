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
class AdminJmsblogSettingController extends ModuleAdminController
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
        parent::__construct();
    }

    public function renderList()
    {

        $this->_html = '';
        /* Validate & process */
        if (Tools::isSubmit('submitSetting')) {
            if ($this->_postValidation()) {
                $this->_postProcess();
                $this->_html .= $this->renderSettingForm();
            } else {
                $this->_html .= $this->renderNavigation();
                $this->_html .= $this->renderSettingForm();
            }
        } else {
            $this->_html .= $this->renderSettingForm();
        }
        return $this->_html;
    }

    private function _postValidation()
    {
        $errors = array();

        /* Validation for configuration */
        if (Tools::isSubmit('submitSetting')) {
            if (!Validate::isInt(Tools::getValue('status_id_category'))) {
                $errors[] = $this->l('Invalid Category');
            }
            if (!Validate::isUnsignedInt(Tools::getValue('JMSBLOG_ITEMS_PER_PAGE'))) {
                $errors[] = $this->l('Invalid items per page');
            }
            if (!Validate::isUnsignedInt(Tools::getValue('JMSBLOG_MAX_HEADING_POST'))) {
                $errors[] = $this->l('Invalid heading post number');
            }
            if (!Validate::isUnsignedInt(Tools::getValue('JMSBLOG_MAX_SUB_POST'))) {
                $errors[] = $this->l('Invalid subheading post number');
            }
            if (!Validate::isUnsignedInt(Tools::getValue('JMSBLOG_MAX_HEADINGPOST_CATEGORY'))) {
                $errors[] = $this->l('Invalid heading post number');
            }
            if (!Validate::isUnsignedInt(Tools::getValue('JMSBLOG_MAX_SUBPOST_CATEGORY'))) {
                $errors[] = $this->l('Invalid subheading post number');
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
        if (Tools::isSubmit('submitSetting')) {
            $res = Configuration::updateValue('JMSBLOG_INTROTEXT_LIMIT', (int)Tools::getValue('JMSBLOG_INTROTEXT_LIMIT'));
            $res &= Configuration::updateValue('JMSBLOG_ITEMS_PER_PAGE', Tools::getValue('JMSBLOG_ITEMS_PER_PAGE'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_CATEGORY', Tools::getValue('JMSBLOG_SHOW_CATEGORY'));
            $res &= Configuration::updateValue('JMSBLOG_BOOTSTRAP', Tools::getValue('JMSBLOG_BOOTSTRAP'));

            $res &= Configuration::updateValue('JMSBLOG_IMAGE_WIDTH', (int)Tools::getValue('JMSBLOG_IMAGE_WIDTH'));
            $res &= Configuration::updateValue('JMSBLOG_IMAGE_HEIGHT', (int)Tools::getValue('JMSBLOG_IMAGE_HEIGHT'));
            $res &= Configuration::updateValue('JMSBLOG_IMAGE_THUMB_WIDTH', (int)Tools::getValue('JMSBLOG_IMAGE_THUMB_WIDTH'));
            $res &= Configuration::updateValue('JMSBLOG_IMAGE_THUMB_HEIGHT', (int)Tools::getValue('JMSBLOG_IMAGE_THUMB_HEIGHT'));

            $res &= Configuration::updateValue('JMSBLOG_COMMENT_ENABLE', Tools::getValue('JMSBLOG_COMMENT_ENABLE'));
            $res &= Configuration::updateValue('JMSBLOG_ALLOW_GUEST_COMMENT', Tools::getValue('JMSBLOG_ALLOW_GUEST_COMMENT'));
            $res &= Configuration::updateValue('JMSBLOG_FACEBOOK_COMMENT', Tools::getValue('JMSBLOG_FACEBOOK_COMMENT'));
            $res &= Configuration::updateValue('JMSBLOG_COMMENT_DELAY', (int)Tools::getValue('JMSBLOG_COMMENT_DELAY'));
            $res &= Configuration::updateValue('JMSBLOG_AUTO_APPROVE_COMMENT', Tools::getValue('JMSBLOG_AUTO_APPROVE_COMMENT'));

            $res &= Configuration::updateValue('JMSBLOG_SHOW_FACEBOOK', Tools::getValue('JMSBLOG_SHOW_FACEBOOK'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_TWITTER', Tools::getValue('JMSBLOG_SHOW_TWITTER'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_GOOGLEPLUS', Tools::getValue('JMSBLOG_SHOW_GOOGLEPLUS'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_LINKEDIN', Tools::getValue('JMSBLOG_SHOW_LINKEDIN'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_PINTEREST', Tools::getValue('JMSBLOG_SHOW_PINTEREST'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_EMAIL', Tools::getValue('JMSBLOG_SHOW_EMAIL'));
			$res &= Configuration::updateValue('JMSBLOG_POST_LAYOUT', Tools::getValue('JMSBLOG_POST_LAYOUT'));
			$res &= Configuration::updateValue('JMSBLOG_CATEGORY_LAYOUT', Tools::getValue('JMSBLOG_CATEGORY_LAYOUT'));
			$res &= Configuration::updateValue('JMSBLOG_CATEGORIES_LAYOUT', Tools::getValue('JMSBLOG_CATEGORIES_LAYOUT'));

            $res &= Configuration::updateValue('JMSBLOG_CATEGORIES_BOX', Tools::getValue('JMSBLOG_CATEGORIES_BOX'));
            $res &= Configuration::updateValue('JMSBLOG_TOP_BOX', Tools::getValue('JMSBLOG_TOP_BOX'));
            $res &= Configuration::updateValue('JMSBLOG_CATEGORIES_BOX_COLUMN', Tools::getValue('JMSBLOG_CATEGORIES_BOX_COLUMN'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_HEADING', Tools::getValue('JMSBLOG_SHOW_HEADING'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_INTRO_SUB', Tools::getValue('JMSBLOG_SHOW_INTRO_SUB'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_CAT_SUB', Tools::getValue('JMSBLOG_SHOW_CAT_SUB'));
            $res &= Configuration::updateValue('JMSBLOG_MAX_HEADING_POST', Tools::getValue('JMSBLOG_MAX_HEADING_POST'));
            $res &= Configuration::updateValue('JMSBLOG_MAX_SUB_POST', Tools::getValue('JMSBLOG_MAX_SUB_POST'));
            $showcat = implode(',', Tools::getValue('JMSBLOG_TOP_DISPLAY', array()));
            $res &= Configuration::updateValue('JMSBLOG_TOP_DISPLAY', $showcat);
            $showcat = implode(',', Tools::getValue('JMSBLOG_BOX_DISPLAY', array()));
            $res &= Configuration::updateValue('JMSBLOG_BOX_DISPLAY', $showcat);
            $res &= Configuration::updateValue('JMSBLOG_SHOW_SUB', Tools::getValue('JMSBLOG_SHOW_SUB'));


            $res &= Configuration::updateValue('JMSBLOG_SHOW_SUBCAT_CATEGORY', Tools::getValue('JMSBLOG_SHOW_SUBCAT_CATEGORY'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_HEADING_CATEGORY', Tools::getValue('JMSBLOG_SHOW_HEADING_CATEGORY'));
            $res &= Configuration::updateValue('JMSBLOG_MAX_HEADINGPOST_CATEGORY', Tools::getValue('JMSBLOG_MAX_HEADINGPOST_CATEGORY'));
            $res &= Configuration::updateValue('JMSBLOG_MAX_SUBPOST_CATEGORY', Tools::getValue('JMSBLOG_MAX_SUBPOST_CATEGORY'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_INTRO_CATEGORY', Tools::getValue('JMSBLOG_SHOW_INTRO_CATEGORY'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_DATE_CATEGORY', Tools::getValue('JMSBLOG_SHOW_DATE_CATEGORY'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_VIEWS', Tools::getValue('JMSBLOG_SHOW_VIEWS'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_COMMENTS', Tools::getValue('JMSBLOG_SHOW_COMMENTS'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_MEDIA', Tools::getValue('JMSBLOG_SHOW_MEDIA'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_SOCIAL_SHARING', Tools::getValue('JMSBLOG_SHOW_SOCIAL_SHARING'));
            //post page
            $res &= Configuration::updateValue('JMSBLOG_SHOW_CAT_DETAIL', Tools::getValue('JMSBLOG_SHOW_CAT_DETAIL'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_DATE_DETAIL', Tools::getValue('JMSBLOG_SHOW_DATE_DETAIL'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_VIEWS_DETAIL', Tools::getValue('JMSBLOG_SHOW_VIEWS_DETAIL'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_COMMENTS_DETAIL', Tools::getValue('JMSBLOG_SHOW_COMMENTS_DETAIL'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_IMAGE_DETAIL', Tools::getValue('JMSBLOG_SHOW_IMAGE_DETAIL'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_VIDEO_DETAIL', Tools::getValue('JMSBLOG_SHOW_VIDEO_DETAIL'));
            $res &= Configuration::updateValue('JMSBLOG_SOCIAL_SHARING_DETAIL', Tools::getValue('JMSBLOG_SOCIAL_SHARING_DETAIL'));
            $res &= Configuration::updateValue('JMSBLOG_SHOW_COVER_DETAIL', Tools::getValue('JMSBLOG_SHOW_COVER_DETAIL'));

        }

        if (!$res) {
            $this->_html .= Tools::displayError(implode('<br />', $this->l('An error occurred during the save process.')));
        } elseif (Tools::isSubmit('submitSetting')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmsblogSetting', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
        }
    }

    public function renderNavigation()
    {
        $html = '<div class="navigation">';
        $html .= '<a class="btn btn-default" href="'.AdminController::$currentIndex.
            '&configure='.$this->name.'
                &token='.Tools::getAdminTokenLite('AdminJmsblogSetting').'" title="Back to Dashboard"><i class="icon-home"></i>Back to Dashboard</a>';
        $html .= '</div>';
        return $html;
    }
    public function renderSettingForm()
    {
        $this->context->controller->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin_style.css', 'all');
        $this->context->controller->addJqueryUI('ui.sortable');
		$postlayouts = JmsBlogHelper::getFiles('post');
		$categorieslayouts = JmsBlogHelper::getFiles('categories');
		$categorylayouts = JmsBlogHelper::getFiles('category');
        $categoryBoxs = JmsBlogHelper::getFiles('cbox-', 'categoryBox');
        $topBoxs = JmsBlogHelper::getFiles('heading', 'headingPosts');
        $postGrids = JmsBlogHelper::getFiles('grid-', 'postgrid');
        $boxColumn = array();
        $boxColumn[] = array('col' => 1);
        $boxColumn[] = array('col' => 2);
        $boxColumn[] = array('col' => 3);
        $categories = array();
        $topSelect = array();
        $boxSelect = array();
        JmsBlogHelper::getCategoryOptions(0, $categories);
        $boxShow = trim(Configuration::get('JMSBLOG_BOX_DISPLAY'),',');
        $TopShow = trim(Configuration::get('JMSBLOG_TOP_DISPLAY'),',');
        $boxSelect = JmsBlogHelper::getBoxDisplayConfig($boxShow);
        if(strlen($TopShow) > 0){
            $catArray = explode(',', $TopShow);
            foreach ($catArray as $value) {
                $topSelect[] = $categories[$value];
            }
        }
        // p($topSelect);
        // d($boxSelect);

        $general_fields = array(
            array(
                'type' => 'text',
                'label' => $this->l('Introtext Character Limit'),
                'name' => 'JMSBLOG_INTROTEXT_LIMIT',
                'desc' => $this->l('Number of character of introtext in post list page'),
                'class' => ' fixed-width-xl',
                'tab' => 'general'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Items Per Page'),
                'name' => 'JMSBLOG_ITEMS_PER_PAGE',
                'desc' => $this->l('Number of posts per page'),
                'class' => ' fixed-width-xl',
                'tab' => 'general'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Load Bootstrap'),
                'name' => 'JMSBLOG_BOOTSTRAP',
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'general'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Image Width'),
                'name' => 'JMSBLOG_IMAGE_WIDTH',
                'desc' => $this->l('Maximun Image Width'),
                'class' => ' fixed-width-xl',
                'tab' => 'image'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Image Height'),
                'name' => 'JMSBLOG_IMAGE_HEIGHT',
                'desc' => $this->l('Maximun Image Height'),
                'class' => ' fixed-width-xl',
                'tab' => 'image'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Thumb Width'),
                'name' => 'JMSBLOG_IMAGE_THUMB_WIDTH',
                'desc' => $this->l('Thumbnail Image Width'),
                'class' => ' fixed-width-xl',
                'tab' => 'image'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Thumb Height'),
                'name' => 'JMSBLOG_IMAGE_THUMB_HEIGHT',
                'desc' => $this->l('Thumbnail Image Height'),
                'class' => ' fixed-width-xl',
                'tab' => 'image'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Comment Enable'),
                'name' => 'JMSBLOG_COMMENT_ENABLE',
                'desc' => $this->l('Enable/Disable Comment System.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Yes')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('No'))
                ),
                'tab' => 'comment'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Facebook Comment'),
                'name' => 'JMSBLOG_FACEBOOK_COMMENT',
                'desc' => $this->l('If set to Yes, facebook comment will be used intead of default comment.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Yes')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('No'))
                ),
                'tab' => 'comment'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Allow Guest Comments'),
                'name' => 'JMSBLOG_ALLOW_GUEST_COMMENT',
                'desc' => $this->l('If set to Yes, Guest can comment for posts.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Yes')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('No'))
                ),
                'tab' => 'comment'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Minimum time between 2 comments from the same user'),
                'name' => 'JMSBLOG_COMMENT_DELAY',
                'desc' => $this->l('Minimum time between 2 comments from the same User'),
                'class' => ' fixed-width-xl',
                'suffix' => 'seconds',
                'tab' => 'comment'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Auto Approve Comment'),
                'name' => 'JMSBLOG_AUTO_APPROVE_COMMENT',
                'desc' => $this->l('If set to Yes, comment after submit will auto set to public dont need approve from an employee.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Yes')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('No'))
                ),
                'tab' => 'comment'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Facebook Enable'),
                'name' => 'JMSBLOG_SHOW_FACEBOOK',
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Yes')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('No'))
                ),
                'tab' => 'social'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Twitter Enable'),
                'name' => 'JMSBLOG_SHOW_TWITTER',
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Yes')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('No'))
                ),
                'tab' => 'social'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Google Plus Enable'),
                'name' => 'JMSBLOG_SHOW_GOOGLEPLUS',
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Yes')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('No'))
                ),
                'tab' => 'social'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Linkedin Enable'),
                'name' => 'JMSBLOG_SHOW_LINKEDIN',
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Yes')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('No'))
                ),
                'tab' => 'social'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Pinterest Enable'),
                'name' => 'JMSBLOG_SHOW_PINTEREST',
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Yes')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('No'))
                ),
                'tab' => 'social'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Email Enable'),
                'name' => 'JMSBLOG_SHOW_EMAIL',
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Yes')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('No'))
                ),
                'tab' => 'social'
            ),
			array(
                'type' => 'select',
                'label' => $this->l('Single Post Layout'),
                'name' => 'JMSBLOG_POST_LAYOUT',
                'options' => array('query' => $postlayouts,'id' => 'file','name' => 'file'),
                'tab' => 'layout'
            ),
			array(
                'type' => 'select',
                'label' => $this->l('Single Category Layout'),
                'name' => 'JMSBLOG_CATEGORY_LAYOUT',
                'options' => array('query' => $categorylayouts,'id' => 'file','name' => 'file'),
                'tab' => 'layout'
            ),
			array(
                'type' => 'select',
                'label' => $this->l('Categories Layout'),
                'name' => 'JMSBLOG_CATEGORIES_LAYOUT',
                'options' => array('query' => $categorieslayouts,'id' => 'file','name' => 'file'),
                'tab' => 'layout'
            ),
            array(
                'type' => 'link_choice',
                'label' => $this->l('Top Heading Display'),
                'name' => 'JMSBLOG_TOP_DISPLAY',
                'id' => 1,
                'mdesc' => $this->l('Choose Categories To Show In Top Heading Section'),
                'choices' => $categories,
                'selected_items' => $topSelect,
                'tree' => false,
                'tab' => 'mainpage'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Top Heading Template'),
                'name' => 'JMSBLOG_TOP_BOX',
                'options' => array('query' => $topBoxs,'id' => 'file','name' => 'file'),
                'tab' => 'mainpage'
            ),
            array(
                'type' => 'html',
                'name' => '',
                'html_content' => '<hr class="col-sm-6">',
                'tab' => 'mainpage',
            ),
            array(
                'type' => 'link_choice',
                'label' => $this->l('Categories Display'),
                'name' => 'JMSBLOG_BOX_DISPLAY',
                'id' => 2,
                'mdesc' => $this->l('Choose Categories To Show In Content Section'),
                'choices' => $categories,
                'selected_items' => $boxSelect,
                'tree' => true,
                'tab' => 'mainpage'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Category Box'),
                'name' => 'JMSBLOG_CATEGORIES_BOX',
                'options' => array('query' => $categoryBoxs,'id' => 'file','name' => 'file'),
                'tab' => 'mainpage'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Box Column'),
                'name' => 'JMSBLOG_CATEGORIES_BOX_COLUMN',
                'options' => array('query' => $boxColumn,'id' => 'col','name' => 'col'),
                'tab' => 'mainpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Heading Post'),
                'name' => 'JMSBLOG_SHOW_HEADING',
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'mainpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Sub Heading Post'),
                'name' => 'JMSBLOG_SHOW_SUB',
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'mainpage'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Heading Post'),
                'name' => 'JMSBLOG_MAX_HEADING_POST',
                'class' => ' fixed-width-xl',
                'tab' => 'mainpage'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Sub Heading Post'),
                'name' => 'JMSBLOG_MAX_SUB_POST',
                'class' => ' fixed-width-xl',
                'tab' => 'mainpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Introtext Subheading Post'),
                'name' => 'JMSBLOG_SHOW_INTRO_SUB',
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'mainpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Category Subheading Post'),
                'name' => 'JMSBLOG_SHOW_CAT_SUB',
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'mainpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Social Sharing Enable'),
                'name' => 'JMSBLOG_SHOW_SOCIAL_SHARING',
                'desc' => $this->l('Social Sharing Enable/Disable in Post List.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Yes')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('No'))
                ),
                'tab' => 'catpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Sub Categories'),
                'name' => 'JMSBLOG_SHOW_SUBCAT_CATEGORY',
                'desc' => $this->l('Show/Hide sub categories on Post List.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'catpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Heading Box'),
                'name' => 'JMSBLOG_SHOW_HEADING_CATEGORY',
                'desc' => $this->l('Show/Hide Heading Post on Post List.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'catpage'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Heading Post'),
                'name' => 'JMSBLOG_MAX_HEADINGPOST_CATEGORY',
                'class' => ' fixed-width-xl',
                'tab' => 'catpage'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Sub Heading Post'),
                'name' => 'JMSBLOG_MAX_SUBPOST_CATEGORY',
                'class' => ' fixed-width-xl',
                'tab' => 'catpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Category'),
                'name' => 'JMSBLOG_SHOW_CATEGORY',
                'desc' => $this->l('Show/Hide Category Under Post Title In Post List.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'catpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Media'),
                'name' => 'JMSBLOG_SHOW_MEDIA',
                'desc' => $this->l('Show/Hide Image on Post List.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'catpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Intro Text'),
                'name' => 'JMSBLOG_SHOW_INTRO_CATEGORY',
                'desc' => $this->l('Show/Hide intro text on Post List.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'catpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Views'),
                'name' => 'JMSBLOG_SHOW_VIEWS',
                'desc' => $this->l('Show/Hide Views Under Post Title In Post List.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'catpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Date'),
                'name' => 'JMSBLOG_SHOW_DATE_CATEGORY',
                'desc' => $this->l('Show/Hide Date Under Post Title In Post List.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'catpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Comments'),
                'name' => 'JMSBLOG_SHOW_COMMENTS',
                'desc' => $this->l('Show/Hide Comments Under Post Title In Post List.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'catpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Social Sharing Enable'),
                'name' => 'JMSBLOG_SOCIAL_SHARING_DETAIL',
                'desc' => $this->l('Social Sharing Enable/Disable in Post Detail Page.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'postpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Post Cover'),
                'name' => 'JMSBLOG_SHOW_COVER_DETAIL',
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'postpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Category'),
                'name' => 'JMSBLOG_SHOW_CAT_DETAIL',
                'desc' => $this->l('Show/Hide Category Under Post Title In Post Detail Page.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'postpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Date'),
                'name' => 'JMSBLOG_SHOW_DATE_DETAIL',
                'desc' => $this->l('Show/Hide Date Under Post Title In Post Detail Page.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'postpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Views'),
                'name' => 'JMSBLOG_SHOW_VIEWS_DETAIL',
                'desc' => $this->l('Show/Hide Views Under Post Title In Post Detail Page.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'postpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Comments'),
                'name' => 'JMSBLOG_SHOW_COMMENTS_DETAIL',
                'desc' => $this->l('Show/Hide Comments Under Post Title In Post Detail Page.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'postpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Images'),
                'name' => 'JMSBLOG_SHOW_IMAGE_DETAIL',
                'desc' => $this->l('Show/Hide Image on Post Detail Page.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'postpage'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Videos'),
                'name' => 'JMSBLOG_SHOW_VIDEO_DETAIL',
                'desc' => $this->l('Show/Hide Videos on Post Detail Page.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Show')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Hide'))
                ),
                'tab' => 'postpage'
            ),
        );
        /* RENDER */
        $this->fields_options[0]['form'] = array(
            'tinymce' => true,
            'tabs' => array('general' => 'General', 'layout' => 'Layout', 'image' => 'Image', 'comment' => 'Comment', 'social' => 'Social Sharing', 'mainpage' => 'Main Page', 'catpage' => 'Category Page', 'postpage' => 'Post Detail Page'),
            'legend' => array('title' => '<span class="label label-info">'.$this->l('Jms Blog Setting').'</span>','icon' => 'icon-cogs',),
            'input' => $general_fields,
            'submit' => array('title' => $this->l('Save'), 'class' => 'button btn btn-primary'),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSetting';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminJmsblogSetting', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->tpl_vars = array(
            'base_url' => $this->root_url,
            'fields_value' => JmsBlogHelper::getSettingFieldsValues(),
        );
        return $helper->generateForm($this->fields_options);
    }
}
