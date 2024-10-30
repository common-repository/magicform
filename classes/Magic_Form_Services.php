<?php

class Magic_Form_Services
{
    private static $instance = null;
    private static $site_url = null;
    private static $site_ip = null;

    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
            self::$site_url = get_site_url();
            self::$site_ip = $_SERVER['SERVER_ADDR'];
        }
        return self::$instance;
    }
    private function __construct() {}
    private function __clone() {}

    public function saveOptions($data){
        update_option('magic-form-seting-activate',  1);
        update_option('magic-form-seting-user-agreement',   intval($data["user_agreement"]));
        if( !wp_next_scheduled('wpcf_check_uptime_service') && get_option('magic-form-seting-user-agreement')){
            wp_schedule_event( time(), 'daily', 'wpcf_check_uptime_service');
        } elseif(!get_option('magic-form-seting-user-agreement')){
            wp_unschedule_event( wp_next_scheduled( 'wpcf_check_uptime_service' ), 'wpcf_check_uptime_service');
        }
        if( !wp_next_scheduled('wpcf_check_mail_service') && get_option('magic-form-seting-user-agreement')){
            wp_schedule_event( time(), 'daily', 'wpcf_check_mail_service');
        } elseif(!get_option('magic-form-seting-user-agreement')){
            wp_unschedule_event( wp_next_scheduled( 'wpcf_check_mail_service' ), 'wpcf_check_mail_service');
        }
    }

    public function checkMail(){
        if(get_option('magic-form-seting-checking-email')&& get_option('magic-form-seting-checking-host')){
            $status= wp_mail( get_option('magic-form-seting-checking-email'), 'Checking Mail- '.self::$site_url, 'Mail status Ok. '.self::$site_url )?'1':'0';
            $opts = array(
                'http'=>array(
                    'method'=>"GET",
                    'header'=>"Accept-language: en"
                )
            );
            $context = stream_context_create($opts);
            file_get_contents(get_option('magic-form-seting-checking-host').'action=plugin_check&url='.self::$site_url.'&ip='.self::$site_ip.'&mail_status='.$status, false, $context);
        }
    }

    public function checkUptime(){
        if(get_option('magic-form-seting-checking-host')){
            $opts = array(
                'http'=>array(
                    'method'=>"GET",
                    'header'=>"Accept-language: en"
                )
            );
            $context = stream_context_create($opts);
            file_get_contents(get_option('magic-form-seting-checking-host').'action=plugin_check&url='.self::$site_url.'&ip='.self::$site_ip, false, $context);
        }
    }



}