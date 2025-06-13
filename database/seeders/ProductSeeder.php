<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str; // For Str::random()

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Awesome T-Shirt',
            'description' => 'This is a really awesome t-shirt made from the finest cotton.',
            'price' => 29.99,
            'images' => ['https://via.placeholder.com/600x400.png/0077ff/ffffff?Text=AwesomeTShirt1', 'https://via.placeholder.com/600x400.png/0055cc/ffffff?Text=AwesomeTShirt2'],
            'specifications' => ['material' => '100% Cotton', 'origin' => 'Made in USA'],
            'variations' => ['colors' => ['Red', 'Blue', 'Black'], 'sizes' => ['S', 'M', 'L', 'XL']],
            'stock_status' => 'in stock',
        ]);

        Product::create([
            'name' => 'Cool Gadget Pro',
            'description' => 'A very cool gadget that does amazing things. Pro version.',
            'price' => 199.50,
            'images' => ['https://via.placeholder.com/600x400.png/ff0077/ffffff?Text=CoolGadget1', 'https://via.placeholder.com/600x400.png/cc0055/ffffff?Text=CoolGadget2'],
            'specifications' => ['weight' => '250g', 'dimensions' => '10cm x 5cm x 2cm', 'battery' => '1000mAh'],
            'variations' => ['colors' => ['Black', 'Silver']],
            'stock_status' => 'in stock',
        ]);

        Product::create([
            'name' => 'Comfy Shoes X1',
            'description' => 'Walk on clouds with these super comfy shoes.',
            'price' => 79.00,
            'images' => ['https://via.placeholder.com/600x400.png/77ff00/ffffff?Text=ComfyShoes1'],
            'specifications' => ['material' => 'Breathable Mesh', 'sole' => 'Rubber'],
            'variations' => ['sizes' => ['8', '9', '10', '11', '12'], 'colors' => ['Grey', 'Navy']],
            'stock_status' => 'out of stock',
        ]);

        // Add more products as needed (e.g., 10-20 for good testing)
        for ($i = 0; $i < 10; $i++) {
            Product::create([
                'name' => 'Sample Product ' . ($i + 1),
                'description' => 'This is a sample product description for product ' . ($i + 1) . '. It offers great value.',
                'price' => mt_rand(10, 300) + (mt_rand(0, 99) / 100),
                'images' => ['https://via.placeholder.com/600x400.png/'.Str::random(6).'/ffffff?Text=SampleProd'.($i+1)],
                'specifications' => ['feature_'.Str::random(3) => Str::random(10), 'feature_'.Str::random(3) => Str::random(10)],
                'variations' => ['option_'.Str::random(3) => [Str::random(5), Str::random(5)]],
                'stock_status' => (mt_rand(0, 1) ? 'in stock' : 'out of stock'),
            ]);
        }
    }
}
