<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAction extends Model
{
    use HasFactory;

    protected $table = 'product_actions';

    protected $fillable = [
        'product_id',
        'action_type',
        'ip_address',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
