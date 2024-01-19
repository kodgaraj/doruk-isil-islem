<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('siparisler', function (Blueprint $table) {
            $table->timestamp('raporOlusturuldu')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('siparisler', function (Blueprint $table) {
            $table->dropColumn('raporOlusturuldu');
        });
    }
};
