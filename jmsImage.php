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
class JmsImage extends ObjectModel
{
    public $file;
    public $image;
    public $ordering;
    public $cover;
    public $post_id;

    public static $definition = array(
        'table' => 'jmsblog_posts_images',
        'primary' => 'id',
        'multilang' => false,
        'fields' => array(
            'image'         =>  array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
            'ordering'      =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'cover'        =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'post_id' =>    array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
        )
    );

    public function duplicate($newpost_id)
    {
        $res = true;
        if (strlen($this->image) < 1 && Validate::isUnsignedInt($this->post_id)) {
            $res = false;
            return;
        }
        $base_url = _PS_MODULE_DIR_.'/jmsblog/views/img/';
        $type = strrchr($this->image, '.');
        $newImage = new JmsImage();
        $newImage->image = sha1(microtime()).$type;
        $newImage->post_id = $newpost_id;
        $newImage->cover = $this->cover;
        if (file_exists($base_url.$this->image)) {
            $res &= copy($base_url.$this->image, $base_url.$newImage->image);
            $res &= copy($base_url.'resized_'.$this->image, $base_url.'resized_'.$newImage->image);
            $res &= copy($base_url.'thumb_'.$this->image, $base_url.'thumb_'.$newImage->image);
        }
        $res &= $newImage->addSample();
        return $res;
    }

    public function add($autodate = true, $null_values = false)
    {
        $result['status'] = true;
        $jmsblog_setting = JmsBlogHelper::getSettingFieldsValues();

        $type = Tools::strtolower(Tools::substr(strrchr($this->file['name'], '.'), 1));
        $temp_name = $this->file['save_path'];
        $salt = sha1(microtime());
        $this->image = $salt.'.'.$type;
        $result['message'] = $this->image;
        if (!ImageManager::resize($temp_name, _PS_MODULE_DIR_.'/jmsblog/views/img/'.$this->image, null, null, $type)) {
            $result['status'] = false;
            $result['message'] = 'resize error';
        }
        if (isset($temp_name)) {
            @unlink($temp_name);
        }
        JmsBlogHelper::createThumb(_PS_MODULE_DIR_.'/jmsblog/views/img/', $this->image, $jmsblog_setting['JMSBLOG_IMAGE_WIDTH'], $jmsblog_setting['JMSBLOG_IMAGE_HEIGHT'], 'resized_', 0);
        JmsBlogHelper::createThumb(_PS_MODULE_DIR_.'/jmsblog/views/img/', $this->image, $jmsblog_setting['JMSBLOG_IMAGE_THUMB_WIDTH'], $jmsblog_setting['JMSBLOG_IMAGE_THUMB_HEIGHT'], 'thumb_', 0);
        if (!parent::add($autodate, $null_values)) {
            $result['status'] = false;
            $result['message'] = 'error while adding image';
        }
        return $result;
    }
    public function addSample()
    {
        return parent::add();
    }

    public function delete()
    {
        $res = true;

        $image = $this->image;
        if (preg_match('/sample/', $image) === 0) {
            if ($image && file_exists(dirname(__FILE__).'/views/img/'.$image)) {
                $res &= @unlink(dirname(__FILE__).'/views/img/'.$image);
                $res &= @unlink(dirname(__FILE__).'/views/img/resized_'.$image);
                $res &= @unlink(dirname(__FILE__).'/views/img/thumb_'.$image);
            }
        }
        $res &= parent::delete();
        return $res;
    }
    public static function getCoverImage($id_post)
    {
        $imgCol = new PrestaShopCollection('JmsImage');
        $imgCol->where('post_id', '=', $id_post);
        $imgCol->where('cover', '=', 1);
        return $imgCol->getFirst();
    }
    public static function getImages($id_post)
    {
        $images = array();
        $resultSet = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'jmsblog_posts_images WHERE post_id='.$id_post.' ORDER BY ordering, id DESC');
        foreach ($resultSet as $rs) {
            $image = new self();
            $image->image = $rs['image'];
            $image->id = $rs['id'];
            $image->ordering = $rs['ordering'];
            $image->cover = $rs['cover'];
            $image->post_id = $rs['post_id'];
            $images[] = $image;
        }
        return $images;
    }
    public static function countImages($post_id)
    {
        return Db::getInstance()->getValue(
            'SELECT COUNT(*)
            FROM '._DB_PREFIX_.'jmsblog_posts_images
            WHERE post_id = '.$post_id
        );
    }
}
