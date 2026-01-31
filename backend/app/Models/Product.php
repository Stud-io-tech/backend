<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'store_id',
        'description',
        'price',
        'amount',
        'active',
        'image',
        'public_id',
        'preparation_time',
        'is_perishable',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function cartItem()
    {
        return $this->hasMany(CartItem::class);
    }
}
