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

class JmsCategory extends ObjectModel
{
    public $title;
    public $alias;
    public $description;
    public $image;
    public $active;
    public $ordering;
    public $parent;
    public $category_id;
    public $meta_desc;
    public $meta_key;

    public static $definition = array(
        'table' => 'jmsblog_categories',
        'primary' => 'category_id',
        'multilang' => true,
        'fields' => array(
            'active'        =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'ordering'      =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'parent'        =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'description'   =>  array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 4000),
            'title'         =>  array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
            'alias'         =>  array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
            'image'         =>  array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
            'meta_desc'     =>  array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 500),
            'meta_key'      =>  array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 500),
        )
    );

    public function __construct($category_id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($category_id, $id_lang, $id_shop);
    }

    public function add($autodate = true, $null_values = false)
    {
        $res = true;
        $id_shop = Context::getContext()->shop->id;
        $res &= Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'jmsblog_categories SET ordering = ordering+1');
        $res&=parent::add($autodate, $null_values);
        $res&= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'jmsblog_shop`(`category_id`, `id_shop`) VALUES('.(int)$this->id.', '.$id_shop.');');
        $res &= Configuration::updateValue('JMSBLOG_BOX_DISPLAY', Configuration::get('JMSBLOG_BOX_DISPLAY').','.$this->id);
        return $res;
    }

    public function delete()
    {
        $res = true;

        $images = $this->image;
        foreach ($images as $image) {
            if (preg_match('/sample/', $image) === 0) {
                if ($image && file_exists(dirname(__FILE__).'/views/img/'.$image)) {
                    $res &= @unlink(dirname(__FILE__).'/views/img/'.$image);
                    $res &= @unlink(dirname(__FILE__).'/views/img/resized_'.$image);
                    $res &= @unlink(dirname(__FILE__).'/views/img/sthumb_'.$image);
                    $res &= @unlink(dirname(__FILE__).'/views/img/thumb_'.$image);
                }
            }
        }
        $showcat = explode(',', Configuration::get('JMSBLOG_BOX_DISPLAY'));
        if (($key = array_search($this->id, $showcat)) !== false) {
            unset($showcat[$key]);
            $res &= Configuration::updateValue('JMSBLOG_BOX_DISPLAY', implode(',', $showcat));
        }
        $res &= Db::getInstance()->delete('jmsblog_shop', 'category_id = '.$this->id);
        $res &= parent::delete();
        return $res;
    }

    public function reOrderPositions()
    {
        $sql = '
            SELECT hss.`ordering` as ordering, hss.`category_id` as category_id
            FROM `'._DB_PREFIX_.'jmsblog_categories` hss
            WHERE hss.`ordering` > '.(int)$this->ordering;
        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($rows as $row) {
            $current_item = new JmsCategory($row['category_id']);
            --$current_item->position;
            $current_item->update();
            unset($current_item);
        }
        return true;
    }
}
