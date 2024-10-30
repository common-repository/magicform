<?php

function migrateForMagicForm(){
    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

    $table_name = $wpdb->get_blog_prefix() . 'magic_forms_plagin';
    $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
	`id`  int (20) unsigned NOT NULL auto_increment,
	`user_id`  int (20) unsigned NOT NULL default 0,
	`title` varchar(255) NOT NULL default '',
	`shortcode` varchar(255) NOT NULL default '',
	`fields` text NOT NULL default '',
	`update_date` datetime NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY  (id)
	)
	{$charset_collate};";
    dbDelta($sql);

    $table_name = $wpdb->get_blog_prefix() . 'magic_forms_logs';
    $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
	`id`  int (20) unsigned NOT NULL auto_increment,
	`form_id`  int (11) unsigned NOT NULL default 0,
	`fields` text NOT NULL default '',
	`errors` text NOT NULL default '',
	`send_date` datetime NOT NULL default '0000-00-00 00:00:00',
	`user_ip` varchar(255) NOT NULL default '',
	`user_email` varchar(255) NOT NULL default '',
	`letter_theme` varchar(255) NOT NULL default '',
	PRIMARY KEY  (id)
	)
	{$charset_collate};";
    dbDelta($sql);


    $table_name = $wpdb->get_blog_prefix() . 'magic_forms_mails';
    $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
	`form_id`  int (20) unsigned NOT NULL default 0,
	`from_mail` varchar(255) NOT NULL default '',
	`to_mail` varchar(255) NOT NULL default '',
	`title` varchar(255) NOT NULL default '',
	`body` text NOT NULL default '',
	`use_html`  int (1) NULL default 0,
	`is_auto_response` int (1) NULL default 0,
	`is_send` int (1) NULL default 0
	)
	{$charset_collate};";
    dbDelta($sql);

    $table_name = $wpdb->get_blog_prefix() . 'magic_forms_messages';
    $sql = "
    CREATE TABLE `".$table_name."` (
	`id`  int (11) unsigned NOT NULL auto_increment,
	`name_event` varchar(255) NOT NULL default '',
	`value_event` varchar(255) NOT NULL default '',
	`value_data` text NOT NULL default '',
	PRIMARY KEY  (`id`)
	)
	{$charset_collate};";
    $wpdb->query("DROP TABLE IF EXISTS `".$table_name."`;");
    dbDelta($sql);
    load_plugin_textdomain( 'wpcftr', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    $data = [];
    $data['form_sent_ok'] = [0=>__('Уведомление успешной отправки формы', 'wpcftr'),1=>__('Спасибо, форма отправлена', 'wpcftr')];
    $data['form_sent_er'] = [0=>__('Уведомление ошибки отправки формы', 'wpcftr'), 1=>__('Error. При отправке формы возникли ошибки, свяжитесь с администратором', 'wpcftr')];
    $data['spam'] = [0=>__('Отправляемые данные определены как спам. Теги [label] [spam]', 'wpcftr'),1=>__('Отправляемые данные определены как спам [label] [spam]', 'wpcftr')];
    $data['quiz_val_er'] = [0=>__('Уведомление неверный ответ на проверочный вопрос', 'wpcftr'), 1=>__('Неверный ответ на проверочный вопрос', 'wpcftr')];
    $data['required'] = [0=>__('Поле [label] обязательное', 'wpcftr'), 1=>__('Поле [label] обязательное', 'wpcftr')];
    $data['date_min'] = [0=>__('[label] меньше допустимой', 'wpcftr'), 1=>__('Поле [label] меньше допустимой', 'wpcftr')];
    $data['date_max'] = [0=>__('[label] больше допустимой', 'wpcftr'), 1=>__('Поле [label] больше допустимой', 'wpcftr')];
    $data['str_len_max'] = [0=>__('[label] длина больше допустимой', 'wpcftr'), 1=>__('Поле [label] длина больше допустимой', 'wpcftr')];
    $data['str_len_min'] = [0=>__('[label] длина меньше допустимой', 'wpcftr'), 1=>__('Поле [label] длина меньше допустимой', 'wpcftr')];
    $data['fil_size_er'] = [0=>__('Уведомление о превышении размера загружаемого файла', 'wpcftr'), 1=>__('Превышен размер загружаемого файла', 'wpcftr')];
    $data['fil_ext_er'] = [0=>__('Уведомление о неподходящем расширении файла', 'wpcftr'), 1=>__('Расширение не подходит', 'wpcftr')];
    $data['phone_val_er'] = [0=>__('[label] ввод запрещенных символов', 'wpcftr'), 1=>__('Поле [label] разрешается вводить только цифры 0-9 и символы ),(,-', 'wpcftr')];
    $data['sendmailerror'] = [0=>__('Ошибка отправки письма', 'wpcftr'), 1=>__('При отправке письма возникли ошибки, проверьте вкладку Отправка письма', 'wpcftr')];
    $data['autosendmailerror'] = [0=>__('Ошибка автоответчика', 'wpcftr'), 1=>__('При отправке письма автоответчиком возникли ошибки, проверьте вкладку Автоответчик', 'wpcftr')];
    $data['recaptchaerror'] = [0=>__('Проверка reCAPTCHA не пройдена', 'wpcftr'), 1=>__('Вы не прошли проверку Я не робот', 'wpcftr')];

    $val = '';
    $i = 0;
    foreach($data as $key=>$value){
        if(!$i == 0){
            $val .= ', ';
        }
        $val .= '( "'.$key.'", "'.$value[0].'", "'.$value[1].'" )';
        $i++;
    }
    $sql = 'insert into '.$table_name.' (`name_event`, `value_event`, `value_data`) values '.$val.';';
    $wpdb->query($sql);
}