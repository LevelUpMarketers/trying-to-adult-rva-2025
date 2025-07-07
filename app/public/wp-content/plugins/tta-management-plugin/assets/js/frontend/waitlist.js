jQuery(function($){
  function openWL(){
    var $f = $("#tta-waitlist-form");
    $f.find(".tta-admin-progress-response-p").removeClass("updated error").text("");
    $("#tta-waitlist-overlay").fadeIn(200);
  }
  function closeWL(){
    $("#tta-waitlist-overlay").fadeOut(200);
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
        $resp = $form.find(".tta-admin-progress-response-p");
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
    $.ajax({url: tta_waitlist.ajax_url, method:"POST", data:data, dataType:"json"})
      .done(function(res){
        if(res.success){
          $resp.addClass("updated").text("Added to waitlist!");
        }else{
          var m = res.data && res.data.message ? res.data.message : "Failed to join waitlist";
          $resp.addClass("error").text(m);
        }
      })
      .fail(function(){
        $resp.addClass("error").text("Request failed.");
      })
      .always(function(){
        $spin.fadeTo(200,0,function(){ $spin.hide(); });
        $btn.prop("disabled", false);
      });
  });
  });
});

