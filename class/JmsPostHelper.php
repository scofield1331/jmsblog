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

class JmsPostHelper
{
    public static function getPostCount($category_id = 0, $state = -1)
    {
        $context = Context::getContext();
        $id_lang = $context->language->id;
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

}
