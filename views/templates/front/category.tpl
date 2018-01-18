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

<h1 class="title-blog">{$current_category.title|escape:'htmlall':'UTF-8'}</h1>

{if $config.JMSBLOG_SHOW_SOCIAL_SHARING}
	<div class="social-sharing">
		{literal}
			<script type="text/javascript">var switchTo5x=true;</script>
		{/literal}
		{if $use_https}
			<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
		{else}
			<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
		{/if}
		{literal}
			<script type="text/javascript">stLight.options({publisher: "a6f949b3-864b-44c5-b0ec-4140186ad958", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
		{/literal}
		<span class='st_sharethis_large' displayText='ShareThis'></span>
		{if $config.JMSBLOG_SHOW_FACEBOOK}
		<span class='st_facebook_large' displayText='Facebook'></span>
		{/if}
		{if $config.JMSBLOG_SHOW_TWITTER}
		<span class='st_twitter_large' displayText='Tweet'></span>
		{/if}
		{if $config.JMSBLOG_SHOW_GOOGLEPLUS}
		<span class='st_googleplus_large' displayText='Google +'></span>
		{/if}
		{if $config.JMSBLOG_SHOW_LINKEDIN}
		<span class='st_linkedin_large' displayText='LinkedIn'></span>
		{/if}
		{if $config.JMSBLOG_SHOW_PINTEREST}
		<span class='st_pinterest_large' displayText='Pinterest'></span>
		{/if}
		{if $config.JMSBLOG_SHOW_EMAIL}
		<span class='st_email_large' displayText='Email'></span>
		{/if}
	</div>
{/if}
{if isset($posts) AND $posts}
	{if isset($current_category.childs) && count($current_category.childs) > 0}
		{include file='./childCatMenu.tpl' categories=$current_category.childs}
	{/if}
	{if $config['JMSBLOG_SHOW_HEADING_CATEGORY']}
		{$config['JMSBLOG_SHOW_HEADING'] = 1}
		{$config['JMSBLOG_SHOW_SUB'] = 1}
		{$config['JMSBLOG_CATEGORIES_BOX_COLUMN'] = 2}
		{include file='./categoryBox/cbox-left-right.tpl' category=$current_category seeMore=false}
	{/if}
	<div class="post-list">
		{foreach $posts as $post}
		{include './postgrid/grid-common.tpl'}
		{/foreach}
	</div>
	{$catparams.category_id = $current_category.category_id}
	{$catparams.slug = {$current_category.title|replace:'-':'_'|replace:' ':'_'}}
	{include file="./pagination.tpl" c_name='category'}
{else}
{l s='Sorry, dont have any post in this category' mod='jmsblog'}
{/if}


