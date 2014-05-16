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
                        if (!$(this).hasClass('clone')) {
//                            // trigger play button at center of video, since can't call play function reliably
//                            $(this).find('.mejs-overlay-play').click();
//
                            var $iframe = $(this).find('iframe');
                            if ($iframe.length > 0) {
                                var src = $iframe.attr('src');

                                if (src.indexOf('http://www.youtube.com/embed/') == 0) {
                                    var ars = src.substring('http://www.youtube.com/embed/'.length);
                                    var ar = ars.split('?');
                                    var videoID = ar[0];

                                    src = 'http://www.youtube.com/v/' + videoID + '?version=3&playlist=' + videoID;

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

                                if (src.indexOf('vimeo') >= 0 /*|| src.indexOf('youtube') >= 0*/) {
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
                            }
                        } else {
                          // clone
                            $('#slide-' + postID +' .clone').remove();
                        }

                    });
                }
            }
        }

        // don't load cloned video or else sounds will overlap
        setInterval(function () {
            $('#loopedSlider .slide.clone iframe').each(function () {
                if ($(this).attr('src') != '') {
                    $(this).attr('src', '');
                }
            });
        }, 100);

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
