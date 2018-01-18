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
{extends file="helpers/form/form.tpl"}

{block name="field"}
    {if $input.type == 'generate'}
    {capture name='mes'}
        The {$input.route_id|replace:'jmsblog-':''} link will look like
    {/capture}
    <div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if}{if !isset($input.label)} col-lg-offset-3{/if}">
        <div class="btn btn-default" style="margin:0 5px 5px 0" id="generateBtn"><i class="icon-random"></i>generate</div>
        {foreach from=$languages item=language}
        <div class="alert alert-warning translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
            <i class="icon-link"></i> {$smarty.capture.mes|escape:'html'}<br/>
            <strong id="generate_{$language.id_lang}">{jmsblog::getPageLink($input.route_id, $fields_value[$input.name][$language.id_lang])|escape:'UTF-8'}</strong>
        </div>
        {/foreach}
        <script type="text/javascript">
            $(document).ready(function() {
                var url = '{$input.url}{$input.rule}';
                var keywords = JSON.parse('{$input.keywords}');
                {if $input.route_id == 'jmsblog-post'}
                var cat_data = JSON.parse('{$input.cat_data}');
                var catselect = [];
                findCat();
                function findCat() {
                    var index = $("#category_id").prop('selectedIndex')-1;
                    if (index < 0) {
                        catselect['alias'] = {literal}'{category}'{/literal};
                    } else {
                        catselect = cat_data[$("#category_id").prop('selectedIndex')-1];
                    }
                }
                {/if}
                function generate() {
                    $("input[name^=title]").each(function(item) {
                        var name = $(this).attr('name');
                        var aliasInput = '#'+name.replace('title','alias');
                        $(aliasInput).val(makeAlias($(this).val()));
                        generateLink(aliasInput);
                    })
                }
                function generateLink(input) {
                    var name = $(input).attr('name');
                    var generateLink = '#'+name.replace('alias','generate');
                    var params = [];
                    {if $input.route_id == 'jmsblog-post'}
                    if ((params['post_id'] = $('#id_post').val()) == undefined) {
                        params['post_id'] = {literal}'{id}'{/literal};
                    }
                    params['category_slug'] = catselect['alias'];
                    params['slug'] = makeAlias($(input).val());
                    {/if}
                    {if $input.route_id == 'jmsblog-category'}
                    if ((params['category_id'] = $('#id_category').val()) == undefined) {
                        params['category_id'] = {literal}'{id}'{/literal};
                    }
                    params['slug'] = makeAlias($(input).val());
                    {/if}
                    var link = url;
                    $(keywords).each(function(index) {
                        {literal}
                        link = link.replace('{'+this+'}',params[this]);
                        {/literal}
                    })
                    $(generateLink).text(link);
                }
                {literal}
                function makeAlias(url) {
                    url = url.trim();
                    url = url.replace(/[^a-z A-Z0-9-]*/g, '');
                    url = url.replace(/\s/g, '-');
                    url = url.replace(/-{2,}/g, '-');
                    url = url.toLowerCase();
                    return url;
                }
                {/literal}
                $("#generateBtn").click(function() {
                    generate();
                })
                $("#category_id").change(function() {
                    findCat();
                    generate();
                })
                $("input[name^=alias]").keyup(function() {
                    generateLink(this);
                })
                $("input[name^=alias]").blur(function() {
                    generateLink(this);
                })
            })
        </script>
    </div>
    {/if}
    {if $input.type == 'multitext'}
    <div class="multitext">
        <button class="btn btn-default addField pull-right" type="button">
            <i class="icon-plus"></i> Add Field...
        </button>
        <div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if}{if !isset($input.label)} col-lg-offset-3{/if}">
            <input type="hidden" class="fieldsCount" value="1">
            <div class="fieldsContainer">
                {if count($input.videos) > 0}
                    {assign var=index value=0}
                    {foreach from=$input.videos item=videos key=k}
                        {foreach from=$videos item=video}
                        <div class="row nowrap">
                            <input data-id="{if is_object($video)}{$video->id|escape:'html'}{/if}" type="text"
                                name="{$input.name|escape:'html':'UTF-8'}[{$k|escape:''}][{if is_object($video)}{$video->id|escape:'html'}{/if}]"
                                value="{if is_object($video)}{$video->link_video|escape:'html':'UTF-8'}{else}{$video|escape:'html':'UTF-8'}{/if}"
                                class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
                                {if isset($input.size)} size="{$input.size}"{/if}
                                {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                                {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                                {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                {if isset($input.required) && $input.required} required="required" {/if}
                                {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder}"{/if} />
                                <div class="removeField btn btn-default {if $index++==0}hide{/if}">Remove</div>
                        </div>
                        {/foreach}
                    {/foreach}
                {else}
                    <div class="row nowrap">
                        <input type="text"
                            name="{$input.name|escape:'html':'UTF-8'}[new][]"
                            class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
                            {if isset($input.size)} size="{$input.size}"{/if}
                            {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                            {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                            {if isset($input.required) && $input.required} required="required" {/if}
                            {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder}"{/if} />
                        <div class="removeField btn btn-default hide">Remove</div>
                    </div>
                {/if}

            </div>
            {block name="description"}
                <p class="help-block">
                    {if is_array($input.mdesc)}
                        {foreach $input.mdesc as $p}
                            {if is_array($p)}
                                <span id="{$p.id}">{$p.text}</span><br />
                            {else}
                                {$p}<br />
                            {/if}
                        {/foreach}
                    {else}
                        {$input.mdesc}
                    {/if}
                </p>
            {/block}
        </div>
    </div>
        <script type="text/javascript">
            $(document).ready(function() {
                $('.addField').click(function() {
                    var multitext = $(this).parent();
                    var count = multitext.find(".fieldsCount").first().val();
                    var container = multitext.find(".fieldsContainer");
                    var row = container.children().first().clone();
                    $(row.children()[0]).attr('data-id','');
                    $(row.children()[0]).val('');
                    $(row.children()[0]).attr('name',"{$input.name|escape:'html':'UTF-8'}[new][]");
                    $(row.children()[1]).toggleClass('hide');
                    row.appendTo(container);
                })
                $('.fieldsContainer').on('click', '.removeField', function() {
                    var row = $(this).parent();
                    row.hide();
                    var input = row.find('input').first();
                    if (input.attr('data-id').length>0) {
                        input.attr('value', input.attr('data-id'))
                        input.attr('name', "{$input.name|escape:'html':'UTF-8'}[remove][]");
                    } else {
                        row.remove();
                    }
                })
            })
        </script>
    {/if}
    {if $input.type == 'multiselect'}
    <div class="multitext">
        <button class="btn btn-default addField pull-right" type="button">
            <i class="icon-plus"></i> Add Field...
        </button>
        <div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if}{if !isset($input.label)} col-lg-offset-3{/if}">
            <input type="hidden" class="fieldsCount" value="1">
            <div class="fieldsContainer">
                <div class="row nowrap hide">
                    <select name="{$input.name|escape:'html':'UTF-8'}[]" class="form-control">
                        <option disabled selected value> -- select an option -- </option>
                        {foreach $input.options as $option}
                        <option value="{$option.id_option}">{$option.name_option}</option>
                        {/foreach}
                    </select>
                    <div class="removeField btn btn-default">Remove</div>
                </div>
                {if count($input.values) > 0}
                    {foreach from=$input.values item=value key=k}
                        <div class="row nowrap">
                            <select name="{$input.name|escape:'html':'UTF-8'}[]" class="form-control">
                                <option disabled selected value> -- select an option -- </option>
                                {foreach $input.options as $option}
                                <option value="{$option.id_option}" {if $option.id_option == $value}selected{/if}>{$option.name_option}</option>
                                {/foreach}
                            </select>
                            <div class="removeField btn btn-default">Remove</div>
                        </div>
                    {/foreach}
                {else}
                {/if}
            </div>
            {block name="description"}
                <p class="help-block">
                    {if is_array($input.mdesc)}
                        {foreach $input.mdesc as $p}
                            {if is_array($p)}
                                <span id="{$p.id}">{$p.text}</span><br />
                            {else}
                                {$p}<br />
                            {/if}
                        {/foreach}
                    {else}
                        {$input.mdesc}
                    {/if}
                </p>
            {/block}
        </div>
    </div>
        <script type="text/javascript">
            $( function() {
                $( ".fieldsContainer" ).sortable();
            } );
            $(document).ready(function() {
                $('.addField').click(function() {
                    var multitext = $(this).parent();
                    var count = multitext.find(".fieldsCount").first().val();
                    var container = multitext.find(".fieldsContainer");
                    var row = container.children().first().clone();
                    $(row.children()[0]).val('');
                    row.toggleClass('hide');
                    row.appendTo(container);
                })
                $('.fieldsContainer').on('click', '.removeField', function() {
                    var row = $(this).parent();
                    row.remove();
                })
            })
        </script>
    {/if}
    {if $input.type=='link_choice'}
    {function renderSelectedItems lvl=0}
    <div {if $lvl!=0}class="groupitem"{else}id="items{$input.id|escape:'htmlall'}"{/if}>
        {foreach $selected_items as $item}
        <div data-parent="{$item.parent|escape:'htmlall':'UTF-8'}" data-value="{$item.category_id|escape:'htmlall':'UTF-8'}" class="lvl{$item.lvl|escape:'htmlall':'UTF-8'}">
            <div class="item">
                <input type="hidden" name="{$input.name|escape:'html'}[]" value="{$item.category_id|escape:'htmlall':'UTF-8'}">
                <span>{$item.title|escape:''}</span>
            </div>
            {if isset($item.childs) && count($item.childs) > 0}
                {renderSelectedItems selected_items=$item.childs lvl=$lvl+1}
            {/if}
        </div>
        {/foreach}
    </div>
    {/function}
        <div class="row col-lg-9">
            <div class="col-lg-1">
            </div>
            <div class="col-lg-4">
                <h4 style="margin-top:5px;">{l s='Selected items' mod='jmsblog'}</h4>
                <div style="padding:5px;height: 160px; border: 1px solid #c7d6db; width:300px; overflow-y: scroll; overflow-x: hidden">
                    {renderSelectedItems selected_items=$input.selected_items}
                </div>
            </div>
            <div class="col-lg-4">
                <h4 style="margin-top:5px;">{l s='Available items' mod='jmsblog'}</h4>
                <select multiple="multiple" id="availableItems{$input.id|escape:'htmlall'}" style="width: 300px; height: 160px;">
                {foreach $input.choices as $item}
                <option data-lvl="{$item.lvl}" data-parent="{$item.parent}" value="{$item.category_id}">{$item.name_option}</option>
                {/foreach}
                </select>
            </div>

        </div>
        <br/>
        <div class="row">
            <div class="col-lg-4"></div>
            <div class="col-lg-4"><a href="#" id="removeItem{$input.id|escape:'htmlall'}" class="btn btn-default"><i class="icon-arrow-right"></i> {l s='Remove' mod='jmsblog'}</a></div>
            <div class="col-lg-4"><a href="#" id="addItem{$input.id|escape:'htmlall'}" class="btn btn-default"><i class="icon-arrow-left"></i> {l s='Add' mod='jmsblog'}</a></div>
        </div>
        <script type="text/javascript">
            $(document).ready(function(){
            var items = "#items{$input.id|escape:'htmlall'}";
            var availableItems = "#availableItems{$input.id|escape:'htmlall'}";
            var removeItem = "#removeItem{$input.id|escape:'htmlall'}";
            var addItem = "#addItem{$input.id|escape:'htmlall'}";
            $(items).sortable();
            $(".groupitem").sortable();
            $(items).closest('form').on('submit', function(e) {
                $(items+" option").prop('selected', true);
            });
            $(addItem).click(add);
            $(availableItems).dblclick(add);
            $(removeItem).click(remove);
            $(items).on('click', '.item' ,function(){
                $(this).toggleClass('selected');
            })
            function createItem(val, text, parent)
            {
                var div = document.createElement('div');
                $(div).attr('data-parent',parent);
                $(div).attr('data-value',val);
                var item = document.createElement('div');
                var input = document.createElement('input');
                $(item).addClass('item selected');
                $(input).attr('type','hidden');
                $(input).val(val);
                $(input).attr('name',"{$input.name|escape:'html'}[]");
                $(item).append(input);
                $(item).append('<span>'+text+'</span>');
                $(div).append(item);
                return div;

            }
            function add()
            {
                $(items).find(".selected").removeClass("selected");
                $(items+" option:selected").removeAttr('selected');
                $(availableItems+" option:selected").each(function(i, e){
                    addOption(e);
                });
                return false;
            }
            function remove()
            {
                $(items+" .item.selected").each(function(i,e){
                    removeOption($(e).parent());
                });
                return false;
            }
            {if $input.tree}
            function addOption(i){
                var parent = $(i).attr('data-parent');
                var val = $(i).val();
                var text = $(i).text();
                text = text.replace(/(^\s*)|(\s*$)/gi,"");
                if ($(items+" div[data-value="+val+"]").length) {
                    $(items+" div[data-value="+val+"] .item").first().addClass('selected');
                    return;
                }
                if (parent!=0 && !$(items+" div[data-value="+parent+"]").length) {
                    addOption($(availableItems+" option[value="+parent+"]"));
                }
                if (parent!=0) {
                    var parentdiv = $(items+" div[data-value="+parent+"]");
                    if (parentdiv.find(".groupitem").length < 1) {
                        parentdiv.append('<div class="groupitem ui-sortable"></div');
                        $(".groupitem").sortable();
                    }
                    parentdiv.find(".groupitem").append(createItem(val,text,parent));
                } else {
                    $(items).append(createItem(val,text,0));
                }
            }
            function removeOption(i){
                var val = $(i).attr('data-value');
                $(items+" .item[data-parent="+val+"]").each(function(i,e){
                    removeOption(e);
                })
                $(i).remove();
            }
            {else}
            function addOption(i){
                var val = $(i).val();
                if ($(items+" div[data-value="+val+"]").length) {
                    $(items+" div[data-value="+val+"] .item").first().addClass('selected');
                    return;
                }
                var text = $(i).text();
                text = text.replace(/(^\s*)|(\s*$)/gi,"");
                $(items).append(createItem(val,text,0));
            }
            function removeOption(i){
                $(i).remove();
            }
            {/if}
            });
        </script>
    {/if}
    {$smarty.block.parent}
{/block}