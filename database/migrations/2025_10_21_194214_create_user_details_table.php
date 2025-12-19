<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create the user_details table for the library system.
     * This table stores additional information about users that extends
     * the base User model with library-specific data.
     */
    public function up(): void
    {
        Schema::create('user_details', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key to users table
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Personal Information
            $table->string('nik')->nullable(); // National ID (16 digits)
            $table->string('nis')->nullable(); // Student ID
            $table->string('nisn')->nullable(); // National Student ID (10 digits)
            $table->string('class')->nullable(); // Class (e.g., 12A, 11B)

            // Contact Information
            $table->text('address')->nullable(); // Full address
            $table->string('phone_number')->nullable(); // Phone/WhatsApp number

            // Birth Information
            $table->date('birth_date')->nullable(); // Date of birth
            $table->string('birth_place')->nullable(); // Place of birth

            // Gender and Religion
            $table->enum('gender', ['male', 'female'])->nullable(); // Gender (L=Male, P=Female)
            $table->string('religion')->nullable(); // Religion

            // Employment Information
            $table->date('join_date')->nullable(); // Date joined organization

            // Library Membership
            $table->enum('membership_status', ['active', 'suspended', 'expired', 'pending'])->default('active'); // Membership status
            $table->string('profile_photo')->nullable(); // Profile photo path

            // QR Code for member identification
            $table->string('qr_code')->nullable(); // QR Code for member

            // Timestamps
            $table->timestamps();
            $table->softDeletes(); // Soft deletes for data integrity

            // Indexes for better performance
            $table->index('user_id');
            $table->index('nis'); // For quick student lookup
            $table->index('nisn'); // For quick student lookup
            $table->index('membership_status');
            $table->index(['user_id', 'membership_status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('siswa')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
