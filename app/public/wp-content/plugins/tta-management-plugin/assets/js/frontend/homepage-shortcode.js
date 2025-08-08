(function($){
    function animateCounter($el){
        var target = parseInt($el.data('target'),10);
        var duration = 5000;
        var startTime = null;
        function step(timestamp){
            if(!startTime){ startTime = timestamp; }
            var progress = Math.min((timestamp - startTime)/duration,1);
            var eased = 1 - Math.pow(1 - progress, 3); // ease out
            var value = Math.floor(eased * target);
            $el.text(value.toLocaleString());
            if(progress < 1){
                window.requestAnimationFrame(step);
            } else {
                $el.text(target.toLocaleString());
            }
        }
        window.requestAnimationFrame(step);
    }
    $(function(){
        $('.tta-counter').each(function(){
            animateCounter($(this));
        });
    });
})(jQuery);
