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

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('jmsImage.php');

$context = Context::getContext();
$rows = array();
if (Tools::getValue('action') == 'updateCategoryOrdering' && Tools::getValue('categories')) {
    $categories = Tools::getValue('categories');
    $start = (int)Tools::getValue('start',0);

    foreach ($categories as $position => $id_category) {
        $sql = '
            UPDATE `'._DB_PREFIX_.'jmsblog_categories` SET `ordering` = '.((int)$position+$start).'
            WHERE `category_id` = '.(int)$id_category;
        $res = Db::getInstance()->execute($sql);
    }
    die(Tools::jsonEncode(array('status' => $sql)));
} elseif (Tools::getValue('action') == 'updatePostOrdering' && Tools::getValue('posts')) {
    $posts = Tools::getValue('posts');
    $start = (int)Tools::getValue('start');

    foreach ($posts as $position => $post_id) {
        $sql = '
            UPDATE `'._DB_PREFIX_.'jmsblog_posts` SET `ordering` = '.((int)$position+$start).'
            WHERE `post_id` = '.(int)$post_id;
        $res = Db::getInstance()->execute($sql);
    }
} elseif (Tools::getValue('action') == 'updateImageOrdering' && Tools::isSubmit('imageList')) {
    $imageList = Tools::getValue('imageList');

    foreach ($imageList as $position => $image_id) {
        $sql = '
            UPDATE `'._DB_PREFIX_.'jmsblog_posts_images` SET `ordering` = '.(int)$position.'
            WHERE `id` = '.(int)$image_id;
        $res = Db::getInstance()->execute($sql);
    }
    die(Tools::jsonEncode(array('status' => $res)));
} elseif (Tools::getValue('action') == 'deletePostImage' && Tools::isSubmit('image')) {
    $id = str_replace('imageList_', '', Tools::getValue('image'));
    $img = new JmsImage((int)$id);
    $res = $img->delete();
    die(Tools::jsonEncode(array('status' => $res)));
} elseif (Tools::getValue('action') == 'updateImageCover' && Tools::isSubmit('image')) {
    $id = str_replace('imageList_', '', Tools::getValue('image'));
    $id_post = Tools::getValue('id_post');
    $image = new JmsImage($id);
    $coverImage = $image->getCoverImage($id_post);
    $image->cover = true;
    $image->update();
    $res['newcover'] = $image->id;
    if($coverImage != null) {
        $coverImage->cover = false;
        $coverImage->update();
        $res['oldcover'] = $coverImage->id;
    }
    else {
        $res['oldcover'] = 0;
    }
    $res['status'] = true;
    die(Tools::jsonEncode($res));
}