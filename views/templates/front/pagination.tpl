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
<div class="jms-blog-pagination">
	{if $total > $limit}
	{assign var=controller value="jmsblog-$c_name"}
	{if $c_name=='archive' }
		{assign var=params value=['archive'=>$month, 'start'=>'']}
	{else}
		{assign var=params value=$catparams}
	{/if}
	<ul class="jms-pagination">

		{for $foo = 1 to $pages}
			{$params.start = $foo}
			<li class=""><a class="{if $foo == $start}active{/if} jmsbutton" href="{if Configuration::get('PS_REWRITING_SETTINGS')}{jmsblog::getPageLink($controller, $params)|escape:'htmlall':'UTF-8'}{else}{$current_uri|escape:'htmlall':'UTF-8'}{if isset($param)}{$param|escape:''}{/if}&start={$foo|escape:'htmlall':'UTF-8'}&limit={$limit|escape:'htmlall':'UTF-8'}{/if}">{$foo|escape:'htmlall':'UTF-8'}</a></li>
		{/for}
	</ul>
	{/if}
	<div class="counter-div">
		<span>Total : {$total|escape:'htmlall':'UTF-8'} items</span>
	</div>
</div>