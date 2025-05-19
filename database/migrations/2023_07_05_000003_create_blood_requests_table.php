
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
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('blood_group');
            $table->string('location');
            $table->string('hospital_name')->nullable();
            $table->string('patient_name')->nullable();
            $table->enum('urgency', ['normal', 'urgent', 'critical'])->default('normal');
            $table->integer('units_required')->default(1);
            $table->string('contact_phone');
            $table->text('additional_info')->nullable();
            $table->boolean('is_public')->default(true);
            $table->enum('status', ['active', 'fulfilled', 'expired', 'cancelled'])->default('active');
            $table->timestamp('fulfilled_date')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
