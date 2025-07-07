jQuery(function($){
  function openWL(){
    var $f = $("#tta-waitlist-form");
    $f.find(".tta-admin-progress-response-p").removeClass("updated error").text("");
    $("#tta-waitlist-overlay").fadeIn(200);
  }
  function closeWL(){
    $("#tta-waitlist-overlay").fadeOut(200);
  }

  $(document).on('click', '#tta-join-waitlist', function(e){
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
  $("#tta-waitlist-overlay").on("click touchstart", function(e){
    if(e.target===this){
      e.preventDefault();
      closeWL();
    }
  });

  $("#tta-waitlist-form").on("submit", function(e){
    e.preventDefault();
    var $form = $(this),
        $btn  = $form.find('button[type="submit"]'),
        $spin = $form.find(".tta-admin-progress-spinner-svg"),
        $resp = $form.find(".tta-admin-progress-response-p"),
        start = Date.now();
    $resp.removeClass("updated error").text("");
    $btn.prop("disabled", true);
    $spin.stop(true).css({display:"inline-block",opacity:0}).fadeTo(200,1);
    var data = {
      action: "tta_join_waitlist",
      nonce: tta_waitlist.nonce,
      event_ute_id: tta_waitlist.eventUte,
      ticket_id: tta_waitlist.ticketId,
      ticket_name: tta_waitlist.ticketName,
      event_name: tta_waitlist.eventName,
      first_name: $form.find("input[name=first_name]").val(),
      last_name: $form.find("input[name=last_name]").val(),
      email: $form.find("input[name=email]").val(),
      phone: $form.find("input[name=phone]").val(),
      opt_email: $form.find("input[name=opt_email]").is(":checked") ? 1 : 0,
      opt_sms: $form.find("input[name=opt_sms]").is(":checked") ? 1 : 0
    };
    var respClass = "";
    var respText = "";
    $.ajax({url: tta_waitlist.ajax_url, method:"POST", data:data, dataType:"json"})
      .done(function(res){
        if(res.success){
          respClass = "updated";
          respText = "Added to waitlist!";
        }else{
          respClass = "error";
          respText = res.data && res.data.message ? res.data.message : "Failed to join waitlist";
        }
      })
      .fail(function(){
        respClass = "error";
        respText = "Request failed.";
      })
      .always(function(){
        var elapsed = Date.now() - start;
        var delay = elapsed < 3000 ? 3000 - elapsed : 0;
        setTimeout(function(){
          $spin.fadeTo(200,0,function(){ $spin.hide(); });
          $btn.prop("disabled", false);
          if(respText){
            $resp.addClass(respClass).text(respText);
          }
        }, delay);
      });
  });
});
