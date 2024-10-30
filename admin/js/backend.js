jQuery(document).ready(function($) {

    //скопировать в буфер обмена
    $('.copy-shortcode').click(function () {

        var code = $(this).parent().find('code').text();

        var tmp   = document.createElement('INPUT'), // Создаём новый текстовой input
            focus = document.activeElement; // Получаем ссылку на элемент в фокусе (чтобы не терять фокус)
        tmp.value = code; // Временному input вставляем текст для копирования
        document.body.appendChild(tmp); // Вставляем input в DOM
        tmp.select(); // Выделяем весь текст в input
        document.execCommand('copy'); // Магия! Копирует в буфер выделенный текст (см. команду выше)
        document.body.removeChild(tmp); // Удаляем временный input
        focus.focus(); // Возвращаем фокус туда, где был
        alert(b_l10n_obj.copyshortcode);
    });

    $('.tabs_menu a').click(function(e) {
        e.preventDefault();
        $('.tabs_menu .active').removeClass('active');
        $(this).addClass('active');
        var tab = $(this).attr('href');
        $('.tab').not(tab).css({'display':'none'});
        $(tab).fadeIn(400);
    });


    $('#save-form').submit( function (e) {
        e.preventDefault();
        if(setingField.length<2){
            setTimeout(function () {
                $('.notice').remove();
                $('#pagetitle').after('<div id="message" class="notice notice-error is-dismissible"><p>'+b_l10n_obj.formmin2fields+'</p><button id="my-dismiss-message" type="button" class="notice-dismiss" ><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
                $("#my-dismiss-message").click(function(event) {
                    event.preventDefault();
                    $('.notice').remove();
                });
            },700);
        }else{
            $('#form-value').val(JSON.stringify(setingField));
            $(this).off('submit');
            $(this).submit();
        }
    });


/*========================перетяжка и сортировка==============================*/

    $( function() {
        $( "#sortable" ).sortable({
            items:"> li",
            revert: true,
            // placeholder: "ui-state-highlight",
            stop: function( event, ui ) {
                var input = $(ui.item);
                if($(input).hasClass('addfieldinform')){
                    $(input).removeAttr("style");
                    var new_field_flag = true;

                    addFieldToSettingField(input);
                    itemfield++;
                    showHideDragDropField(itemfield);
                    showSettingField( input, new_field_flag);
                    magicFormShowModal();
                    // console.log('add new field');
                }
            }
        });

        var $draggable = $( "#draggable" );
        $( "li", $draggable ).draggable({
            connectToSortable: "#sortable",
            helper: "clone",
            revert: "invalid",
        });

        $( "ul, li" ).disableSelection();

        $( "#droppable" ).droppable({
            classes: {
                "ui-droppable-active": "dragdrop__fields-active",
                "ui-droppable-hover": "dragdrop__fields-hover"
            },
            drop: function( event, ui ) {
                $('div.dragdrop__fields__empty').addClass('dragdrop__fields__hidden');
                $('div.dragdrop__fields__empty--title').addClass('dragdrop__fields__hidden');
            }
        });
    });

    var itemfield = 0;
    var setingField = [];
    var title = [];
    title['input'] = b_l10n_obj.txt_field;
    title['text'] =  b_l10n_obj.txt_field;
    title['checkbox'] =  b_l10n_obj.sel_checkbox;
    title['select'] = b_l10n_obj.sel_list;
    title['radio'] = b_l10n_obj.sel_radio;
    title['textarea'] = b_l10n_obj.big_txtar;
    title['email'] = b_l10n_obj.email;
    title['phone'] = b_l10n_obj.phone;
    title['password'] = b_l10n_obj.password;
    title['url'] = b_l10n_obj.url_field;
    title['date'] = b_l10n_obj.date_field;
    title['submit'] = b_l10n_obj.send_but;
    title['file'] = b_l10n_obj.file_field;
    title['quiz'] = b_l10n_obj.quiz;
    title['recaptcha'] = b_l10n_obj.recaptcha;

    //установить данные если были
    if(window.setingField){
        setingField = JSON.parse(window.setingField);
        itemfield = setingField.length;

        showHideDragDropField(itemfield);

        setingField.forEach(function(item, i, setingField) {
            var icon_div = $("li[data-name='"+item.type+"']>div>div").html();
            var input = '<li class="ui-state-default modal seting-field" data-name="'+item.type+'" data-itemfield="'+i+'"><div class="inner-addfieldinform"><div class="addfieldinform-ico">'+icon_div+'</div>'+title[item.type]+'<span class="deleteinform"><svg width="11px" height="11px">\n' +
                '<path fill-rule="evenodd"  fill="rgb(190, 190, 190)"\n' +
                ' d="M10.450,8.328 L7.621,5.500 L10.450,2.672 L8.328,0.550 L5.500,3.379 L2.672,0.550 L0.550,2.672 L3.379,5.500 L0.550,8.328 L2.672,10.450 L5.500,7.621 L8.328,10.450 L10.450,8.328 Z"/>\n' +
                '</svg></span></div></li>';
            $('#sortable').append(input);
        });
        showForm();
    }

    //delete for form
    $('.dragdrop').on('click', '.deleteinform', function () {
        $(this).parent().parent('.ui-state-default.modal.seting-field').remove();
        itemfield--;
        changePosInput();
        showHideDragDropField(itemfield);
        showAdminMsgSaveForm();
    });

    //сохранит форму
    $('#formsinglefield').submit(function (e) {
        var data = {};
        var flag = true;
        var mindate = 0;
        var maxdate = 0;
        var maxlen = 0;
        var minlen = 0;
        var message = '';
        // console.log(this);
        $('#formsinglefield').find ('input, textarea, select').each(function() {
            if(!this.name){
                return;
            }
            else if(this.name == 'required' || this.name == 'firstEmpty'|| this.name == 'next_day' ){
                var checked = $('[name="'+this.name+'"]').is(':checked');
                if(checked) {
                    $(this).val(1);
                }
                else{
                    $(this).val(0);
                }
            }


            if(this.name == 'name'){
                if(!$(this).val()){
                    flag = false;
                    message = b_l10n_obj.fillreqfields;
                } else if($(this).val().indexOf(' ') != -1){
                    flag = false;
                    message = this.name+' '+b_l10n_obj.hasspaces;
                }
            }
            if(this.name == 'key'||this.name == 'secretkey'){
                if(!$(this).val()){
                    flag = false;
                    message = b_l10n_obj.fillreqfields;
                }
            }

            if(this.name == 'id' && $(this).val().indexOf(' ') != -1){
                flag = false;
                message = this.name+' '+b_l10n_obj.hasspaces;
            }

            if(this.name == 'min_date'){
                mindate = this.valueAsNumber;
            }
            if(this.name == 'max_date'){
                maxdate = this.valueAsNumber;
            }

            if(this.name == 'size' || this.name == 'minlength' || this.name == 'maxlength'|| this.name == 'width'){
                $(this).val($(this).val().replace(/[^\d]/g, ""));
            }

            if(this.name == 'minlength'){
                    minlen = this.value.trim()*1;
            }
            if(this.name == 'maxlength'){
                    maxlen = this.value.trim()*1;
            }

            if(this.name == 'lists' && $('#formsinglefield caption').text()=='Quiz'){
                var arr_quiz = this.value.split('\n');
                arr_quiz.forEach(function(item){
                    if(item.indexOf('|') == -1){
                        flag = false;
                        message = b_l10n_obj.optionslist+'\n'+b_l10n_obj.qapair;
                    }
                });
            }

            data[this.name] = $.trim($(this).val().replace('"',''));

        });
        if(minlen!=0 && maxlen!=0 && minlen>maxlen){
            flag = false;
            message = b_l10n_obj.minlennotmoremaxlen;
        }

        if(flag) {
            var item = $('#formsinglefield').attr('data-item-field');
            if ($('#formsinglefield').attr('data-name-field')!='recaptcha'){
                data.name = data.name.replace(/\s/g, '');
                data.id = data.id.replace(/\s/g, '');
            }

            setingField[item] = data;

            closeModal();
            showForm();
            showAdminMsgSaveForm();
            changePosInput();
        }
        else{
            alert(message);
        }
    });
// ------------------Показать-скрыть Admin message------------------------
    function closeAdminMsg(){
        $('.notice').remove();
    }

    function showAdminMsgSaveForm(){
        setTimeout(function () {
            $('.notice').remove();
            $('#pagetitle').after('<div id="message" class="notice notice-warning is-dismissible"><p>'+b_l10n_obj.uneedtosaveform+'</p><button id="my-dismiss-message" type="button" class="notice-dismiss" ><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
            $("#my-dismiss-message").click(function(event) {
                event.preventDefault();
                $('.notice').remove();
            });
        },700);
    }

    //--------------------------------

    $('input:not(.not-adm-msgs):not(.button), select, textarea').on('change', function(){
        showAdminMsgSaveForm();
    });

//-------------------показать-скрыть dragdrop поле---------------------------

    function showHideDragDropField(fieldscount){
        if (fieldscount > 0){
            $('div.dragdrop__fields__empty').addClass('dragdrop__fields__hidden');
            $('div.dragdrop__fields__empty--title').addClass('dragdrop__fields__hidden');
            $( "#sortable" ).css("padding-top", "75px");
            $( "#sortable" ).css("padding-bottom", "75px");
            $( "#sortable" ).css("max-width", "195px");
        } else if(fieldscount == 0){
            $('div.dragdrop__fields__empty').removeClass('dragdrop__fields__hidden');
            $('div.dragdrop__fields__empty--title').removeClass('dragdrop__fields__hidden');
            $( "#sortable" ).css("padding-top", "");
            $( "#sortable" ).css("padding-bottom", "");
            $( "#sortable" ).css("max-width", "");
        }
    }

//---------   перестроить массив данных формы
    var clientY = 0
    $('#sortable').on('mousedown', '.seting-field', function(e){
        clientY = e.clientY;
    });
    $('#sortable').on('mouseup', '.seting-field', function(e){
        if(clientY != e.clientY){
            changePosInput();
            showAdminMsgSaveForm();
        }
    });

    function changePosInput(){
        // console.log('change pos');
        var tmp_arr = [];

        setTimeout(function () {
            var i = 0;
            $('.seting-field').each(function(item) {
                var index = $(this).attr('data-itemfield');
                $(this).attr('data-itemfield', i);
                tmp_arr[i] = setingField[index];
                i++;
            });
            setingField = tmp_arr;
            // console.log(setingField);
            showForm();
        },700);
    }
//-------------------------------------------
    //спрячем поле следущей даты
    $('body').on('change', '[name="next_day"]', function (e) {
        if($(this).prop('checked')){
            $('[name="next_day"]').parent().parent().parent().find('[name="min_date"]').parent().parent().addClass('hidden-tr');
            var today = new Date();
            var tomorrow = new Date(today.getTime() + (24 * 60 * 60 * 1000)).toISOString().slice(0, 10);
            $('#formsinglefield input[name="min_date"]').val('');
            $('#formsinglefield input[name="max_date"]').attr('min',tomorrow);
        }
        else{
            $('body').find('tr').removeClass('hidden-tr');
            $('#formsinglefield input[name="max_date"]').attr('min','');
        }
    });

//----------------------------------------------

    $('body').on('change', '[name="min_date"]', function (e) {
        $('#formsinglefield input[name="max_date"]').attr('min',this.value);
    });

    $('body').on('change', '[name="max_date"]', function (e) {
        $('#formsinglefield input[name="min_date"]').attr('max',this.value);
    });


//----------------------------------------------
    /**
     * отрисует форму для демонстрации
     */
    function showForm(){
        // console.log('showForm');

        $('#show-form').html('');
        var form = '';
        // console.log('setingField');
        // console.log(setingField);

        setingField.forEach(function(item, i, setingField) {

            if(typeof item == 'undefined' ){
                return;
            }

            var label_style = ' style="';
            var input_style = ' style="';
            if(item.style){
                label_style += item.style+';';
                input_style += item.style+';';
            }
            if(item.width){
                label_style += 'width: '+item.width+'px;';
                input_style += 'width: '+item.width+'px;';
            }
            if(item.label_color && item.label_color != '#000000'){
                label_style += 'color: '+ item.label_color+';';
            }
            if(item.label_font_size != '0'){
                label_style += 'font-size: '+ item.label_font_size+'px;';
            }
            label_style += '" ';

            var field_align = '';
            if(item.align){
                if(item.align == 'left'){
                    field_align = ' field-left-alignment';
                }else if(item.align == 'center'){
                    field_align = ' field-center-alignment'
                }else if(item.align == 'right'){
                    field_align = ' field-right-alignment'
                }
            }

                if(item.input_color && item.input_color != '#000000'){
                input_style += 'color: '+ item.input_color+';';
            }
            if(item.input_font_size != '0'){
                input_style += 'font-size: '+ item.input_font_size+'px;';
            }
            input_style += '" ';

            form += '<div class="form-group'+field_align+'">';
            if(item.type == 'text'
                || item.type == 'email'
                || item.type == 'url'
                || item.type == 'password'
                || item.type == 'phone'){

                form += '<label '+label_style+'>';
                form += item.label;
                form += '<input '+input_style;
                form += ' type="'+item.type+'"';
                form += ' name="'+item.name+'"';
                form += ' id="'+item.id+'"';
                form += ' class="not-adm-msgs form-control '+item.class+'"';
                form += ' value="'+item.value+'"';
                form += ' placeholder="'+item.placeholder+'"';
                form += '></label>';
            }
            else if(item.type == 'checkbox'){

                if(item.label){
                    form += '<span '+label_style+'class="title">'+item.label+'</span>'
                }
                var arr_opt = item.lists.split('\n');
                arr_opt.forEach(function (val, i) {

                    form += '<label '+input_style+'>';
                    form += '<input ';
                    form += ' type="checkbox"';
                    form += ' name="'+item.name+'[]"';
                    form += ' id="'+item.id+'_'+i+'"';
                    form += ' class="not-adm-msgs checkbox '+item.class+'"';
                    form += ' value="'+val+'"';
                    var arr_checked = item.value.split('\n');
                    arr_checked.forEach(function (check, i) {
                        if(val.trim() == check.trim()){
                            form += ' checked';
                        }
                    });
                    if(val == item.value){
                        form += ' checked';
                    }
                    form += '>';
                    form += val;
                    form += '</label>';
                });
            }
            else if(item.type == 'select'){
                form += '<label '+label_style+'>';
                form += item.label;
                form += '<select ' +input_style;
                form += ' name="'+item.name+'"';
                form += ' id="'+item.id+'"';
                form += ' class="not-adm-msgs" '+item.class+'"';
                form += '>';
                if(item.firstEmpty.toString() != '0'){
                    form += '<option></option>';
                }
                var arr_opt = item.lists.split('\n');
                arr_opt.forEach(function (val) {
                    var selected = '';
                    if(val.trim() == item.value.trim()){
                        selected = ' selected ';
                    }
                   form += '<option '+selected+' value="'+val+'">'+val+'</option>';
                });
                form += '</select>';
                form += '</label>';
            }
            else if(item.type == 'radio'){

                if(item.label){
                    form += '<span '+label_style+' class="title">'+item.label+'</span>'
                }
                var arr_opt = item.lists.split('\n');
                arr_opt.forEach(function (val, i) {

                    form += '<label '+input_style+'>';
                    form += '<input ';
                    form += ' type="radio"';
                    form += ' name="'+item.name+'[]"';
                    form += ' id="'+item.id+'_'+i+'"';
                    form += ' class="not-adm-msgs checkbox '+item.class+'"';
                    form += ' value="'+val+'"';
                    if(val == item.value){
                        form += ' checked';
                    }
                    form += '>';
                    form += val;
                    form += '</label>';
                });
            }
            else if(item.type == 'textarea'){
                form += '<label '+label_style+'>';
                form += item.label;
                form += '<textarea '+input_style;
                form += ' name="'+item.name+'"';
                form += ' id="'+item.id+'"';
                form += ' class="not-adm-msgs form-control '+item.class+'"';
                form += ' placeholder="'+item.placeholder+'"';
                form += '>';
                form += item.value;
                form += '</textarea>';
                form += '</label>';
            }
            else if(item.type == 'date'){
                form += '<label '+label_style+'>';
                form += item.label;
                form += '<input '+input_style;
                form += ' type="'+item.type+'"';
                form += ' name="'+item.name+'"';
                form += ' id="'+item.id+'"';
                form += ' class="not-adm-msgs '+item.class+'"';
                form += ' min="'+item.min_date+'"';
                form += ' max="'+item.max_date+'"';
                form += ' value="'+item.value+'"';
                form += '></label>';
            }
            else if( item.type == 'submit'){
                form += '<label '+label_style+'>';
                form += item.label;
                form += '<input '+input_style;
                form += ' type="'+item.type+'"';
                form += ' name="'+item.name+'"';
                form += ' id="'+item.id+'"';
                form += ' class="not-adm-msgs form-btn '+item.class+'"';
                form += ' value="'+item.value+'"';
                form += '></label>';
            }
            else if( item.type == 'file'){
                form += '<label '+label_style+'>';
                form += item.label;
                form += '<input '+input_style;
                form += ' type="'+item.type+'"';
                form += ' name="'+item.name+'"';
                form += ' id="'+item.id+'"';
                form += ' class="not-adm-msgs '+item.class+'"';
                form += '></label>';
                if(item.extension.trim().length>0){
                    var extarr = item.extension.trim().toLowerCase().split(',');
                    extarr.forEach(function(item, i, extarr) {
                        extarr[i] = '*.'+item;
                    });
                    // console.log(extarr);
                    form += '<br><span class="description">'+b_l10n_obj.extallowed+' '+extarr.toString()+'</span>';
                }
                if(item.size>0){
                    form += '<br><span class="description">'+b_l10n_obj.sizeallowed+' '+item.size+'MB</span>';
                }

            }
            else if( item.type == 'quiz'){
                form += '<label '+label_style+'>';
                var arr_quiz = item.lists.split('\n');
                var rand_quiz = arr_quiz[Math.floor(Math.random()*arr_quiz.length)].split('|');
                // console.log(rand_quiz);
                form += rand_quiz[0];
                form += '<input '+input_style;
                form += ' type="'+item.type+'"';
                form += ' name="'+item.name+'"';
                form += ' id="'+item.id+'"';
                form += ' class="not-adm-msgs form-control '+item.class+'"';
                form += '></label>';
            }
            else if( item.type == 'recaptcha'){
                    //<!-- элемент для вывода ошибок -->
                form += '<div class="captcha-item" id="captcha_backend" data-rkey="'+item.key+'"></div>';
            }
            else{
                form += b_l10n_obj.notprocyet;
            }

            form += '</div>';
        });

        $('#show-form').append(form);
        $('#show-form').attr('style', $('textarea[name="form-setting[style]"]').text());
    }




    $('body').on('click', '.seting-field', function(){
        var new_field_flag = false;

        if(!$(this).hasClass("ui-sortable-helper")){
            showSettingField( this, new_field_flag );
            magicFormShowModal();
        }
    });

    $('body').on('click', '.addfieldinform', function(){
        var new_field_flag = true;
        var input = $(this).clone();
        $('#sortable').append(input);
        addFieldToSettingField(input);
        itemfield++;
        showHideDragDropField(itemfield);
        showSettingField( input, new_field_flag );
        magicFormShowModal();
    });

    $('body').on('click', '.resetfont', function(){
        $(this).prev().val('0');
    });

    $('body').on('click', '.resetcolor', function(){
        $(this).prev().val('#000000');
    });

    $('.modal_show_more-opt').click( function(){
        this.blur();
        if($('.showmore').is(':visible')){
            $('.showmore').hide();
            $(this).val(b_l10n_obj.showadvopt);
        } else {
            $('.showmore').show();
            $(this).val(b_l10n_obj.hideadvopt);
        }
    });

    //----------------Из кнопки Инструментов создаем кнопку Параметров--------------

    function addFieldToSettingField(field){
        $(field).addClass('modal seting-field ui-sortable-handle');
        $(field).attr('data-itemfield', itemfield);
        $(field).removeClass('addfieldinform ui-draggable ui-draggable-handle');
        $(field).find('span.addinform').html('<svg width="11px" height="11px">\n' +
            '<path fill-rule="evenodd"  fill="rgb(190, 190, 190)"\n' +
            ' d="M10.450,8.328 L7.621,5.500 L10.450,2.672 L8.328,0.550 L5.500,3.379 L2.672,0.550 L0.550,2.672 L3.379,5.500 L0.550,8.328 L2.672,10.450 L5.500,7.621 L8.328,10.450 L10.450,8.328 Z"/>\n' +
            '</svg>');
        $(field).find('span.addinform').addClass('deleteinform').removeClass('addinform');
    }

    function showSettingField( field, flag ) {
        var item = $(field).attr('data-itemfield');
        if($(field).attr('data-name')=='recaptcha'){
            $('.modal_show_more-opt').hide();
            }else{
                $('.modal_show_more-opt').show();
            }

        var out = '<table><caption><h3>'+title[$(field).attr('data-name')]+'</h3></caption>';
        var current_field;

        current_field = setObject(field);

        for (var key in current_field) {

            if((typeof setingField[item] != 'undefined')&&(key in setingField[item])){
                current_field[key] = setingField[item][key];
            }

            var input = '';
            var showmore = ' class="showmore"';
            var arr_text = ['id','name','style','class', 'regExp', 'label', 'placeholder', 'title', 'key', 'secretkey'];
            var arr_values = ['text', 'email', 'phone', 'url', 'textarea'];
            if(arr_text.indexOf(key) != -1){
                var helper = '';
                if(key == 'label'){
                    showmore = '';
                    helper = b_l10n_obj.fieldlable;
                }else if(key == 'placeholder'){
                    showmore = '';
                    helper = b_l10n_obj.fieldplaceholder;
                }else if(key=='key'){
                    showmore = '';

                }else if(key=='secretkey'){
                    showmore = '';
                    helper = '<a href="http://www.google.com/recaptcha/admin">'+b_l10n_obj.link_get_keys+'</a>';

                }else if(key == 'regExp'){
                    helper = b_l10n_obj.fieldregexp;
                }else if(key == 'name' && current_field['type']!='submit'){
                    helper = b_l10n_obj.fieldname;
                }
                input = '<input name="'+key+'" type="text" value="'+current_field[key]+'">';
                input += '<br><span class="description">'+helper+'</span>';
            }
            else if(key == 'value'){
                showmore = '';
                var helper = '';
                if(current_field['type'] == 'textarea' || current_field['type'] == 'checkbox'){
                    input = '<textarea name="'+key+'">'+current_field[key]+'</textarea>'
                }
                else{
                    input = '<input name="'+key+'" type="text" value="'+current_field[key]+'">';
                }
                if(arr_values.indexOf(current_field['type']) != -1){
                    helper = b_l10n_obj.fieldval;
                } else if(current_field['type'] == 'submit'){
                    helper= b_l10n_obj.buttonsign;
                } else if(current_field['type'] == 'select'){
                    helper= b_l10n_obj.selval;
                } else if(current_field['type'] == 'checkbox'){
                    helper= b_l10n_obj.checkboxvalues;
                } else if(current_field['type'] == 'radio'){
                    helper= b_l10n_obj.radiovalue;
                }

                input += '<br><span class="description">'+helper+'</span>';
            }
            else if(key == 'required' || key == 'firstEmpty' || key == 'next_day'){
                showmore = '';
                var selected = '';
                if( current_field[key].toString() != '0'  ){
                    selected = ' checked="checked"';
                }
                input = '<input name="'+key+'" type="checkbox"'+selected+' value="'+current_field[key]+'">';
                if(key == 'next_day'){
                    input += '<br><span class="description">'+b_l10n_obj.nextday+'</span>';
                }
            }
            else if( key == 'type'){
                input = '<input name="'+key+'" type="hidden" value="'+current_field[key]+'">';
                key = '';
            }
            else if(key == 'lists'){
                showmore = '';
                var helper = '';
                if(current_field['type'] == 'quiz'){
                    helper = '<br>'+b_l10n_obj.qapair;
                }
                input = '<textarea name="'+key+'">'+current_field[key]+'</textarea>';
                input += '<br><span class="description">'+b_l10n_obj.optionslist+helper+'</span>';
            }
            else if(key == 'min_date' || key == 'max_date'){
                showmore = '';
                input = '<input name="'+key+'" type="date" value="'+current_field[key]+'">';
                var helper = '';
                if(key == 'min_date'){
                    helper= b_l10n_obj.mindatefield;
                }else{
                    helper= b_l10n_obj.maxdatefield;
                }
                input += '<br><span class="description">'+helper+'</span>';
            }
            else if(key == 'label_font_size' || key == 'input_font_size'){
                var arr_size = [0,8,9,10,11,12,14,16,18,20,22,24,26,28,36,48,72];

                input = '<select name="'+key+'">';
                arr_size.forEach(function(val, i) {
                    var select = '';
                    var size = val;
                    if(size == 0){
                        size = b_l10n_obj.themefont;
                    }
                    if(current_field[key] == val){
                        select = ' selected';
                    }
                    input += '<option value="'+val+'"'+select+'>'+size+'</option>';
                });

                input += '</select><span class="resetfont" title="'+b_l10n_obj.todefault+'"> &times;</span>';
            }
            else if(key == 'input_color' || key == 'label_color'){
                input = '<input name="'+key+'" type="color" value="'+current_field[key]+'"><span class="resetcolor" title="'+b_l10n_obj.todefault+'"> &times;</span>';
            }
            else if(key == 'extension'){
                showmore = '';
                input = '<input name="'+key+'" type="text" value="'+current_field[key]+'">';
                input += '<br><span class="description">'+b_l10n_obj.filextlist+'</span>';
            }
            else if(key == 'size'){
                showmore = '';
                input = '<input name="'+key+'" type="text" value="'+current_field[key]+'">';
                input += '<br><span class="description">'+b_l10n_obj.setmaxfilsize+'</span>';
            }
            else if(key == 'minlength' || key == 'maxlength'){
                input = '<input name="'+key+'" type="text" value="'+current_field[key]+'">';
                var helper = '';
                if(key == 'minlength'){
                    helper= b_l10n_obj.minlengthfield;
                }else{
                    helper= b_l10n_obj.maxlengthfield;
                }
                input += '<br><span class="description">'+helper+'</span>';
            }
            else if (key == 'width'){
                input = '<input name="'+key+'" type="text" value="'+current_field[key]+'">';
            }
            else if (key == 'align'){
                var arr_align = ['left', 'center', 'right'];

                input = '<select name="'+key+'">';
                arr_align.forEach(function(val, i) {
                    var select = '';
                    var align = val;
                    if(val == 'left'){
                        align = b_l10n_obj.align_left;
                    } else if (val == 'center'){
                        align = b_l10n_obj.align_center;
                    } else {
                        align = b_l10n_obj.align_right;
                    }

                    if(current_field[key] == val){
                        select = ' selected';
                    }
                    input += '<option value="'+val+'"'+select+'>'+align+'</option>';
                });

                input += '</select>';
            }
            else{
                input = b_l10n_obj.onemoreprop+'<input name="'+key+'" type="text" value="'+current_field[key]+'">';
            }

            var requered = '';
            if(key == 'name'|| key=='key'|| key=='secretkey'){
                requered = ' <span class="red">*</span>';
            }

            var fieldname='';

            switch (key) {
                case 'label':
                    fieldname=b_l10n_obj.namefield+' ('+key+')';
                    break;
                case 'label_font_size':
                    if(current_field['type'] == 'quiz'){
                        fieldname = b_l10n_obj.questionfontsize;
                    } else {
                        fieldname= b_l10n_obj.labelfontsize;
                    }
                    break;
                case 'label_color':
                    if(current_field['type'] == 'quiz'){
                        fieldname = b_l10n_obj.questionfontcolor;
                    } else {
                        fieldname= b_l10n_obj.labelfontcolor;
                    }
                    break;
                case 'input_font_size':
                    if(current_field['type'] == 'checkbox' || current_field['type'] == 'radio' || current_field['type'] == 'select'){
                        fieldname = b_l10n_obj.listsfontsize;
                    } else if(current_field['type'] == 'submit'){
                        fieldname = b_l10n_obj.buttonfontsize;
                    } else {
                        fieldname= b_l10n_obj.inputfontsize;
                    }
                    break;
                case 'input_color':
                    if(current_field['type'] == 'checkbox' || current_field['type'] == 'radio' || current_field['type'] == 'select'){
                        fieldname = b_l10n_obj.listsfontcolor;
                    } else if(current_field['type'] == 'submit'){
                        fieldname = b_l10n_obj.buttonfontcolor;
                    } else  {
                        fieldname= b_l10n_obj.inputfontcolor;
                    }
                    break;
                case 'minlength':
                    fieldname= b_l10n_obj.minlength;
                    break;
                case 'maxlength':
                    fieldname= b_l10n_obj.maxlength;
                    break;
                case 'min_date':
                    fieldname= b_l10n_obj.mindate;
                    break;
                case 'max_date':
                    fieldname= b_l10n_obj.maxdate;
                    break;
                case 'lists':
                    fieldname= b_l10n_obj.lists;
                    break;
                case 'size':
                    fieldname= b_l10n_obj.filesize;
                    break;
                case 'extension':
                    fieldname= b_l10n_obj.fileextensions;
                    break;
                case 'width':
                    fieldname= b_l10n_obj.field_width;
                    break;
                case 'align':
                    fieldname= b_l10n_obj.field_align;
                    break;
                case 'key':
                    fieldname= b_l10n_obj.recaptcha_key;
                    break;
                case 'secretkey':
                    fieldname= b_l10n_obj.recaptcha_secretkey;
                    break;
                default:
                    fieldname = key;
            }
            out += '<tr'+showmore+'><td>'+fieldname+requered+'</td><td>'+input+'</td></tr>';
        }

        out += '</table>';
        $('#contentmodal').html(out);
        $('#save-button').show();

        $('#formsinglefield').attr('data-item-field', item);
        $('#formsinglefield input[name="max_date"]').attr('min',$('#formsinglefield input[name="min_date"]').val());
        $('#formsinglefield input[name="min_date"]').attr('max',$('#formsinglefield input[name="max_date"]').val());
        if(flag){
            var field_name = $(field).attr('data-name');
            $('#formsinglefield').attr('data-name-field', field_name);
            if($('#formsinglefield').attr('data-name-field') != 'recaptcha'){
                $('#formsinglefield input[name="name"]').val(Math.random().toString(16).slice(2));
            }

            $('#formsinglefield input[name="id"]').val("id_"+(new Date()).getTime());
        }
        $('[name="next_day"]').trigger('change');
    }

    /* Зaкрытие мoдaльнoгo oкнa, тут делaем тo же сaмoе нo в oбрaтнoм пoрядке */
    $('.modal_close, #overlay, .modal_cancel').click( function(){ // лoвим клик пo крестику или пoдлoжке
        if(setingField.length<itemfield){
            itemfield--;
            $("li[data-itemfield='"+itemfield+"']").remove();
            showHideDragDropField(itemfield);
            changePosInput();
        }
        closeModal();
    });

    function closeModal() {
        $('#modal_form')
            .animate({opacity: 0, top: '27%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
                function(){ // пoсле aнимaции
                    $(this).css('display', 'none'); // делaем ему display: none;
                    $('#overlay').fadeOut(400); // скрывaем пoдлoжку
                }
            );
        $('.modal_show_more-opt').val(b_l10n_obj.showadvopt);
        jQuery('#modal_form').css({'width' : 500,  'margin-left': '-250px'});
    }


    function setObject(field) {
        var type = $(field).attr('data-name');
        var attrFields = {};
        switch (type) {
            case 'text':
                attrFields = {label:'', value:'', placeholder:'', required:0, minlength:0, maxlength:0, label_font_size:0, label_color:'', input_font_size:0, input_color:'', id:'', name:'', width:'', style:'',class:'', regExp:'', type: 'text' };
                break;
            case 'checkbox':
                attrFields = {label:'', value:'', lists:'', required:0, label_font_size:0, label_color:'', input_font_size:0, input_color:'',id:'', name:'', style:'',class:'', type: 'checkbox'};
                break;
            case 'select':
                attrFields = {label:'', value:'', required:0, lists:'', firstEmpty:0, label_font_size:0, label_color:'', input_font_size:0, input_color:'',id:'', name:'', width:'', style:'',class:'', type: 'select'};
                break;
            case 'radio':
                attrFields = {label:'', value:'', lists:'',required:0,  label_font_size:0, label_color:'', input_font_size:0, input_color:'',id:'', name:'', style:'',class:'', type: 'radio'};
                break;
            case 'textarea':
                attrFields = {label:'', value:'', placeholder:'', required:0,  minlength:0, maxlength:0, label_font_size:0, label_color:'', input_font_size:0, input_color:'',id:'', name:'', width:'', style:'',class:'', regExp:'', type: 'textarea'};
                break;
            case 'email':
                attrFields = {label:'', value:'', placeholder:'', required:0, minlength:0, maxlength:0, label_font_size:0, label_color:'', input_font_size:0, input_color:'',id:'', name:'', width:'', style:'',class:'', regExp:'', type: 'email'};
                break;
            case 'phone':
                attrFields = {label:'', value:'', placeholder:'', required:0, minlength:0, maxlength:0, label_font_size:0, label_color:'', input_font_size:0, input_color:'',id:'', name:'', width:'', style:'',class:'', regExp:'', type: 'phone'};
                break;
            case 'password':
                attrFields = {label:'', required:0, minlength:0, maxlength:0, label_font_size:0, label_color:'', input_font_size:0, input_color:'',id:'', name:'', width:'', style:'',class:'', type: 'password'};
                break;
            case 'url':
                attrFields = {label:'', value:'', placeholder:'', required:0, minlength:0, maxlength:0, label_font_size:0, label_color:'', input_font_size:0, input_color:'',id:'', name:'', width:'', style:'',class:'', regExp:'', type: 'url'};
                break;
            case 'date':
                attrFields = {label:'', next_day:0, min_date:'', max_date:'', required:0, label_font_size:0, label_color:'', input_font_size:0, input_color:'',id:'', name:'', width:'', style:'',class:'', type: 'date'};
                break;
            case 'file':
                attrFields = {label:'', extension:'', size:0, required:0, label_font_size:0, label_color:'', input_font_size:0, input_color:'',id:'', name:'', style:'',class:'', type: 'file'};
                break;
            case 'submit':
                attrFields = {label:'', value:'send', label_font_size:0, label_color:'', input_font_size:0, input_color:'',id:'', name:'', width:'', align:'left', style:'',class:'', type: 'submit'};
                break;
            case 'quiz':
                attrFields = {lists:'', required:1, label_font_size:0, label_color:'', input_font_size:0, input_color:'', id:'', name:'', width:'', style:'',class:'', type: 'quiz'};
                break;


            case 'recaptcha':
                attrFields = {key:'', secretkey:'',name:'g-recaptcha-response', type: 'recaptcha'};
                break;

            case 'Conditional field':
                alert( b_l10n_obj.notenough );
                break;

            default:
                alert( b_l10n_obj.idntknow );
        }

        return attrFields;
    }

});

var magicFormRecaptchaCallback = function() {
    var siteKey = '[[++recaptchav2.site_key]]';
    jQuery('.captcha-item').each(function(index) {
        grecaptcha.render(jQuery(this).attr('id'), {
            'sitekey' : jQuery(this).attr('data-rkey')
        });
    });
};

//--------modal --------------------------
function magicFormShowModal(){
    jQuery('#overlay').fadeIn(400, // снaчaлa плaвнo пoкaзывaем темную пoдлoжку
        function(){ // пoсле выпoлнения предъидущей aнимaции
            jQuery('#modal_form')
                .css('display', 'block') // убирaем у мoдaльнoгo oкнa display: none;
                .animate({opacity: 1, top: '27%'}, 200); // плaвнo прибaвляем прoзрaчнoсть oднoвременнo сo съезжaнием вниз
        });
}

function magicFormShowModalAfterSaveForm(data){
    var data = '<p>'+b_l10n_obj.modal_your_shortcode+' <b>['+window.shortcode_for_popup+']</b></p>' +
        '<p>'+b_l10n_obj.modal_you_should_put_it+'</p>' +
        '<p><img class="modal-img" src="'+b_l10n_obj.modal_img_path+'" ></p>' +
        '<p>'+b_l10n_obj.modal_and_save_it+'</p>'
    jQuery('#save-button').hide();
    jQuery('#contentmodal').html(data);
    jQuery('#modal_form').css({'width' : 900,  'margin-left': '-450px'});
    ;
    magicFormShowModal();

}