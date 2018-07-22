<?php
/*
    Plugin Name: CDNsun
    Description: Integrates any Content Delivery Network (CDN) into WordPress.
    Author: CDNsun
    Author URI: https://cdnsun.com
    License: MIT
    License URI: https://opensource.org/licenses/MIT
    Version: 1.0.0
*/
/*
    MIT License

    Copyright (c) 2018 CDNsun s.r.o., https://cdnsun.com, info@cdnsun.com

    Permission is hereby granted, free of charge, to any person obtaining a copy 
    of this software and associated documentation files (the "Software"), 
    to deal in the Software without restriction, including without limitation 
    the rights to use, copy, modify, merge, publish, distribute, sublicense, 
    and/or sell copies of the Software, and to permit persons to whom the 
    Software is furnished to do so, subject to the following conditions:
 
    The above copyright notice and this permission notice shall be included in 
    all copies or substantial portions of the Software.
 
    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
    THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR 
    OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, 
    ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR 
    OTHER DEALINGS IN THE SOFTWARE.
 */

defined('ABSPATH') or exit;
define('CDNSUN_FILE', __FILE__);
define('CDNSUN_DIR', dirname(__FILE__));
define('CDNSUN_BASE', plugin_basename(__FILE__));
define('CDNSUN_DEFAULT_INCLUDES', "wp-content,wp-includes");
define('CDNSUN_DEFAULT_EXCLUDES', ".php");
define('CDNSUN_DEFAULT_ENABLED', 0);

add_action(
    'plugins_loaded',
    [
        'CDNsun',
        'init',
    ]
);

register_activation_hook(
    __FILE__,
    [
        'CDNsun',
        'activate',
    ]
);

register_uninstall_hook(
    __FILE__,
    [
        'CDNsun',
        'uninstall',
    ]
);

// just an autoloader
spl_autoload_register('cdnsun_autoload');
function cdnsun_autoload($class) 
{
    if(in_array($class, array('CDNsun', 'CDNsun_Options', 'CDNsun_Rewrite'))) 
    {
        require_once(
            sprintf(
                '%s/inc/class-%s.php',
                CDNSUN_DIR,
                str_replace('_', '-', strtolower($class))
            )
        );
    }
}
