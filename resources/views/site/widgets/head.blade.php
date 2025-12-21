<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />

{!! $settings['google_console'] ?? null !!}
{!! $settings['google_analytics'] ?? null !!}
{!! $settings['microsoft_clarity'] ?? null !!}

@if ($isDesktop)
    <link href="{{ url('assets/adv/desktop-adx.css') }}?v=1.0" rel="stylesheet" type="text/css">
@else
    <link href="{{ url('assets/adv/mobile-adx.css') }}?v=1.0" rel="stylesheet" type="text/css">
@endif

@if (isset($headerScript))
    @foreach ($headerScript as $header)
        {!! $header->script !!}
    @endforeach
@endif

<style>
    nav.nav_header {
        margin-bottom: 0;
    }

    #section-brand {
        width: 100%;
        height: 100%;
    }

    .banner-row {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        gap: 20px;
        margin-top: 20px;
    }

    .banner-side {
        width: 160px;
        flex-shrink: 0;
    }

    .banner-side img {
        width: 100%;
        height: auto;
        display: block;
    }

    .adv-center {
        flex: 1;
        max-width: 100%;
    }

    .main-content {
        margin-top: 20px;
    }


    .banner-row {
        width: 70%;
        margin: 0 auto;
    }

    .fixed-banner {
        position: fixed;
        top: 130px;
        z-index: 999;
        width: 120px;
    }

    .fixed-left {
        left: 8%;
    }

    .fixed-right {
        right: 8%;
    }

    .fixed-banner img {
        width: 100%;
        height: auto;
        display: block;
    }

    .banner-side img {
        margin-bottom: 5px;
    }


    /* #section-brand {
            display: flex;
            flex-wrap: wrap;
        } */

    /* #section-brand p {
            width: 50%;
            box-sizing: border-box;
        } */

    @media screen and (max-width:1200px) {
        .banner-row {
            width: 100%;
            margin: 0 auto;
        }

        .banner-row {

            margin-top: 0px;
        }

        .main-content {
            margin-top: 0px;
        }

        div#section-brand {
            padding: 15px;
        }

        div#section-brand p {
            margin-bottom: 0;
        }

    }


    .banner-catfish-bottom img {
        width: 100%;
    }

    .banner-catfish-bottom a {
        /* width: 80%; */
    }

    .banner-catfish-bottom:nth-child(odd) img {
        width: 80%;
        display: block;
        margin-left: auto;
    }

    .banner-catfish-bottom:nth-child(even) img {
        width: 80%;
        display: block;
        margin-right: auto;
    }


    .banner-catfish-bottom {
        box-shadow: none;
    }


    .banner-preload-container>a {
        max-width: 560px;
    }

    #section-brand p {
        width: 49%;
        box-sizing: border-box;
    }

    .section-header-brand {
        width: 67%;
        margin: 0 auto;
    }

    #section-brand {
        display: flex;
        flex-wrap: wrap;
    }

    #section-brand img {
        width: 100%;
    }

    .sidebar-ads {
        position: sticky;
        top: 80px;
        z-index: 10;
    }

    #section-brand img {
        object-fit: scale-down;
    }


    /* CSS */
    .advrightfooter {
        position: fixed;
        right: 12px;
        bottom: 50%;
        z-index: 9999;
    }

    .advrightfooter a img {
        display: block;
    }

    .advrightfooter .ad-close {
        position: absolute;
        top: -8px;
        /* cho nút tràn ra ngoài 1 chút giống hình */
        right: -8px;
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 50%;
        background: #F7D54A;
        /* nền vàng như mẫu */
        color: #111;
        font-size: 20px;
        line-height: 28px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .25);
    }

    .wrapper .footer__main,
    .wrapper .header__main,
    .wrapper .main,
    .wrapper .menus__wrapper {
        margin-left: auto;
        margin-right: auto;
        max-width: 65%;
        padding-left: 0px;
        padding-right: 0px;
    }

    #section-brand {
        margin: 0 !important;
    }

    .banner-catfish-bottom:nth-child(odd) img {
        width: 100%;
    }

    .adv-inline {
        display: none;
    }

    @media screen and (max-width:720px) {
        .fixed-banner {
            display: none;
        }

        #section-brand p {
            width: 100%;
            box-sizing: border-box;
        }

        .catfish-bottom {
            text-align: center;
        }

        .catfish-bottom img {
            width: 100%;
        }

        .catfish-bottom a {
            width: 50%;
            box-sizing: border-box;
        }

        .catfish-bottom {
            display: flex;
            flex-wrap: wrap;
        }

        .section-header-brand {
            width: 100%;
            margin: 0 auto;
        }

        #section-brand p {
            width: 50%;
        }

        #section-brand {
            display: inline-flex;
            flex-wrap: wrap;
            padding: 0px !important;
        }

        div#section-brand {
            display: block;
        }

        #section-brand img {
            height: auto;
        }

        div#section-brand a {
            /* display: block; */
        }

        div#section-brand p {
            width: 100%;
        }

        div#section-brand {
            padding: 10px 0px;
        }

        .section-header-brand {
            display: block;
            position: relative;
        }

        nav.nav_header {
            margin-bottom: 10px;
        }

        .section-header-brand {
            float: inline-end;
        }

        .advrightfooter a img {
            display: block;
            width: 70px !important;
        }

        .advrightfooter {
            display: block;
        }

        div#bottomRightAdvs {
            bottom: 3%;
        }

        .adv-inline {
            display: block;
        }

        .adv-inline img {
            display: block;
            margin: 0 auto;
            padding: 15px 0px;
        }

    }

    @media screen and (max-width:600px) {

        .wrapper .footer__main,
        .wrapper .header__main,
        .wrapper .main,
        .wrapper .menus__wrapper {
            margin-left: auto;
            margin-right: auto;
            max-width: 100%;
            padding-left: 0px;
            padding-right: 0px;
            padding: 0 10px;
        }

        .footer-inner {
            padding: 20px 10px;
        }

        .sidebar-ads {
            display: none;
        }

        #section-brand p {
            display: none;
        }

        #section-brand p:first-of-type {
            display: block;
        }

        #section-brand p {
            width: 100%;
            box-sizing: border-box;
            width: 98%;
            max-width: 728px;
            margin: 3px 0 !important;
        }

        #section-brand {
            margin: 0 !important;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            align-items: start;
            justify-items: center;
        }

    }
</style>

@php($siteCss = \DB::table('settings')->where('key', 'custom_css')->first())
@if (!empty($siteCss))
    <style id="custom-css">
        {!! $siteCss->value !!}
    </style>
@endif
