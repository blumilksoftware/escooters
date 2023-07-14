<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("city_providers", function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger("provider_id");
            $table->foreign("provider_id")
                ->references("id")
                ->on("providers")
                ->onDelete("cascade");

            $table->unsignedBigInteger("city_id");
            $table->foreign("city_id")
                ->references("id")
                ->on("cities")
                ->onDelete("cascade");
            $table->string("created_by")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("city_providers");
    }
};
