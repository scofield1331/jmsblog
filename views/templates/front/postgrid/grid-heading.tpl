{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


{if isset($post)}
{$catparams['category_id'] = $post['category_id']}
{$catparams['slug'] = {$post['category_alias']|replace:' ':'_'|replace:'-':'_'}}
{$postparams['post_id'] = $post['post_id']}
{$postparams['category_slug'] = {$post['category_alias']|replace:' ':'_'|replace:'-':'_'}}
{$postparams['slug'] = $post['alias']}
<div class="heading">
    <div class="thumb">
        <a href="{jmsblog::getPageLink('jmsblog-post', $postparams)|escape:'UTF-8'}">
            <img src="{$image_baseurl|escape:'htmlall':'UTF-8'}{$post['image']|escape:'htmlall'}" alt="{$post['title']|escape:'htmlall'}">
        </a>
        <a class="category" href="{jmsblog::getPageLink('jmsblog-category', $catparams)|escape:'UTF-8'}">{$post['category_title']|escape:'htmlall':'UTF-8'}</a>
    </div>
    <div class="entry-title">
        <a href="{jmsblog::getPageLink('jmsblog-post', $postparams)|escape:'UTF-8'}">{$post['title']|escape:'htmlall':'UTF-8'}</a>
    </div>
    <div class="date">{$post['created']|escape:'htmlall':'UTF-8'}</div>
    <div class="introtext">{$post['introtext']|escape:'htmlall':'UTF-8'}</div>
</div>
{/if}