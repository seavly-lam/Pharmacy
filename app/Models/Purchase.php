<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
    'product',
    'category_id',
    'supplier_id',
    'cost_price',
    'quantity',
    'original_quantity',  // <-- Make sure it's here
    'expiry_date',
    'image',
];

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
