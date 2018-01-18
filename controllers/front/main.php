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

class JmsblogMainModuleFrontController extends ModuleFrontController
{
    public $config;

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->config = Configuration::getMultiple(
            array(
                'JMSBLOG_CATEGORIES_BOX',
                'JMSBLOG_TOP_BOX',
                'JMSBLOG_CATEGORIES_BOX_COLUMN',
                'JMSBLOG_SHOW_HEADING',
                'JMSBLOG_SHOW_INTRO_SUB',
                'JMSBLOG_MAX_HEADING_POST',
                'JMSBLOG_MAX_SUB_POST',
                'JMSBLOG_SHOW_SUB',
                'JMSBLOG_SHOW_VIEWS'
            )
        );
        $this->config['JMSBLOG_SHOW_CATEGORY'] = Configuration::get('JMSBLOG_SHOW_CAT_SUB');
        $this->config['JMSBLOG_SHOW_INTRO'] = Configuration::get('JMSBLOG_SHOW_INTRO_SUB');
        $this->display_column_left = false;
        $this->display_column_right = false;
        $topDisplay = trim(Configuration::get('JMSBLOG_TOP_DISPLAY'),',');
        $boxDisplay = trim(Configuration::get('JMSBLOG_BOX_DISPLAY'),',');
        parent::initContent();
        // $this->addCSS($this->module->getPathUri().'views/css/style.css', 'all');
        function sort_date($a, $b) {
            return strtotime($b['created'])-strtotime($a['created']);
        }
        $rootCat = array();
        $topHeading = array();
        if (strlen($topDisplay)) {
            $topHeading = $this->getAllHeadingPost($topDisplay);
        }
        if (strlen($boxDisplay)) {
            $rootCat = $this->getCategories(0, $boxDisplay);
            foreach ($rootCat as &$item) {
                $item['childs'] = $this->getCategories($item['category_id'], $boxDisplay);
                foreach ($item['childs'] as $child) {
                    $item['heading'] = array_merge($item['heading'], $child['heading']);
                    $item['common'] = array_merge($item['common'], $child['common']);
                }
                usort($item['heading'], "sort_date");
                usort($item['common'], "sort_date");
                $item['heading'] = array_slice($item['heading'], 0, $this->config['JMSBLOG_MAX_HEADING_POST']);
                $item['common'] = array_slice($item['common'], 0, $this->config['JMSBLOG_MAX_SUB_POST']);
            }
        }
        // d($rootCat);
        $catparam = array('category_id' => '', 'slug' => '', 'start' => '');
        $postparam = array('category_slug' => '', 'post_id' => '', 'slug' => '');
        $this->context->smarty->assign(array(
            'config' => $this->config,
            'headingPosts' => $topHeading,
            'categories'   => $rootCat,
            'image_baseurl' => $this->module->getPathUri().'views/img/',
            'catparams' => $catparam,
            'postparams' => $postparam,
        ));
        $this->setTemplate('main.tpl');
    }
    // get heading post for each category
    private function getAllHeadingPost($in)
    {
        if (!strlen($in)) {
            return null;
        }
        $id_lang = $this->context->language->id;
        $newHeading = new DbQuery();
        $newHeading->select('MAX(post_id) as new, category_id');
        $newHeading->from('jmsblog_posts');
        $newHeading->where('active = 1 AND heading = 1');
        $newHeading->where('category_id IN ('.$in.')');
        $newHeading->groupBy('category_id');
        $sql = new DbQuery();
        $sql->select('p.created, p.views, pl.*, cl.title AS category_title, cl.alias AS category_alias, y.category_id');
        $sql->select('CASE WHEN im.image IS NULL THEN "default.jpg" ELSE im.image END AS image');
        $sql->from('jmsblog_posts_lang', 'pl');
        $sql->join('JOIN ('.$newHeading->__toString().') y ON y.new = pl.post_id');
        $sql->innerJoin('jmsblog_posts', 'p', 'y.new = p.post_id');
        $sql->leftJoin('jmsblog_categories', 'c', 'y.category_id = c.category_id');
        $sql->leftJoin('jmsblog_categories_lang', 'cl', 'y.category_id = cl.category_id AND pl.id_lang = cl.id_lang');
        $sql->leftJoin('jmsblog_posts_images', 'im', 'y.new = im.post_id AND im.cover = 1');
        $sql->where('c.active = 1');
        $sql->where('pl.id_lang = '.$id_lang);
        $sql->orderBy('FIELD(y.category_id,'.$in.')');
        return Db::getInstance()->executeS($sql);
    }

    private function getCategories($parent, $in)
    {
        if (!strlen($in)) {
            return null;
        }
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $sql = new DbQuery();
        $sql->select('c.category_id, cl.image, cl.title, cl.alias');
        $sql->from('jmsblog_categories', 'c');
        $sql->innerJoin('jmsblog_categories_lang', 'cl', 'c.category_id=cl.category_id');
        $sql->innerJoin('jmsblog_shop', 's', 's.category_id=c.category_id');
        $sql->where('cl.id_lang='.$id_lang);
        $sql->where('s.id_shop='.$id_shop);
        $sql->where('c.parent='.$parent);
        $sql->where('c.active= 1');
        $sql->where('c.category_id IN ('.$in.')');
        $sql->orderBy('FIELD(c.category_id,'.$in.')');
        $items = Db::getInstance()->executeS($sql);
        if (count($items)) {
            while ($element = current($items)) {
                if ($this->config['JMSBLOG_SHOW_HEADING']) {
                    $items[key($items)]['heading']=$this->getPosts($element['category_id'], 1, $this->config['JMSBLOG_MAX_HEADING_POST']);
                }
                if ($this->config['JMSBLOG_SHOW_SUB']) {
                    $items[key($items)]['common']=$this->getPosts($element['category_id'], 0, $this->config['JMSBLOG_MAX_SUB_POST']);
                }
                next($items);
            }
        }
        return $items;
    }
    private function getPosts($category_id, $heading, $limit = 0)
    {
        $id_lang = $this->context->language->id;
        $sql = new DbQuery();
        $sql->select('p.created, p.views, p.post_id, p.category_id, pl.title, pl.introtext, pl.alias, cl.title AS category_title, cl.alias AS category_alias, COUNT(cmt.comment_id) AS comment_count');
        $sql->select('CASE WHEN im.image IS NULL THEN "default.jpg" ELSE im.image END AS image');
        $sql->from('jmsblog_posts', 'p');
        $sql->innerJoin('jmsblog_posts_lang', 'pl', 'p.post_id=pl.post_id');
        $sql->innerJoin('jmsblog_categories_lang', 'cl', 'cl.category_id='.$category_id);
        $sql->leftJoin('jmsblog_posts_images', 'im', 'p.post_id = im.post_id AND im.cover = 1');
        $sql->leftJoin('jmsblog_posts_comments', 'cmt', 'p.post_id = cmt.post_id AND cmt.status = 1');
        $sql->where('p.active = 1 AND p.heading = '.$heading);
        $sql->where('p.category_id = '.$category_id);
        $sql->where('pl.id_lang = '.$id_lang);
        $sql->where('cl.id_lang = '.$id_lang);
        $sql->limit($limit);
        $sql->orderBy('p.created DESC');
        $sql->groupBy('p.post_id');
        return Db::getInstance()->executeS($sql);
    }
}
