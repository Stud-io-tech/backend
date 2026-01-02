<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'stores';

    protected $fillable = [
        'name',
        'image',
        'description',
        'public_id',
        'user_id',
        'active',
        'pix_key',
        'schedules',
        'is_open',
        'is_delivered',
        'delivery_time_km',
        'dynamic_freight_km',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

}
