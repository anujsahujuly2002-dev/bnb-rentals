<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'property_id'
    ];

    public function property() {
        return $this->belongsTo(PropertyListing::class,'property_id');
    }
}
