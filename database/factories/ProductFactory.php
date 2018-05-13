<?php

use Faker\Generator as Faker;

$factory->define(App\Product::class, function (Faker $faker) {
    return [
        'title' => $faker->text(35),
        'description' => $faker->sentence(8),
        //'image' => $faker->image(storage_path('app/public/products'), 640, 480,'technics',false)
    ];
});
