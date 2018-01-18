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
<script>
    $( function() {
        var primaryform = $("#configuration_form");
        input = $('<input>');
        input.attr('type','hidden');
        input.attr('name','post_tab');
        input.val({$post_tab|escape:'htmlall'});
        input.appendTo(primaryform);
        $('#tabs').tabs({
            active: {$post_tab|escape:'htmlall'},
            activate: function(event, ui) {
                var index = $( "#tabs" ).tabs( "option", "active" );
                var input = $("input[name='post_tab']").val(index);
            }
        }).removeClass('ui-widget-content');
        $('#tabs ul').removeClass('ui-widget-header');
    } );

</script>
<div id="tabs" class="ui-tabs-vertical ui-helper-clearfix">
    <ul class="col-lg-2 col-md-3 list-group">
    {foreach from=$form item=f key=i}
        <li class="nopadding noborder"><a class="list-group-item" href="#tabs-{$i|escape:'htmlall'}">{$i|escape:''}</a></li>
    {/foreach}
    </ul>
    <div class="col-lg-10 col-md-9 nopadding">
        {foreach from=$form item=content key=i}
        <div id="tabs-{$i|escape:'htmlall'}" class="nopadding" style="display:none">
            {$content|escape:''}
        </div>
        {/foreach}
    </div>

</div>
<script type="text/javascript">
    $(document).ready(function(){
        var form = $("form[id^='configuration_form_']");
        var primaryform = $("#configuration_form");
        $(".submitPost, .submitPostAndStay, .duplicate").click(function() {
            form.find(":input").clone().hide().appendTo(primaryform);
            var input = $('<input>');
            input.attr('type','hidden');
            input.attr('name',$(this).attr('name'));
            input.appendTo(primaryform);
            primaryform.submit();
        })
    })
</script>