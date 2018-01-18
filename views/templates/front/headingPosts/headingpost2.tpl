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
<div class="headingPost row">
    <div id="mainheading" class="col-sm-6 nopadding">
        {if isset($headingPosts[0])}
            {include '../postgrid/grid-topheading.tpl' post=$headingPosts[0]}
        {/if}
    </div>
    <div class="col-sm-6 nopadding">
        <div class="sub-box">
        {$topconfig['JMSBLOG_SHOW_CAT_SUB'] = true}
        {$topconfig['JMSBLOG_SHOW_COMMENTS'] = false}
        {for $i=1 to count($headingPosts)-1}
            {include '../postgrid/grid-common.tpl' post=$headingPosts[$i] config=$topconfig}
        {/for}
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var img = $("#mainheading img");
        function adjustBox() {
            $(".sub-box").css('height',$("#mainheading").height()-6);
            $(".sub-box").css('overflow-y','scroll');
        }
        if (img[0].complete) {
            adjustBox();
        }
        img.load(function() {
            adjustBox();
        })
    })
</script>