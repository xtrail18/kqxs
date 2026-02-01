<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "KQXS ONLINE",
    "alternateName": "KQSX.Online – KQXS - Kết quả xổ số 3 miền - Nhanh và chính xác",
    "@id": "https://kqxs.online/",
    "url": "https://kqxs.online/",
    "logo": "https://kqxs.online/storage/uploads/logo/logo-1769660360.png",
    "image": "https://kqxs.online/storage/uploads/logo/logo-1769660360.png",
    "description": "Kết quả Xổ Số (kqxs.online) – Cập nhật kết quả xổ số 3 miền Bắc, Trung, Nam nhanh & chính xác. Thống kê kqxsmb, kqxsmt, kqxsmn nhanh nhất",
    "telephone": "0359121723",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "146 Săm Brăm, Ea Tam",
        "addressLocality": "Buôn Ma Thuột",
        "addressRegion": "Đắk Lắk",
        "postalCode": "63000",
        "addressCountry": "VN"
    },
    "sameAs": [
        "https://www.facebook.com/kqxsonline1/",
        "https://www.youtube.com/channel/UCbxlMw46jrmRzy2Uhj-wyNQ",
        "https://www.pinterest.com/kqxsonline1",
        "https://www.twitch.tv/kqxsonline",
        "https://www.reddit.com/user/Icy-Antelope7394/",
        "https://www.tumblr.com/kqxsonline1",
        "https://gravatar.com/kqxsonline1",
        "https://500px.com/p/kqxsonline",
        "https://issuu.com/kqxsonline",
        "https://x.com/kqxsonline11",
        "https://vimeo.com/kqxsonline1",
        "https://about.me/kqxsonline",
        "https://linktr.ee/kqxsonline",
        "https://heylink.me/kqxsonline/",
        "https://www.instagram.com/kqxsonline",
        "https://www.blogger.com/profile/08272991881097121320",
        "https://sites.google.com/view/kqxsonline1",
        "https://www.diigo.com/profile/kqxsonline1",
        "https://github.com/kqxsonline1",
        "https://bit.ly/4qVIblw"
    ]
}
</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "✅Kết Quả Xổ Số Kiến Thiết 3 Miền",
            "item": "https://kqxs.online/"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "✅Trực Tiếp Kết Quả Xổ Số",
            "item": "https://kqxs.online/"
        }
    ]
}
</script>

<header class="header header-modern">
    <div class="header-wrapper">
        <div class="header-left">
            <span class="btn-pushbar-3 menu-toggle" data-pushbar-target="left">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </span>
            <div class="header-logo">
                <a href="/">
                    <img alt="trang chu xo so" class="header-logo-img"
                        src="{{ isset($settings['logo']) && $settings['logo'] != '' ? sourceSetting($settings['logo']) : '/images/logo.svg' }}"
                        width="135" height="48">
                </a>
            </div>
        </div>

        <div class="header-center">
            <div class="header-time">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>{{ today_vietnamese() }}</span>
            </div>
        </div>

        <div class="header-right">
            @auth
                <div class="user-dropdown">
                    <button class="user-btn" id="userDropdownBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span class="user-name">{{ Auth::user()->name ?? 'Tài khoản' }}</span>
                    </button>
                    <div class="dropdown-menu" id="userDropdown">
                        <a href="/tai-khoan" class="dropdown-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            Tài khoản
                        </a>
                        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="dropdown-item logout-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="auth-buttons">
                    <a href="/dang-nhap" class="btn-auth btn-login">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        <span>Đăng nhập</span>
                    </a>
                    <a href="/dang-ky" class="btn-auth btn-register">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <line x1="20" y1="8" x2="20" y2="14"></line>
                            <line x1="23" y1="11" x2="17" y2="11"></line>
                        </svg>
                        <span>Đăng ký</span>
                    </a>
                </div>
            @endauth
        </div>
    </div>
</header>
