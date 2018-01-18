$(document).ready(function(){
    $('.more li').click(function(){
        $(this).closest('.catmenu').children('li').removeClass('active');
    })
    $('.catmenu>li').click(function(){
        $(this).closest('.catmenu').find('.more li').removeClass('active');
    })
})