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
<article class="common">
    {if !isset($config['JMSBLOG_SHOW_MEDIA']) || $config['JMSBLOG_SHOW_MEDIA']}
    <div class="thumb">
        <a href="{jmsblog::getPageLink('jmsblog-post', $postparams)|escape:'UTF-8'}">
            <img src="{$image_baseurl|escape:'htmlall':'UTF-8'}{$post['image']|escape:'htmlall'}" alt="{$post['title']|escape:'htmlall'}">
        </a>
    </div>
    {/if}
    <div class="info" {if isset($config['JMSBLOG_SHOW_MEDIA']) && !$config['JMSBLOG_SHOW_MEDIA']}style="margin-left:0"{/if}>
        <div class="entry-title">
            <a href="{jmsblog::getPageLink('jmsblog-post', $postparams)|escape:'UTF-8'}">{$post['title']|escape:'htmlall':'UTF-8'}</a>
        </div>
        <div class="sm-info">
            {if !isset($config['JMSBLOG_SHOW_CATEGORY']) || $config['JMSBLOG_SHOW_CATEGORY']}
                <a class="category" href="{jmsblog::getPageLink('jmsblog-category', $catparams)|escape:'UTF-8'}">{$post['category_title']|escape:'htmlall':'UTF-8'}</a> /
            {/if}
            {if !isset($config['JMSBLOG_SHOW_DATE']) || $config['JMSBLOG_SHOW_DATE']}
                <span class="date">{$post['created']|escape:'htmlall':'UTF-8'}</span> /
            {/if}
            {if !isset($config['JMSBLOG_SHOW_VIEWS']) || $config['JMSBLOG_SHOW_VIEWS']}
                <span class="view">{l s='Views: ' mod='jmsblog'}{$post.views|escape:'htmlall':'UTF-8'}</span>
            {/if}
            {if !isset($config['JMSBLOG_SHOW_COMMENTS']) || $config['JMSBLOG_SHOW_COMMENTS']}
                <div class="comment">
                    {$post['comment_count']|escape:'htmlall':'UTF-8'}
                </div>
            {/if}
        </div>
        {if !isset($config['JMSBLOG_SHOW_INTRO']) || $config['JMSBLOG_SHOW_INTRO']}
            <div class="introtext">{$post['introtext']|escape:'UTF-8'}</div>
        {/if}
    </div>
</article>
{/if}