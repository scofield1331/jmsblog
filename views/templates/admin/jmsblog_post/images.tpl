{*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">

    $(function() {
        var list = $("#imageList");
        list.sortable({
            opacity: 0.6,
            cursor: "move",
            update: function() {
                var order = $(list).sortable("serialize") + "&action=updateImageOrdering";
                $.post("{$ajaxpath|escape:''}", order);
            },
            stop: function( event, ui ) {
                showSuccessMessage("Saved!");
            }
        });
        list.hover(function() {
            $(this).css("cursor","move");
            },
            function() {
            $(this).css("cursor","auto");
        });
    });
</script>
<div class="panel">
    <div class="">
        <input type="hidden" name="submitted_tabs[]" value="Images" />
        <div class="panel-heading tab" >
            {l s='Images'}
            <span class="badge" id="countImage">{$countImages|escape:'htmlall':'UTF-8'}</span>
        </div>
        <div class="row">
            <div class="form-group">
                <label class="control-label col-lg-3 file_upload_label">
                    <span class="label-tooltip" data-toggle="tooltip"
                        title="{l s='Format:'} JPG, GIF, PNG. {l s='Filesize:'} {$max_image_size|string_format:"%.2f"} {l s='MB max.'}">
                        {if isset($id_image)}{l s='Edit this product\'s image:'}{else}{l s='Add a new image to this product'}{/if}
                    </span>
                </label>
                <div class="col-lg-9">
                    {$image_uploader}
                </div>
            </div>
        </div>
        <div class="table-responsive-row clearfix">
            <table class="table tableDnD">
                <tr class="heading">
                    <th>{l s='Image' mod='jmsblog'}</th>
                    <th>{l s='Cover' mod='jmsblog'}</th>
                    <th></th>
                    <th class="right">{l s='Action' mod='jmsblog'}</th>
                </tr>
                <tbody id="imageList">
                    {foreach from=$Images key=i item=image}
                        <tr id="imageList_{$image->id|escape:'htmlall':'UTF-8'}" class="{if $i%2 == 1}odd{/if}">
                            <td class="row-id">
                                <img class="img-thumbnail" src="{$baseDir|escape:'htmlall':'UTF-8'}thumb_{$image->image|escape:'htmlall':'UTF-8'}">
                            </td>
                        <td>
                            <a class="cover" href="javascript:void(0)">
                                <i class="{if $image->cover}icon-check-sign{else}icon-check-empty{/if} icon-2x covered"></i>
                            </a>
                        </td>
                        <td>
                            <span><i class="icon-arrows "></i></span>
                        </td>
                        <td>
                            <div class="btn-group-action pull-right delete_post_image">
                                <a class="pull-right btn btn-default" >
                                    <i class="icon-trash"></i> {l s='Delete this image'}
                                </a>
                            </div>
                        </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
            <table style="display:none;">
                <tbody id="lineType">
                    <tr id="image_id"">
                        <td class="row-id">
                            <img class="img-thumbnail" src="{$baseDir|escape:'htmlall':'UTF-8'}default.jpg">
                        </td>
                        <td>
                            <a class="cover" href="javascript:void(0)">
                                <i class="icon-check-empty icon-2x covered"></i>
                            </a>
                        </td>
                        <td>
                            <span><i class="icon-arrows "></i></span>
                        </td>
                        <td>
                            <div class="btn-group-action pull-right delete_post_image">
                                <a class="pull-right btn btn-default" >
                                    <i class="icon-trash"></i> {l s='Delete this image'}
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- <div class="panel-footer">
        <button value="1" id="configuration_form_submit_btn" name="submitPost" class="btn btn-default pull-right submitPost">
            <i class="process-icon-save"></i> Save
        </button>
        <button type="submit" class="btn btn-default btn btn-default pull-right submitPostAndStay" name="submitPostAndStay"><i class="process-icon-save"></i> Save and Stay</button>
    </div> -->
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        $('#imageList').on('click', '.delete_post_image', function() {
            if (confirm("{l s='Delete this image?' mod='jmsblog'}")) {
                var row = $(this).parent().parent();
                $.get("{$ajaxpath|escape:''}", "action=deletePostImage&image="+row.attr('id') , function(data) {
                    var response = JSON.parse(data);
                    if (response.status) {
                        row.remove();
                        $("#countImage").html(parseInt($("#countImage").html()) - 1);
                        showSuccessMessage("{l s='Deleted!' mod='jmsblog'}");
                    } else {
                        console.log('error');
                    }
                });
            }
        })

        $('#imageList').on('click', '.cover', function() {
            var row = $(this).parent().parent();
            $.get("{$ajaxpath|escape:''}", 'action=updateImageCover&id_post={$id_post|escape:'htmlall'}&image='+row.attr('id'), function(data) {
                var response = JSON.parse(data);
                console.log(response);
                if (response.status) {
                    $('#imageList_'+response.newcover).find('.icon-check-empty').toggleClass('icon-check-empty icon-check-sign');
                    if(response.oldcover != 0) {
                        $('#imageList_'+response.oldcover).find('.icon-check-sign').toggleClass('icon-check-sign icon-check-empty');
                    }
                    showSuccessMessage("{l s='Updated!' mod='jmsblog'}");
                } else {
                    console.log('error');
                }
            });
        })

    })
</script>
