
<script type="text/javascript" src="{{ url('assets/adv/vl-header-adx.js?v=' . time()) }}?v=1.0"></script>

@if ($isDesktop)
    <script type="text/javascript" src="{{ url('assets/adv/vl-desktop-adx.js?v=' . time()) }}?v=1.0"></script>
@else
    <script type="text/javascript" src="{{ url('assets/adv/vl-mobile-adx.js?v=' . time()) }}?v=1.0"></script>
@endif

{{-- push js --}}
@if (isset($pushJs))
    @foreach ($pushJs as $push)
        {!! $push->script !!} <br>
    @endforeach
@endif

{{-- popup js --}}
@if (isset($popupJs))
    @foreach ($popupJs as $popup)
        {!! $popup->script !!} <br>
    @endforeach
@endif

{{-- bottom script --}}
@if (isset($bottomScript))
    @foreach ($bottomScript as $bottom)
        {!! $bottom->script !!} <br>
    @endforeach
@endif


@if (isset($popunder) && count($popunder) > 0)
    <script src="{{ url('assets/adv/popunder.js') }}"></script>
@endif

<script>
    (function() {
        var KEY = 'adv_close_count';
        var MAX = 3; // bấm X 3 lần thì thôi hiện
        var EXPIRE_HOURS = 3; // cookie sống 3 giờ

        function getCookie(name) {
            return document.cookie.split('; ').reduce(function(prev, cur) {
                var p = cur.split('=');
                return p[0] === name ? decodeURIComponent(p[1]) : prev;
            }, null);
        }

        function setCookie(name, value, hours) {
            var d = new Date();
            d.setTime(d.getTime() + hours * 3600 * 1000);
            document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + d.toUTCString() + '; path=/';
        }

        document.addEventListener('DOMContentLoaded', function() {
            var box = document.getElementById('bottomRightAdvs');
            if (!box) return;

            var count = parseInt(getCookie(KEY) || '0', 10);
            if (count >= MAX) {
                box.style.display = 'none';
                return;
            }

            var btn = box.querySelector('.ad-close');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var current = parseInt(getCookie(KEY) || '0', 10) + 1;
                    setCookie(KEY, current, EXPIRE_HOURS);
                    box.style.display = 'none';
                });
            }
        });
    })();
</script>

<style>
    .adv-inline-row {
        display: flex;
        gap: 12px;
        margin: 10px 0 16px
    }

    .adv-inline-row .ad-col {
        flex: 1
    }

    .adv-inline-row .ad-col img {
        width: 100%;
        height: auto;
        display: block
    }

    .adv-inline-row .ad-col.left {
        flex: 2
    }

    .adv-inline-row .ad-col.right {
        flex: 3
    }

    @media (max-width:480px) {
        .adv-inline-row {
            flex-direction: column
        }

        .adv-inline-row .ad-col {
            flex: unset
        }
    }
</style>

@php
    $ads = [];
    if (!empty($banners)) {
        foreach ($banners as $b) {
            $path = $b->des_media ?: $b->mob_media;
            if ($path) {
                $ads[] = [
                    'href' => $b->link ?: '#',
                    'src'  => route('web.adv.banner', ['path' => $path]),
                    'alt'  => $b->title ?: 'Quảng cáo',
                ];
            }
        }
    }
@endphp

@if (!empty($ads))
<script>
  const BANNERS = @json($ads, JSON_UNESCAPED_UNICODE);

  (function () {
    if (!Array.isArray(BANNERS) || BANNERS.length === 0) return;

    var startAfter = 1;  
    var gapSections = 1;

    function renderBanner(ad, slotIndex) {
      return '<div class="adv-inline" data-slot="content-left-'+slotIndex+'">'+
               '<a href="'+(ad.href||'#')+'" target="_blank" rel="noopener nofollow">'+
                 '<img src="'+ad.src+'" alt="'+(ad.alt||'Quảng cáo')+'" loading="lazy">'+
               '</a>'+
             '</div>';
    }

    function placeBanners() {
      var wrap = document.querySelector('.content-left');
      if (!wrap) return;

      var sections = wrap.querySelectorAll(':scope > section');
      if (sections.length === 0) return;

      for (var i = 0; i < BANNERS.length; i++) {
        var secIndex = startAfter + i * (gapSections + 1);
        if (secIndex >= sections.length) break;

        if (wrap.querySelector('.adv-inline[data-slot="content-left-'+i+'"]')) continue;

        sections[secIndex].insertAdjacentHTML('afterend', renderBanner(BANNERS[i], i));
      }
    }

    document.addEventListener('DOMContentLoaded', placeBanners);

    var mo = new MutationObserver(function () {
      placeBanners();

      var wrap = document.querySelector('.content-left');
      if (!wrap) return;

      var sections = wrap.querySelectorAll(':scope > section');
      var maxSlots = Math.max(0, Math.floor((sections.length - startAfter + (gapSections)) / (gapSections + 1)));
      var shown = wrap.querySelectorAll('.adv-inline[data-slot^="content-left-"]').length;

      if (shown >= Math.min(BANNERS.length, maxSlots)) mo.disconnect();
    });
    mo.observe(document.documentElement, { childList: true, subtree: true });
  })();
</script>
@endif
