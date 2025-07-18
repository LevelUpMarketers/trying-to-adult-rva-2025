jQuery(function($){
    var cfg = window.ttaNoticeBar || {};
    var expires = parseInt(cfg.expires || 0, 10);

    var $bar = $('<div id="tta-notice-bar" class="tta-notice-bar container wpex-relative wpex-py-15 wpex-md-flex wpex-justify-between wpex-items-center wpex-text-center wpex-md-text-initial wpex-flex-row-reverse"/>');
    var $links = $('<div class="tta-notice-links"/>').appendTo($bar);
    $links.append('<a href="/the-tta-partner-program/">Partner With Us</a>');
    $links.append('<span>-</span>');
    $links.append('<a href="/rules-policies">Rules &amp; Policies</a>');

    var socials = '<div id="top-bar-social" class="top-bar-right wpex-mt-10 wpex-md-mt-0 social-style-flat-color">\
        <ul id="top-bar-social-list" class="wpex-inline-block wpex-list-none wpex-align-bottom wpex-m-0 wpex-last-mr-0">\
            <li class="wpex-inline-block wpex-mr-5 jre-top-bar-meetup-icon">\
                <a style="background:none!important" href="/" title="Meetup" class="wpex-meetup wpex-social-btn wpex-social-btn-flat wpex-social-bg" rel="noopener noreferrer">\
                    <img src="https://trying-to-adult-rva-2025.local/wp-content/uploads/2022/12/cropped-TTA2_Full-1.png" alt="">\
                    <span class="screen-reader-text">Trying to Adult Logo</span>\
                </a>\
            </li>\
            <li class="wpex-inline-block wpex-mr-5 jre-top-bar-meetup-icon">\
                <a href="https://www.meetup.com/trying-to-adult/" title="Meetup" target="_blank" class="wpex-meetup wpex-social-btn wpex-social-btn-flat wpex-social-bg" rel="noopener noreferrer">\
                    <img src="https://trying-to-adult-rva-2025.local/wp-content/uploads/2023/01/Background.png" alt="">\
                    <span class="screen-reader-text">Meetup</span>\
                </a>\
            </li>\
            <li class="wpex-inline-block wpex-mr-5">\
                <a href="https://www.facebook.com/groups/tryingtoadultrva" title="Facebook" target="_blank" class="wpex-facebook wpex-social-btn wpex-social-btn-flat wpex-social-bg" rel="noopener noreferrer">\
                    <span class="ticon ticon-facebook" aria-hidden="true"></span>\
                    <span class="screen-reader-text">Facebook</span>\
                </a>\
            </li>\
            <li class="wpex-inline-block wpex-mr-5">\
                <a href="https://www.instagram.com/tryingtoadultrva/" title="Instagram" target="_blank" class="wpex-instagram wpex-social-btn wpex-social-btn-flat wpex-social-bg customize-unpreviewable" rel="noopener noreferrer">\
                    <span class="ticon ticon-instagram" aria-hidden="true"></span>\
                    <span class="screen-reader-text">Instagram</span>\
                </a>\
            </li>\
            <li class="wpex-inline-block wpex-mr-5">\
                <a href="mailto:contact@tryingtoadultrva.com" title="Email" class="wpex-email wpex-social-btn wpex-social-btn-flat wpex-social-bg">\
                    <span class="ticon ticon-envelope" aria-hidden="true"></span>\
                    <span class="screen-reader-text">Email</span>\
                </a>\
            </li>\
        </ul>\
    </div>';
    $links.append(socials);
    $links.append('<a href="' + cfg.cart_url + '" class="tta-cart-link">Cart</a>');

    var $msg = $('<div id="tta-notice-message" class="tta-notice-message"/>').html(cfg.message || '');
    $bar.append($msg);

    $('body').prepend($bar);

    if (expires) {
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
    }
});
