jQuery(function($){
    var $bar = $('#tta-notice-bar');
    var expires = parseInt($bar.data('expires'), 10);
    if (expires) {
        var $msg = $('#tta-notice-message');
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
            timerSpan.text('('+m+':' + (s<10?'0':'')+s+')');
        }
        update();
        var interval = setInterval(update,1000);
    }
});
