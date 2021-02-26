<?php

namespace Database\Seeders;

use App\Models\Files;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        
        DB::table('providers')->insert([
            'name' => 'Google'
        ]);
        DB::table('providers')->insert([
            'name' => 'Snapchat'
        ]);

        Files::factory()->count(3)->create(['provider_id'=>1]);
        Files::factory()->count(2)->create(['provider_id'=>2]);
    }
}
