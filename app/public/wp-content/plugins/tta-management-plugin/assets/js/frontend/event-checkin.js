jQuery(function($){
  // Toggle event rows
  $(document).on('click', '.tta-event-row', function(e){
    if ($(e.target).is('button, a')) return;
    var $row       = $(this),
        $arrow     = $row.find('.tta-toggle-arrow'),
        ute        = $row.data('event-ute-id'),
        isMobile   = window.matchMedia('(max-width:1199px)').matches,
        $toggleCell= $row.find('.tta-toggle-cell'),
        $container = $toggleCell.find('.tta-inline-container'),
        $ex        = $row.next('.tta-inline-row');

    if (isMobile){
      if ($toggleCell.hasClass('open')){
        $arrow.removeClass('open');
        $row.removeClass('open');
        $toggleCell.removeClass('open');
        $container.slideUp().empty();
        return;
      }
      $('.tta-toggle-cell.open').removeClass('open').find('.tta-inline-container').slideUp().empty();
      $('.tta-inline-row').remove();
      $('.tta-toggle-arrow').removeClass('open');
      $('.tta-event-row').removeClass('open');
      $.post(TTA_Checkin.ajax_url, { action:'tta_get_event_attendance', nonce:TTA_Checkin.get_nonce, event_ute_id: ute }, function(res){
        if(!res.success) return;
        $container.html(res.data.html).slideDown();
        $arrow.addClass('open');
        $row.addClass('open');
        $toggleCell.addClass('open');
      }, 'json');
      return;
    }

    if ($ex.length){
      $arrow.removeClass('open');
      $row.removeClass('open');
      $ex.remove();
      return;
    }

    $('.tta-inline-row').remove();
    $('.tta-toggle-arrow').removeClass('open');
    $('.tta-event-row').removeClass('open');
    $('.tta-toggle-cell.open').removeClass('open').find('.tta-inline-container').empty().hide();

    $.post(TTA_Checkin.ajax_url, { action:'tta_get_event_attendance', nonce:TTA_Checkin.get_nonce, event_ute_id: ute }, function(res){
      if(!res.success) return;
      var colspan = $row.find('td').length;
      var $new = $('<tr class="tta-inline-row"><td colspan="'+colspan+'">'+res.data.html+'</td></tr>');
      $row.after($new);
      $arrow.addClass('open');
      $row.addClass('open');
    }, 'json');
  });

  // Set attendance status
  $(document).on('click', '.tta-mark-attendance', function(e){
    e.preventDefault();
    var $btn = $(this), id = $btn.data('attendee-id'), status = $btn.data('status');
    $.post(TTA_Checkin.ajax_url, { action:'tta_set_attendance', nonce:TTA_Checkin.set_nonce, attendee_id:id, status:status }, function(res){
      if(!res.success) return;
      var label = $btn.closest('tr').find('.status-label');
      var text  = status.replace('_',' ');
      text = text.replace(/\b\w/g, function(c){ return c.toUpperCase(); });
      label.text(text);
    }, 'json');
  });
});
