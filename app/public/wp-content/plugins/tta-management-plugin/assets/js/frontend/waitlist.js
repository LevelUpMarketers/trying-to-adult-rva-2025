jQuery(function($){
  function openWL(){
    $('#tta-waitlist-overlay').show();
  }
  function closeWL(){
    $('#tta-waitlist-overlay').hide();
  }

  $('#tta-join-waitlist').on('click', function(e){
    e.preventDefault();
    var d = window.tta_waitlist || {};
    $('#tta-waitlist-form input[name="first_name"]').val(d.firstName||'');
    $('#tta-waitlist-form input[name="last_name"]').val(d.lastName||'');
    $('#tta-waitlist-form input[name="email"]').val(d.email||'');
    $('#tta-waitlist-form input[name="phone"]').val(d.phone||'');
    openWL();
  });

  $(document).on('click','.tta-waitlist-close',function(e){
    e.preventDefault();
    closeWL();
  });

  $('#tta-waitlist-form').on('submit', function(e){
    e.preventDefault();
    var data = {
      action: 'tta_join_waitlist',
      nonce: tta_waitlist.nonce,
      event_ute_id: tta_waitlist.eventUte,
      ticket_id: tta_waitlist.ticketId,
      ticket_name: tta_waitlist.ticketName,
      event_name: tta_waitlist.eventName,
      first_name: $('input[name="first_name"]', this).val(),
      last_name: $('input[name="last_name"]', this).val(),
      email: $('input[name="email"]', this).val(),
      phone: $('input[name="phone"]', this).val(),
      opt_email: $('input[name="opt_email"]', this).is(':checked') ? 1 : 0,
      opt_sms: $('input[name="opt_sms"]', this).is(':checked') ? 1 : 0
    };
    $.post(tta_waitlist.ajax_url, data, function(resp){
      closeWL();
      // Simple alert for now
      alert(resp.success ? 'Added to waitlist!' : 'Failed to join waitlist');
    });
  });
});

