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
{if count($headingPosts)%2}
{assign var="odd" value=false}
{else}
{assign var="odd" value=true}
{/if}
<div class="headingPost row">
    <div class="col-sm-6 nopadding">
        {if isset($headingPosts[0])}
            {include '../postgrid/grid-topheading.tpl' post=$headingPosts[0]}
        {/if}
    </div>
    <div class="col-sm-6 nopadding">
            {if $odd}
                {if isset($headingPosts[1])}
                    {include '../postgrid/grid-topheading.tpl' post=$headingPosts[1] type='medium'}
                {/if}
            {/if}
        <div>
            <div class="col-sm-6 nopadding">
            {for $i=1+$odd to ceil(count($headingPosts)/2)+$odd-1}
                {if isset($headingPosts[$i])}
                    {include '../postgrid/grid-topheading.tpl' post=$headingPosts[$i] type='small'}
                {/if}
            {/for}
            </div>
            <div class="col-sm-6 nopadding">
            {for $i=ceil(count($headingPosts)/2)+$odd to count($headingPosts)-1}
                {if isset($headingPosts[$i])}
                    {include '../postgrid/grid-topheading.tpl' post=$headingPosts[$i] type='small'}
                {/if}
            {/for}
            </div>
        </div>
    </div>
</div>