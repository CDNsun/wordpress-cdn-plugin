<?php

class CDNsun_Options
{
    public static function init()
    {
        register_setting(
            'cdnsun',
            'cdnsun',
            [
                __CLASS__,
                'validate',
            ]
        );
    }

    public static function validate($data)
    {        
        return  [
                    'cdn_url'         => esc_url($data['cdn_url']),
                    'includes'        => esc_attr($data['includes']),
                    'excludes'        => esc_attr($data['excludes']),   
                    'enabled'         => !empty($data['enabled']) ? 1 : 0,
                ];
    }

    public static function add_options_page()
    {
        $page = add_options_page(
            'CDNsun',
            'CDNsun',
            'manage_options',
            'cdnsun',
            [
                __CLASS__,
                'display_options_page',
            ]
        );
    }
    
    // in (array) $data here are links on this page: /wp-admin/plugins.php for this plugin, such as "Deactivate"
    public static function add_options_page_link($data) 
    {        
        if(!current_user_can('manage_options')) 
        {            
            return $data;
        }

        return array_merge(
            $data,
            [
                sprintf(
                    '<a href="%s">%s</a>',
                    add_query_arg(
                        [
                            'page' => 'cdnsun',
                        ],
                        admin_url('options-general.php')
                    ),
                    __("Settings")
                ),
            ]
        );
    }

    public static function display_options_page()
    { 
        $options            = CDNsun::get_options();
        $enabled_checked    = checked(1, $options['enabled'], false);
        $default_includes   = CDNSUN_DEFAULT_INCLUDES;
        $default_excludes   = CDNSUN_DEFAULT_EXCLUDES;
        
        $html =<<<EOT
        <div class="wrap">
           <h2>
               CDNsun Options
           </h2>        
           <div class="notice notice-info">
               <p>
                   This plugin integrates any Content Delivery Network (CDN) into WordPress. 
                   Provided by <b><a target="_blank" href="https://cdnsun.com/knowledgebase/integrations/wordpress-cdn-integration">CDNsun</a></b>.
               </p>
           </div>     
           <form method="post" action="options.php">
EOT;
        echo $html;
        
        settings_fields('cdnsun');
        
        $html =<<<EOT
               <table class="form-table">
                   <tr valign="top">
                       <th scope="row">
                           CDN URL
                       </th>
                       <td>
                           <fieldset>
                               <label for="cdnsun_cdn_url">
                                   <input type="text" name="cdnsun[cdn_url]" id="cdnsun_cdn_url" value="{$options['cdn_url']}" size="64" class="regular-text code" />
                               </label>
                               <p class="description">
                                   Enter the CDN URL, it has to start with https:// or http://
                               </p>
                           </fieldset>
                       </td>
                   </tr>
                   <tr valign="top">
                       <th scope="row">
                           Include Directories
                       </th>
                       <td>
                           <fieldset>
                               <label for="cdnsun_includes">
                                   <input type="text" name="cdnsun[includes]" id="cdnsun_includes" value="{$options['includes']}" size="64" class="regular-text code" />                                   
                               </label>
                               <p class="description">
                                   Enter directories to include, separated by <code>,</code><br/>
                                   Default: <code>{$default_includes}</code><br/>
                                   URLs of assets in include directories will be replaced with the CDN URL<br/>
                               </p>
                           </fieldset>
                       </td>
                   </tr>
                   <tr valign="top">
                       <th scope="row">
                           Exclude Phrases
                       </th>
                       <td>
                           <fieldset>
                               <label for="cdnsun_excludes">
                                   <input type="text" name="cdnsun[excludes]" id="cdnsun_excludes" value="{$options['excludes']}" size="64" class="regular-text code" />                                   
                               </label>
                               <p class="description">
                                   Enter phrases to exclude, separated by <code>,</code><br/>
                                   Default: <code>{$default_excludes}</code><br/>
                                   URLs containing at least one exclude phrase will not be replaced
                               </p>
                           </fieldset>
                       </td>
                   </tr>                         
                   <tr valign="top">
                       <th scope="row">
                           Plugin Enabled
                       </th>
                       <td>
                           <fieldset>
                               <label for="cdnsun_enabled">
                                   <input type="checkbox" name="cdnsun[enabled]" id="cdnsun_enabled" value="1" {$enabled_checked} />                                   
                               </label>
                               <p class="description">
                                   Whether the plugin is enabled or disabled
                               </p>                                      
                           </fieldset>
                       </td>
                   </tr>                
               </table>                                   
EOT;
        echo $html;
        
        submit_button();          
        
        $html =<<<EOT
           </form>
        </div>
EOT;
        echo $html;
    }        
}
