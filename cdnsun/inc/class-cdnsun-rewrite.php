<?php

class CDNsun_Rewrite
{
    private $origin_url     = null;    
    private $cdn_url        = null;    
    private $includes       = [];    
    private $excludes       = []; 
    
    public function __construct($origin_url, $cdn_url, array $includes, array $excludes) 
    {
        $this->origin_url       = $origin_url;
        $this->cdn_url          = $cdn_url;
        $this->includes         = $includes;
        $this->excludes         = $excludes;       
    }
    
    public function start() 
    {
        ob_start([$this,'rewrite']);
    }

    private function rewrite($html) 
    {              
        if(empty($html))
        {
            return $html;
        }
        
        $includes_regex =   implode('|', array_map('quotemeta', $this->includes));
        $origin_url     =   '(https?:|)' . quotemeta($this->get_double_slash_url($this->origin_url));            
        $regex_rule     =   '#(?<=[(\"\'])(?:' . 
                            $origin_url . 
                            ')?/(?:((?:' . 
                            $includes_regex . 
                            ')[^\"\')]+)|([^/\"\']+\.[^/\"\')]+))(?=[\"\')])#';
                                
        return preg_replace_callback($regex_rule, [&$this, 'rewrite_url'], $html);
    }
        
    private function rewrite_url(&$asset) 
    {                      
        if(empty($asset[0]))
        {
            return $asset[0];
        }
        if($this->is_url_excluded($asset[0])) 
        {
            return $asset[0];
        }
        
        // eg: //origin.com/file
        $origin_url_double_slash    = $this->get_double_slash_url($this->origin_url);
        $origin_url_https           = 'https:'   . $origin_url_double_slash;        
        $origin_url_http            = 'http:'    . $origin_url_double_slash;
        
        // rewrite everything to the cdn_url
        if(strstr($asset[0], $origin_url_https)) // eg: https://origin.com/file -> https://cdn.com/file
        {
            return str_replace($origin_url_https, $this->cdn_url, $asset[0]);
        }        
        elseif(strstr($asset[0], $origin_url_http)) // eg: http://origin.com/file -> https://cdn.com/file
        {
            return str_replace($origin_url_http, $this->cdn_url, $asset[0]);
        }
        elseif(strstr($asset[0], $origin_url_double_slash)) // eg: //origin.com/file -> https://cdn.com/file
        {
            return str_replace($origin_url_double_slash, $this->cdn_url, $asset[0]);
        }        
        else // eg: /file -> https://cdn.com/file
        {
            return $this->cdn_url . $asset[0];
        }                  
    }
    
    private function is_url_excluded(&$asset) 
    {
        foreach($this->excludes as $exclude) 
        {
            if(!empty($exclude) && stristr($asset, $exclude) != false) 
            {
                return true;
            }
        }
        return false;
    }
 
    // http://domain.com    -> //domain.com
    // https://domain.com   -> //domain.com
    private function get_double_slash_url($url) 
    {
        return substr($url, strpos($url, '//'));
    }    
}
