<?php

declare(strict_types=1);

namespace App\Traits;

trait AdvPathTrait
{
    
    // section-brand.js
    public function vlHeaderAdx()
    {
        return public_path('assets/adv/vl-header-adx.js');
    }

    public function vlDesktopAdx()
    {
        return public_path('assets/adv/vl-desktop-adx.js');
    }

    public function vlMobileAdx()
    {
        return public_path('assets/adv/vl-mobile-adx.js');
    }

    public function vlUnderAdx()
    {
        return public_path('assets/adv/vl-underplayer-adx.js');
    }
}
