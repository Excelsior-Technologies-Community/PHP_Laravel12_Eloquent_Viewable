<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;

class Product extends Model implements Viewable
{
    use InteractsWithViews;

    protected $fillable = [
        'name',
        'price'
    ];
}