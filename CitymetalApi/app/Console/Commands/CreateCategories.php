<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\HandbookCategory;
use Storage;

class CreateCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'category:gen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates category';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $root_categories = json_decode(Storage::get('db.json'), true);
        foreach ($root_categories as $category) {
            $node = new HandbookCategory([
                'ru_title' => $category['ru_title'],
                'uz_title' => $category['uz_title']
            ]);
            $node->save();
            $sample_country = null;
            foreach ($category['children'] as $country) {
                if (count($country['children']) > 0) {
                    $sample_country = $country;
                    break;
                }
            }
            $countries = [
                '🇺🇿 Узбекистон' => '🇺🇿 Узбекистан',
                '🇷🇺 Россия' => '🇷🇺 Россия',
                '🇰🇿 Козогистон' => '🇰🇿 Казахстан',
                '🇰🇬 Киргизистон' => '🇰🇬 Киргизстан',
                '🇨🇳 Хитой' => '🇨🇳 Китай',
                '🇺🇦 Украина' => '🇺🇦 Украина',
                '🇮🇷 Эрон' => '🇮🇷 Иран',
            ];
            foreach ($countries as $uz => $ru) {
                $country_node = new HandbookCategory([
                    'ru_title' => $ru,
                    'uz_title' => $uz
                ]);
                $country_node->appendToNode($node)->save();
                foreach ($sample_country['children'] as $sample) {
                    $cat_node = new HandbookCategory([
                        'ru_title' => $sample['ru_title'],
                        'uz_title' => $sample['uz_title']
                    ]);
                    $cat_node->appendToNode($country_node)->save();
                }
            }
        }
    }
}