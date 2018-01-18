<?php
/**
* 2007-2017 PrestaShop
*
* Jms Theme Layout
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2017 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

class JmsVideo extends ObjectModel
{
    public $post_id;
    public $link_video;

    public static $definition = array(
        'table' => 'jmsblog_posts_videos',
        'primary' => 'id',
        'multilang' => false,
        'fields' => array(
            'post_id' =>    array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'link_video'    =>      array('type' => self::TYPE_HTML, 'size' => 1000, 'required' => true),
        )
    );
}
