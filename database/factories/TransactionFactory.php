<?php

use Faker\Generator as Faker;
use App\Transaction;
use App\Seller;
use App\User;

$factory->define(Transaction::class, function (Faker $faker) {

    $vendedor = Seller::has('products')->get()->random(23);
    $comprador = User::all()->except($vendedor->id)->random();
    
    return [
        'quantity' => $faker->numberBetween(1, 5),
        'buyer_id' => $comprador->id,
        'product_id' => $vendedor->products->random()->id,
    ];
});
