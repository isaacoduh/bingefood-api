<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                "name" => "Fried Rice",
                "price" => 450.00,
                "image" => "https://images.pexels.com/photos/3926133/pexels-photo-3926133.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260"
            ],
            [
                "name" => "Jollof Rice",
                "price" => 400.00,
                "image" => "https://simshomekitchen.com/wp-content/uploads/2020/06/Jollof_Rice_recipe.jpeg"
            ],
            [
                "name" => "Chicken",
                "price" => 650.00,
                "image" => "https://guardian.ng/wp-content/uploads/2018/09/Photo-FoodAce-Blog.jpg"
            ],
            [
                "name" => "Turkey",
                "price" => 750.00,
                "image" => "https://cheflolaskitchen.com/wp-content/uploads/2021/08/DSC0303-air-fryer-turkey-wings-684x1024.jpg"
            ]
        ];

        foreach($products as $item) {
            $product = new Product();
            $product->name = $item['name'];
            $product->price = $item['price'];
            $product->image = $item['image'];

            $product->save();
        }
    }
}
