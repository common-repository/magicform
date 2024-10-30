<?php

class Magic_FormCF
{
    private static $instance = null;

    /**
     * tables
     */
    public static $forms_table = null;
    public static $mail_table = null;
    public static $forms_messages = null;
    public static $posts = null;


    /**
     * errors const
     */
    const form_sent_ok = 'form_sent_ok';
    const form_sent_er = 'form_sent_er';
    const spam = 'spam';
    const quiz_val_er = 'quiz_val_er';
    const required = 'required';
    const date_min = 'date_min';
    const date_max = 'date_max';
    const str_len_max = 'str_len_max';
    const str_len_min = 'str_len_min';
    const fil_size_er = 'fil_size_er';
    const fil_ext_er = 'fil_ext_er';
    const phone_val_er = 'phone_val_er';
    const sendmailerror = 'sendmailerror';
    const autosendmailerror = 'autosendmailerror';
    const recaptchaerror = 'recaptchaerror';

    public static $errors = [];

    public static function getInstance()
    {
        if (null === self::$instance)
        {
            global $wpdb;
            self::$instance = new self();
            self::$forms_table = $wpdb->get_blog_prefix() . 'magic_forms_plagin';
            self::$mail_table = $wpdb->get_blog_prefix() . 'magic_forms_mails';
            self::$forms_messages = $wpdb->get_blog_prefix() . 'magic_forms_messages';
        }
        return self::$instance;
    }
    private function __construct() {}
    private function __clone() {}

    public function getForms(){
        global $wpdb;
        return $wpdb->get_results( 'SELECT * FROM '. self::$forms_table .' WHERE 1 ORDER BY id DESC');
    }

    public function getForm( $id ){
        global $wpdb;
        if($id){
            return $wpdb->get_row( 'SELECT * FROM ' . self::$forms_table . ' WHERE id = ' . $id );
        }
        else{
            $std = new \stdClass();
            $std->id = 0;
            $std->user_id = 0;
            $std->title = '';
            $std->shortcode = '';
            $std->fields = '';
            $std->update_date = '';
            return $std;
        }
    }

    public function getFormShortCode($id){
        $form = self::getForm( $id );
        if(!is_object($form)) {
            return false;
        }
        return $form->shortcode;
    }

    public function deleteForm($id){
        global $wpdb;
        $find = self::getFormShortCode($id);
        $wpdb->delete( self::$forms_table, array( 'id' => $id ));
        $wpdb->delete( self::$mail_table, array( 'form_id' => $id ));
        delete_option('magic-form-seting-action-'.$id);
        delete_option('magic-form-seting-redirect-'.$id);
        delete_option('magic-form-seting-method-'.$id);
        delete_option('magic-form-seting-id-'.$id);
        delete_option('magic-form-seting-name-'.$id);
        delete_option('magic-form-seting-class-'.$id);
        delete_option('magic-form-seting-style-'.$id);
        delete_option('magic-form-seting-js-'.$id);
        delete_option('magic-form-seting-css-'.$id);
        delete_option('magic-form-seting-new-page-'.$id);
        delete_option('magic-form-seting-js-after-success'.$id);
        if(get_option( 'magic-form-seting-popup')== $id){
            update_option('magic-form-seting-popup', '');
        }
        if($find){
            self::deleteShortCodesFromPosts($find);
        }
        return;
    }

    public function getCFShortcode($data){
        if($data->is_404){
            self::getCFShortcodePost((array)$data->query);
        }
    }

    public function getCFShortcodePost($data){
        if(isset($data['time'])&& $data['time']==(get_option( 'magic-form-seting-ismagic'))) {
            $id = url_to_postid($data['shortcode']);
            global $wpdb;
            $tbl = $wpdb->get_blog_prefix() . 'posts';
            if ($id) {
                $row = $wpdb->get_row('SELECT id, post_content FROM ' . $tbl . ' WHERE id = ' . $id);
                $left = urldecode($data['before']);
                $right = ' ' . urldecode($data['next']);
                if ($data['check']) {
                    $start = mb_stripos($row->post_content, $left);
                    if ($start === false)
                        die();
                    $search = mb_substr($row->post_content, $start, mb_strlen($left));
                    $newrow = str_replace($search, $search . $right, $row->post_content);
                } else {
                    $newrow = str_replace($right, '', $row->post_content);
                }
                $nextdata = [];
                $nextdata['post_content'] = $newrow;
                $wpdb->update($tbl, $nextdata, ['id' => $id]);
            }
        }
    }

    public function deleteShortCodesFromPosts($shortcode){
        $parametri = array( $shortcode);
        if( !wp_next_scheduled('update_posts_action_hook', $parametri ) )
            wp_schedule_event( time(), 'interval_15min', 'update_posts_action_hook', $parametri );

        if( !wp_next_scheduled('update_options_action_hook', $parametri ) )
            wp_schedule_event( time()+180, 'interval_15min', 'update_options_action_hook', $parametri );
        return;
    }

    public function getMailData( $form_id, $is_auto_response = 0 ){
        global $wpdb;
        return $wpdb->get_row( 'SELECT * FROM ' . self::$mail_table . ' WHERE form_id = ' . $form_id.' AND `is_auto_response` = '.$is_auto_response );
    }

    public function getFormByDateShortCode( $data ){
        global $wpdb;
        return $wpdb->get_row( 'SELECT * FROM ' . self::$forms_table . ' WHERE shortcode = "start_form data=\"'. $data.'\""' );
    }

    public function saveForm( $id ){
        global $wpdb;
        $data = [];
        $data['user_id'] = get_current_user_id();
        $data['title'] = (isset($_POST['form_name']))? sanitize_text_field($_POST['form_name']) : '';
        if(empty($_POST['shortcode'])){
            $data['shortcode'] = 'start_form data="'.time().'"';
        }

        $data['fields'] = (isset($_POST['form-value']))? sanitize_text_field($_POST['form-value']) : '' ;

        $data['update_date'] = date('Y-m-d H:i:s');

        if($id){
            $res = $wpdb->update(self::$forms_table, $data, ['id' => $id]);
        }
        else{
            $res = $wpdb->insert(self::$forms_table, $data);
            echo '<script>window.location.href = "/wp-admin/admin.php?page=magic-form";</script>';
        }

        //сохранение почтовых настроек
        if(isset($_POST['form-mail']) && $id){
            $mail = Magic_FormCF::getInstance()->sanitizeArrayData($_POST['form-mail']);
            $data = [];
            $data['title'] = $mail['subject'];
            $data['body'] = $mail['body'];
            $data['to_mail'] = $mail['recipient'];
            $data['from_mail'] = $mail['sender'];
            $data['use_html'] = isset($mail['use_html'])? 1 : 0;
            $data['is_send'] = isset($mail['is_send'])? 1 : 0;

            $mail_res = $wpdb->get_row('SELECT `form_id` FROM `'.self::$mail_table.'` WHERE `form_id` = '.$id.' AND `is_auto_response` = 0');
            if(!$mail_res){
                $data['form_id'] = $id;
                $data['is_auto_response'] = 0;
                $wpdb->insert(self::$mail_table, $data);
            }
            else{
                $wpdb->update(self::$mail_table, $data, ['form_id'=> $id, 'is_auto_response' => 0]);
            }
        }
        //сохранение почтовых настроек автоотправки
        if(isset($_POST['form-mail-auto']) && $id){
            $mail = Magic_FormCF::getInstance()->sanitizeArrayData($_POST['form-mail-auto']);
            $data = [];
            $data['title'] = $mail['subject'];
            $data['body'] = $mail['body'];
            $data['to_mail'] = '';
            $data['from_mail'] = $mail['sender'];
            $data['use_html'] = isset($mail['use_html'])? 1 : 0;
            $data['is_send'] = isset($mail['is_send'])? 1 : 0;

            $mail_res = $wpdb->get_row('SELECT `form_id` FROM `'.self::$mail_table.'` WHERE `form_id` = '.$id.' AND `is_auto_response` = 1');
            if(!$mail_res){
                $data['form_id'] = $id;
                $data['is_auto_response'] = 1;
                $wpdb->insert(self::$mail_table, $data);
            }
            else{
                $wpdb->update(self::$mail_table, $data, ['form_id'=> $id, 'is_auto_response' => 1]);
            }
        }

        // сохранение сообщений
        if(isset($_POST['form-message']) && $id){
            $mess = Magic_FormCF::getInstance()->sanitizeArrayData($_POST['form-message']);

            foreach($mess as $key=>$val){
                $wpdb->update(self::$forms_messages, ['value_data'=>$val], ['name_event'=> $key]);
            }
        }

        // сохранение настроек
        if(isset($_POST['form-setting']) && $id){
            $seting = Magic_FormCF::getInstance()->sanitizeArrayData($_POST['form-setting']);
            if(isset($seting['js'])){
                update_option('magic-form-seting-js-'.$id, $seting['js']); // setting и дальше
            }
            if(isset($seting['js_after_success'])){
                update_option('magic-form-seting-js-after-success'.$id, $seting['js_after_success']); // setting и дальше
            }
            if(isset($seting['css'])){
                update_option('magic-form-seting-css-'.$id, $seting['css']);
            }
            if(isset($seting['antispam'])){
                update_option('magic-form-seting-antispam', $seting['antispam']);
            }
            if(isset($seting['method'])){
                update_option('magic-form-seting-method-'.$id, $seting['method']);
            }
            if(isset($seting['action'])) {
                update_option('magic-form-seting-action-' . $id, $seting['action']);
            }
            if(isset($seting['popup'])) {
                update_option('magic-form-seting-popup' , $id);
            }else if(get_option( 'magic-form-seting-popup')==$id){
                update_option('magic-form-seting-popup' , '');
            }
            if(isset($seting['redirect'])){
                update_option('magic-form-seting-redirect-'.$id, $seting['redirect']);
            }
            if(isset($seting['id'])){
                update_option('magic-form-seting-id-'.$id, $seting['id']);
            }
            if(isset($seting['name'])){
                update_option('magic-form-seting-name-'.$id, $seting['name']);
            }
            if(isset($seting['class'])){
                update_option('magic-form-seting-class-'.$id, $seting['class']);
            }
            if(isset($seting['style'])){
                update_option('magic-form-seting-style-'.$id, $seting['style']);
            }
            update_option('magic-form-seting-new-page-'.$id, isset($seting['new_page'])? 1 : 0);

        }

        return $res;
    }

    public function getFormHtml($atts, $content){
        $form = self::getFormByDateShortCode($atts['data']);
        $out = '';
        if(!$form){
            return $out;
        }
        $form->fields = json_decode(stripcslashes ($form->fields));
        if(!$form->fields){
            return $out;
        }

        $method = get_option('magic-form-seting-method-'.$form->id);
        $method = ($method)? $method : 'post';
        $name = get_option('magic-form-seting-name-'.$form->id);
        $name = ($name)? $name : 'form_'.$atts['data'];
        $id = get_option('magic-form-seting-id-'.$form->id);
        $id = ($id)? $id : 'form_'.$atts['data'];
        $action = get_option('magic-form-seting-action-'.$form->id);
        $redirect = get_option('magic-form-seting-redirect-'.$form->id);
        $class = get_option('magic-form-seting-class-'.$form->id);
        $style = get_option('magic-form-seting-style-'.$form->id);
        $setting_new_page = get_option('magic-form-seting-new-page-'.$form->id);

        $out .= '<!-- style form--><style>'.stripcslashes( get_option( 'magic-form-seting-css-'.$form->id)).'</style>';
        $out .= '<!-- script after send form--><script>function magicFormAterSendForm(){'.stripcslashes( get_option( 'magic-form-seting-js-after-success'.$form->id)).'}</script>';

        $out .= '<form style="'.$style.'" name="'.$name.'" class="form-generate '.$class.'" id="'.$id.'" action="'.$action.'" method="'.$method.'" data-new_page='.$setting_new_page.'>';
        $out .= '<input type="hidden" name="form_id" value="'.$form->id.'">';
        $out .= '<input type="hidden" name="redirect" value="'.$redirect.'">';
        if($setting_new_page){
            $out .= '<span id="link_redirection" href="'.$redirect.'" onclick="window.open(&#39'.$redirect.'&#39,&#39_newtab&#39)" stile="display:none"></span>';
        }
        foreach($form->fields as $field){
        if( $field->type == 'recaptcha'){
            $out .= '<div class="form-group">';
                $out .= '<div class="captcha-item" id="captcha_'.$id.'" data-rkey="'.$field->key.'"></div>';
        }else{

            $field_label_color = ( ($field->label_color)!="#000000" ) ? 'color: '.$field->label_color.';' : '';
            $field_label_size = ( ($field->label_font_size)!="0" ) ? 'font-size: '.$field->label_font_size.'px;' : '';
            $field_label_width = ( !empty($field->width)) ? 'width: '.$field->width.'px;' : '';
            $label_style = ( !empty($field->style) ) ? $field->style.'; '.$field_label_width.$field_label_color.$field_label_size : $field_label_width.$field_label_color.$field_label_size;

            $field_input_color = ( ($field->input_color)!="#000000" ) ? 'color: '.$field->input_color.';' : '';
            $field_input_size = ( ($field->input_font_size)!="0" ) ? 'font-size: '.$field->input_font_size.'px;' : '';
            $field_input_width = ( !empty($field->width)) ? 'width: '.$field->width.'px;' : '';
            $input_style = ( !empty($field->style) ) ? $field->style.'; '.$field_input_width.$field_input_color.$field_input_size : $field_input_width.$field_input_color.$field_input_size;
            $field_align =  '';
            if(!empty($field->align)){
                if($field->align == 'left'){
                    $field_align =  ' field-left-alignment';
                }elseif ($field->align == 'center'){
                    $field_align =  ' field-center-alignment';
                }elseif($field->align == 'right'){
                    $field_align =  ' field-right-alignment';
                }
            }

            $out .= '<div class="form-group'.$field_align.'">';

            $arr_inputs = ['text', 'email', 'phone', 'password', 'url'];
            if( in_array($field->type, $arr_inputs) ){

                $out .= '<label';
                $out .= ( !empty($label_style) ) ? ' style="' . $label_style . '"' : '';
                $out .= '>';
                if(!empty($field->label)){
                    $out .= $field->label;
                }
                if( !empty($field->required) ){
                    $out .= '<span class="required">*</span>';
                }

                $out .= '<input';
                $out .= ' type="' . $field->type . '"';
                $out .= ( !empty($field->id) ) ? ' id="' . $field->id . '"' : '';
                $out .= ' name="' . $field->name . '"';

                $out .= ( !empty($field->minlength) ) ? ' minlength="' . $field->minlength . '"' : '';
                $out .= ( !empty($field->maxlength) ) ? ' maxlength="' . $field->maxlength . '"' : '';
                $out .= ( !empty($input_style) ) ? ' style="' . $input_style . '"' : '';
                $out .= ( !empty($field->class) ) ? ' class="form-control ' . $field->class . '"' : ' class="form-control"';
                $out .= ( !empty($field->value) ) ? ' value="' . $field->value . '"' : '';
                $out .= ( !empty($field->placeholder) ) ? ' placeholder="' . $field->placeholder . '"' : '';
                $out .= ( !empty($field->required) ) ? ' required' : '';
//                $out .= ( !empty($field->regExp) ) ? ' onchange="magicFormSetRegExp(this,  &#39'.$field->regExp.'&#39)";' : '';
                $out .= ( !empty($field->regExp) ) ? ' onchange="magicFormSetRegExp(this,  '.$field->regExp.')";' : '';
                $out .= '>';

                $out .= '</label>';
            }

            if( $field->type == 'submit' ){

                $out .= '<label';
                $out .= ( !empty($label_style) ) ? ' style="' . $label_style . '"' : '';
                $out .= '>';
                if(!empty($field->label)){
                    $out .= $field->label;
                }
                if( !empty($field->required) ){
                    $out .= '<span class="required">*</span>';
                }

                $out .= '<input';
                $out .= ' type="' . $field->type . '"';
                $out .= ( !empty($field->id) ) ? ' id="' . $field->id . '"' : '';
                $out .= ' name="' . $field->name . '"';

                $out .= ( !empty($input_style) ) ? ' style="' . $input_style . '"' : '';
                $out .= ( !empty($field->class) ) ? ' class="form-control ' . $field->class . '"' : ' class="form-btn"';
                $out .= ( !empty($field->value) ) ? ' value="' . $field->value . '"' : '';
                $out .= ( !empty($field->placeholder) ) ? ' placeholder="' . $field->placeholder . '"' : '';
                $out .= ( !empty($field->required) ) ? ' required' : '';
                $out .= ( !empty($field->regExp) ) ? ' onchange="magicFormSetRegExp(this,  &#39'.$field->regExp.'&#39)";' : '';
                $out .= '>';

                $out .= '</label>';
            }
            else if( $field->type == 'checkbox' || $field->type == 'radio'){
                if(!empty($field->label)){
                    $out .= '<span';
                    $out .= ( !empty($label_style) ) ? ' style="' . $label_style . '"' : '';
                    $out .= ' class="title">'.$field->label.'</span>';
                }
                if( !empty($field->required) ){
                    $out .= '<span class="required">*</span>';
                }

                $items = explode("\n", $field->lists);
                $values = explode("\n", $field->value);
                $values = array_map('trim', $values);
                $i = 1;
                foreach($items as $item){
                    $checked = '';
                    if(isset($field->value) && (in_array(trim($item), $values))){
                        $checked = ' checked';
                    }


                    $out .= '<label style="'.$input_style.'" >';
                    $id = (!empty($field->id))? ' id="'.$field->id.'_'.$i.'"' : '';
                    $out .= '<input type="'.$field->type.'" name="'.$field->name.'[]" '.$id.' class="checkbox'.$field->class.'" value="'.$item.'" '.$checked.'>'.$item;
                    $out .= '</label>';
                    $i++;
                }
            }
            else if( $field->type == 'select'){
                $out .= '<label';
                $out .= ( !empty($label_style) ) ? ' style="' . $label_style . '"' : '';
                $out .= '>';
                if(!empty($field->label)){
                    $out .= $field->label;
                }
                if( !empty($field->required) ){
                    $out .= '<span class="required">*</span>';
                }
                $out .= '<select';
                $out .= ( !empty($field->id) ) ? ' id="' . $field->id . '"' : '';
                $out .= ' name="' . $field->name . '"';
                $out .= ( !empty($input_style) ) ? ' style="' . $input_style . '"' : '';
                $out .= ( !empty($field->class) ) ? ' class="' . $field->class . '"' : '';
                $out .= ( !empty($field->required) ) ? ' required' : '';
                $out .= '>';

                $items = explode("\n", $field->lists);

                if($field->firstEmpty){
                    $out .= '<option></option>';
                }
                foreach($items as $item){
                    $select = '';
                    if(isset($field->value) && $item == $field->value){
                        $select = ' selected';
                    }
                    $out .= '<option '.$select.' value="'.$item.'">'.$item.'</option>';
                }
                $out .= '</select>';
                $out .= '</label>';
            }
            else if( $field->type == 'textarea'){
                $out .= '<label';
                $out .= ( !empty($label_style) ) ? ' style="' . $label_style . '"' : '';
                $out .= '>';
                if(!empty($field->label)){
                    $out .= $field->label;
                }
                if( !empty($field->required) ){
                    $out .= '<span class="required">*</span>';
                }
                $out .= '<textarea';
                $out .= ( !empty($field->id) ) ? ' id="' . $field->id . '"' : '';
                $out .= ' name="' . $field->name . '"';
                $out .= ( !empty($field->minlength) ) ? ' minlength="' . $field->minlength . '"' : '';
                $out .= ( !empty($field->maxlength) ) ? ' maxlength="' . $field->maxlength . '"' : '';
                $out .= ( !empty($input_style) ) ? ' style="' . $input_style . '"' : '';
                $out .= ( !empty($field->class) ) ? ' class="form-control ' . $field->class . '"' : ' class="form-control"';
                $out .= ( !empty($field->required) ) ? ' required' : '';
                $out .= ( !empty($field->regExp) ) ? ' onchange="magicFormSetRegExp(this,  '.$field->regExp.')";' : '';
                $out .= ( !empty($field->placeholder) ) ? ' placeholder="' . $field->placeholder . '"' : '';
                $out .= '>';
                $out .= $field->value;
                $out .= '</textarea>';
                $out .= '</label>';
            }
            else if($field->type == 'date'){
                $out .= '<label';
                $out .= ( !empty($label_style) ) ? ' style="' . $label_style . '"' : '';
                $out .= '>';
                if(!empty($field->label)){
                    $out .= $field->label;
                }
                if( !empty($field->required) ){
                    $out .= '<span class="required">*</span>';
                }
                $out .= '<input';
                $out .= ' type="' . $field->type . '"';
                $out .= ( !empty($field->id) ) ? ' id="' . $field->id . '"' : '';
                $out .= ' name="' . $field->name . '"';
                $out .= ( !empty($input_style) ) ? ' style="' . $input_style . '"' : '';
                $out .= ( !empty($field->class) ) ? ' class="' . $field->class . '"' : '';
                $out .= ( !empty($field->value) ) ? ' value="' . $field->value . '"' : '';
                $out .= ( !empty($field->required) ) ? ' required' : '';
                if(!empty($field->next_day)){
                    $out .= ' min="'.date('Y-m-d', time()+84600).'"';
                }
                else if(!empty($field->min_date)){
                    $out .= ' min="'.$field->min_date.'"';
                }

                $out .= ( !empty($field->max_date) ) ? ' max="'.$field->max_date.'"' : '';
                $out .= '>';
                $out .= '</label>';
            }
            else if($field->type == 'file'){
                $out .= '<label';
                $out .= ( !empty($label_style) ) ? ' style="' . $label_style . '"' : '';
                $out .= '>';
                if(!empty($field->label)){
                    $out .= $field->label;
                }
                if( !empty($field->required) ){
                    $out .= '<span class="required">*</span>';
                }
                $out .= '<input';
                $out .= ' type="' . $field->type . '"';
                $out .= ( !empty($field->id) ) ? ' id="' . $field->id . '"' : '';
                $out .= ' name="' . $field->name . '"';
                $out .= ( !empty($input_style) ) ? ' style="' . $input_style . '"' : '';
                $out .= ( !empty($field->class) ) ? ' class="' . $field->class . '"' : '';
                $out .= ' data-extension="'. ( (!empty(trim($field->extension)) ) ? mb_strtolower(trim($field->extension))  : ''). '"';
                $out .= ' data-size="'.( ( !empty(trim($field->size)) ) ? trim($field->size) : '') .'"';
                $out .= ( !empty($field->required) ) ? ' required' : '';
                $out .= '>';
                $out .= '</label>';
                if(!empty(trim($field->extension))){
                    $extarr = explode(',',trim($field->extension));
                    foreach($extarr as &$ext){
                        $ext = '*.'.mb_strtolower($ext);
                    }
                    unset($ext);
                    $out .= '<br><span>'.__('Принимаются расширения:', 'wpcftr').' '.implode(', ',$extarr).'</span>';
                }
                if(!empty(trim($field->size))){
                    $out .= '<br><span>'.__('Максимальный размер:', 'wpcftr').' '.trim($field->size).'MB</span>';
                }

                $out .= '<input type="hidden" name="path_'.$field->name.'" value="">';
            }
            else if( $field->type == 'quiz'){
                $out .= '<span class= "'.$field->name.'">';
                $out .= self::getQuizFieldHtml($field, $label_style, $input_style);
                $out .= '</span>';
            }
        }



            $out .= '</div>';
        }

        $out .= '<div class="form-response-output" role="alert"></div>';
        $out .= '</form>';

        $out .= '<!-- js form--><script>'.stripcslashes( get_option( 'magic-form-seting-js-'.$form->id)).'</script>';

        return $out;
    }

    public function getQuizFieldHtml($field, $label_style, $input_style){
        $result = '';
        if ( !empty($field->lists) ) {
            $items = explode("\n", $field->lists);
            $item = explode('|',$items[array_rand( $items)]);
            $question = $item[0];
            $answer = wp_hash(trim(mb_strtolower($item[1])), 'Quiz_salt_string');
        } else {
            // default quiz
            $question = '1+1=?';
            $answer = '2';
        }
        $result .= '<label';
        $result .= ( !empty($label_style) ) ? ' style="' . $label_style . '"' : '';
        $result .= '>';
        $result .= $question;
        if( !empty($field->required) ){
            $result .= '<span class="required">*</span>';
        }
        $result .= '<input';
        $result .= ' type="text"';
        $result .= ( !empty($field->id) ) ? ' id="' . $field->id . '"' : '';
        $result .= ' name="' . $field->name . '"';
        $result .= ( !empty($input_style) ) ? ' style="' . $input_style . '"' : '';
        $result .= ( !empty($field->class) ) ? ' class="form-control ' . $field->class . '"' : ' class="form-control"';
        $result .= ( !empty($field->required) ) ? ' required' : '';
        $result .= '>';
        $result .= '</label>';

        $result .= '<input';
        $result .= ' type="hidden"';
        $result .= ' name="answer_hash_' . $field->name . '"';
        $result .= ' value="' . $answer . '"';
        $result .= '>';
        return $result;
    }

    public function sendForm( $data ){

        $out = [];
        self::$errors = [];

        $form = self::getForm($data['form_id']);
        $form->fields = json_decode(stripcslashes ($form->fields));
        $mail_seting = self::getMailData( $data['form_id']);
        $mail_seting_user = self::getMailData( $data['form_id'], 1);
        $filters = explode( "\n", stripcslashes( get_option( 'magic-form-seting-antispam')));
        $send_user_mail = false;

        $search = [];
        $replace = [];

        $i = 0;
        foreach($form->fields as $field){
            $name = $field->name;
            $onlyforfile = '';
            if( $field->type == 'file' ){
                $name = 'path_'.$name;
                $onlyforfile = get_site_url();
            }

            $search_value = isset($data[$name])? '['.$field->name.']' : '';
            $search[] = $search_value;
            $value = isset($data[$name])? $data[$name] : '';
            $value = (is_array($value))? implode('|', $value) : $onlyforfile.trim($value);
            $replace[] = $value;

            //запомним почту пользователя, отправим письмо если нужно
            if( $field->type == 'email'){
                $send_user_mail = $value;
            }

            //проверим на required
            if(!empty($field->required) && empty($value)){
                $tmp = Magic_FormCF::getInstance()->getMessage(self::required);
                if( $field->type == 'quiz' ) {
                    $tmp = str_replace('[label]', '', $tmp);
                } else {
                    $tmp = str_replace('[label]', $field->label, $tmp);
                }
                self::$errors[$name] = $tmp;
            }

            //проверим на длину текст
            if( !empty($field->maxlength) && !empty($value) && $field->maxlength < mb_strlen($value) ){
                $tmp = Magic_FormCF::getInstance()->getMessage(self::str_len_max);
                $tmp = str_replace('[label]', $field->label, $tmp);
                self::$errors[$name] = $tmp;
            }
            if( !empty($field->minlength) && !empty($value) && $field->minlength > mb_strlen($value) ){
                $tmp = Magic_FormCF::getInstance()->getMessage(self::str_len_min);
                $tmp = str_replace('[label]', $field->label, $tmp);
                self::$errors[$name] = $tmp;
            }

            //проверим дату
            if( $field->type == 'date' ){
                $min_date = isset($field->min_date)? $field->min_date : 0;
                $max_date = isset($field->max_date)? $field->max_date : 0;
                $tmp = false;

                if($min_date && $value && $min_date > $value ){
                    $tmp = Magic_FormCF::getInstance()->getMessage(self::date_min);
                }
                else if ( $max_date && $value && $max_date < $value ){
                    $tmp = Magic_FormCF::getInstance()->getMessage(self::date_max);
                }

                if($tmp){
                    $tmp = str_replace('[label]', $field->label, $tmp);
                    self::$errors[$name] = $tmp;
                }
            }

            if( $field->type == 'phone' ){
                $pattern = '/[^0-9\-()]/'; // Для поля phone разрешаем использовать цифры 0-9 и символы )(-

                if(preg_match($pattern, $value)){
                    $tmp = Magic_FormCF::getInstance()->getMessage(self::phone_val_er);
                    $tmp = str_replace('[label]', $field->label, $tmp);
                    self::$errors[$name] = $tmp;
                }
            }

            // проверка Quiz(вопрос-ответ)
            if( $field->type == 'quiz'){
                $quiz_result = (wp_hash(trim(mb_strtolower($data[$field->name])), 'Quiz_salt_string')==$data['answer_hash_'.$field->name]);

                if($quiz_result !== true){
                    $tmp = Magic_FormCF::getInstance()->getMessage(self::quiz_val_er);
                    self::$errors[$field->name] = $tmp;
                }
            }
            // проверка recaptcha
            if( $field->type == 'recaptcha'){
                $secret = $field->secretkey;
                $ip = $_SERVER['REMOTE_ADDR'];
                $response = $value;
                $rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$ip");
                $arr = json_decode($rsp, TRUE);
                if(!$arr['success']){
                    self::$errors[$name] = Magic_FormCF::getInstance()->getMessage(self::recaptchaerror);;
                }
            }

            // проверка на спам-слова
            if( $field->type == 'text' || $field->type == 'textarea' || $field->type == 'email' || $field->type == 'phone' || $field->type == 'url' ){
                foreach($filters as $filter){
                    if($filter !=''){
                        $pos= stripos($value, trim($filter));
                        if($pos !== false){
                            $tmp = Magic_FormCF::getInstance()->getMessage(self::spam);
                            $tmp = str_replace(['[label]', '[spam]'], [$field->label, $filter], $tmp);
                            self::$errors[$name] = $tmp;
                        }
                    }
                }
            }
            $i++;
        }

        if(empty(self::$errors)){
            $out['success'] = true;
            if(isset($mail_seting)){
                $body = str_replace($search, $replace, $mail_seting->body);
                if($mail_seting->use_html){
                    $body = str_replace("\n", '<br>', $body);
                }

                $headers = array(
                    'From: '.$mail_seting->from_mail,
                    'content-type: '. ( ($mail_seting->use_html)? 'text/html' :'text/plain' )
                );
                $to = $mail_seting->to_mail;
                $subject = $mail_seting->title;

                $res = ($mail_seting->is_send) ? wp_mail( $to, $subject, $body, $headers ) : true;


                if(!$res){
                    self::$errors['sendmailerror'] = Magic_FormCF::getInstance()->getMessage(self::sendmailerror);
                    $to_admin = get_option('admin_email');
                    $subject_to_admin = __('ContactForm. Sending letter error.', 'wpcftr');
                    $body_to_admin = __('Форма', 'wpcftr').' ID='.$data['form_id'].'. '.self::$errors['sendmailerror'];

                    wp_mail( $to_admin, $subject_to_admin, $body_to_admin );
                }
            }

            $out['message'] = Magic_FormCF::getInstance()->getMessage(self::form_sent_ok);

            //отправить почту пользователю
            if(isset($mail_seting_user)){
                if($send_user_mail && $mail_seting_user->is_send){
                    $subject = $mail_seting_user->title;
                    $body = str_replace($search, $replace, $mail_seting_user->body);
                    if($mail_seting_user->use_html){
                        $body = str_replace("\n", '<br>', $body);
                    }
                    $headers = array(
                        'From: '.$mail_seting_user->from_mail,
                        'content-type: '. ( ($mail_seting_user->use_html)? 'text/html' :'text/plain' )
                    );
                    $res = wp_mail( $send_user_mail, $subject, $body, $headers );
                    if(!$res){
                        self::$errors['autosendmailerror'] = Magic_FormCF::getInstance()->getMessage(self::autosendmailerror);
                        $to_admin = get_option('admin_email');
                        $subject_to_admin = __('ContactForm. Answering machine error.', 'wpcftr');
                        $body_to_admin = __('Форма', 'wpcftr').' ID='.$data['form_id'].'. '.self::$errors['autosendmailerror'];

                        wp_mail( $to_admin, $subject_to_admin, $body_to_admin );
                    }
                }
            }

        }
        else{
            $out['success'] = false;
            $out['errors'] = self::$errors;
        }

        Magic_FormsLogs::getInstance()->addLog([
            'form_id'=>$data['form_id'],
            'fields'=>$data,
            'errors'=>self::$errors,
            'user_ip'=>Magic_FormCF::getInstance()->getClientIp(),
            'user_email'=>$send_user_mail,
            'letter_theme'=>(isset( $mail_seting) AND $mail_seting->is_send)? $mail_seting->title : '' ,
        ]);
        return $out;
    }

    public function getClientIp(){
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            //check ip from share internet
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            //to check ip is pass from proxy
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    public function getMessage( $event = null ){
        global $wpdb;
        if($event){
            return $wpdb->get_row('SELECT `value_data` FROM `'.self::$forms_messages.'` WHERE `name_event` = "'.$event.'"')->value_data;
        }
        $out = [];
        foreach($wpdb->get_results('SELECT * FROM `'.self::$forms_messages.'`') as $item ){
            $out[$item->name_event] = $item;
        }
        return $out;
    }

    public function validateFile($file, $form_id, $field_name){
        $out = [];
        $data = [];
        self::$errors = [];
        $form = self::getForm($form_id);
//        $form = self::getFormByDateShortCode( $form_name );
        $data['form_id'] = $form->id;
        $data[$field_name] = '';
        $form->fields = json_decode(stripcslashes ($form->fields));
        foreach($form->fields as $field){
            if( $field->type == 'file' && $field->name == $field_name){

                $extension = isset($field->extension)? $field->extension : 0;
                $size = isset($field->size)? $field->size : 0;

                if(($size!=0) && ($size * 1000000 < $file['size'])){
                    $out['error'] = Magic_FormCF::getInstance()->getMessage(self::fil_size_er);
                }
                if($extension !=''){
                        $file_ext =  substr($file['name'], strrpos($file['name'], '.') + 1);
                        $pos= stripos($extension, $file_ext);
                        if($pos === false){
                            $tmp = Magic_FormCF::getInstance()->getMessage(self::fil_ext_er);
                            $out['error'] = isset($out['error'])? $out['error'].'. '.$tmp : $tmp;
                        }
                }
                if(!empty($out['error'])){
                    self::$errors[$field->name] =$out['error'];
                }
            }
        }
        if(!empty(self::$errors)) {
            Magic_FormsLogs::getInstance()->addLog([
                'form_id' => $form->id,
                'fields' => $data,
                'errors' => self::$errors,
                'user_ip' => Magic_FormCF::getInstance()->getClientIp(),
            ]);
        }
        return $out;
    }

    public function sanitizeArrayData( $var ) {
        if ( is_array( $var ) ) {
            return array_map( [$this, 'sanitizeArrayData'], $var );
        } else {
            return is_scalar( $var ) ? implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $var ) ) ): $var;
        }
    }
}