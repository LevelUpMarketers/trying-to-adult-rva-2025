jQuery(function($){
  function handleResult($resp, $spin, $btn, res){
    $spin.fadeOut(200);
    $btn.prop('disabled', false);
    $resp.removeClass('updated error');
    if(res.success){
      window.location.reload();
    } else {
      $resp.addClass('error').text(res.data.message || 'Error');
    }
  }

  $('#tta-login-form').on('submit', function(e){
    e.preventDefault();
    e.stopPropagation();
    var $form = $(this);
    var $btn  = $form.find('button');
    var $spin = $form.find('.tta-admin-progress-spinner-svg');
    var $resp = $('#tta-login-response');
    $resp.text('');
    $btn.prop('disabled', true);
    $spin.show().css({opacity:0}).fadeTo(200,1);
    $.post( tta_ajax.ajax_url, {
      action: 'tta_login',
      nonce: tta_ajax.nonce,
      username: $form.find('[name="log"]').val(),
      password: $form.find('[name="pwd"]').val()
    }, function(res){ handleResult($resp, $spin, $btn, res); }, 'json').fail(function(){
      handleResult($resp, $spin, $btn, { success:false, data:{ message:'Request failed.' } });
    });
  });

  $('#tta-register-form').on('submit', function(e){
    e.preventDefault();
    e.stopPropagation();
    var $form = $(this);
    var $btn  = $form.find('button');
    var $spin = $form.find('.tta-admin-progress-spinner-svg');
    var $resp = $('#tta-register-response');
    $resp.text('');

    var email       = $form.find('[name="email"]').val();
    var emailVerify = $form.find('[name="email_verify"]').val();
    var pass        = $form.find('[name="password"]').val();
    if(email !== emailVerify){
      $resp.addClass('error').text('Email addresses do not match.');
      return;
    }

    if(!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(pass)){
      $resp.addClass('error').text( tta_ajax.password_requirements_msg );
      return;
    }

    $btn.prop('disabled', true);
    $spin.show().css({opacity:0}).fadeTo(200,1);
    $.post( tta_ajax.ajax_url, {
      action: 'tta_register',
      nonce: tta_ajax.nonce,
      first_name: $form.find('[name="first_name"]').val(),
      last_name:  $form.find('[name="last_name"]').val(),
      email:      email,
      password:   pass
    }, function(res){ handleResult($resp, $spin, $btn, res); }, 'json').fail(function(){
      handleResult($resp, $spin, $btn, { success:false, data:{ message:'Request failed.' } });
    });
  });
});
