<?php

Magic_Form_AdminFunc::getInstance();

class Magic_Form_AdminFunc
{
    private static $instance = null;

    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function __construct() {}
    private function __clone() {}

    public function tab1(){
        ?>

        <div class="dragdrop">
            <div class="dragdrop__wrapper">
                <div class="dragdrop__fields">
                    <div class="form-title"><?php _e('Инструменты', 'wpcftr') ?></div>

                    <ul  id="draggable">
                        <li class="ui-state-default addfieldinform" data-name="text">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <svg width="21" height="6">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/wp-content/plugins/magic-form/admin/img/svgs.svg#i-textfield"></use>
                                    </svg>
                                </div>
                                <?php _e('Текстовое поле', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>
                        <li class="ui-state-default addfieldinform" data-name="checkbox">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <div class="round-checkbox">
                                        <svg width="16" height="16">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/wp-content/plugins/magic-form/admin/img/svgs.svg#i-checkbox"></use>
                                        </svg>
                                    </div>
                                </div>
                                <?php _e('CheckBox', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>
                        <li class="ui-state-default addfieldinform" data-name="select">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <svg width="21" height="16" id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 16"><defs><style>.cls-1{fill:#bebebe;}.cls-2{fill:#fff;}</style></defs><title>pop-up</title><path id="Прямокутник_із_заокругленими_к1_копія_5" data-name="Прямокутник із заокругленими к1 копія 5" class="cls-1" d="M3,0H18a3,3,0,0,1,0,6H3A3,3,0,0,1,3,0ZM1.5,8h18a1.5,1.5,0,0,1,0,3H1.5a1.5,1.5,0,0,1,0-3Zm0,5h18a1.5,1.5,0,0,1,0,3H1.5a1.5,1.5,0,0,1,0-3Z"/><path id="Прямокутник_17" data-name="Прямокутник 17" class="cls-2" d="M19,2,16.5,5,14,2h5Z"/></svg>
                                </div>
                                <?php _e('Выпадающий список', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>
                        <li class="ui-state-default addfieldinform" data-name="radio">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <svg width="12" height="12">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/wp-content/plugins/magic-form/admin/img/svgs.svg#i-radiobutton"></use>
                                    </svg>
                                </div>
                                <?php _e('RadioButton', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>
                        <li class="ui-state-default addfieldinform" data-name="textarea">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <svg width="21" height="6">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/wp-content/plugins/magic-form/admin/img/svgs.svg#i-textfield"></use>
                                    </svg>
                                </div>
                                <?php _e('Textarea', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>
                        <li class="ui-state-default addfieldinform" data-name="email">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <svg width="16" height="14">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/wp-content/plugins/magic-form/admin/img/svgs.svg#i-email"></use>
                                    </svg>
                                </div>
                                <?php _e('E-mail', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>
                        <li class="ui-state-default addfieldinform" data-name="phone">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <svg width="14" height="14">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/wp-content/plugins/magic-form/admin/img/svgs.svg#i-phone"></use>
                                    </svg>
                                </div>
                                <?php _e('Телефон', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>
                        <li class="ui-state-default addfieldinform" data-name="password">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <svg width="20" height="6">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/wp-content/plugins/magic-form/admin/img/svgs.svg#i-password"></use>
                                    </svg>
                                </div>
                                <?php _e('Пароль', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>
                        <li class="ui-state-default addfieldinform" data-name="url">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <span>://</span>
                                </div>
                                <?php _e('Url', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>
                        <li class="ui-state-default addfieldinform" data-name="date">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <svg width="15" height="16">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/wp-content/plugins/magic-form/admin/img/svgs.svg#i-data"></use>
                                    </svg>
                                </div>
                                <?php _e('Дата', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>
                        <li class="ui-state-default addfieldinform" data-name="file">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <svg width="15" height="20">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/wp-content/plugins/magic-form/admin/img/svgs.svg#i-file"></use>
                                    </svg>
                                </div>
                                <?php _e('Файл', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>
                        <li class="ui-state-default addfieldinform" data-name="quiz">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <svg width="19" height="16">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/wp-content/plugins/magic-form/admin/img/svgs.svg#i-quiz"></use>
                                    </svg>
                                </div>
                                <?php _e('Quiz', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>
                        <li class="ui-state-default addfieldinform" data-name="submit">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <svg width="20" height="10">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/wp-content/plugins/magic-form/admin/img/svgs.svg#i-send"></use>
                                    </svg>
                                </div>
                                <?php _e('Кнопка ОТПРАВИТЬ', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>
                        <li class="ui-state-default addfieldinform" data-name="recaptcha">
                            <div class="inner-addfieldinform">
                                <div class="addfieldinform-ico">
                                    <div class="round-checkbox">
                                        <svg width="16" height="16">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/wp-content/plugins/magic-form/admin/img/svgs.svg#i-checkbox"></use>
                                        </svg>
                                    </div>
                                </div>
                                <?php _e('reCAPTCHA', 'wpcftr') ?><span class="addinform"></span>
                            </div>
                        </li>

                    </ul>
                </div>
                <div class="dragdrop__fields--show">
                    <div class="form-title"><?php _e('Параметры полей формы', 'wpcftr') ?></div>
                    <div class="dragdrop__fields--show__items">
                        <ul id="sortable">
                        <div id="droppable" class="dragdrop__fields__empty ui-droppable">
                            <div class="dragdrop__fields__empty--title"><?php _e('Добавьте  необходимое поле формы', 'wpcftr') ?></div>
                            <div class="dragdrop__fields__empty--icon"></div>
                        </div>

                        </ul>
                        <p class="submit under-the-fields">
                            <button type="submit" name="save" class="save-form" value=""><?php _e('Сохранить форму', 'wpcftr') ?></button>
                        </p>
                    </div>
                </div>
                <div class="dragdrop__fields--results">
                    <div class="form-title"><?php _e('Визуализация формы', 'wpcftr') ?></div>
                    <div class="dragdrop__fields--results__items">
                        <div class="dragdrop__fields__empty--title"><?php _e('Отображение  формы', 'wpcftr') ?></div>
                        <div id="show-form" class="show-form"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    public function tab2( $form ){
        $mail_data = Magic_FormCF::getInstance()->getMailData( $form->id );
        $mes = '
Message Body:
[your-message]
--
';

        $recipient = (isset($mail_data->to_mail))? $mail_data->to_mail : get_option('admin_email');
        $sender = (isset($mail_data->from_mail))? $mail_data->from_mail : get_option('blogname').'@wordpress.ru';
        $title = (isset($mail_data->title))? $mail_data->title : 'your-subject';
        $body = (isset($mail_data->body))? $mail_data->body : $mes;
        $checked = (!empty($mail_data->use_html))? ' checked' : '';
        $checked2 = (!empty($mail_data->is_send))? ' checked' : '';
        $fields = json_decode(stripcslashes ($form->fields));

        echo '<h2>'.__('Отправка письма','wpcftr').'</h2>';
        echo '<p>'.__('При отправке формы, на указаную почту будет отправленно письмо с данными формы','wpcftr');
        echo '<p>'.__('В шаблоне вы можете использовать эти теги для вставки данных','wpcftr');
        foreach($fields as $field){
            echo ' ['.$field->name.'], ';
        }
        echo '</p>';
        ?>
        <table class="form-table send-form__tab--form">
            <tbody>
            <tr>
                <td scope="row">
                    <label class="send-form__tab--form__check">
                        <input type="checkbox" name="form-mail[is_send]" <?= $checked2 ?> value="1"><?php _e('Отправить письмо', 'wpcftr') ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label><?php _e('Кому', 'wpcftr') ?></label>
                </th>
            </tr>
            <tr>
                <td>
                    <input type="text" name="form-mail[recipient]" class="large-text code"  value="<?= $recipient ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label><?php _e('От кого', 'wpcftr') ?></label>
                </th>
            </tr>
            <tr>
                <td>
                    <input type="text" name="form-mail[sender]" class="large-text code" value="<?= $sender ?>">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label><?php _e('Тема', 'wpcftr') ?></label>
                </th>
            </tr>
            <tr>
                <td>
                    <input type="text" name="form-mail[subject]" class="large-text code" value="<?= $title ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label><?php _e('Тело письма', 'wpcftr') ?></label>
                </th>
            </tr>
            <tr>
                <td class="message">
                    <?php
                    $settings = array(
                        'textarea_name'	=>	'form-mail[body]',
                        'editor_class'	=>	'large-text code', // несколько классов через пробел
                        'media_buttons' => false,
                        'textarea_rows' => 18,
                        'editor_height' => 425,
                    );
                    ?>
                    <?php wp_editor( $body, 'wpeditormail', $settings); ?>

                </td>
            </tr>
            <tr>
                <th scope="row">

                </th>
            </tr>
            <tr>
                <td>
                    <label>
                        <input type="checkbox" name="form-mail[use_html]" <?= $checked ?> value="1"><?php _e('Использовать HTML-формат письма', 'wpcftr') ?>
                    </label>
                </td>
            </tr>

            </tbody>
        </table><?php
    }
    public function tab3($form){
        $messages = Magic_FormCF::getInstance()->getMessage();

        $events = [];
        $events['form_sent_ok'] = __('Уведомление успешной отправки формы', 'wpcftr');
        $events['form_sent_er'] = __('Уведомление ошибки отправки формы', 'wpcftr');
        $events['spam'] = __('Отправляемые данные определены как спам. Теги [label] [spam]', 'wpcftr');
        $events['quiz_val_er'] = __('Уведомление неверный ответ на проверочный вопрос', 'wpcftr');
        $events['required'] = __('Поле [label] обязательное', 'wpcftr');
        $events['date_min'] = __('[label] меньше допустимой', 'wpcftr');
        $events['date_max'] = __('[label] больше допустимой', 'wpcftr');
        $events['str_len_max'] = __('[label] длина больше допустимой', 'wpcftr');
        $events['str_len_min'] =__('[label] длина меньше допустимой', 'wpcftr');
        $events['fil_size_er'] = __('Уведомление о превышении размера загружаемого файла', 'wpcftr');
        $events['fil_ext_er'] = __('Уведомление о неподходящем расширении файла', 'wpcftr');
        $events['phone_val_er'] = __('[label] ввод запрещенных символов', 'wpcftr');
        $events['sendmailerror'] = __('Ошибка отправки письма', 'wpcftr');
        $events['autosendmailerror'] = __('Ошибка автоответчика', 'wpcftr');
        $events['recaptchaerror'] = __('Проверка reCAPTCHA не пройдена', 'wpcftr');
        echo '<h2>'.__('Сообщения об ошибках','wpcftr').'</h2>';
        echo '<div class="description">'.__('Здесь вы можете редактировать сообщения, используемые в различных ситуациях.', 'wpcftr').'</div>';
        echo '<fieldset>';
        foreach($messages as $key=>$val){?>
            <label><?= $events[$val->name_event]?><br>
                <input type="text" name="form-message[<?= $val->name_event ?>]" class="large-text code" size="70" value="<?= $val->value_data ?>">
            </label>
        <?php
        }
        echo '</fieldset>';
    }
    public function tab4($form){?>
        <h2><?php _e('Настройки формы', 'wpcftr') ?></h2>
        <div class="description"><?php _e('Здесь вы можете задать поведение формы и ее внешний вид.', 'wpcftr') ?></div>

        <fieldset>
        <?php
        $new_page = get_option( 'magic-form-seting-new-page-'.$form->id );
        $checked_new_page = '';
        if($new_page ){
            $checked_new_page = 'checked';
        }
        ?>
        <legend><?php _e('Url, на который будет перенаправлен пользователь после отправки формы. Если оставить пустым, пользователь останется на той же странице', 'wpcftr') ?></legend>
        <input type="checkbox" name="form-setting[new_page]" class="form-settings-checkbox" <?= $checked_new_page ?> value="1"> <?php _e('Открыть Url в новой вкладке. Если оставить пустым, Url откроется в той же вкладке', 'wpcftr') ?>
        <br>
        <input type="text" name="form-setting[redirect]"class="large-text form-settings" value="<?= stripcslashes( get_option( 'magic-form-seting-redirect-'.$form->id )); ?>">


        <?php
        $popup_id = get_option( 'magic-form-seting-popup');
        $checked = '';
        $use_popup = __('Использовать форму во всплывающем окне', 'wpcftr');
        if($popup_id && $popup_id !=$form->id ){
            $use_popup= __('Во всплывающем окне используется форма', 'wpcftr').' ID ='.$popup_id.'. '.__('Чтобы использовать эту форму, необходимо отметить чекбокс', 'wpcftr');

        } else if($popup_id ==$form->id ){
            $checked = 'checked';
        }


        ?>
        <legend><?php _e('Всплывающее окно', 'wpcftr') ?></legend>

        <input type="checkbox" name="form-setting[popup]" class="form-settings-checkbox" <?= $checked ?> value="1"><?= $use_popup  ?>

        <legend><?php _e('Url на который будет отправлена форма. Если оставить пустым, то данные формы придут только на почту.', 'wpcftr') ?></legend>
        <input type="text" name="form-setting[action]"class="large-text form-settings" value="<?= stripcslashes( get_option( 'magic-form-seting-action-'.$form->id )); ?>">

        <?php $method = get_option( 'magic-form-seting-method-'.$form->id ) ?>
        <legend><?php _e('Метод отправки.', 'wpcftr') ?></legend>
        <select name="form-setting[method]">
            <option <?= ($method == 'post')? ' selected' : '' ?> value="post">post</option>
        <option <?= ($method == 'get')? ' selected' : '' ?> value="get">get</option>
        </select>

        <legend><?php _e('Id формы', 'wpcftr') ?></legend>
        <input type="text" name="form-setting[id]"class="large-text form-settings" value="<?= stripcslashes( get_option( 'magic-form-seting-id-'.$form->id )); ?>">

        <legend><?php _e('Имя Формы', 'wpcftr') ?></legend>
        <input type="text" name="form-setting[name]"class="large-text form-settings" value="<?= stripcslashes( get_option( 'magic-form-seting-name-'.$form->id )); ?>">

        <legend><?php _e('Добавьте class к форме', 'wpcftr') ?></legend>
        <input type="text" name="form-setting[class]"class="large-text form-settings" value="<?= stripcslashes( get_option( 'magic-form-seting-class-'.$form->id )); ?>">

        <legend><?php _e('Добавьте style к форме', 'wpcftr') ?></legend>
        <textarea name="form-setting[style]" cols="100" rows="8" class="large-text form-settings"><?= stripcslashes( get_option( 'magic-form-seting-style-'.$form->id )); ?></textarea>

        <legend><?php _e('Добавьте js строки на Ваш сайт', 'wpcftr') ?></legend>
        <textarea name="form-setting[js]" cols="100" rows="8" class="large-text form-settings"><?= stripcslashes( get_option( 'magic-form-seting-js-'.$form->id )); ?></textarea>

        <legend><?php _e('Добавьте js строки на Ваш сайт, которые будут выполнены после успешной отправки формы', 'wpcftr') ?></legend>
        <textarea name="form-setting[js_after_success]" cols="100" rows="8" class="large-text form-settings"><?= stripcslashes( get_option( 'magic-form-seting-js-after-success'.$form->id )); ?></textarea>

        <legend><?php _e('Добавьте css строки на Ваш сайт', 'wpcftr') ?></legend>
        <textarea name="form-setting[css]" cols="100" rows="8" class="large-text form-settings"><?= stripcslashes( get_option( 'magic-form-seting-css-'.$form->id )); ?></textarea>
        </fieldset><?php
    }
    public function tab5($form){?>
        <h2>Form logs</h2>
        <table class="form-logs" cellspacing="0" cellpadding="0">
            <tr>

                <th><?php _e('№', 'wpcftr') ?></th>
                <th><?php _e('IP адрес отправителя', 'wpcftr') ?></th>
                <th><?php _e('Дата отправки', 'wpcftr') ?></th>
                <th><?php _e('e-mail отправителя', 'wpcftr') ?></th>
                <th><?php _e('Тема письма', 'wpcftr') ?></th>
                <th><?php _e('Подробней', 'wpcftr') ?></th>
            </tr>
            <tbody id="body-log-form">
            <?php $logs = Magic_FormsLogs::getInstance()->getLogs($form->id, 0, $limit=10);
                echo $logs['html'];
            ?>
            </tbody>

        </table><?php

        if($logs['more']){
            echo '<p><input type="button" class="button button-primary show-more-log" data-form_id="'.$form->id.'" data-offset="'.$limit.'" data-limit="'.$limit.'" data-action="magic_form_log_show_more" value="'.__('Показать еще', 'wpcftr').'"></p>';
        }

    }
    public function tab6($form){
        $mail_data = Magic_FormCF::getInstance()->getMailData( $form->id, 1 );
        $mes = '
Message Body:
[your-message]
--
';

        $sender = (isset($mail_data->from_mail))? $mail_data->from_mail : get_option('blogname').'@wordpress.ru';
        $title = (isset($mail_data->title))? $mail_data->title : 'your-subject';
        $body = (isset($mail_data->body))? $mail_data->body : $mes;
        $checked = (!empty($mail_data->use_html))? ' checked' : '';
        $checked2 = (!empty($mail_data->is_send))? ' checked' : '';
        $fields = json_decode(stripcslashes ($form->fields));

        echo '<h2>'.__('Автоответчик','wpcftr').'</h2>';
        echo '<p>'.__('Если в форме будет поле Email то это письмо будет отправлено на указанный адрес','wpcftr');
        echo '<p>'.__('В шаблоне вы можете использовать эти теги для вставки данных','wpcftr');
        foreach($fields as $field){
            echo ' ['.$field->name.'], ';
        }
        echo '</p>';
        ?>
        <table class="form-table send-form__tab--form">
        <tbody>
        <tr>
            <td scope="row">
                <label>
                    <input type="checkbox" name="form-mail-auto[is_send]" <?= $checked2 ?> value="1"><?php _e('Отправить письмо', 'wpcftr') ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label><?php _e('От кого', 'wpcftr') ?></label>
            </th>
        </tr>
        <tr>
            <td>
                <input type="text" name="form-mail-auto[sender]" class="large-text code" value="<?= $sender ?>">
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label><?php _e('Тема', 'wpcftr') ?></label>
            </th>
        </tr>
        <tr>
            <td>
                <input type="text" name="form-mail-auto[subject]" class="large-text code" value="<?= $title ?>">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label><?php _e('Тело письма', 'wpcftr') ?></label>
            </th>
        </tr>
        <tr>
            <td class="message">
                <?php
                $settings = array(
                    'textarea_name'	=>	'form-mail-auto[body]',
                    'editor_class'	=>	'large-text code', // несколько классов через пробел
                    'media_buttons' => false,
                    'textarea_rows' => 18,
                    'editor_height' => 425,
                );
                ?>
                <?php wp_editor( $body, 'wpeditorauto', $settings); ?>
            </td>
        </tr>
        <tr>
            <td>
                <label>
                    <input type="checkbox" name="form-mail-auto[use_html]" <?= $checked ?> value="1"><?php _e('Использовать HTML-формат письма', 'wpcftr') ?>
                </label>
            </td>
        </tr>

        </tbody>
        </table><?php
    }
    public function tab7($form){?>
        <h2><?php _e('Антиспам', 'wpcftr') ?></h2>
        <div class="description"><?php _e('Добавьте слова с новой строки. При нахождении их в форме. Данные будут сохранены в лог, но не отправлены в форме и на почту', 'wpcftr') ?> </div>
        <fieldset>
            <legend><?php _e('Фразы', 'wpcftr') ?></legend>
            <textarea name="form-setting[antispam]" cols="100" rows="8" class="large-text form-settings"><?= stripcslashes( get_option( 'magic-form-seting-antispam')); ?></textarea>
        </fieldset>
        <?php
    }

}

