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
class JmsblogArchiveModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {

        parent::initContent();
        $_month = Tools::getValue('archive');
        $jmsblog_setting = JmsBlogHelper::getSettingFieldsValues();
        $start = Tools::getValue('start', 0);
        $link = $this->context->link->getModuleLink($this->module->name, 'archive');
        if (!Validate::isUnsignedInt($start)) {
            $start = 0;
        }
        $limit = Tools::getValue('limit', $jmsblog_setting['JMSBLOG_ITEMS_PER_PAGE']);
        $total = JmsPostHelper::getPostCount(0, 1);
        if ($total % $limit) {
            $pages = (int)($total / $limit) + 1;
        } else {
            $pages = $total / $limit;
        }
        $posts = $this->getPosts($_month, $start, $limit);
        for ($i = 0; $i < count($posts); $i++) {
            $posts[$i]['introtext'] = JmsBlogHelper::genIntrotext($posts[$i]['introtext'], $jmsblog_setting['JMSBLOG_INTROTEXT_LIMIT']);
            $posts[$i]['comment_count'] = JmsBlogHelper::getCommentCount($posts[$i]['post_id']);
        }
        $this->context->controller->addCSS($this->module->getPathUri().'views/css/style.css', 'all');
        $this->context->smarty->assign(array('meta_title' => $_month));
        $this->context->smarty->assign(array(
            'month' => $_month,
            'posts' => $posts,
            'jmsblog_setting' => $jmsblog_setting,
            'image_baseurl' => $this->module->getPathUri().'views/img/',
            'start'=>$start,
            'limit'=>$limit,
            'pages'=>$pages,
            'current_uri'=>$link,
            'total'=>$total,
            'c_name'=>'archive'
        ));
        $this->setTemplate('archive.tpl');
    }
    public function getPosts($_month = '', $start = 0, $limit)
    {
        $this->context = Context::getContext();
        $id_lang = $this->context->language->id;
        $sql = '
            SELECT hss.`post_id`,hss.`category_id`, hss.`ordering`, hss.`active`, hssl.`title`, hss.`created`, hss.`modified`, hss.`views`,
            hssl.`alias`, hssl.`fulltext`, hssl.`introtext`,hssl.`meta_desc`, hssl.`meta_key`, hssl.`key_ref`, catsl.`title` AS category_name, catsl.`alias` AS category_alias
            FROM '._DB_PREFIX_.'jmsblog_posts hss
            LEFT JOIN '._DB_PREFIX_.'jmsblog_posts_lang hssl ON (hss.`post_id` = hssl.`post_id`)
            LEFT JOIN '._DB_PREFIX_.'jmsblog_categories_lang catsl ON (catsl.`category_id` = hss.`category_id`)
            WHERE hss.`active` = 1 AND hssl.`id_lang` = '.(int)$id_lang.
            ' AND DATE_FORMAT(hss.created,"%Y-%m") LIKE "%'.$_month.'%"
            GROUP BY hss.`post_id`
            ORDER BY hss.`created` DESC
            LIMIT '.$start.','.$limit;
        $posts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($posts as &$post) {
            $post['image'] = JmsImage::getImages($post['post_id']);
            $videoCol = new PrestashopCollection('JmsVideo');
            $videoCol->where('post_id','=', $post['post_id']);
            $post['link_video'] = $videoCol->getResults();
        }
        return $posts;
    }
}
