jQuery(document).ready(function($){

    //------Через 3 сек выводим popup окно и скрываем через 30 сек--------------
    var delay_popup = 3000; /*время до активации окна 3 секунды*/
    setTimeout(function(){
        // document.getElementById('parent_popup_contactform').style.display = 'none';
        jQuery('#parent_popup_contactform').hide();
    }, 30000); /*время до автоматического закрытия окна 30 секунд*/
    // setTimeout("document.getElementById('parent_popup_contactform').style.display='block'", delay_popup);
    setTimeout("jQuery('#parent_popup_contactform').show();", delay_popup);
    //---------------------------------------------------------

    $('input[type="phone"]').bind("change keyup input click", function() {
        if (this.value.match(/[^0-9\-()]/g)) {
            this.value = this.value.replace(/[^0-9\-()]/g, '');
        }
    });
});

function magicFormSetRegExp( input, exp ){
    var val = jQuery(input).val();
    val = val.replace(exp, '');
    jQuery(input).val(val);
}

var magicFormRecaptchaCallback = function() {

    jQuery('.captcha-item').each(function(index) {
        jQuery(this).attr('widget-id',grecaptcha.render(jQuery(this).attr('id'), {
            'sitekey' : jQuery(this).attr('data-rkey')
        }));
    });
};