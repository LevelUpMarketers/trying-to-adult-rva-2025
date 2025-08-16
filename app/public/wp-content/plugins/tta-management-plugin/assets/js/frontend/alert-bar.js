jQuery(function($){
    var $bar = $('#tta-alert-bar.tta-alert-cart');
    if(!$bar.length) return;
    var expires = parseInt($bar.data('expires'),10) || 0;
    if(!expires) return;
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
