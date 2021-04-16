<?php

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Apartment::class)
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignIdFor(User::class)
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->decimal('user_bid');
            $table->decimal('provider_bid')->nullable();
            $table->text('user_message')->nullable();
            $table->text('provider_message')->nullable();
            $table->enum('status', ['negotiate', 'accepted', 'canceled', 'completed'])->default('negotiate');
            $table->boolean('accepted_by_user')->default(1);
            $table->boolean('accepted_by_provider')->default(0);
            $table->boolean('completed')->default(0);
            $table->date('start');
            $table->date('end');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rents');
    }
}
