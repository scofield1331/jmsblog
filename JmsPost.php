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

class JmsPost extends ObjectModel
{
    public $title;
    public $alias;
    public $introtext;
    public $fulltext;
    public $meta_desc;
    public $meta_key;
    public $key_ref;
    public $active;
    public $heading;
    public $ordering;
    public $category_id;
    public $created;
    public $modified;
    public $link_video;
    public $tags;
    public $views;

    public static $definition = array(
        'table' => 'jmsblog_posts',
        'primary' => 'post_id',
        'multilang' => true,
        'fields' => array(
            'active'        =>      array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'heading'       =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'ordering'      =>      array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'category_id'   =>      array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'created'       =>      array('type' => self::TYPE_DATE),
            'modified'      =>      array('type' => self::TYPE_DATE),
            'views'         =>      array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'introtext'     =>      array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString', 'size' => 4000),
            'fulltext'      =>      array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString', 'size' => 40000),
            'title'         =>      array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
            'alias'         =>      array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
            'meta_desc'     =>      array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 500),
            'meta_key'      =>      array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 500),
            'key_ref'       =>      array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 200),
            'tags'          =>      array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString', 'size' => 4000),
        )
    );

    public function __construct($post_id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($post_id, $id_lang, $id_shop);
    }

    public function add($duplicate = 0, $autodate = true, $null_values = false)
    {
        $res = true;
        $res &= Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'jmsblog_posts SET ordering = ordering+1');
        $res &= parent::add($autodate, $null_values);
        if (isset($this->link_video['new'])) {
            foreach ($this->link_video['new'] as $link) {
                $video = new JmsVideo();
                $video->post_id = $this->id;
                $video->link_video = $link;
                $res &= $video->add();
            }
        }
        if ($duplicate) {
            $imageCol = new PrestashopCollection('JmsImage');
            $imageCol->where('post_id','=', $duplicate);
            $imageCol->orderBy('ordering', 'desc');
            $images = $imageCol->getResults();
            foreach ($images as $image) {
                $res &= $image->duplicate($this->id);
            }
        }
        return $res;
    }

    public function update($null_values = false)
    {
        $res = true;
        if (isset($this->link_video['new'])) {
            foreach ($this->link_video['new'] as $link) {
                $video = new JmsVideo();
                $video->post_id = $this->id;
                $video->link_video = $link;
                $res &= $video->add();
            }
        }
        if (isset($this->link_video['old'])) {
            foreach ($this->link_video['old'] as $video) {
                $res &= $video->update();
            }
        }
        if (isset($this->link_video['remove'])) {
            foreach ($this->link_video['remove'] as $video) {
                $res &= $video->delete();
            }
        }
        $res &= parent::update($null_values);
        return $res;
    }

    public function delete()
    {
        $res = true;

        $imageCol = new PrestashopCollection('JmsImage');
        $imageCol->where('post_id','=', $this->id);
        $images = $imageCol->getResults();
        foreach ($images as $image) {
            $res &= $image->delete();
        }
        $videoCol = new PrestashopCollection('JmsVideo');
        $videoCol->where('post_id','=', $this->id);
        $videos = $videoCol->getResults();
        foreach ($videos as $video) {
            $res &= $video->delete();
        }
        $res &= parent::delete();
        return $res;
    }
}
