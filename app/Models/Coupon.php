<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'discount_type', 'discount_value', 'expiry_date'];

    public function isExpired(): bool
    {
        if ($this->expiry_date) {
            return Carbon::createFromDate($this->expiry_date)->isBefore(now());
        }
        return false;
    }
}
