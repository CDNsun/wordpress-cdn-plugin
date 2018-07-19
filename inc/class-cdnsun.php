<?php

class CDNsun
{
    public function __construct() 
    {
        add_action(
            'template_redirect',
            [
                __CLASS__,
                'rewrite',
            ]
        );        
        add_action(
            'admin_init',
            [
                'CDNsun_Options',
                'init',
            ]
        );
        add_action(
            'admin_menu',
            [
                'CDNsun_Options',
                'add_options_page',
            ]
        );   
        add_filter(
            'plugin_action_links_' . CDNSUN_BASE,
            [
                'CDNsun_Options',
                'add_options_page_link',
            ]
        );  
    }  
    
    public static function init() 
    {
        new self();
    }
                            
    public static function get_options() 
    {
        return wp_parse_args(
            get_option('cdnsun'),
            [
                'cdn_url'             => get_option('home'),
                'includes'            => CDNSUN_DEFAULT_INCLUDES,
                'excludes'            => CDNSUN_DEFAULT_EXCLUDES,               
                'enabled'             => CDNSUN_DEFAULT_ENABLED,
            ]
        );
    }
    
    public static function activate() 
    {
        add_option(
            'cdnsun',
            [
                'cdn_url'            => get_option('home'),
                'includes'           => CDNSUN_DEFAULT_INCLUDES,
                'excludes'           => CDNSUN_DEFAULT_EXCLUDES,
                'enabled'            => CDNSUN_DEFAULT_ENABLED,
            ]
        );
    }   

    public static function rewrite() 
    {
        $options = self::get_options();

        if(empty($options['enabled'])) 
        {
            return true;
        }  
        if(empty($options['cdn_url'])) 
        {
            return true;
        } 
        if($options['cdn_url'] == get_option('home')) 
        {
            return true;
        } 
        if(empty($options['includes']))
        {
            $options['includes'] = CDNSUN_DEFAULT_INCLUDES;
        }
        if(empty($options['excludes']))
        {
            $options['excludes'] = CDNSUN_DEFAULT_EXCLUDES;
        }
        
        $includes = array_map('trim', explode(',', $options['includes']));
        $excludes = array_map('trim', explode(',', $options['excludes']));
        
        $rewrite = new CDNsun_Rewrite(
            get_option('home'),                
            $options['cdn_url'],
            $includes,
            $excludes              
        );        
        $rewrite->start();
    }        
    
    public static function uninstall() 
    {
        delete_option('cdnsun');
    }
}
