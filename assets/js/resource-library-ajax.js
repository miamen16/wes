/**
 * Resource Library – AJAX Filtering + Pagination
 */
jQuery(document).ready(function($) {
    var ajaxurl = typeof wes_drl_ajax !== 'undefined' ? wes_drl_ajax.ajaxurl : '/wp-admin/admin-ajax.php';
    var pageMap = {};

    function getBlockSettings(block) {
        var uid = block.data('uid') || '';
        if (!pageMap[uid]) pageMap[uid] = 1;
        return {
            cpt: block.data('cpt') || '',
            tax: block.data('tax') || '',
            limit: parseInt(block.data('limit'), 10) || -1,
            postsPerPage: parseInt(block.data('posts-per-page'), 10) || 4,
            orderby: block.data('orderby') || 'date',
            order: block.data('order') || 'DESC',
            showFlags: block.data('show-flags') || '',
            selectedTerms: block.data('selected-terms') || '',
            uid: uid
        };
    }

    function loadFilter(block, filterSlug, page) {
        var settings = getBlockSettings(block);
        var listWrap = block.find('.rlib-list-wrap');
        if (!listWrap.length) return;

        if (typeof page === 'undefined') page = 1;
        pageMap[settings.uid] = page;

        listWrap.addClass('rlib-loading');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wes_drl_filter',
                cpt: settings.cpt,
                tax: settings.tax,
                filter: filterSlug,
                limit: settings.limit,
                posts_per_page: settings.postsPerPage, 
                orderby: settings.orderby,
                order: settings.order,
                page: page,
                show_flags: settings.showFlags,
                selected_terms: settings.selectedTerms
            },
            success: function(response) {
                listWrap.html(response);
                listWrap.removeClass('rlib-loading');
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                listWrap.removeClass('rlib-loading');
            }
        });
    }

    // ---- Tab clicks ----
    $(document).on('click', '.seg-tab', function(e) {
        e.preventDefault();

        var btn = $(this);
        var block = btn.closest('.block-resource-library');
        if (!block.length) return;

        block.find('.seg-tab').each(function() {
            var isActive = $(this).is(btn);
            $(this).toggleClass('is-active', isActive);
            $(this).attr('aria-selected', isActive ? 'true' : 'false');
        });

        var select = block.find('.rlib__select');
        if (select.length) {
            select.val(btn.data('filter'));
        }

        var filter = btn.data('filter') || '*';
        loadFilter(block, filter, 1);
    });

    // ---- Select change ----
    $(document).on('change', '.rlib__select', function() {
        var select = $(this);
        var block = select.closest('.block-resource-library');
        if (!block.length) return;

        var value = select.val();
        var tab = block.find('.seg-tab[data-filter="' + value + '"]');

        if (tab.length) {
            tab.click();
        } else {
            loadFilter(block, value, 1);
        }
    });

    // ---- Pagination clicks ----
    $(document).on('click', '.rlib__page-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var block = btn.closest('.block-resource-library');
        if (!block.length) return;

        var page = parseInt(btn.data('page'), 10);
        if (!page || page < 1) return;

        var activeTab = block.find('.seg-tab.is-active');
        var filter = activeTab.length ? activeTab.data('filter') : '*';

        loadFilter(block, filter, page);
    });
});