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
<div id="cat-{$category['category_id']|escape:'html'}" class="category tab-pane fade {if !isset($active)}active in{else}{/if}" >
    {if !count($category['heading']) && !count($category['common'])}
     <h3>No posts found!</h3>
    {else}
        {if $config['JMSBLOG_SHOW_HEADING']}
            {assign var=col value=$config['JMSBLOG_CATEGORIES_BOX_COLUMN']}
            {if count($category['heading']) < $col}
                {$col = count($category['heading'])}
            {/if}
            {foreach from=$category['heading'] item=heading key=k}
                {if $k mod $col==0}
                <div class="row">
                {/if}
                <div class="col-sm-{12/$col}">
                    {include '../postgrid/grid-heading.tpl' post=$heading}
                </div>
                {if $k mod $col==$col-1 || $k==count($category['heading'])-1}
                </div>
                {/if}
            {/foreach}
        {/if}
        {if $config['JMSBLOG_SHOW_SUB']}
            {foreach from=$category['common'] item=subheading key=k}
                {if $k mod $config['JMSBLOG_CATEGORIES_BOX_COLUMN']==0}
                <div class="row">
                {/if}
                <div class="col-sm-{12/$config['JMSBLOG_CATEGORIES_BOX_COLUMN']}">
                    {include '../postgrid/grid-common.tpl' post=$subheading}
                </div>
                {if $k mod $config['JMSBLOG_CATEGORIES_BOX_COLUMN']==$config['JMSBLOG_CATEGORIES_BOX_COLUMN']-1 || $k==count($category['common'])-1}
                </div>
                {/if}
            {/foreach}
        {/if}
        {if !isset($seeMore) || $seeMore}
        <div class="row">
            {$catparams['category_id'] = $category['category_id']}
            {$catparams['slug'] = {$category['alias']|replace:' ':'_'|replace:'-':'_'}}
            <a class="category" href="{jmsblog::getPageLink('jmsblog-category', $catparams)|escape:'UTF-8'}">
                <button class="readmore">{l s='SEE MORE' mod='jmsblog'}<i class="icon-forward"></i></button>
            </a>
        </div>
        {/if}
    {/if}
</div>