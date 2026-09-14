(function ($) {
    if (typeof wes_gi_ajax === 'undefined') return;
  var $container = $('#gi-org-grid');
  var $pagination = $('#gi-pagination');
  if (!$container.length) return;

  var $filters = $('.filter-wrap select');
  var currentPage = 1;

  function loadOrgs(page) {
    var data = {
      action: 'wes_get_involved_filter',
      page: page || 1,
    };

    $filters.each(function () {
      var $s = $(this);
      if ($s.val()) {
        data[$s.attr('name')] = $s.val();
      }
    });

    $container.addClass('loading');

    $.post(wes_gi_ajax.ajaxurl, data, function (res) {
      $container.html(res.html).removeClass('loading');
      $pagination.html(res.pagination);
      currentPage = page || 1;
    }, 'json').fail(function () {
    $container.removeClass('loading');
  });
  }

  $filters.on('change', function () {
    loadOrgs(1);
  });

  $(document).on('click', '.gi-page', function () {
    var page = $(this).data('page');
    if (page) {
      loadOrgs(page);
    }
  });

})(jQuery);
