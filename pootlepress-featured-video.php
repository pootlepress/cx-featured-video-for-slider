<?php
/*
Plugin Name: Canvas Extension - Featured Video for Business Slider
Plugin URI: http://pootlepress.com/
Description: An extension for WooThemes Canvas that allow you to use featured video in slider
Version: 1.1.0
Author: PootlePress
Author URI: http://pootlepress.com/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( 'pootlepress-featured-video-functions.php' );
require_once( 'classes/class-pootlepress-featured-video.php' );

$GLOBALS['pootlepress_featured_video'] = new Pootlepress_Featured_Video( __FILE__ );
$GLOBALS['pootlepress_featured_video']->version = '1.1.0';

?>
