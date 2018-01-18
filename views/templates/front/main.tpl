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
{assign var=toppath value="./headingPosts/{$config['JMSBLOG_TOP_BOX']}"}
{include file=$toppath}

{assign var=path value="./categoryBox/{$config['JMSBLOG_CATEGORIES_BOX']}"}
{foreach from=$categories item=category}
    <div class="block-category-wrap">
        <div class="block-category">
            <span class="parent-category">{$category['title']|escape:'htmlall':'UTF-8'}</span>
            <ul class="catmenu">
                <li class="childs-category catlv1 single-cat active" >
                    <a href="#cat-{$category['category_id']|escape:'html'}" data-toggle="tab">{l s='All' mod='jmsblog'}</a>
                </li>
                {foreach from=$category['childs'] item=child key=k}
                    {if $k==6}
                    <li class="childs-category catlv1 parent-cat" >More<i class="icon-caret-down"></i>
                        <ul class="more">
                    {/if}
                    <li class="childs-category catlv1 single-cat" >
                        <a href="#cat-{$child['category_id']|escape:'html'}" data-toggle="tab">{$child['title']}</a>
                    </li>
                    {if $k==(count($category['childs'])-1)}
                        </ul>
                    </li>
                    {/if}
                {/foreach}
            </ul>
        </div>
    </div>
    <div class="tab-content">
        {include file=$path category=$category}
        {foreach $category['childs'] as $child}
            {include file=$path category=$child active=false}
        {/foreach}
    </div>

{/foreach}

