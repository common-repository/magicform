<?php

## Общие CSS стили для админ-панели.
add_action( 'admin_enqueue_scripts', function(){
    wp_deregister_script( 'magic_form_recaptcha' );
    wp_register_script( 'magic_form_recaptcha', 'https://www.google.com/recaptcha/api.js?onload=magicFormRecaptchaCallback&render=explicit');
    wp_enqueue_script( 'magic_form_recaptcha' );

    wp_enqueue_script("jquery-ui-core", array('jquery'));
    wp_enqueue_script("jquery-ui-sortable", array('jquery','jquery-ui-core'));
    wp_enqueue_script("jquery-ui-draggable", array('jquery','jquery-ui-core'));
    wp_enqueue_script("jquery-ui-droppable", array('jquery','jquery-ui-core'));


    wp_enqueue_style( 'magic_form_admin_css', plugins_url( '/css/style.css', __FILE__ ));
    wp_enqueue_style( 'magic_form_admin2_css', plugins_url( '/css/tmp.css', __FILE__ ));
    wp_enqueue_script( 'magic_form_backend', plugins_url( '/js/backend.js', __FILE__ ), array('jquery'), '1.0', true );

    wp_localize_script( 'magic_form_backend', 'b_l10n_obj',
        array(
            'txt_field' => __('Текстовое поле', 'wpcftr'),
            'sel_checkbox' => __('CheckBox', 'wpcftr'),
            'sel_list' => __('Выпадающий список', 'wpcftr'),
            'sel_radio' => __('RadioButton', 'wpcftr'),
            'big_txtar' => __('Textarea', 'wpcftr'),
            'email' => __('E-mail', 'wpcftr'),
            'phone' => __('Телефон', 'wpcftr'),
            'password' => __('Пароль', 'wpcftr'),
            'url_field' => __('Url', 'wpcftr'),
            'date_field' => __('Дата', 'wpcftr'),
            'send_but' => __('Кнопка ОТПРАВИТЬ', 'wpcftr'),
            'file_field' => __('Файл', 'wpcftr'),
            'quiz' => __('Quiz', 'wpcftr'),
            'fillreqfields' => __('Заполните обязательные поля формы', 'wpcftr'),
            'notprocyet' => __('нет еще обработки', 'wpcftr'),
            'fieldlable' => __('Подпись при отображении формы.', 'wpcftr'),
            'fieldplaceholder' => __('Выводит текст внутри поля формы, который исчезает при вводе текста в этом поле.', 'wpcftr'),
            'fieldregexp' => __('Из формы будет удален текст, указанный здесь.', 'wpcftr'),
            'fieldval' => __('Внутри поля формы будет размещен введенный здесь текст, его можно будет стереть и вводить свои символы.', 'wpcftr'),
            'buttonsign' => __('Подпись для кнопки.', 'wpcftr'),
            'selval' => __('Значение, выбранное по умолчанию.', 'wpcftr'),
            'qapair' => __('Пара вопрос|ответ.', 'wpcftr'),
            'optionslist' => __('Перечислите свойства, новое свойство с новой строки.', 'wpcftr'),
            'mindatefield' => __('Минимально допустимая дата. Если дата не указана, то ограничений нет.', 'wpcftr'),
            'maxdatefield' => __('Максимально допустимая дата. Если дата не указана, то ограничений нет.', 'wpcftr'),
            'themefont' => __('Использовать размер шрифта темы', 'wpcftr'),
            'filextlist' => __('Перечислите допустимые расширения файлов через запятую без пробелов. Если пустое поле, то ограничений нет.', 'wpcftr'),
            'setmaxfilsize' => __('Укажите максимальный размер загружаемого файла в Мб. Если указан 0 или пустое поле, то размер файла не ограничен, и это может привести к переполнению хранилища файлами большого размера.', 'wpcftr'),
            'minlengthfield' => __('Минимально допустимая длина. Если 0 или пустое поле, то ограничений нет.', 'wpcftr'),
            'maxlengthfield' => __('Максимально допустимая длина. Если 0 или пустое поле, то ограничений нет.', 'wpcftr'),
            'onemoreprop' => __('еще одно свойство поля', 'wpcftr'),
            'notnough' => __('Маловато', 'wpcftr'),
            'idntknow' => __('Я таких значений не знаю', 'wpcftr'),
            'extallowed' => __('Принимаются расширения:', 'wpcftr'),
            'sizeallowed' => __('Максимальный размер:', 'wpcftr'),
            'uneedtosaveform' => __('Для применения изменений необходимо сохранить форму', 'wpcftr'),
            'maxdatemoremindate' => __('Значение максимальной даты должно быть всегда больше значения минимальной даты', 'wpcftr'),
            'maxlenmoreminlen' => __('Значение максимальной длины должно быть всегда больше значения минимальной длины', 'wpcftr'),
            'minlennotmoremaxlen' => __('Значение минимальной длины не может быть больше значения максимальной длины', 'wpcftr'),
            'todefault' => __('По умолчанию', 'wpcftr'),
            'namefield' => __('Название поля', 'wpcftr'),
            'sizelesszero' => __('Значение размера не может быть отрицательным', 'wpcftr'),
            'phonepattern' => __('Устанавливает критерий/шаблон ввода.', 'wpcftr'),
            'nextday' => __('Выбрав это поле минимальная дата всегда будет завтрашней.', 'wpcftr'),
            'copyshortcode' => __('Шорткод скопирован в буфер обмена.', 'wpcftr'),
            'showadvopt' => __('Показать дополнительные параметры', 'wpcftr'),
            'hideadvopt' => __('Скрыть дополнительные параметры', 'wpcftr'),
            'questionfontsize' => __('Размер шрифта задаваемого вопроса', 'wpcftr'),
            'labelfontsize' => __('Размер шрифта названия поля', 'wpcftr'),
            'questionfontcolor' => __('Цвет шрифта задаваемого вопроса', 'wpcftr'),
            'labelfontcolor' => __('Цвет шрифта названия поля', 'wpcftr'),
            'listsfontsize' => __('Размер шрифта свойств поля', 'wpcftr'),
            'buttonfontsize' => __('Размер шрифта подписи кнопки', 'wpcftr'),
            'inputfontsize' => __('Размер шрифта внутри поля формы', 'wpcftr'),
            'listsfontcolor' => __('Цвет шрифта свойств поля', 'wpcftr'),
            'buttonfontcolor' => __('Цвет шрифта подписи кнопки', 'wpcftr'),
            'inputfontcolor' => __('Цвет шрифта внутри поля формы', 'wpcftr'),
            'minlength' => __('Мин. длина', 'wpcftr'),
            'maxlength' => __('Макс. длина', 'wpcftr'),
            'mindate' => __('Мин. дата', 'wpcftr'),
            'maxdate' => __('Макс. дата', 'wpcftr'),
            'lists' => __('Список свойств', 'wpcftr'),
            'fieldname' => __('Под этим именем данные отправляются на сервер. Используется в качестве тегов при создании шаблонов писем.', 'wpcftr'),
            'hasspaces' => __('не может содержать пробелы', 'wpcftr'),
            'lesszero' => __('не может быть отрицательным', 'wpcftr'),
            'filesize' => __('Размер', 'wpcftr'),
            'fileextensions' => __('Расширения', 'wpcftr'),
            'checkboxvalues' => __('Свойства, перечисленные в этом поле, будут в форме отображаться выбранными, новое свойство с новой строки.', 'wpcftr'),
            'radiovalue' => __('Свойство, указанное в этом поле, будет в форме отображаться выбранным. Можно выбрать только одно свойство.', 'wpcftr'),
            'formmin2fields' => __('Форма должна содержать минимум 2 поля', 'wpcftr'),
            'field_width' => __('Ширина поля', 'wpcftr'),
            'field_align' => __('Выравнивание', 'wpcftr'),
            'align_left' => __('Выравнивание по левому краю', 'wpcftr'),
            'align_center' => __('Выравнивание по центру', 'wpcftr'),
            'align_right' => __('Выравнивание по правому краю', 'wpcftr'),
            'recaptcha' => __('reCAPTCHA', 'wpcftr'),
            'recaptcha_key' => __('Ключ', 'wpcftr'),
            'recaptcha_secretkey' => __('Секретный ключ', 'wpcftr'),
            'link_get_keys' => __('Ключи получить можно здесь', 'wpcftr'),
            'write_email' => __('Введите e-mail', 'wpcftr'),
            'areushuredelete' => __('Вы действительно хотите удалить все записи с e-mail', 'wpcftr'),
            'totaldeleted' => __('Количество удаленных записей:', 'wpcftr'),
            'modal_your_shortcode' => __('Ваш шорткод', 'wpcftr'),
            'modal_you_should_put_it' => __('Вам необходимо поместить его в Текстовое Поле', 'wpcftr'),
            'modal_and_save_it' => __('И сохранить', 'wpcftr'),
            'modal_img_path' => plugins_url( '/img/for-modal.png', __FILE__ ),
            'locale' => get_locale()
        )
        );
}, 99 );


add_action('admin_menu', function(){
    add_menu_page( __('Настройки формы', 'wpcftr'), __('Magic Forms', 'wpcftr'), 'manage_options', 'magic-form', 'add_wpcftr_page', '', 21 );
} );


// функция отвечает за вывод страницы настроек
// подробнее смотрите API Настроек: http://wp-kama.ru/id_3773/api-optsiy-nastroek.html
function add_wpcftr_page(){
    $admin_funk = Magic_Form_AdminFunc::getInstance();
    if(isset($_POST['delete'])){
        Magic_FormCF::getInstance()->deleteForm( intval($_POST['id_form']));
        echo '<div id="message" class="updated notice notice-success is-dismissible"><p>'.__('Форма удалена', 'wpcftr').'</p></div>';
    }

    if(isset($_GET['id_form']) && !empty($_POST)){
        Magic_FormCF::getInstance()->saveForm( intval($_GET['id_form']));
        $shortcode_for_popup = Magic_FormCF::getInstance()->getForm( intval($_GET['id_form']))->shortcode;
        ?>
        <script>
            window.shortcode_for_popup = "<?= htmlspecialchars($shortcode_for_popup) ?>";
        </script>
        <?php


        echo '<div id="message" data-shortcode="'. $shortcode_for_popup.'" class="updated notice notice-success is-dismissible"><p>'.__('Данные сохранены', 'wpcftr').'</p></div>';
        echo '<script> setTimeout(function(){
                            magicFormShowModalAfterSaveForm();
                       }, 1000);
              </script>';
    }
    if(isset($_POST['save_options']) ){
        Magic_Form_Services::getInstance()->saveOptions(Magic_FormCF::getInstance()->sanitizeArrayData($_POST));
    }
    if(!get_option('magic-form-seting-activate') || isset($_GET['options']) ){
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e('Опции', 'wpcftr') ?></h1>

            <form action="/wp-admin/admin.php?page=magic-form" method="POST" id="save_options">
                <table class="form-table ">
                    <tbody>
                    <tr>
                        <td>
                            <p> <?php _e('Соглашаясь с этим пунктом ваш сайт будет отправлять название домена на сервер разработчика, чтобы последний мог проверять доступность сайта. Функция mail () будет отправлять тестовое письмо (пустое) на сервер разработчика для проверки работоспособности последней. Никакие данные в виде email адресов, контактов, телефонов и т.п. не будут отправляться.', 'wpcftr') ?></p>
                        </td>
                    </tr>
                        <tr>
                            <td>
                                <label>
                                    <input id="user_agreement" type="checkbox" name="user_agreement"
                                           value="1" <?= !get_option('magic-form-seting-activate')|| get_option('magic-form-seting-user-agreement')? 'checked':'' ?> ><?php _e('Я согласен', 'wpcftr') ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>
                                    <input id="save_options" type="submit" name="save_options" class="button "
                                           value="<?php _e('Сохранить', 'wpcftr') ?>">
                                </label>
                            </td>
                    </tbody>
                </table>
            </form>
        </div>
        <?php
    } elseif(isset($_GET['id_form'])){
            $form = Magic_FormCF::getInstance()->getForm( intval($_GET['id_form']));
            ?>

            <script>
                window.setingField = "<?= $form->fields ?>";
            </script>

            <div class="wrap">
                <h2 id="pagetitle"><?php echo get_admin_page_title() ?></h2>

                <?php
                // settings_errors() не срабатывает автоматом на страницах отличных от опций
                if( get_current_screen()->parent_base !== 'options-general' )
                    settings_errors(__('название_опции', 'wpcftr'));
                ?>


                <div id="modal_form"><!-- Сaмo oкнo -->
                    <span class="modal_close"><svg viewBox="0 0 475.2 475.2" width='15px' height='15px'>
		<path d="M405.6,69.6C360.7,24.7,301.1,0,237.6,0s-123.1,24.7-168,69.6S0,174.1,0,237.6s24.7,123.1,69.6,168s104.5,69.6,168,69.6
			s123.1-24.7,168-69.6s69.6-104.5,69.6-168S450.5,114.5,405.6,69.6z M386.5,386.5c-39.8,39.8-92.7,61.7-148.9,61.7
			s-109.1-21.9-148.9-61.7c-82.1-82.1-82.1-215.7,0-297.8C128.5,48.9,181.4,27,237.6,27s109.1,21.9,148.9,61.7
			C468.6,170.8,468.6,304.4,386.5,386.5z"/>
		<path d="M342.3,132.9c-5.3-5.3-13.8-5.3-19.1,0l-85.6,85.6L152,132.9c-5.3-5.3-13.8-5.3-19.1,0c-5.3,5.3-5.3,13.8,0,19.1
			l85.6,85.6l-85.6,85.6c-5.3,5.3-5.3,13.8,0,19.1c2.6,2.6,6.1,4,9.5,4s6.9-1.3,9.5-4l85.6-85.6l85.6,85.6c2.6,2.6,6.1,4,9.5,4
			c3.5,0,6.9-1.3,9.5-4c5.3-5.3,5.3-13.8,0-19.1l-85.4-85.6l85.6-85.6C347.6,146.7,347.6,138.2,342.3,132.9z"/>
</svg></span> <!-- Кнoпкa зaкрыть -->
                    <form id="formsinglefield" action="#" method="post" onsubmit="return false;">
                        <div id="contentmodal"></div>
                        <div id="save-button">
                            <input type="submit" value="<?php _e('Save', 'wpcftr') ?>" class="not-adm-msgs modal_save">
                            <input type="button" class="not-adm-msgs modal_cancel" value="<?php _e('Cancel', 'wpcftr') ?>">
                            <input type="button" class="not-adm-msgs modal_show_more-opt" value="<?php _e('Показать дополнительные параметры', 'wpcftr') ?>">
                        </div>
                    </form>
                </div>
                <div id="overlay"></div><!-- Пoдлoжкa -->




                <form action="/wp-admin/admin.php?page=magic-form&id_form=<?= $_GET['id_form'] ?>" method="POST" id="save-form">

                    <div id="titlediv">
                        <div id="titlewrap">
                            <label class="screen-reader-text" id="title-prompt-text" for="title"><?php _e('Введите заголовок', 'wpcftr') ?></label>
                            <input type="text" name="form_name" value="<?= $form->title ?>" placeholder="<?php _e('название формы', 'wpcftr') ?>" size="30" id="title" spellcheck="true" autocomplete="off">
                        </div>
                        <input type="hidden" name="form-value" id="form-value" value="">
                        <input type="hidden" name="shortcode" value="<?= $form->shortcode ?>">
                        <p class="submit">
                            <button type="submit" name="save" class="save-form" value=""><?php _e('Сохранить форму', 'wpcftr') ?></button>
                        </p>
                        <div class="tabs_box">
                            <ul class="tabs_menu">
                                <li><a href="#tab1" class="active"><?php _e('Конструктор', 'wpcftr') ?></a></li>
                                <?php
                                if($_GET['id_form']){ ?>
                                    <li><a href="#tab2"><?php _e('Отправка письма', 'wpcftr') ?></a></li>
                                    <li><a href="#tab3"><?php _e('Сообщения об ошибках', 'wpcftr') ?></a></li>
                                    <li><a href="#tab4"><?php _e('Настройки формы', 'wpcftr') ?></a></li>
                                    <li><a href="#tab5"><?php _e('Form logs', 'wpcftr') ?></a></li>
                                    <li><a href="#tab6"><?php _e('Автоответчик', 'wpcftr') ?></a></li>
                                    <li><a href="#tab7"><?php _e('Антиспам', 'wpcftr') ?></a></li>
<!--                                    <li><a href="#tab8">--><?php //_e('Conditional fields', 'wpcftr') ?><!--</a></li>-->
                                    <?php
                                } ?>

                            </ul>
                            <div class="tab" id="tab1">
                                <?php $admin_funk->tab1() ?>
                            </div>
                            <div class="tab send-form__tab" id="tab2">
                                <?php $admin_funk->tab2($form) ?>
                            </div>
                            <div class="tab send-form__error" id="tab3">
                                <?php $admin_funk->tab3($form) ?>
                            </div>
                            <div class="tab send-form__error" id="tab4">
                                <?php $admin_funk->tab4($form) ?>
                            </div>
                            <div class="tab send-form__tab" id="tab5">
                                <?php $admin_funk->tab5($form) ?>
                            </div>
                            <div class="tab send-form__tab" id="tab6">
                                <?php $admin_funk->tab6($form) ?>
                            </div>
                            <div class="tab send-form__error" id="tab7">
                                <?php $admin_funk->tab7($form) ?>
                            </div>
                        </div>


                    </div>
                </form>
            </div>
            <?php
        }
        elseif(isset($_GET['find_logs_email'])){?>
        <h2 id="log_table_h2"><?php _e('Поиск по e-mail', 'wpcftr') ?></h2>
            <input id="logs_email" type="email" name="logs_email" placeholder="<?php _e('Введите e-mail', 'wpcftr')?>">
            <input type="button" class="button button-primary find-email-log" value="<?php _e('Поиск', 'wpcftr')?>">
            <input id="export-email-log" type="button" class="button button-primary export-email-log" style="display: none" value="<?php _e('Экспортировать в CSV', 'wpcftr')?>">
            <input id="delete-email-log" type="button" class="button button-primary delete-email-log" style="display: none" value="<?php _e('Удалить', 'wpcftr')?>">
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
        </tbody>

    </table>
            <p><input type="button" class="button button-primary show-more-log" data-offset="" data-limit="" data-action="magic_form_find_email_log_show_more" style="display: none" value="<?php _e('Показать еще', 'wpcftr') ?>"></p>
            <div id="modal_form"><!-- Сaмo oкнo -->
                <span class="modal_close"><svg viewBox="0 0 475.2 475.2" width='15px' height='15px'>
		<path d="M405.6,69.6C360.7,24.7,301.1,0,237.6,0s-123.1,24.7-168,69.6S0,174.1,0,237.6s24.7,123.1,69.6,168s104.5,69.6,168,69.6
			s123.1-24.7,168-69.6s69.6-104.5,69.6-168S450.5,114.5,405.6,69.6z M386.5,386.5c-39.8,39.8-92.7,61.7-148.9,61.7
			s-109.1-21.9-148.9-61.7c-82.1-82.1-82.1-215.7,0-297.8C128.5,48.9,181.4,27,237.6,27s109.1,21.9,148.9,61.7
			C468.6,170.8,468.6,304.4,386.5,386.5z"/>
		<path d="M342.3,132.9c-5.3-5.3-13.8-5.3-19.1,0l-85.6,85.6L152,132.9c-5.3-5.3-13.8-5.3-19.1,0c-5.3,5.3-5.3,13.8,0,19.1
			l85.6,85.6l-85.6,85.6c-5.3,5.3-5.3,13.8,0,19.1c2.6,2.6,6.1,4,9.5,4s6.9-1.3,9.5-4l85.6-85.6l85.6,85.6c2.6,2.6,6.1,4,9.5,4
			c3.5,0,6.9-1.3,9.5-4c5.3-5.3,5.3-13.8,0-19.1l-85.4-85.6l85.6-85.6C347.6,146.7,347.6,138.2,342.3,132.9z"/>
</svg></span> <!-- Кнoпкa зaкрыть -->
                <div id="contentmodal"></div>
            </div>
            <div id="overlay"></div><!-- Пoдлoжкa -->
            <?php

        }
        else{
            ?>
            <div class="wrap">
                <h1 class="wp-heading-inline"><?php _e('Существующие формы', 'wpcftr') ?></h1>
                <a href="/wp-admin/admin.php?page=magic-form&id_form=0" class="page-title-action"><?php _e('Добавить новую', 'wpcftr') ?></a>

                <table class="wp-list-table widefat fixed striped pages">
                    <thead>
                    <tr>
                        <th scope="col"  class="manage-column"><?php _e('Заголовок', 'wpcftr') ?></th>
                        <th scope="col"  class="manage-column"><?php _e('Шорткод', 'wpcftr') ?></th>
                        <th scope="col"  class="manage-column"><?php _e('Автор', 'wpcftr') ?></th>
                        <th scope="col"  class="manage-column"><?php _e('Дата обновления', 'wpcftr') ?></th>
                        <th scope="col"  class="manage-column"><?php _e('Удалить', 'wpcftr') ?></th>
                    </tr>
                    </thead>

                    <tbody id="the-list"><?php
                    $forms = Magic_FormCF::getInstance()->getForms();
                    $datetimeformat = Magic_FormsLogs::getInstance()->getDateTimeFormat();
                    foreach($forms as $form){
                        $user       = get_userdata($form->user_id); ?>

                        <tr>
                        <td scope="col" class="manage-column">
                            <div><?php echo ( $form->title )? $form->title : __('Без названия', 'wpcftr')  ?></div>
                            <div><span class="edit"><a href="/wp-admin/admin.php?page=magic-form&id_form=<?= $form->id ?>"><?php _e('Изменить', 'wpcftr') ?></a></span></div>
                        </td>
                        <td scope="col"  class="manage-column"><code>[<?= $form->shortcode?>]</code><img title="<?php _e('копировать шорткод', 'wpcftr') ?>" class="copy-shortcode" src="<?php echo plugins_url( '/../img/copy.png', __FILE__ ) ?>"></td>
                        <td scope="col"  class="manage-column"><?= $user->user_login ?></td>
                        <td scope="col"  class="manage-column"><?= date($datetimeformat, strtotime($form->update_date)) ?></td>
                        <td scope="col"  class="manage-column">
                            <form action="/wp-admin/admin.php?page=magic-form" method="POST">
                                <input type="submit" name="delete" class="button " value="<?php _e('Удалить', 'wpcftr') ?>" onclick="return confirm('Вы действительно хотите удалить форму?');">
                                <input type="hidden" name="id_form" value="<?= $form->id ?>">
                            </form>
                        </td>
                        </tr><?php
                    } ?>

                    </tbody>
                </table>
                <div><span><a href="/wp-admin/admin.php?page=magic-form&options"><?php _e('Опции', 'wpcftr') ?></a>, <a href="/wp-admin/admin.php?page=magic-form&find_logs_email"><?php _e('Поиск по e-mail, экспорт и удаление данных', 'wpcftr') ?></a></span></div>
            </div>
            <?php
        }

}

add_action('admin_print_footer_scripts', 'magic_form_backend_ajax_javascript', 99);
function magic_form_backend_ajax_javascript() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            jQuery('.find-email-log').click(function(e) {
                var offset = 0;
                var limit = 10;
                jQuery('#export-email-log').hide();
                jQuery('#delete-email-log').hide();
                jQuery('.show-more-log').hide();
                jQuery("#body-log-form").empty();
                if(!jQuery("#logs_email").val().length>0){
                    alert (b_l10n_obj.write_email);
                    return;
                }
                jQuery('.show-more-log').attr('data-email', jQuery("#logs_email").val());
                var email = jQuery('.show-more-log').attr('data-email');
                var data = {
                    limit: limit,
                    offset: offset,
                    email: email,
                    action: 'magic_form_find_email_log'
                };

                jQuery.post(ajaxurl, data, function(response) {
                    if(response.html.length >0 ){
                        $('#export-email-log').show();
                        $('#delete-email-log').show();
                    }
                    $('#body-log-form').append(response.html);
                    var new_offset = parseInt(limit) + parseInt(offset);
                    $('.show-more-log').attr('data-offset', new_offset);
                    $('.show-more-log').attr('data-limit', limit);

                    $("#logs_email").val('');
                    if(response.more){
                        $('.show-more-log').show();
                    }
                });
            });

            jQuery('#body-log-form').on('click', '.show-detail-log', function(e) {
                jQuery('#save-button').hide();
                var id = jQuery(this).attr('data-id');
                var data = {
                    id: id,
                    action: 'magic_form_show_detail_log'
                };

                jQuery.ajax({
                    url: ajaxurl,
                    type: "post",
                    data: data,
                    beforeSend: function (data) {

                    },
                    success: function(data){
                        // console.log(data);
                        jQuery('#contentmodal').html(data);
                        magicFormShowModal();
                    }
                });
            });

            $('.show-more-log').click(function(e) {
                var offset = $(this).attr('data-offset');
                var limit = $(this).attr('data-limit');
                var form_id = $(this).attr('data-form_id');
                var email = $(this).attr('data-email');
                var action = $(this).attr('data-action');
                var button = this;

                $.ajax({
                    url: ajaxurl,
                    type: "post",
                    data: {
                        limit: limit,
                        offset: offset,
                        form_id: form_id,
                        email: email,
                        action: action
                    },
                    beforeSend: function (data) {
                        $(button).hide();
                    },
                    success: function(data){
                        $('#body-log-form').append(data.html);
                        var new_offset = parseInt(limit) + parseInt(offset);
                        $(button).attr('data-offset', new_offset);
                        if(data.more){
                            $(button).show();
                        }
                    }
                });
            });

            $('.export-email-log').click(function(e) {
                var email = $('.show-more-log').attr('data-email');
                var button = this;

                $.ajax({
                    url: ajaxurl,
                    type: "post",
                    data: {
                        email: email,
                        action: 'magic_form_export_email_log'
                    },
                    success: function(data){
                        location = data;
                    }
                });
            });

            $('.delete-email-log').click(function(e) {
                var email = $('.show-more-log').attr('data-email');
                if (!confirm(b_l10n_obj.areushuredelete+' '+email+'?')){
                    return;
                }

                $.ajax({
                    url: ajaxurl,
                    type: "post",
                    data: {
                        email: email,
                        action: 'magic_form_delete_email_log'
                    },

                    success: function(data){
                        // console.log(data);
                        if(data>0){
                            alert (b_l10n_obj.totaldeleted+' '+data);
                        }
                        $('#export-email-log').hide();
                        $('#delete-email-log').hide();
                        $('.show-more-log').hide();
                        $("#body-log-form").empty();

                    }
                });
            });
        });
    </script>
    <?php
}

function magicFormJSONExit( $JSON = null ) {

    if( MAGIC_FORM_ISAJAXREQUEST ) {
        header( 'Content-Type: application/json; charset=UTF-8' );
        echo json_encode( $JSON );
    } else {
        header( 'Content-Type: text/plain; charset=UTF-8' );
        print_r( $JSON );
    }

    exit;
}

add_action( 'wp_ajax_magic_form_find_email_log', 'magic_form_find_email_log' );

function magic_form_find_email_log() {
    $JSON = Magic_FormsLogs::getInstance()->getLogsEmail(sanitize_email($_POST['email']), 0, 10);
    magicFormJSONExit( $JSON);
    wp_die();
}

add_action( 'wp_ajax_magic_form_show_detail_log', 'magic_form_show_detail_log' );

function magic_form_show_detail_log() {
    $JSON = Magic_FormsLogs::getInstance()->getLogDetail(intval($_POST['id']));
    magicFormJSONExit( $JSON);
    wp_die();
}

add_action( 'wp_ajax_magic_form_find_email_log_show_more', 'magic_form_find_email_log_show_more' );

function magic_form_find_email_log_show_more() {
    $JSON = Magic_FormsLogs::getInstance()->getLogsEmail(sanitize_email($_POST['email']), intval($_POST['offset']), intval($_POST['limit']));
    magicFormJSONExit( $JSON);
    wp_die();
}

add_action( 'wp_ajax_magic_form_log_show_more', 'magic_form_log_show_more' );

function magic_form_log_show_more() {
    $JSON = Magic_FormsLogs::getInstance()->getLogs(intval($_POST['form_id']), intval($_POST['offset']), intval($_POST['limit']));
    magicFormJSONExit( $JSON);
    wp_die();
}

add_action( 'wp_ajax_magic_form_export_email_log', 'magic_form_export_email_log' );

function magic_form_export_email_log() {
    $JSON = Magic_FormsLogs::getInstance()->exportLogs( sanitize_email($_POST['email']));
    magicFormJSONExit( $JSON);
    wp_die();
}

add_action( 'wp_ajax_magic_form_delete_email_log', 'magic_form_delete_email_log' );

function magic_form_delete_email_log() {
    $JSON = Magic_FormsLogs::getInstance()->deleteLogs( sanitize_email($_POST['email']));
    magicFormJSONExit( $JSON);
    wp_die();
}

?>
