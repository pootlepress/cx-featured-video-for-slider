<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Pootlepress_Featured_Video Class
 *
 * Base class for the Pootlepress Masonry Shop.
 *
 * @package WordPress
 * @subpackage Pootlepress_Featured_Video
 * @category Core
 * @author Pootlepress
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * public $token
 * public $version
 * 
 * - __construct()
 * - add_theme_options()
 * - get_menu_styles()
 * - load_stylesheet()
 * - load_script()
 * - load_localisation()
 * - check_plugin()
 * - load_plugin_textdomain()
 * - activation()
 * - register_plugin_version()
 * - get_header()
 * - woo_nav_custom()
 */
class Pootlepress_Featured_Video {
	public $token = 'pootlepress-featured-video';
	public $version;
	private $file;

	/**
	 * Constructor.
	 * @param string $file The base file of the plugin.
	 * @access public
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		$this->file = $file;
		$this->load_plugin_textdomain();
		//add_action( 'init', 'check_main_heading', 0 );
		add_action( 'init', array( &$this, 'load_localisation' ), 0 );

		// Run this on activation.
		register_activation_hook( $file, array( &$this, 'activation' ) );

		// Add the custom theme options.
		//add_filter( 'option_woo_template', array( &$this, 'add_theme_options' ) );

        add_action( 'add_meta_boxes', array(&$this, 'add_meta_box') );

        add_action( 'admin_print_scripts', array( &$this, 'load_admin_script' ) );
        add_action( 'admin_print_styles', array( &$this, 'load_admin_style' ) );

        add_action( 'wp_enqueue_scripts', array( &$this, 'load_script' ) );

        add_action('wp_head', array(&$this, 'option_css'));

        add_filter('woo_slider_autoheight', '__return_true');

        add_action('save_post', array(&$this, 'save_post'));

        add_shortcode( 'pp_fv_video', array(&$this, 'video_shortcode') );



	} // End __construct()

    public function add_meta_box() {
        add_meta_box('pootlepress-featured-video', 'Featured Video', array(&$this, 'featured_video_meta_box'), 'slide', 'side', 'low');
    }

    public function featured_video_meta_box($post) {
        $videoUrl = get_post_meta( $post->ID, 'pp_fv_video_url', true );
        $addFrom = get_post_meta($post->ID, 'pp_fv_add_from', true);
        $autoplay = get_post_meta($post->ID, 'pp_fv_autoplay', true);
        $loop = get_post_meta($post->ID, 'pp_fv_loop', true);
        echo $this->meta_box_html( $videoUrl, $addFrom, $autoplay, $loop, $post->ID );
    }

    public function meta_box_html( $videoUrl, $addFrom, $autoplay, $loop, $post) {

        $post = get_post( $post );
        $upload_iframe_src = esc_url( get_upload_iframe_src('video', $post->ID ) );

        if ( $videoUrl) {
            $setLinkDisplay = 'style="display: none;"';
        } else {
            $setLinkDisplay = '';
        }

        $content = "<div id='pp-fv-container'>";

        $content .= "<div id='pp-fv-set-video' " . $setLinkDisplay . " >";
        $set_video_link = '<p class="hide-if-no-js set-link"><a title="' . esc_attr__( 'Set featured video' ) . '" href="%s" id="set-post-video" class="thickbox">%s</a></p>';
        $content .= sprintf( $set_video_link, $upload_iframe_src, esc_html__( 'Set featured video' ) );
        $content .= "<label>Url: <input id='pp-fv-url-text-box' type='text' /></label>";
//        $content .= "<br />";
        $content .= ' <button id="pp-fv-add-url-button" class="button">Add</button>';
        $content .= "</div>"; // pp-fv-set-video

        $optionDisplay = 'style="display: none;"';

        if ($videoUrl != '') {
            $viewDisplay = '';

            if(strpos($videoUrl, 'vimeo') >= 0 ||
                strpos($videoUrl, 'http://www.youtube.com/watch?v=') >= 0 ||
                strpos($videoUrl, 'youtu.be') >= 0) {
                $optionDisplay = '';
            }
        } else {
            $viewDisplay = 'style="display: none;"';
        }

        if ($autoplay == '1') {
            $autoplayChecked = 'checked';
        } else {
            $autoplayChecked = '';
        }

        if ($loop == '1') {
            $loopChecked = 'checked';
        } else {
            $loopChecked = '';
        }

        $html = '<a href="' . esc_attr($videoUrl) . '" target="_blank" rel="external">View File</a>';
        $content .= '<div class="no_image" ' . $viewDisplay . ' ><span class="file_link">' . $html . '</span><a href="#" class="remove-button button">Remove</a>
        <div class="additional-options" ' . $optionDisplay . '>
        <div class="separator"></div>
        <label><span>Autoplay:</span><input type="checkbox" class="autoplay-checkbox" name="autoplay" value="1" ' . $autoplayChecked . '/></label><br />
        <label><span>Loop:</span><input type="checkbox" class="loop-checkbox" name="loop" value="1" ' . $loopChecked . '/></label>
        </div>
        </div>';

        $content .= "<input id='pp-fv-video-url' name='featured-video-url' type='hidden' value='" . esc_attr($videoUrl) . "' />";
        $content .= "<input id='pp-fv-video-add-from' name='featured-video-add-from' type='hidden' value='" . esc_attr($addFrom) . "'/>";
        $content .= "</div>";

        return $content;
    }

    public function save_post($postID) {

        if (isset($_POST['post_type']) && 'slide' != $_POST['post_type']) {
            return;
        }

        if (isset($_REQUEST['featured-video-url']) &&
            isset($_REQUEST['featured-video-add-from']))
        {
            $videoUrl = $_REQUEST['featured-video-url'];
            update_post_meta($postID, 'pp_fv_video_url', $videoUrl);
            update_post_meta($postID, 'pp_fv_add_from', $_REQUEST['featured-video-add-from']);
        }

        if (isset($_REQUEST['autoplay'])) {
            update_post_meta($postID, 'pp_fv_autoplay', '1');
        } else {
            update_post_meta($postID, 'pp_fv_autoplay', '0');
        }

        if (isset($_REQUEST['loop'])) {
            update_post_meta($postID, 'pp_fv_loop', '1');
        } else {
            update_post_meta($postID, 'pp_fv_loop', '0');
        }
    }

    private function convertUrlQuery($query) {
        $queryParts = explode('&', $query);

        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = urldecode($item[1]);
        }

        return $params;
    }

    public function load_admin_script() {
        $screen = get_current_screen();
        if ($screen->base == 'post' && $screen->id == 'slide') {
            $pluginFile = dirname(dirname(__FILE__)) . '/pootlepress-featured-video.php';
            wp_enqueue_script('pootlepress-featured-video-admin', plugin_dir_url($pluginFile) . 'scripts/featured-video-admin.js', array('jquery'));
        }
    }

    public function load_admin_style() {
        $screen = get_current_screen();
        if ($screen->base == 'post' && $screen->id == 'slide') {
            $pluginFile = dirname(dirname(__FILE__)) . '/pootlepress-featured-video.php';
            wp_enqueue_style('pootlepress-featured-video-admin', plugin_dir_url($pluginFile) . 'styles/featured-video-admin.css');
        }
    }

    public function load_script() {
        $pluginFile = dirname(dirname(__FILE__)) . '/pootlepress-masonry-shop.php';
        wp_enqueue_script('pootlepress-featured-video', plugin_dir_url($pluginFile) . 'scripts/featured-video.js', array('jquery'));

        $sliderFullWidthEnabled = get_option('woo_slider_biz_full', 'false');
        $b = ($sliderFullWidthEnabled === 'true');
        wp_localize_script('pootlepress-featured-video', 'FeaturedSliderParam', array('isSliderFullWidth' => $b));


    }

    public function video_shortcode( $attr, $content = '' ) {
        global $content_width;
        $post_id = get_post() ? get_the_ID() : 0;

        static $instances = 0;
        $instances++;

        /**
         * Override the default video shortcode.
         *
         * @since 3.7.0
         *
         * @param null              Empty variable to be replaced with shortcode markup.
         * @param array  $attr      Attributes of the shortcode.
         * @param string $content   Shortcode content.
         * @param int    $instances Unique numeric ID of this video shortcode instance.
         */
        $html = apply_filters( 'wp_video_shortcode_override', '', $attr, $content, $instances );
        if ( '' !== $html )
            return $html;

        $video = null;

        $default_types = wp_get_video_extensions();
        $defaults_atts = array(
            'src'      => '',
            'poster'   => '',
            'loop'     => '',
            'autoplay' => '',
            'preload'  => 'metadata',
            'height'   => 360,
            'width'    => empty( $content_width ) ? 640 : $content_width,
        );

        foreach ( $default_types as $type )
            $defaults_atts[$type] = '';

        $atts = shortcode_atts( $defaults_atts, $attr, 'video' );
        extract( $atts );

        $w = $width;
        $h = $height;
        if ( is_admin() && $width > 600 )
            $w = 600;
        elseif ( ! is_admin() && $w > $defaults_atts['width'] )
            $w = $defaults_atts['width'];

        if ( $w < $width )
            $height = round( ( $h * $w ) / $width );

        $width = $w;

        $primary = false;
        if ( ! empty( $src ) ) {
            $type = wp_check_filetype( $src, wp_get_mime_types() );
            if ( ! in_array( strtolower( $type['ext'] ), $default_types ) )
                return sprintf( '<a class="wp-embedded-video" href="%s">%s</a>', esc_url( $src ), esc_html( $src ) );
            $primary = true;
            array_unshift( $default_types, 'src' );
        } else {
            foreach ( $default_types as $ext ) {
                if ( ! empty( $$ext ) ) {
                    $type = wp_check_filetype( $$ext, wp_get_mime_types() );
                    if ( strtolower( $type['ext'] ) === $ext )
                        $primary = true;
                }
            }
        }

        if ( ! $primary ) {
            $videos = get_attached_media( 'video', $post_id );
            if ( empty( $videos ) )
                return;

            $video = reset( $videos );
            $src = wp_get_attachment_url( $video->ID );
            if ( empty( $src ) )
                return;

            array_unshift( $default_types, 'src' );
        }

        $library = apply_filters( 'wp_video_shortcode_library', 'mediaelement' );
        if ( 'mediaelement' === $library && did_action( 'init' ) ) {
            wp_enqueue_style( 'wp-mediaelement' );
            wp_enqueue_script( 'wp-mediaelement' );
        }

        $atts = array(
            'class'    => apply_filters( 'wp_video_shortcode_class', 'wp-video-shortcode' ),
            'id'       => sprintf( 'video-%d-%d', $post_id, $instances ),
            'width'    => absint( $width ),
            'height'   => absint( $height ),
            'poster'   => esc_url( $poster ),
            'loop'     => $loop,
            'autoplay' => $autoplay,
            'preload'  => $preload,
        );

        // These ones should just be omitted altogether if they are blank
        foreach ( array( 'poster', 'loop', 'autoplay', 'preload' ) as $a ) {
            if ( empty( $atts[$a] ) )
                unset( $atts[$a] );
        }

        $attr_strings = array();
        foreach ( $atts as $k => $v ) {
            $attr_strings[] = $k . '="' . esc_attr( $v ) . '"';
        }

        $html = '';
        if ( 'mediaelement' === $library && 1 === $instances )
            $html .= "<!--[if lt IE 9]><script>document.createElement('video');</script><![endif]-->\n";
        $html .= sprintf( '<video %s controls="controls">', join( ' ', $attr_strings ) );

        $fileurl = '';
        $source = '<source type="%s" src="%s" />';
        foreach ( $default_types as $fallback ) {
            if ( ! empty( $$fallback ) ) {
                if ( empty( $fileurl ) )
                    $fileurl = $$fallback;
                $type = wp_check_filetype( $$fallback, wp_get_mime_types() );
                // m4v sometimes shows up as video/mpeg which collides with mp4
                if ( 'm4v' === $type['ext'] )
                    $type['type'] = 'video/m4v';
                $html .= sprintf( $source, $type['type'], esc_url( $$fallback ) );
            }
        }
        if ( 'mediaelement' === $library )
            $html .= wp_mediaelement_fallback( $fileurl );
        $html .= '</video>';

//        $sliderFullWidthEnabled = get_option('woo_slider_biz_full', 'false');
//        if ($sliderFullWidthEnabled === 'true') {
            $html = sprintf( '<div style="max-width: 100%%;" class="wp-video">%s</div>', $html );
//        } else {
//            $html = sprintf( '<div style="width: %dpx; max-width: 100%%;" class="wp-video">%s</div>', $width, $html );
//        }

        return apply_filters( 'wp_video_shortcode', $html, $atts, $video, $post_id, $library );
    }

	/**
	 * Add theme options to the WooFramework.
	 * @access public
	 * @since  1.0.0
	 * @param array $o The array of options, as stored in the database.
	 */
	public function add_theme_options ( $o ) {

        return $o;
	} // End add_theme_options()



    public function option_css() {
            $css = '';

            $css .= "#loopedSlider .slide { text-align: center; }\n";
            $css .= "#loopedSlider .slide .wp-video { display: inline-block; }\n";
            $css .= "#header { padding-left: 0 !important; padding-right: 0 !important; }\n";
            echo "<style>".$css."</style>";
    }

	/**
	 * Load stylesheet required for the style, if has any.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function load_stylesheet () {

	} // End load_stylesheet()

	/**
	 * Load the plugin's localisation file.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( $this->token, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load the plugin textdomain from the main WordPress "languages" folder.
	 * @access public
	 * @since  1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = $this->token;
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	 
	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( $this->token . '-version', $this->version );
		}
	} // End register_plugin_version()


} // End Class


