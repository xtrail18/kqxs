<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <link rel="shortcut icon"
        href="{{ isset($settings['favicon']) && $settings['favicon'] != '' ? sourceSetting($settings['favicon']) : '/favicon.ico' }}"
        type="image/x-icon">

    {{-- Title & basic meta --}}
    @if (request()->is('/'))
        <title>{{ $settings['title'] ?? '' }}</title>

        @if (!empty($settings['description']))
            <meta name="description" content="{{ $settings['description'] }}">
        @endif
    @else
        <title>{{ $metaSeo['title'] ?? '' }}</title>

        @if (!empty($metaSeo['desc']))
            <meta name="description" content="{{ $metaSeo['desc'] }}">
        @endif
    @endif


    @if (!empty($metaSeo['keywords']))
        <meta name="keywords" content="{{ $metaSeo['keywords'] }}">
    @endif

    @if (!empty($metaSeo['robots']))
        <meta name="robots" content="{{ $metaSeo['robots'] }}">
    @endif

    @if (!empty($metaSeo['author']))
        <meta name="author" content="{{ $metaSeo['author'] }}">
    @endif

    {{-- Canonical & favicon --}}
    @if (!empty($metaSeo['canonical']))
        <link rel="canonical" href="{{ $metaSeo['canonical'] }}">
    @endif
   

    {{-- Optional legacy meta (nếu muốn giữ) --}}
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="format-detection" content="telephone=no">

    {{-- Open Graph / Twitter (chỉ render các key có trong mảng) --}}
    @if (!empty($metaSeo['og']) && is_array($metaSeo['og']))
        @foreach ($metaSeo['og'] as $prop => $val)
            @if (!is_null($val) && $val !== '')
                <meta property="{{ $prop }}" content="{{ $val }}">
            @endif
        @endforeach
    @endif

    {{-- JSON-LD --}}
    @if (!empty($metaSeo['ld_json']) && is_array($metaSeo['ld_json']))
        @foreach ($metaSeo['ld_json'] as $json)
            @if (is_string($json) && trim($json) !== '')
                <script type="application/ld+json">
        {
            !!$json!!
        }
    </script>
            @endif
        @endforeach
    @endif

    {{-- CSS nội bộ của site --}}
    <link rel="stylesheet" href="{{ url('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/xsmbresult.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/styleall.min.css') }}">


    {{-- Style lấy từ upstream đã chuẩn hoá trong controller --}}
    {!! $headStyles ?? '' !!}

    <link rel="stylesheet" href="{{ url('assets/css/tnresult.min.css') }}">

    <style>
        .the-article-author {
            display: none;
        }
    </style>

    @include('site.widgets.head')

</head>


<body>
    <header class="header">
        <div class="main-content"><span class="btn-pushbar-3" data-pushbar-target="left"><img alt="menu xo so"
                    class="icon-menu" src="{{ url('/images/ic_menu_24px.svg') }}"></span>
            <div class="header-logo"><a href="/">

                    <img alt="trang chu xo so" class="header-logo-img"
                        src="{{ isset($settings['logo']) && $settings['logo'] != '' ? sourceSetting($settings['logo']) : '/images/logo.svg' }}"
                        width="135" height="48">
                </a>
            </div>
            <div class="header-time">{{ today_vietnamese() }}</div>
        </div>
    </header>

    {!! $nav ?? '' !!}

    <div class="section-header-brand">
        <div id="section-brand"></div>
    </div>


    <main class="main">
        <div class="main-content">
            {!! $breadcrumb ?? '' !!}

            <div class="content-left">
                {!! $main ?? '' !!}
            </div>

            @include('site.widgets.sidebar')

        </div>
    </main>

    {!! $navMobile ?? '' !!}

    <footer class="footer" id="e_c" p="p-top-10 p-bo-10" h="P90ybwbbDGvSuxYOVzDkHA==">
        <section class="main-content">
            <div class="nav-bottom">
                <a href="/" class="nav-bottom-item" title="Trang chủ">Trang chủ</a>
                <a href="/xo-so-mien-bac/xsmb-p1.html" class="nav-bottom-item" title="XSMB">XSMB</a>
                <a href="/xo-so-mien-nam/xsmn-p1.html" class="nav-bottom-item" title="XSMN">XSMN</a>
                <a href="/xo-so-mien-trung/xsmt-p1.html" class="nav-bottom-item" title="XSMT">XSMT</a>
                <a href="/xo-so-tu-chon-mega-645.html" class="nav-bottom-item" title="Mega 6/45">Mega 6/45</a>
                <a href="/ket-qua-xs-keno.html" class="nav-bottom-item" title="Xổ số Keno">Xổ số Keno</a>
                <a href="/phan-tich-kqxs-c407-p1.html" class="nav-bottom-item" title="Phân tích">Phân tích</a>
                <a href="/cau-mien-bac/cau-bach-thu.html" class="nav-bottom-item" title="Thống kê vị trí">Thống
                    kê vị trí</a>
                <a href="/thong-ke-lo-gan.html" class="nav-bottom-item" title="Thống kê Lô gan">Thống kê Lô gan</a>
                <a href="/thong-ke-tong-hop.html" class="nav-bottom-item" title="Thống kê tổng hợp">Thống kê tổng
                    hợp</a>
                <a href="/thong-ke-keno-theo-chu-ky.html" class="nav-bottom-item"
                    title="Thống kê Keno theo chu kỳ">Thống kê Keno theo chu kỳ</a>
                <a href="/thong-ke-tan-suat-loto.html" class="nav-bottom-item" title="Thống kê tần suất loto ">Thống
                    kê tần suất loto </a>
                <a href="/thong-ke-tan-suat-cap-loto.html" class="nav-bottom-item"
                    title="Thống kê tần suất cặp loto">Thống kê tần suất cặp loto</a>
                <a href="/thong-ke-giai-dac-biet-ngay-mai.html" class="nav-bottom-item"
                    title="Thống kê giải đặc biệt ngày mai">Thống kê giải đặc biệt ngày mai</a>
                @php
                    $firstGenre = \App\Models\Genre::orderBy('id')->first();
                    $href = $firstGenre
                        ? route('genre', ['slug' => $firstGenre->slug], false) ?? url('/genre/' . $firstGenre->slug)
                        : '/';
                    $title = $firstGenre->name ?? 'Tin Xổ Số';
                @endphp

                <a href="{{ $href }}" class="nav-bottom-item"
                    title="{{ $title }}">{{ $title }}</a>

            </div>

            <div class="footer-content">
                <div class="footer-add">
                    <div class="copyright">
                        <p><strong>Copyright © {{ date('Y') }} {{ $settings['site_name'] }}, All Rights
                                Reserved</strong></p>

                        <p><a href="#">Điều
                                khoản thỏa thuận và quy ước sử dụng dịch vụ</a></p>
                        <p><a href="#">Chính
                                sách bảo mật và bảo vệ dữ liệu cá nhân</a></p>
                    </div>

                    <p class="copyright">Nội dung và dịch vụ trên website được chúng tôi cập nhật từ nguồn công khai,
                        chỉ mang tính chất tham khảo. Vui lòng đối chiếu với kết quả chính thức được công bố bởi các
                        công ty xổ số kiến thiết.</p>
                </div>
            </div>
        </section>

    </footer>

    <div id=bottomMobileAdvs class=advfixfooter></div>

    <!-- HTML -->

    @if (isset($fixedBanner))
        @php
            $mediaPath = $fixedBanner->des_media ?: $fixedBanner->mob_media;
            $bannerUrl = $mediaPath ? route('web.adv.banner', ['path' => $mediaPath]) : null;
        @endphp

        @if (!empty($bannerUrl))
            <div id="bottomRightAdvs" class="advrightfooter">
                <button class="ad-close" aria-label="Đóng quảng cáo" title="Đóng">&times;</button>
                <a href="{{ $fixedBanner->link }}" target="_blank" rel="noopener">
                    <img src="{{ $bannerUrl }}" alt="{{ $fixedBanner->title }}" style="width:100px">
                </a>
            </div>
        @endif
    @endif

    <a href="#" class="backtotop">
        <img alt="len dau" class="top-arrow" src="{{ url('images/top-arrow.svg') }}">
    </a>

    <div data-app="Wap" data-position="Ballon"></div>

    <script src="{{ url('/assets/js/jsall.min.js') }}"></script>

    <script>
        function getHNTime() {
            var offset = 7;
            var d = new Date();
            localTime = d.getTime();
            localOffset = d.getTimezoneOffset() * 60000;
            utc = localTime + localOffset;
            var nd = new Date(utc + (3600000 * offset));
            return nd;
        }
    </script>

    <script>
        (function() {
            // Chỉ thực thi nếu có phần tử #custom-month
            if (!document.getElementById('custom-month')) return;

            // Tạo thẻ <script> để tải calendar.min.js động
            var script = document.createElement('script');
            script.src = "{{ url('assets/js/calendar.min.js') }}";
            script.onload = function() {
                // Khi file calendar.min.js đã load xong -> chạy đoạn logic
                var d = new Date();
                var yyyy = d.getFullYear();
                var mm = String(d.getMonth() + 1).padStart(2, '0');
                var dd = String(d.getDate()).padStart(2, '0');
                var today = yyyy + '-' + mm + '-' + dd;

                if (typeof $.cookie === 'function') {
                    $.cookie('SelectedDate', today, {
                        expires: 7,
                        path: '/'
                    });
                } else {
                    var exp = new Date();
                    exp.setDate(exp.getDate() + 7);
                    document.cookie = 'SelectedDate=' + encodeURIComponent(today) +
                        '; expires=' + exp.toUTCString() + '; path=/';
                }

                var kqUrl = "/xsmb-";
                CreateCalendarUC();
            };

            document.head.appendChild(script);
        })();
    </script>

    <div class="pushbar_overlay"></div>
    <link rel=stylesheet href=//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css>

    {!! $tailScripts !!}

    @php
        $needLivePatch = preg_match('#/js/lottery[_\-]?live[_\-]?all[^"\']*\.js#i', $tailScripts ?? '');
    @endphp

    @if ($needLivePatch)
        <script>
            (function(d, w) {
                var SRC = "/js/lottery_live_all.min.js?v={{ time() }}";

                w.__liveQ = w.__liveQ || [];
                if (typeof w.LiveMBV2 !== "function") w.LiveMBV2 = function() {
                    w.__liveQ.push(arguments);
                };
                if (typeof w.getHNTime !== "function") w.getHNTime = function() {
                    return new Date();
                };

                function hasLib() {
                    return Array.prototype.some.call(d.scripts, s => /\/js\/lottery[_-]?live[_-]?all/i.test(s.src || ""));
                }

                function load(cb) {
                    if (hasLib() || d.querySelector('script[data-live-lib]')) return cb();
                    var s = d.createElement("script");
                    s.src = SRC;
                    s.defer = true;
                    s.setAttribute("data-live-lib", "1");
                    s.onload = cb;
                    s.onerror = cb;
                    d.head.appendChild(s);
                }

                function waitReady(ms) {
                    var t0 = Date.now();
                    (function loop() {
                        if (typeof w.LiveMBV2 === "function") {
                            var q = w.__liveQ;
                            if (q && q.length) {
                                for (var i = 0; i < q.length; i++) try {
                                    w.LiveMBV2.apply(w, q[i]);
                                } catch (e) {}
                                q.length = 0;
                            }
                            if (typeof w.initLive === "function") try {
                                w.initLive();
                            } catch (e) {}
                            return;
                        }
                        if (Date.now() - t0 > ms) return;
                        setTimeout(loop, 120);
                    })();
                }
                load(function() {
                    waitReady(10000);
                });
            })(document, window);
        </script>
    @endif

    @include('site.widgets.script')

    @if (isset($brands) && count($brands) > 0)
        @include('site.widgets.brand-script')
    @endif

</body>

</html>
