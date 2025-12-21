<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $table = 'stores';    

    protected $fillable = [
        'name',
        'image',
        'description',
        'public_id',
        'owner_id',
        'active',
        'whatsapp',
        'chave_pix',
    ];

    public function owner() {
        return $this->belongsTo(User::class);
    }

    public function address() {
        return $this->hasOne(Address::class);
    }

}
