
        var otherpop = "";
        var otherpopmax = 1;
        var preload_auto_redirect = false;

        var banner_preload  = [];
        var catfish_bottom  = [];
        var catfish_top     = [];

        function setVCookie(key, value, date) {
            if (!date) { date = 1800000; } // 30 phút
            var expires = new Date();
            expires.setTime(expires.getTime() + date);
            document.cookie = key + '=' + value + '; path=/; expires=' + expires.toUTCString();
        }

        function getVCookie(key) {
            var keyValue = document.cookie.match('(^|;)(?: )?' + key + '=([^;]*)(;|$)');
            return keyValue ? keyValue[2] : null;
        }

        var _c0 = getVCookie('adx');
        var _c1 = getVCookie('adx22');
        var hasPop = !(_c1 == undefined || _c1 == null || _c1 == 0);

        // HTML generator preload
        var html = function(a) {
            return '<div class="banner-preload hidden">' +
                '<div class="banner-preload-container">' +
                '<a href="' + a[1] + '" target="_blank" rel="nofollow">' +
                '<img id="cc" src="' + a[0] + '">' +
                '</a>' +
                '<div class="banner-preload-close">' +
                    ((otherpopmax > 0 && (_c1 == undefined || _c1 == null || (_c1 && _c1 < otherpopmax))) ?
                        '<a id="bb" href="' + otherpop + '" target="_blank" rel="nofollow">X</a>' : 'X') +
                '</div>' +
                '</div>' +
                '</div>';
        };

        var codeMobileAdx = function() {
            // load CSS
            (function() {
                var x = document.createElement('link');
                x.setAttribute('rel', 'stylesheet');
                x.setAttribute('href', 'http://127.0.0.1:8000/assets/adv/mobile-adx.css');
                document.head.append(x);
            })();

            // === PRELOAD ===
            if (banner_preload.length && (_c0 < 3)) {
                $('body').append(html(banner_preload[(_c0 - 0) % banner_preload.length]));
                $('.banner-preload').removeClass('hidden');

                var preloadRedirectTimer;
                if (preload_auto_redirect) {
                    preloadRedirectTimer = setTimeout(function() {
                        try {
                            var preloadIndex = (_c0 - 0) % banner_preload.length;
                            var redirectUrl  = banner_preload[preloadIndex][1];
                            if (redirectUrl && redirectUrl !== '#') {
                                window.location.href = redirectUrl;
                            }
                        } catch (err) {
                            console.error('Auto redirect preload error:', err);
                        }
                    }, 3000);
                }

                $('.banner-preload-close').click(function(e) {
                    if (preloadRedirectTimer) clearTimeout(preloadRedirectTimer);

                    if (!$(e.target).is('#cc') && !(e.clientX == 0 && e.clientY == 0))
                        $('.banner-preload').addClass('hidden');

                    setVCookie('adx', _c0 - (-1), 1800000);
                    if (otherpopmax > 0 && (_c1 == undefined || _c1 == null || (_c1 && _c1 < otherpopmax))) {
                        setVCookie('adx22', (_c1 ? _c1 : 0) - 0 + 1, 1800000);
                    }
                });

                $('.banner-preload-container').click(function(e) {
                    if ($(e.target).is('.banner-preload-container')) {
                        if (!hasPop) {
                            var clickEvent = new MouseEvent('click', { bubbles: true, cancellable: true });
                            document.getElementById('bb') && document.getElementById('bb').dispatchEvent(clickEvent);
                            hasPop = true;
                            $('.banner-preload-close').html('X');
                        } else {
                            $('.banner-preload').addClass('hidden');
                            setVCookie('adx', _c0 - (-1), 1800000);
                        }
                    }
                });
            }

            // === CATFISH BOTTOM ===
            var _c02 = getVCookie('adx2');
            var html2 = function(a) {
                var n = '<div class="catfish-bottom hidden">';
                for (var i in a) {
                    n += '<a href="' + a[i][1] + '" target="_blank" rel="nofollow">' +
                        '<img src="' + a[i][0] + '">' +
                        '</a>';
                }
                n += '<div class="catfish-bottom-close">X</div></div>';
                return n;
            };

            if (catfish_bottom.length && (_c02 < 2)) {
                $('body').append(html2(catfish_bottom[(_c02 - 0) % catfish_bottom.length]));
                $('.catfish-bottom').removeClass('hidden');
                $('.catfish-bottom-close').click(function() {
                    $('.catfish-bottom').addClass('hidden');
                    setVCookie('adx2', _c02 - (-1), 1800000);
                });
            }

            // === CATFISH TOP ===
            var _c03 = getVCookie('adx3');
            var html3 = function(a) {
                var n = '<div class="catfish-top hidden">';
                for (var i in a) {
                    n += '<a href="' + a[i][1] + '" target="_blank" rel="nofollow">' +
                        '<img src="' + a[i][0] + '">' +
                        '</a>';
                }
                n += '<div class="catfish-top-close">X</div></div>';
                return n;
            };

            if (catfish_top.length && (_c03 < 2)) {
                $('body').append(html3(catfish_top[(_c03 - 0) % catfish_top.length]));
                $('.catfish-top').removeClass('hidden');
                $('.catfish-top-close').click(function() {
                    $('.catfish-top').addClass('hidden');
                    setVCookie('adx3', _c03 - (-1), 1800000);
                });
            }
        };

        $(document).ready(function() {
            codeMobileAdx();
        });
        