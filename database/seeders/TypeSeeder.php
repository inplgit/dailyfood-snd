<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = ["USER", "MANAGER", "DELIVERY", "KPU"];
        for ($i=0; $i < count($types); $i++) { 
            Type::create([
                'type' => $types[$i],
            ]);
        }
    }
}
