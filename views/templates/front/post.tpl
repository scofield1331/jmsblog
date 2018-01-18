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
{capture name=path}{$post.title|escape:'htmlall':'UTF-8'}{/capture}
<h3 class="title-blog">{$post.title|escape:'htmlall':'UTF-8'}</h3>
<div class="blog-post">
	{assign var=catparams value=['category_id' => $post.category_id, 'slug' => $post.category_alias|replace:'-':'_'|replace:' ':'_', 'start'=>'']}
	<div class="post-meta">
		{if $jmsblog_setting.JMSBLOG_SHOW_CAT_DETAIL}
		<span class="post-category"><a href="{jmsblog::getPageLink('jmsblog-category', $catparams)|escape:'htmlall':'UTF-8'}">{$post.category_name|escape:'htmlall':'UTF-8'}</a> / </span>
		{/if}
		{if $jmsblog_setting.JMSBLOG_SHOW_DATE_DETAIL}
		<span class="post-created">{$post.created|escape:'htmlall':'UTF-8'|date_format:"%B %e, %Y"} / </span>
		{/if}
		{if $jmsblog_setting.JMSBLOG_SHOW_VIEWS_DETAIL}
		<span class="post-views">{l s='Views:' mod='jmsblog'}{$post.views|escape:'htmlall':'UTF-8'} / </span>
		{/if}
		{if $jmsblog_setting.JMSBLOG_SHOW_COMMENTS_DETAIL}
		<span class="post-comment-count"><a title="Comment on {$post.title|escape:'htmlall':'UTF-8'}" href="#comments">{l s='Comments:' mod='jmsblog'}{$comments|@count|escape:'htmlall':'UTF-8'}</a></span>
		{/if}
	</div>
	{if $post.cover && $jmsblog_setting.JMSBLOG_SHOW_COVER_DETAIL}
		<div class="post-image">
			<img src="{$image_baseurl|escape:'htmlall':'UTF-8'}{$post.cover|escape:'htmlall':'UTF-8'}" />
		</div>
	{/if}
	<div class="post-fulltext">
		{$post.fulltext|escape:'UTF-8'}
	</div>
	{if isset($post.image) && count($post.image)>0}
		<h1>{l s='Photos Gallery' mod='jmsblog'}</h1>
		<div class="row" id="row">
			<div id="gcontainer">
			{foreach from=$post.image item=img}

				<div class="gallery">
					<a class="galleryimage" rel="galleryimage" href="{$image_baseurl|escape:'htmlall':'UTF-8'}{$img->image|escape:'htmlall':'UTF-8'}"><img src="{$image_baseurl|escape:'htmlall':'UTF-8'}{$img->image|escape:'htmlall':'UTF-8'}" alt=""/></a>
				</div>
			{/foreach}
			</div>
		    <a class="prev" ><i class="icon-chevron-left"></i></a>
		    <a class="next" ><i class="icon-chevron-right"></i></a>
		</div>
		<script type="text/javascript">
			$(document).ready(function() {
				width = $("#row").width();
				gwidth = $("#gcontainer").width();
				$(".prev").click(function() {
					left = $("#gcontainer").position().left+width;
					if (left <= 0) {
						$("#gcontainer").css('left',left+'px');
					}
				})
				$(".next").click(function() {
					left = $("#gcontainer").position().left-width;
					if (left*(-1) <= gwidth) {
						$("#gcontainer").css('left',left+'px');
					}

				})
				$("a.galleryimage").fancybox({
					'transitionIn'	:	'elastic',
					'transitionOut'	:	'elastic',
					'speedIn'		:	600,
					'speedOut'		:	200,
					'overlayShow'	:	false
				});

			});
		</script>
	{/if}
	{if isset($post.link_video) && count($post.link_video)}
	<h1>{l s='Videos' mod='jmsblog'}</h1>
		{foreach from=$post.link_video item=video}
		<div class="post-thumb">
		{$video->link_video|escape:'UTF-8'}
		</div>
		{/foreach}
	{/if}
</div>
{if $jmsblog_setting.JMSBLOG_SOCIAL_SHARING_DETAIL}
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
{if $jmsblog_setting.JMSBLOG_SHOW_FACEBOOK}
<span class='st_facebook_large' displayText='Facebook'></span>
{/if}
{if $jmsblog_setting.JMSBLOG_SHOW_TWITTER}
<span class='st_twitter_large' displayText='Tweet'></span>
{/if}
{if $jmsblog_setting.JMSBLOG_SHOW_GOOGLEPLUS}
<span class='st_googleplus_large' displayText='Google +'></span>
{/if}
{if $jmsblog_setting.JMSBLOG_SHOW_LINKEDIN}
<span class='st_linkedin_large' displayText='LinkedIn'></span>
{/if}
{if $jmsblog_setting.JMSBLOG_SHOW_PINTEREST}
<span class='st_pinterest_large' displayText='Pinterest'></span>
{/if}
{if $jmsblog_setting.JMSBLOG_SHOW_EMAIL}
<span class='st_email_large' displayText='Email'></span>
{/if}
</div>
{/if}
{if $jmsblog_setting.JMSBLOG_COMMENT_ENABLE}
<div id="comments">
{if $jmsblog_setting.JMSBLOG_FACEBOOK_COMMENT == 0}
{include file="{$module_dir}comment_default.tpl"}
{else}
{include file="{$module_dir}comment_facebook.tpl"}
{/if}
</div>
{/if}
