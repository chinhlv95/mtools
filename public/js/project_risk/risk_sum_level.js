/**
 * 
 */
$(document).ready(function(){
    $('.level').val(sum_level());
    $('.propability').on('change',function(){
        $('.level').val(sum_level());
    });
    $('.impact').on('change',function(){
        $('.level').val(sum_level());
    });
});
function sum_level()
{
    var level = $('.propability').val() * $('.impact').val() /100;
    return level;
}