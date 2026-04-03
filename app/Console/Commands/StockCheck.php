<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class StockCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check low stock products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Checking stock...");

        // Gelow stock products
        $products = Product::where('stock','<',10)->get();

        if($products->isEmpty()){
            $this->info("All products have sufficient stock. ");
            return;
        }

        // show table
        $this->table(
            ['ID','Name','Stock'],
            $products->map(function ($p){
                return [$p->id, $p->name, $p->stock];
            })
        );

        $this->warn("Low stock products found: " . $products->count());
    }
}
