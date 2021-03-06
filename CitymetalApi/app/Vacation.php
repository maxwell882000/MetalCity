<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Vacation extends Model
{
    protected $fillable = [
        'title', 'price', 'unit', 'name', 'contacts', 'location', 'user_id'
    ];

    /**
     * Vacation's owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Categories attached to vacation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(HandbookCategory::class, 'category_vacation', 'vacation_id', 'category_id');
    }

    public function getLocation()
    {
        $locations = json_decode(file_get_contents(Storage::path('locations.json')), true);
        if ($this->location == 'all')
            return '🗺 Вся Республика Узбекистан';
        $regionCode = $this->location;
        $regionName = $locations["location.regions.$regionCode"];
        return "$regionName";
    }
}
