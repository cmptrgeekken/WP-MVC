jQuery(function($) {
    var imgCache = [];

    var _activeImage;
    var $albumImage = $('#album-image');
    var $albumThumbsWrapper = $('#album-thumbs');
    var $albumThumbs = $('.album-thumb');
    
    var $activeThumb;
    $albumImage.load(
        function() {
            $albumImage.removeClass('loading');
        }
    );

    $albumThumbsWrapper.lionbars({autohide: true});
    $albumThumbsWrapper.css('height', '300px');

    $albumThumbs.click(function() {
        $activeThumb = $(this);

        var url = $activeThumb.data('xlurl');

        if (!imgCache[url]) {
            var img = new Image();


            $albumImage
                .css('background-image', '')
                .addClass('loading');

            $(img).load(function() {
                $(this).data('loaded', true);

                loadImage(this);
            });
            img.src = url;

            imgCache[url] = img;
        } else {
            loadImage(imgCache[url]);
        }
    });

    $(window).smartresize(loadImage);

    function loadImage(img) {
        img = img || _activeImage;
        if (!img || !$(img).data('loaded')) {
            return;
        }

        _activeImage = img;

        var xPos = $albumThumbsWrapper.width() + (($(window).width() - $albumThumbsWrapper.width()) - img.width) / 2;

        if (xPos <= $albumThumbsWrapper.width() + 10) {
            xPos = $albumThumbsWrapper.width() + 10;
        }

        $albumImage
            .hide()
            .css({
                'width':  img.width,
                'height': img.height
            })
            .removeClass('loading')
            .css({
                'background-image': 'url("' + img.src + '")'
            })
            .show();

        $('#page-content').css('width', (img.width + $albumThumbsWrapper.width() + 10) + 'px');

        $albumThumbsWrapper.css('height', Math.max(img.height, $(window).height() - $('#header-wrapper').height() - 10) + 'px');


    }

    $albumThumbs.eq(0).click();

    $('#hide-album-thumbs').click(function(){
        var $thumbs = $('#album-thumbs-wrapper');
        if ($thumbs.hasClass('hidden')) {
            $thumbs.show().removeClass('hidden');
        } else {
            $thumbs.data('width', $thumbs.width());
            $thumbs.hide().addClass('hidden');
        }
    });
});