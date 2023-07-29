<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organisation', function (Blueprint $table) {
            $table->id();
            $table->string('cle', 32)->unique();
            $table->string('nom', 100);
            $table->text('adresse');
            $table->string('code_postal', 255);
            $table->string('ville', 255);
            $table->string('statut', 20)->comment('One of: Client, Lead, Prospect');;
            $table->softDeletes('deleted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisation');
    }
};
