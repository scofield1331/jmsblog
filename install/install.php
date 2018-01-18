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
include_once(_PS_MODULE_DIR_.'jmsblog/JmsCategory.php');
include_once(_PS_MODULE_DIR_.'jmsblog/JmsPost.php');
include_once(_PS_MODULE_DIR_.'jmsblog/jmsImage.php');
class JmsInstall
{
    public function createTable()
    {
        $sql = array();
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsblog_shop` (
                  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `id_shop` int(10) unsigned NOT NULL DEFAULT \'0\',
                  PRIMARY KEY (`category_id`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsblog_categories` (
                  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `ordering` int(10) unsigned NOT NULL DEFAULT \'0\',
                  `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                  `parent` int(1) unsigned NOT NULL DEFAULT \'0\',
                  PRIMARY KEY (`category_id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsblog_categories_lang` (
                `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_lang` int(10) unsigned NOT NULL,
                `title` varchar(255) NOT NULL,
                `alias` varchar(255) NOT NULL,
                `description` text NOT NULL,
                `image` varchar(255) NOT NULL,
                `meta_desc` text NOT NULL,
                `meta_key` text NOT NULL,
                PRIMARY KEY (`category_id`,`id_lang`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsblog_posts` (
            `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `ordering` int(10) unsigned NOT NULL DEFAULT \'0\',
            `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
            `heading` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
            `category_id` int(1) unsigned NOT NULL DEFAULT \'0\',
            `created` datetime NOT NULL,
            `modified` datetime NOT NULL,
            `views` int(1) unsigned NOT NULL DEFAULT \'0\',
            PRIMARY KEY (`post_id`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsblog_posts_comments` (
                `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `post_id` int(10) unsigned NOT NULL,
                `title` varchar(255) NOT NULL,
                `comment` text NOT NULL,
                `customer_name` varchar(255) NOT NULL,
                `email` varchar(50) NOT NULL,
                `customer_site` varchar(50) NOT NULL,
                `time_add` datetime NOT NULL,
                `status` int(2) NOT NULL,
                PRIMARY KEY (`comment_id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsblog_posts_lang` (
                `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_lang` int(10) unsigned NOT NULL,
                `title` varchar(255) NOT NULL,
                `alias` varchar(255) NOT NULL,
                `introtext` text NOT NULL,
                `fulltext` text NOT NULL,
                `meta_desc` text NOT NULL,
                `meta_key` text NOT NULL,
                `key_ref` text NOT NULL,
                `tags` text NOT NULL,
                PRIMARY KEY (`post_id`,`id_lang`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';
        //update code
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsblog_posts_images` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `post_id` int(10) unsigned NOT NULL,
                `image` varchar(255) NOT NULL,
                `ordering` int(10) unsigned NOT NULL DEFAULT \'0\',
                `cover` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                PRIMARY KEY (`id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsblog_posts_videos` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `post_id` int(10) unsigned NOT NULL,
                `link_video` text NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
        //end update
        foreach ($sql as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
    }

    public function installSamples()
    {
        $languages = Language::getLanguages(false);
        $res = true;
        //add categories
        $title = array(
            'News',
            'Style Hunter',
            'Music',
            'Health & Fitness',
            'Travel',
            'Gadgets',
            'Business',
            'Game',
            'Sport',
            'Children',
            'Football',
            'Other',
        );
        $postTitle = array(
            'Basketball Stars Face Off in Ultimate Playoff Beard Battle',
            'Tokyo Fashion Week Is Making Itself Great Again',
            'Interior Design: Hexagon is the New Circle in 2018',
            'Traveling Tends to Magnify All Human Emotions',
            'Planning a Winter Holiday? Canary Islands Offers',
            'Haunts of the Heart: Landscapes of Lynn Zimmerman',
            'Dream Homes: North Penthouse Listed For $1.7 Million',
            'Express Recipes: How to make Creamy Papaya Raita',
            '10 Foods You Have Been Eating Wrong All Your Life',
            'Seeking Business, Cuomo Heads to Cuba With a New York Trade...',
            'Heritage Museums & Gardens to Open with New Landscape',
            'Patricia Urquiola transparent furniture for Glas Italia with iridescent',
            'Denton Corker Marshall the mysterious black box is biennale pavilion',
            'Man agrees to complete $5,000 Hereford Inlet Lighthouse painting job',
        );
        for ($i=0; $i < 12; $i++) {
            $row = new JmsCategory();
            foreach ($languages as $language) {
                $row->title[$language['id_lang']] = $title[$i];
                $row->alias[$language['id_lang']] = JmsBlogHelper::makeAlias($title[$i]);
            }
            if ($i>7) {
                $row->parent = ($i>8)?9:0;
            } else{
                $row->parent = ($i>1)?1:$i;
            }

            $row->active = 1;
            $row->ordering = 0;
            $res &= $row->add();
        }
        //add Item
        for ($i = 1; $i < 50; $i++) {
            $item = new JmsPost();
            $item->created = date('Y-m-d h:i:s');
            $item->modified = date('Y-m-d h:i:s');
            $item->views = $i;
            $item->ordering = 0;
            $item->category_id = $i%8+1;
            $item->tags = 'fashion,women collection,men fashion';
            $item->active = 1;
            $item->heading = ($i>45)?1:(mt_rand(0,1));
            $image = new JmsImage();
            /* Sets each langue fields */
            foreach ($languages as $language) {
                $item->title[$language['id_lang']] = $postTitle[$i%14];
                $item->alias[$language['id_lang']] = JmsBlogHelper::makeAlias($item->title[$language['id_lang']]).'-'.($i + 1);
                $item->introtext[$language['id_lang']] = 'Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. ivamus elementum semper nisi. Aenean vulputate eleifend tellus.';

                $item->fulltext[$language['id_lang']] = '<p>Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.</p>
<p><span class="quote"><span class="quote-author"><em class="placeholder">Hello</em> wrote:</span>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. At vero eos et accusam et justo duo dolores et ea rebum. Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</span></p>
<h1>h1. Heading 1</h1>
<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p>
<ul>
<li>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet</li>
<li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit</li>
<li>Lorem ipsum dolor sit amet, consetetur sadipscing elitr</li>
</ul>
<h2>h2. Heading 2</h2>
<p>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>
<h3>h3. Heading 3</h3>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Sanctus sea sed takimata ut vero voluptua.</p>';
            }
            $res    &= $item->add();
            $image->image = 'img'.($i%19+1).'.jpg';
            $image->cover = 1;
            $image->ordering = 1;
            $image->post_id = $item->id;
            $res &= $image->addSample();

        }
        return $res;
    }
}
