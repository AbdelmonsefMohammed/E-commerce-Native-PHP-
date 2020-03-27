$(function (){

    'use strict';

    //Dashboard

    $('.toggle-info').click(function(){
        $(this).toggleClass('selected').parent().next('.card-body').fadeToggle(100);
        if($(this).hasClass('selected')){
            $(this).html('<i class="fa fa-minus fa-lg"></i>');
        }else{
            $(this).html('<i class="fa fa-plus fa-lg"></i>');
        }
    });

    //Trigger (RUN) the selectBoxIt 
    $("select").selectBoxIt({
        autoWidth:false
    });

    //Hide placeholder on form focus

    $('[placeholder]').focus(function (){
        $(this).attr('data-text', $(this).attr('placeholder'));
        $(this).attr('placeholder','');
    }).blur(function () {
        $(this).attr('placeholder',$(this).attr('data-text'));
    });

   
    // convert password field into text field
    var passField = $('.password');
    $('.show-pass').hover(function (){
        passField.attr('type','text');
    },function(){
        passField.attr('type','password');
    });

    //confirmation message on Button
    $('.confirm').click(function(){
        return confirm('Are you sure u want to Delete this ?'); 
    });

    // category view options
    $('.cat h3').click(function(){
        $(this).next('.full-view').fadeToggle(200);

    });

    $('.option span').click(function(){
        $(this).addClass('active').siblings('span').removeClass('active');
        if($(this).data('view') === 'full'){
            $('.cat .full-view').fadeIn(200);
        }else{
            $('.cat .full-view').fadeOut(200);
        }
    });
});