<?php

$health = 'ok';

if (!function_exists('check_main_heading')) {
    function check_main_heading() {
        global $health;
        if (!function_exists('woo_options_add') ) {
            function woo_options_add($options) {
                $cx_heading = array( 'name' => __('Canvas Extensions', 'pootlepress-canvas-extensions' ),
                    'icon' => 'favorite', 'type' => 'heading' );
                if (!in_array($cx_heading, $options))
                    $options[] = $cx_heading;
                return $options;
            }
        } else {	// another ( unknown ) child-theme or plugin has defined woo_options_add
            $health = 'ng';
        }
    }
}

add_action( 'admin_init', 'poo_commit_suicide' );

if(!function_exists('poo_commit_suicide')) {
    function poo_commit_suicide() {
        global $health;
        $pluginFile = str_replace('-functions', '', __FILE__);
        $plugin = plugin_basename($pluginFile);
        $plugin_data = get_plugin_data( $pluginFile, false );
        if ( $health == 'ng' && is_plugin_active($plugin) ) {
            deactivate_plugins( $plugin );
            wp_die( "ERROR: <strong>woo_options_add</strong> function already defined by another plugin. " .
                $plugin_data['Name']. " is unable to continue and has been deactivated. " .
                "<br /><br />Please contact PootlePress at <a href=\"mailto:support@pootlepress.com?subject=Woo_Options_Add Conflict\"> support@pootlepress.com</a> for additional information / assistance." .
                "<br /><br />Back to the WordPress <a href='".get_admin_url(null, 'plugins.php')."'>Plugins page</a>." );
        }
    }
}


if ( ! function_exists( 'woo_slider_biz_view' ) ) {
    function woo_slider_biz_view( $args = null, $slides = null ) {

        global $woo_options, $post;

        // Default slider settings.
        $defaults = array(
            'id' => 'loopedSlider',
            'width' => '960',
            'container_css' => '',
            'slide_styles' => ''
        );

        // Merge the arguments with defaults.
        $args = wp_parse_args( $args, $defaults );

        // Init slide count
        $count = 0;

        ?>

        <?php do_action('woo_biz_slider_before'); ?>

        <div id="<?php echo esc_attr( $args['id'] ); ?>"<?php if ( '' != $args['container_css'] ): ?> class="<?php echo esc_attr( $args['container_css'] ); ?>"<?php endif; ?><?php if ( false == apply_filters( 'woo_slider_autoheight', false ) ): ?> style="height: <?php echo apply_filters( 'woo_slider_height', 350 ); ?>px;"<?php endif; ?>>

            <ul class="slides"<?php if ( false == apply_filters( 'woo_slider_autoheight', false ) ): ?> style="height: <?php echo apply_filters( 'woo_slider_height', '350' ); ?>px;"<?php endif; ?>>

                <?php foreach ( $slides as $k => $post ) { setup_postdata( $post ); $count++; ?>

                    <?php
                    // Slide Styles
                    if ( $count >= 2 ) { $args['slide_styles'] .= ' display:none;'; } else { $args['slide_styles'] = ''; }
                    ?>

                    <li id="slide-<?php echo esc_attr( $post->ID ); ?>" class="slide slide-number-<?php echo esc_attr( $count ); ?>" <?php if ( '' != $args['slide_styles'] ): ?>style="<?php echo esc_attr( $args['slide_styles'] ); ?>"<?php endif; ?>>

                        <?php

                        $videoUrl = get_post_meta($post->ID, 'pp_fv_video_url', true);
                        $videoAddFrom = get_post_meta($post->ID, 'pp_fv_add_from', true);
                        if (!empty($videoUrl)) {
                            if ($videoAddFrom == 'file') {
                                $w = $args['width'];

                                echo do_shortcode("[pp_fv_video src='$videoUrl' width='$w' ][/pp_fv_video]");

                            } else if ($videoAddFrom == 'url') {
                                if (isset($GLOBALS['wp_embed'])) {
                                    $em = $GLOBALS['wp_embed'];
                                    $e = $em->run_shortcode("[embed width='960']" . $videoUrl . "[/embed]");
                                    echo $e;

                                    $autoplay = get_post_meta($post->ID, 'pp_fv_autoplay', true) == '1';
                                    $loop = get_post_meta($post->ID, 'pp_fv_loop', true) == '1';
                                    $opts = array('autoplay' => $autoplay, 'loop' => $loop);
                                    echo "<script>var PPFVSettings = PPFVSettings ? PPFVSettings : {}</script>";
                                    echo "<script>PPFVSettings['{$post->ID}'] = " . json_encode($opts) . ";</script>";
                                }
                            } else if ($videoAddFrom == 'embed-code') {

                                $videoEmbedCode = $videoUrl;

                                echo $videoEmbedCode;

                            }

                            $url = get_post_meta( $post->ID, 'url', true );
                            ?>
                            <div class="content">

                                <?php if ( 'true' == $woo_options['woo_slider_biz_title'] ): ?>
                                    <div class="title">
                                        <h2 class="title">
                                            <?php if ( '' != $url ): ?><a href="<?php echo esc_url( $url ); ?>" title="<?php the_title_attribute(); ?>"><?php endif; ?>
                                                <?php the_title(); ?>
                                                <?php if ( '' != $url ): ?></a><?php endif; ?>
                                        </h2>
                                    </div>
                                <?php endif; ?>

                                <div class="excerpt">
                                    <?php
                                    $content = get_the_excerpt();
                                    $content = do_shortcode( $content );
                                    echo wpautop( $content );
                                    ?>
                                </div><!-- /.excerpt -->

                            </div><!-- /.content -->
                            <?php
                        } else {

                            $type = woo_image('return=true');
                            if ( $type ):
                                $url = get_post_meta( $post->ID, 'url', true );
                                ?>

                                <?php if ( '' != $url ): ?><a href="<?php echo esc_url( $url ); ?>" title="<?php the_title_attribute(); ?>"><?php endif; ?>
                                <?php woo_image( 'width=' . $args['width'] . '&link=img&noheight=true' ); ?>
                                <?php if ( '' != $url ): ?></a><?php endif; ?>

                                <div class="content">

                                    <?php if ( 'true' == $woo_options['woo_slider_biz_title'] ): ?>
                                        <div class="title">
                                            <h2 class="title">
                                                <?php if ( '' != $url ): ?><a href="<?php echo esc_url( $url ); ?>" title="<?php the_title_attribute(); ?>"><?php endif; ?>
                                                    <?php the_title(); ?>
                                                    <?php if ( '' != $url ): ?></a><?php endif; ?>
                                            </h2>
                                        </div>
                                    <?php endif; ?>

                                    <div class="excerpt">
                                        <?php
                                        $content = get_the_excerpt();
                                        $content = do_shortcode( $content );
                                        echo wpautop( $content );
                                        ?>
                                    </div><!-- /.excerpt -->

                                </div><!-- /.content -->

                            <?php else: ?>

                                <section class="entry col-full">
                                    <?php the_content(); ?>
                                </section>

                            <?php endif; ?>
                        <?php } ?>
                    </li><!-- /.slide-number-<?php echo esc_attr( $count ); ?> -->

                <?php } // End foreach ?>

                <?php wp_reset_postdata();  ?>

            </ul><!-- /.slides -->

        </div><!-- /#<?php echo $args['id']; ?> -->

        <?php if ( isset( $woo_options['woo_slider_pagination'] ) && $woo_options['woo_slider_pagination'] == 'true' ) : ?>
            <div class="pagination-wrap slider-pagination">
                <ol class="flex-control-nav flex-control-paging">
                    <?php for ( $i = 0; $i < $count; $i++ ): ?>
                        <li><a><?php echo ( $i + 1 ) ?></a></li>
                    <?php endfor; ?>
                </ol>
            </div>
        <?php endif; ?>

        <?php do_action('woo_biz_slider_after'); ?>

    <?php
    } // End woo_slider_biz_view()
}
