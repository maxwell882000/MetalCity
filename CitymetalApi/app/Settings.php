<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Settings extends Model
{
    protected $fillable = [
        'employer_tariff_1', 'employer_tariff_2', 'employer_tariff_3',
        'contractor_tariff_1', 'contractor_tariff_2', 'contractor_tariff_3',
        'faq', 'about', 'partners', 'news',
        'faq_uz', 'about_uz', 'partners_uz', 'news_uz',
        'partners_tariffs', 'partners_tariffs_uz', 'notification_group'
    ];

    public static function get()
    {
        $settings = self::first();
        if (!$settings) {
            $settings = Settings::create(['employer_tariff_1' => 7000,
                'employer_tariff_2' => 6500, 'employer_tariff_3' => 6000, 'contractor_tariff_1' => 1200,
                'contractor_tariff_2' => 1100, 'contractor_tariff_3' => 1000]);
        }
        return $settings;
    }

    public function getAdImage()
    {
        return '/storage/' . File::basename($this->partners_ad_image);
    }

    public function getAdImageUz()
    {
        return '/storage/' . File::basename($this->partners_ad_image_uz);
    }
}
