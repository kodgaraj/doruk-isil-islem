<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $kullaniciTabloAdi = (new User())->getTable();
        if (!Schema::hasColumn($kullaniciTabloAdi, "jwt")) {
            Schema::table($kullaniciTabloAdi, function ($table) {
                $table->text("jwt")->after("password")->nullable();
            });
        }
    }
}
