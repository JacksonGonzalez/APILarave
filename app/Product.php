<?php

namespace App;

use App\Category;
use App\Seller;
use App\Transaction;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const PRODUCTO_DISPONIBLE = 'disponible';
    const PRODUCTO_NO_DISPONIBLE = 'no disponible';

    protected $fillable = [
        'name', 'description', 'quantity', 'status', 'image', 'seller_id'
    ];


    public function estaDisponible(){
        return $this->status == Product::PRODUCTO_DISPONIBLE;
    }

    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    public function seller(){
        return $this->belongsTo(Seller::class);
    }

    public function trasactions(){
        return $this->hasMany(Transaction::class);
    }
}
