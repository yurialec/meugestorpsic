<?php

namespace App\Http\Traits;

use App\Models\Admin\Plan;
use App\Models\Site\MainText;
use App\Models\Site\SiteAbout;
use App\Models\Site\SiteCarousel;
use App\Models\Site\SiteContact;
use App\Models\Site\SiteSocialMedia;

trait TraitSite
{
    public function mainText()
    {
        return MainText::first();
    }

    public function carousels()
    {
        return SiteCarousel::get()->toArray();
    }

    public function contactSite()
    {
        return SiteContact::first();
    }

    public function socialmedias()
    {
        return SiteSocialMedia::get();
    }

    public function aboutSite()
    {
        return SiteAbout::first();
    }

    public function plans()
    {
        return Plan::get()->toArray();
    }
}
