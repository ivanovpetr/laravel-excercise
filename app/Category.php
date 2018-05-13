<?php

namespace App;

use Baum\Node;
use Illuminate\Notifications\Notifiable;

class Category extends Node
{
    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
