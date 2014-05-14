/**
 * Created by Alan on 21/4/2014.
 */

(function ($) {

    $(document).ready(function () {

        if (typeof PPFVSettings != 'undefined' && PPFVSettings != null) {
            for (var postID in PPFVSettings) {
                var autoplay = PPFVSettings[postID].autoplay;
                var loop = PPFVSettings[postID].loop;
                if (autoplay || loop) {
                    $('#slide-' + postID).each(function () {
//                        if (!$(this).hasClass('clone')) {
//                            // trigger play button at center of video, since can't call play function reliably
//                            $(this).find('.mejs-overlay-play').click();
//                        }
                        var $iframe = $(this).find('iframe');
                        var src = $iframe.attr('src');
                        if (src.indexOf('vimeo') >= 0) {
                            if (autoplay) {
                                if (src.indexOf('?') >= 0) {
                                    src += '&autoplay=1';
                                } else {
                                    src += '?autoplay=1';
                                }
                            }

                            if (loop) {
                                if (src.indexOf('?') >= 0) {
                                    src += '&loop=1';
                                } else {
                                    src += '?loop=1';
                                }
                            }
                            $iframe.attr('src', src);
                        }
                    });
                }
            }
        }

    });

    $(window).load(function () {

        $('#loopedSlider .slide > iframe').each(function () {
//            if (typeof 'FeaturedSliderParam' != 'undefined' && FeaturedSliderParam != null &&
//                FeaturedSliderParam.isSliderFullWidth) {
                var width = $(this).attr('width');
                var height = $(this).attr('height');
                var ratio = height / width;
                var newHeight = $(window).width() * ratio;

                console.log('parent width: ' + $(window).width());
                console.log('ratio: ' + ratio);
                console.log('newHeight: ' + newHeight);

                $(this).css('width', '100%');
                $(this).css('height', newHeight + 'px');
//            }
        });



    });

})(jQuery);
