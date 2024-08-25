<?php

namespace App\Models;

use App\Traits\Castable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory, Castable;

    protected $fillable = ['user_id', 'store_name', 'description'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'vendor_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
