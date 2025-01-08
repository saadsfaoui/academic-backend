<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Crée la table "group_user" pour la relation entre groupes et utilisateurs.
     */
    public function up()
    {
        Schema::create('group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Supprime la table "group_user".
     */
    public function down()
    {
        Schema::dropIfExists('group_user');
    }
};
