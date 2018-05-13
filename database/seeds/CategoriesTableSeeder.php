<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //TODO возможно есть вариант делать сидинг со вложеностями категорий
        factory(App\Category::class, 15)->create()->each(function ($s) {
            $s->products()->saveMany(factory(App\Product::class, random_int(1, 4))->create());
        });
    }
}
