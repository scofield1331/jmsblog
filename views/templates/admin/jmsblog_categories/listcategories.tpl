{*
* 2007-2017 PrestaShop
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
{function menu lvl=0}
<div {if $lvl==0}id="categories"{else}class="submenu{$lvl|escape:'htmlall'}" style="display: none"{/if}>
	{foreach $data as $category}
		<div id="categories_{$category.category_id|escape:'htmlall':'UTF-8'}" class="list-item">
			<div class="row lvl{$lvl|escape:'htmlall'}">
				<div class="col col-sm-1 row-id">{$category.category_id|escape:'htmlall':'UTF-8'}</div>
				<div class="col col-sm-3 title">{$category.title|escape:'htmlall':'UTF-8'}</div>
				<div class="col col-sm-1">
					<span><i class="icon-arrows "></i></span>
				</div>
				<div class="col col-sm-7">
					<div class="btn-group-action pull-right">
						{if count($category.childs)>0}
						<a class="btn btn-default expand-collapse" data-expand="1" title="Collapse / Expand">
							<i class="icon-caret-down"></i>
						</a>
						{/if}
						<a class="btn {if $category.active}btn-success{else}btn-danger{/if}"	href="{$link->getAdminLink('AdminJmsblogCategories')|escape:'htmlall':'UTF-8'}&configure=jmsblog&status_id_category={$category.category_id|escape:'htmlall':'UTF-8'}&changeCategoryStatus{$page_params|escape:'UTF-8'}" title="{if $category.active}Enabled{else}Disabled{/if}">
							<i class="{if $category.active}icon-check{else}icon-remove{/if}"></i>{if $category.active}Enabled{else}Disabled{/if}
						</a>
						<a class="btn btn-default"									href="{$link->getAdminLink('AdminJmsblogCategories')|escape:'htmlall':'UTF-8'}&configure=jmsblog&id_category={$category.category_id|escape:'htmlall':'UTF-8'}">
							<i class="icon-edit"></i>
							{l s='Edit' mod='jmsblog'}
						</a>
						<a class="btn btn-default"
								href="{$link->getAdminLink('AdminJmsblogCategories')|escape:'htmlall':'UTF-8'}&configure=jmsblog&delete_id_category={$category.category_id|escape:'htmlall':'UTF-8'}{$page_params|escape:'UTF-8'}" onclick="return confirm('Are you sure you want to delete this item?');">
							<i class="icon-trash"></i>
							{l s='Delete' mod='jmsblog'}
						</a>
					</div>
				</div>
			</div>
			{if count($category.childs)>0}
				{menu data=$category.childs lvl=$lvl+1}
			{/if}
		</div>
	{/foreach}
</div>
{/function}
{assign var=page_params value="&start=$start&limit=$limit"}
<div class="panel"><h3><i class="icon-list-ul"></i> {l s='Categories' mod='jmsblog'}
	<span class="panel-heading-action">
		<a class="list-toolbar-btn" href="{$link->getAdminLink('AdminJmsblogCategories')|escape:'htmlall':'UTF-8'}&configure=jmsblog&addCategory=1">
			<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Add new" data-html="true">
				<i class="process-icon-new "></i>
			</span>
		</a>
	</span>
	</h3>
	<div class="table-responsive-row clearfix">
		<div class="list-heading row">
			<div class="col-sm-1">{l s='ID' mod='jmsblog'}</div>
			<div class="col-sm-3">{l s='Category Title' mod='jmsblog'}</div>
			<div class="col-sm-1"></div>
			<div class="col-sm-7" style="text-align: right">{l s='Action' mod='jmsblog'}</div>
		</div>
		{menu data=$tree}
	</div>
	{include './pagination.tpl'}
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('.expand-collapse').on('click', function() {
			if($(this).attr('data-expand') == 1) {
				$(this).closest('.list-item').children('.ui-sortable').css('display','block');
				$(this).children().attr('class','icon-caret-up');
				$(this).attr('data-expand','0');
			}
			else {
				$(this).closest('.list-item').children('.ui-sortable').css('display','none');
				$(this).children().attr('class','icon-caret-down');
				$(this).attr('data-expand','1');
			}
		});
	})
</script>