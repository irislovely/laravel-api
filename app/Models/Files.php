<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'filepath',
        'thumb',
        'type',
        'provider_id'
    ];

    public function provider() {
        return $this->belongsTo(Providers::class,'provider_id');
    }
}