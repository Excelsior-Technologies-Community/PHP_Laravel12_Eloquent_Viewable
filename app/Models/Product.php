<?php

namespace App\Models;

use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements Viewable
{
    use InteractsWithViews;

    protected $fillable = [
        'name',
        'price',
    ];

    public function actions()
    {
        return $this->hasMany(ProductAction::class);
    }
}