<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Review; // Add this import
use Illuminate\Database\Eloquent\Factories\HasFactory; // Assuming it might be needed

class Product extends Model
{
    use HasFactory; // Add if not present, or ensure it is
    // If $fillable or $guarded is not present, it implies all attributes are mass assignable
    // For robust applications, explicitly define $fillable or $guarded

    protected $casts = [
        'images' => 'array',
        'specifications' => 'array',
        'variations' => 'array',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
