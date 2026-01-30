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
    /* ==================== MODERN HEADER STYLES ==================== */
    .header-modern {
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 0;
        z-index: 1000;
        padding: 0;
        border-bottom: 1px solid #e8e8e8;
    }

    .header-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        gap: 15px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    /* Menu toggle - hidden on desktop, shown on mobile */
    .menu-toggle {
        display: none;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: #f5f5f5;
        color: #333;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
    }

    .menu-toggle:hover {
        background: #e8e8e8;
    }

    .header-logo a {
        display: flex;
        align-items: center;
    }

    .header-logo-img {
        height: 45px;
        width: auto;
        transition: transform 0.3s ease;
    }

    .header-logo a:hover .header-logo-img {
        transform: scale(1.03);
    }

    .header-center {
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .header-time {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f0f7ff;
        padding: 8px 16px;
        border-radius: 20px;
        color: #1e3a5f;
        font-size: 14px;
        font-weight: 500;
    }

    .header-time svg {
        color: #2d5a87;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Auth Buttons */
    .auth-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-auth {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .btn-login {
        background: #f5f5f5;
        color: #333;
        border: 1px solid #e0e0e0;
    }

    .btn-login:hover {
        background: #e8e8e8;
        border-color: #d0d0d0;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-register {
        background: #c90205;
        color: #fff;
        border: none;
        box-shadow: 0 2px 8px rgba(201, 2, 5, 0.3);
    }

    .btn-register:hover {
        background: #a80104;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(201, 2, 5, 0.4);
    }

    /* User Dropdown */
    .user-dropdown {
        position: relative;
    }

    .user-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #f5f5f5;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        color: #333;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .user-btn:hover {
        background: #e8e8e8;
    }

    .user-btn svg {
        color: #1e3a5f;
    }

    .user-name {
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dropdown-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
        min-width: 180px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 1001;
        overflow: hidden;
    }

    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        transition: background 0.2s ease;
        border: none;
        background: none;
        width: 100%;
        cursor: pointer;
    }

    .dropdown-item:hover {
        background: #f5f5f5;
    }

    .dropdown-item svg {
        color: #666;
    }

    .logout-btn {
        color: #dc3545;
        border-top: 1px solid #eee;
    }

    .logout-btn svg {
        color: #dc3545;
    }

    /* Mobile Responsive */
    @media screen and (max-width: 768px) {
        .header-wrapper {
            padding: 10px 15px;
        }

        /* Show menu toggle on mobile */
        .menu-toggle {
            display: flex;
        }

        .header-center {
            display: none;
        }

        .btn-auth span {
            display: none;
        }

        .btn-auth {
            padding: 10px;
            border-radius: 50%;
        }

        .btn-auth svg {
            width: 18px;
            height: 18px;
        }

        .user-name {
            display: none;
        }

        .user-btn {
            padding: 10px;
            border-radius: 50%;
        }

        .header-logo-img {
            height: 38px;
        }
    }

    @media screen and (max-width: 480px) {
        .header-wrapper {
            padding: 8px 10px;
            gap: 10px;
        }

        .menu-toggle {
            width: 36px;
            height: 36px;
        }

        .auth-buttons {
            gap: 8px;
        }
    }

    /* ==================== END HEADER STYLES ==================== */

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

    /* Sub Keywords */
    .sub-key {
       margin: auto;
        width: 100%;
        max-width: 1140px;
        min-width: 240px;
    }
    .sub-key-content {
        margin-top: 20px;
        float: left;
        width: 53.5%;
        position: relative;
        padding-right: 16px;
    }

    .sub-key-item {
        display: inline-block;
        padding: 2px 4px;
        color: #495057;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    @media screen and (max-width: 720px) {
        .sub-key {
            float: none;
            width: 100%;
            padding: 10px 15px;
        }

        .sub-key-item {
            font-size: 12px;
            padding: 5px 12px;
        }
    }
</style>

@php($siteCss = \DB::table('settings')->where('key', 'custom_css')->first())
@if (!empty($siteCss))
    <style id="custom-css">
        {!! $siteCss->value !!}
    </style>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // User dropdown toggle
        const userBtn = document.getElementById('userDropdownBtn');
        const dropdown = document.getElementById('userDropdown');

        if (userBtn && dropdown) {
            userBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target) && !userBtn.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        }
    });
</script>
