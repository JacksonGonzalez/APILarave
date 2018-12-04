<?php

namespace App;

use App\Transaction;
use App\Transformers\BuyerTransformer;
// use Illuminate\Database\Eloquent\Model;

class Buyer extends User
{
    public $transformer = BuyerTransformer::class;
    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}
