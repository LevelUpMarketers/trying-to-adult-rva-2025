jQuery(function($){
    var data = window.ttaAlertBarData || {};
    if(data.is_banned){
        var $bar = $('<div id="tta-alert-bar" class="tta-alert-bar tta-alert-banned"><span class="tta-alert-message"></span><a class="tta-alert-button"></a></div>');
        $bar.find('.tta-alert-message').text(data.banned_message || '');
        $bar.find('.tta-alert-button').text(data.reentry_label || '').attr('href', data.reentry_url || '#');
        $('body').append($bar);
        return;
    }
    var expires = parseInt(data.cart_expires,10) || 0;
    if(!expires) return;
    var $bar = $('<div id="tta-alert-bar" class="tta-alert-bar tta-alert-cart"><span class="tta-alert-message">'+(data.cart_message||'')+' <span class="tta-countdown"></span></span><a class="tta-alert-button" href="'+(data.checkout_url||'#')+'">'+(data.checkout_label||'')+'</a></div>');
    $('body').append($bar);
    var $cd = $bar.find('.tta-countdown');
    function update(){
        var diff = expires - Math.floor(Date.now()/1000);
        if(diff <= 0){
            $bar.remove();
            return;
        }
        var m = Math.floor(diff/60);
        var s = diff%60;
        $cd.text(m+':' + (s<10?'0':'') + s);
    }
    update();
    setInterval(update,1000);
});
