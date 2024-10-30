<?php

class Magic_FormsLogs
{
    private static $instance = null;
    public static $table_log = null;
    public static $table_options = null;

    public static function getInstance()
    {
        if (null === self::$instance)
        {
            global $wpdb;
            self::$instance = new self();
            self::$table_log = $wpdb->get_blog_prefix() . 'magic_forms_logs';
            self::$table_options = $wpdb->get_blog_prefix() . 'options';
        }
        return self::$instance;
    }
    private function __construct() {}
    private function __clone() {}

    public function getLogDetail($id){
        global $wpdb;
        $out = '<h2>'.__('Данные формы', 'wpcftr').'</h2>';
        $log = $wpdb->get_row('SELECT * FROM `'.self::$table_log.'` WHERE `id`= '.$id);
        $log->fields = unserialize($log->fields);
        $log->errors = unserialize($log->errors);

        if(is_array($log->fields)){
            foreach( $log->fields as $key => $value ){
                $out .= '<div>';
                $out .= $key . ' = ' . ( is_array( $value ) ? implode( ' | ', $value ) : $value );
                $out .= '</div>';
            }
        }
        if(is_array($log->errors)){
            $out .= '<h2>'.__('Ошибки формы', 'wpcftr').'</h2>';
            foreach( $log->errors as $key => $value ){
                $out .= '<div>';
                $out .= $key . ' : ' .( is_array( $value ) ? implode( ' | ', $value ) : $value );
                $out .= '</div>';
            }
        }
        return $out;
    }

    public function getLogs($id, $ofset = 0, $limit = 10 ){
        global $wpdb;
        $logs = $wpdb->get_results('SELECT * FROM `'.self::$table_log.'` 
                                    WHERE `form_id` = '.$id.'
                                    ORDER BY `send_date` DESC
                                    LIMIT '.$ofset.', '.$limit);

        $out['html'] = self::getHtml($logs, $ofset);
//        $out['more'] = (!empty($logs))? true : false;
        $out['more'] = (count($logs)==10)? true : false;
        $out['logs'] = $logs;
        return $out;
    }

    public function getLogsEmail( $email = '', $ofset = 0, $limit = 10 ){
        global $wpdb;
        $logs = $wpdb->get_results('SELECT * FROM `'.self::$table_log.'` 
                                    WHERE `user_email` = "'.$email.'"
                                    ORDER BY `send_date` DESC
                                    LIMIT '.$ofset.', '.$limit);

        $out['html'] = self::getHtml($logs, $ofset);
//        $out['more'] = (!empty($logs))? true : false;
        $out['more'] = (count($logs)==10)? true : false;
        $out['logs'] = $logs;
        return $out;
    }

    public function exportLogs($email){

        global $wpdb;
        $logs = $wpdb->get_results('SELECT * FROM `'.self::$table_log.'` 
                                    WHERE `user_email` = "'.$email.'"
                                    ORDER BY `send_date` DESC');
        $array_logs= array(
            array(
                __('Дата отправки','wpcftr'),
                __('Данные формы','wpcftr'),
            ),
        );
        foreach($logs as $log){
            array_push($array_logs, array(date(self::getDateTimeFormat(), strtotime($log->send_date)), $log->fields));
        }
        $upload = wp_upload_dir();
        $uploaddir = $upload['basedir'];
        $uploaddir = $uploaddir . '/magic-form';
        if (!is_dir($uploaddir)) {
            mkdir($uploaddir, 0777);
        }
        $file = $uploaddir.'/'.$email.'.csv';
        $filepath = content_url( 'uploads/magic-form' ).'/'.$email.'.csv';
        $col_delimiter = ';';
        $row_delimiter = "\r\n";

        if( ! is_array($array_logs) )
            return false;

        if( $file && ! is_dir( dirname($file) ) )
            return false;

        // строка, которая будет записана в csv файл
        $CSV_str = '';

        // перебираем все данные
        foreach( $array_logs as $row ){
            $cols = array();

            foreach( $row as $col_val ){
                // строки должны быть в кавычках ""
                // кавычки " внутри строк нужно предварить такой же кавычкой "
                if( $col_val && preg_match('/[",;\r\n]/', $col_val) ){
                    // поправим перенос строки
                    if( $row_delimiter === "\r\n" ){
                        $col_val = str_replace( "\r\n", '\n', $col_val );
                        $col_val = str_replace( "\r", '', $col_val );
                    }
                    elseif( $row_delimiter === "\n" ){
                        $col_val = str_replace( "\n", '\r', $col_val );
                        $col_val = str_replace( "\r\r", '\r', $col_val );
                    }

                    $col_val = str_replace( '"', '""', $col_val ); // предваряем "
                    $col_val = '"'. $col_val .'"'; // обрамляем в "
                }

                $cols[] = $col_val; // добавляем колонку в данные
            }

            $CSV_str .= implode( $col_delimiter, $cols ) . $row_delimiter; // добавляем строку в данные
        }

        $CSV_str = rtrim( $CSV_str, $row_delimiter );

        // задаем кодировку windows-1251 для строки
        if( $file ){
            $CSV_str = iconv( "UTF-8", "cp1251",  $CSV_str );

            // создаем csv файл и записываем в него строку
            $done = file_put_contents( $file, $CSV_str );

            return $filepath;
        }

        return $CSV_str;

    }

    public function deleteLogs($email){
        global $wpdb;
        $rows = $wpdb->delete( self::$table_log, array( 'user_email' => $email) );
        return $rows;

    }

    private function getHtml($logs, $ofset= 0){
$i = 1+$ofset;
        $out = '';
        foreach($logs as $item){
            $out .= '<tr>';
            $out .= '<td>'.$i.'</td>';
            $out .= '<td>'.$item->user_ip.'</td>';
            $out .= '<td>'.date(self::getDateTimeFormat(), strtotime($item->send_date)).'</td>';
            $out .= '<td>'.$item->user_email.'</td>';
            $out .= '<td>'.$item->letter_theme.'</td>';
            $out .= '<td><input type="button" data-id="'.$item->id.'" class="show-detail-log" value="'.__('Подробней', 'wpcftr').'>>"></td>';
            $out .= '</tr>';
            $i++;
        }
        return $out;
    }

    public function addLog($item){
        global $wpdb;
        $data = [];
        $data['send_date'] = date('Y-m-d H:i:s');
        $data['form_id'] = $item['form_id'];
        $data['fields'] = $item['fields'];
        $data['errors'] = $item['errors'];
        $data['user_ip'] = ( !empty($item['user_ip']) ) ? $item['user_ip'] : '';
        $data['user_email'] = ( !empty($item['user_email']) ) ? $item['user_email'] : '';
        $data['letter_theme'] = ( !empty($item['letter_theme']) ) ? $item['letter_theme'] : '';

        if(is_array($data['fields'])){
            $data['fields'] = serialize($data['fields']);
        }
        if(is_array($data['errors'])){
            $data['errors'] = serialize($data['errors']);
        }

        $wpdb->insert(self::$table_log, $data);
    }

    public function getDateTimeFormat(){
        $dateformat = get_option('date_format');
        $timeformat = get_option('time_format');
        return $dateformat.' '.$timeformat;
    }


}