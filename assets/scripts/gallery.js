jQuery(function($) {

    var $container = $('.gallery-thumbs');
    $container.isotope({
        itemSelector: '.gallery-thumb',
        masonry: {
            //columnWidth: $container.width() / 5
        },
        animationEngine: 'best-available',
        sortBy: 'alphabetical',
        getSortData: {
            alphabetical: function( $elem ) {
                return $elem.find('.gallery-title').text();
            }
        }
    });


    $(window).on('hashchange', filterResults).trigger('hashchange');

    function filterResults() {
        var hash = window.location.hash;

        if (hash && hash.substr(0, 1) == '#' && hash.length > 1) {
            hash = hash.substr(1);

            var categoryMatches = hash.split('_');
            if (categoryMatches) {
                var category = categoryMatches[0];
                var subcategory = categoryMatches[1];

                var $category = $('#category-' + category);
                $category.parent().addClass('active').siblings().removeClass('active');

                if (subcategory) {
                    var $subcategory = $('#subcategory-' + category + '_' + subcategory);

                    $('.gallery-subcategory').removeClass('active');
                    $subcategory.parent().addClass('active');
                }

                filter(category, subcategory);

                return;
            }
        }

        $('.gallery-subcategory, .gallery-category').removeClass('active');
        filter();

    }

    function filter(category, subcategory)
    {
        var filter = '';
        if (category) {
            filter = '.category-' + category;

            if (subcategory) {
                filter += '.subcategory-' + subcategory;
            }
        }

        $container.isotope({
            filter: filter
        });
    }

//    $('.gallery-category').each(function(idx) {
//        var $this = $(this);
//        $this.data('idx', idx);
//        $this.click(function(e) {
//            e.preventDefault();
//            $('.gallery-category').removeClass('active');
//            $this.addClass('active');
//            $this.find('.gallery-subcategory-all').click();
//            return false;
//        });
//    });
//
//    $('.gallery-category-all').click(function() {
//        $('.gallery-category').removeClass('active');
//        $('.gallery-subcategory').removeClass('active');
//        $('.gallery-thumbs-category').addClass('active');
//        $('.gallery-thumbs-subcategory').addClass('active');
//
//        showGalleries();
//    });
//
//    $('.gallery-subcategory-all').each(function() {
//        var $this = $(this);
//        var categoryIdx = $this
//            .parents('.gallery-category')
//            .data('idx');
//
//        $this.click(function(e) {
//            e.preventDefault();
//            $('.gallery-subcategory').removeClass('active');
//
//            $('.gallery-thumbs-subcategory').removeClass('active');
//            $('.gallery-thumbs-category')
//                .removeClass('active')
//                .eq(categoryIdx)
//                .addClass('active')
//                .find('.gallery-thumbs-subcategory')
//                .addClass('active');
//
//            showGalleries();
//
//            return false;
//        })
//    });
//
//    $('.gallery-subcategory').each(function(idx) {
//        var $this = $(this);
//        var categoryIdx = $this
//            .parents('.gallery-category')
//            .data('idx');
//
//        $this.click(function(e) {
//            e.preventDefault();
//
//            $('.gallery-subcategory')
//                .removeClass('active');
//            $this.addClass('active');
//
//            $('.gallery-thumbs-category')
//                .removeClass('active')
//                .eq(categoryIdx)
//                .addClass('active');
//
//            $('.gallery-thumbs-subcategory')
//                .removeClass('active')
//                .eq(idx)
//                .addClass('active');
//
//            showGalleries();
//
//            return false;
//        });
//    });
//
//
//    function showGalleries() {
//        $('#gallery-images').empty();
//
//        var $images = $('.gallery-thumbs-subcategory.active .gallery-thumb').clone();
//
//        $images.sort(function(a,b) {
//            var aTitle = $(a).find('.gallery-title').text();
//            var bTitle = $(b).find('.gallery-title').text();
//
//            return aTitle < bTitle ? -1 : (aTitle > bTitle ? 1 : 0);
//        });
//
//        $('#gallery-images').append($images);
//    }
//
//    $('.gallery-category-all').click();
});