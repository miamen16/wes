/**
 * Resource Cards – AJAX Pagination (no URL change, CTA stays visible)
 */
jQuery(document).ready(function($) {
    var ajaxurl = typeof wes_rc_ajax !== 'undefined' ? wes_rc_ajax.ajaxurl : '/wp-admin/admin-ajax.php';

    function getPageNumber($link) {
        var href = $link.attr('href');
        if (!href) return 1;
        var match = href.match(/[?&]resource_page=(\d+)/);
        if (match) return parseInt(match[1]) || 1;
        match = href.match(/[?&]page=(\d+)/);
        if (match) return parseInt(match[1]) || 1;
        var text = $link.text().trim();
        
        // Convert Arabic numerals to English for parseInt
        var arabic = { '٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9' };
        text = text.replace(/[٠-٩]/g, function(c) { return arabic[c] || c; });
        
        var num = parseInt(text);
        if (!isNaN(num) && num > 0) return num;
        return 1;
    }

    function loadPage($wrapper, page) {
        var actionTypes  = $wrapper.data('action-types') || '';
        var audiences    = $wrapper.data('audiences') || '';
        var postsPerPage = $wrapper.data('posts-per-page') || 9;
        var maxPosts     = $wrapper.data('max-posts') || -1;
        var cta            = $wrapper.attr('data-cta') || '';
        var ctaLink        = $wrapper.attr('data-cta-link') || '';
        var resourceSource = $wrapper.attr('data-resource-source') || '';

        var blockId = $wrapper.data('block-id');

        // Show loading state
        $wrapper.addClass('rc-loading');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'load_resource_cards_page',
                page: page,
                action_types: actionTypes,
                audiences: audiences,
                posts_per_page: postsPerPage,
                max_posts: maxPosts,
                cta: cta,
                cta_link: ctaLink,
                resource_source: resourceSource
            },
            success: function(response) {
                var $response = $(response);
                var $newWrapper = $response.find('.rc-dynamic-wrapper');

                // Replace only the wrapper (cards + pagination + CTA)
                if ($newWrapper.length) {
                    $wrapper.replaceWith($newWrapper);
                } else {
                    $wrapper.html($response);
                }

                // Remove loading state
                $('.rc-dynamic-wrapper').removeClass('rc-loading');
            },
            error: function() {
                $wrapper.removeClass('rc-loading');
            }
        });
    }

    // Click on pagination links
    $(document).on('click', '.rc-pagination--ajax a.page-numbers', function(e) {
        e.preventDefault();

        var $link = $(this);
        var $pagination = $link.closest('.rc-pagination--ajax');
        var $wrapper = $('.rc-dynamic-wrapper[data-block-id="' + $pagination.data('block-id') + '"]');

        if (!$wrapper.length) {
            window.location.href = $link.attr('href');
            return;
        }

        var page = getPageNumber($link);
        loadPage($wrapper, page);
    });
});