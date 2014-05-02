/**
 * Created by Alan on 21/4/2014.
 */

(function ($) {

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
