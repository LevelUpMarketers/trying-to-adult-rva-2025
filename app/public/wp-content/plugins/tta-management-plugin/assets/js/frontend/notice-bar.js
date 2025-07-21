jQuery(function($){
    var $msg = $('#tta-notice-message');
    if (!$msg.length) return;

    var expires = parseInt($msg.data('expires'), 10) || 0;
    if (!expires) return;

    var timerSpan = $('<span class="tta-countdown"/>');
    $msg.append(' ').append(timerSpan);

    function update(){
        var diff = expires - Math.floor(Date.now()/1000);
        if (diff <= 0){
            timerSpan.text('');
            clearInterval(interval);
            return;
        }
        var m = Math.floor(diff/60);
        var s = diff%60;
        timerSpan.text('(' + m + ':' + (s < 10 ? '0' : '') + s + ')');
    }
    update();
    var interval = setInterval(update, 1000);
});
