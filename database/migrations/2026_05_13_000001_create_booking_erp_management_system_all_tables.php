<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Booking ERP Management System - All In One Migration
|--------------------------------------------------------------------------
| Target: Laravel Framework 12
|
| Important:
| If your Laravel project already has default migrations for users,
| password reset tokens, sessions, or notifications, remove duplicates
| before running this migration.
*/

return new class extends Migration
{
    public function up(): void
    {
        /* 1. Company, Branch, User */

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('company_code', 50)->unique();
            $table->string('name');
            $table->string('owner_name')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('tax_number')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status']);
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('branch_code', 50);
            $table->string('name');
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'branch_code']);
            $table->index(['company_id', 'status']);
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->enum('user_type', ['super_admin', 'admin', 'staff', 'partner', 'customer'])->default('staff');
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'branch_id', 'status']);
            $table->index(['user_type']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->foreign('manager_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });

        /* 2. Roles & Permissions */

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'name', 'guard_name']);
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->string('module')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
            $table->index(['module']);
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type']);
            $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_primary');
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type']);
            $table->primary(['permission_id', 'model_id', 'model_type'], 'model_has_permissions_primary');
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id'], 'role_has_permissions_primary');
        });

        /* 3. Location */

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iso_code', 10)->nullable();
            $table->string('phone_code', 20)->nullable();
            $table->string('currency_code', 10)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->unique(['iso_code']);
            $table->index(['status']);
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->unique(['country_id', 'slug']);
            $table->index(['country_id', 'status']);
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->unique(['city_id', 'slug']);
            $table->index(['city_id', 'status']);
        });

        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->index(['country_id', 'city_id', 'area_id', 'status']);
        });

        /* 4. Partner */

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('partner_code', 50)->unique();
            $table->string('business_name');
            $table->string('contact_name')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('commission_rate', 8, 2)->default(0);
            $table->unsignedInteger('payment_term_days')->default(0);
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status']);
            $table->index(['phone']);
            $table->index(['email']);
        });

        Schema::create('partner_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->string('contract_no', 50);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('commission_rate', 8, 2)->default(0);
            $table->string('contract_file')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['partner_id', 'contract_no']);
            $table->index(['start_date', 'end_date', 'status']);
        });

        /* 5. Property */

        Schema::create('property_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->foreignId('property_type_id')->nullable()->constrained('property_types')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->string('property_code', 50)->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('star_rating', 3, 1)->default(0);
            $table->longText('description')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->unsignedInteger('min_check_in_age')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('inactive');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'partner_id', 'status']);
            $table->index(['country_id', 'city_id', 'area_id']);
            $table->index(['approval_status', 'is_featured']);
        });

        Schema::create('property_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->index(['property_id', 'is_primary']);
        });

        Schema::create('property_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('image_path');
            $table->string('title')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_cover')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->index(['property_id', 'is_cover', 'sort_order']);
        });

        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->enum('amenity_type', ['property', 'room'])->default('property');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->index(['amenity_type', 'status']);
        });

        Schema::create('property_amenity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained('amenities')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['property_id', 'amenity_id']);
        });

        Schema::create('property_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->enum('policy_type', ['checkin', 'checkout', 'child', 'pet', 'smoking', 'cancellation', 'payment', 'other'])->default('other');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->index(['property_id', 'policy_type', 'status']);
        });

        Schema::create('nearby_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('name');
            $table->string('place_type')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['property_id', 'place_type']);
        });

        /* 6. Room */

        Schema::create('bed_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('capacity')->default(1);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('room_type_code', 50);
            $table->string('name');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->unsignedInteger('max_adults')->default(1);
            $table->unsignedInteger('max_children')->default(0);
            $table->unsignedInteger('max_occupancy')->default(1);
            $table->decimal('room_size', 8, 2)->nullable();
            $table->string('room_size_unit', 20)->default('sqm');
            $table->unsignedInteger('total_rooms')->default(0);
            $table->decimal('base_price', 12, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['property_id', 'room_type_code']);
            $table->unique(['property_id', 'slug']);
            $table->index(['property_id', 'status']);
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->string('room_number', 50);
            $table->string('floor')->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance', 'inactive'])->default('available');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['property_id', 'room_number']);
            $table->index(['property_id', 'room_type_id', 'status']);
        });

        Schema::create('room_type_bed_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->foreignId('bed_type_id')->constrained('bed_types')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
            $table->unique(['room_type_id', 'bed_type_id']);
        });

        Schema::create('room_type_amenity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained('amenities')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['room_type_id', 'amenity_id']);
        });

        Schema::create('room_type_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->string('image_path');
            $table->string('title')->nullable();
            $table->boolean('is_cover')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['room_type_id', 'is_cover', 'sort_order']);
        });

        Schema::create('room_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable();
            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['property_id', 'room_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });

        /* 7. Rate, Price, Availability */

        Schema::create('cancellation_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('properties')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('free_cancel_before_days')->default(0);
            $table->enum('penalty_type', ['none', 'fixed', 'percentage', 'first_night'])->default('none');
            $table->decimal('penalty_value', 12, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['property_id', 'status']);
        });

        Schema::create('rate_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->string('rate_plan_code', 50);
            $table->string('name');
            $table->enum('meal_plan', ['none', 'breakfast', 'half_board', 'full_board'])->default('none');
            $table->foreignId('cancellation_policy_id')->nullable()->constrained('cancellation_policies')->nullOnDelete();
            $table->enum('payment_policy', ['pay_now', 'pay_later', 'pay_at_property'])->default('pay_now');
            $table->boolean('is_refundable')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['property_id', 'rate_plan_code']);
            $table->index(['property_id', 'room_type_id', 'status']);
        });

        Schema::create('daily_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->foreignId('rate_plan_id')->constrained('rate_plans')->cascadeOnDelete();
            $table->date('rate_date');
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('adult_price', 12, 2)->default(0);
            $table->decimal('child_price', 12, 2)->default(0);
            $table->decimal('extra_bed_price', 12, 2)->default(0);
            $table->string('currency_code', 10)->default('USD');
            $table->unsignedInteger('min_stay')->default(1);
            $table->unsignedInteger('max_stay')->nullable();
            $table->boolean('closed_to_arrival')->default(false);
            $table->boolean('closed_to_departure')->default(false);
            $table->boolean('stop_sell')->default(false);
            $table->timestamps();
            $table->unique(['room_type_id', 'rate_plan_id', 'rate_date']);
            $table->index(['property_id', 'room_type_id', 'rate_date']);
            $table->index(['rate_plan_id', 'rate_date']);
        });

        Schema::create('availability_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->date('available_date');
            $table->unsignedInteger('total_rooms')->default(0);
            $table->unsignedInteger('booked_rooms')->default(0);
            $table->unsignedInteger('blocked_rooms')->default(0);
            $table->unsignedInteger('available_rooms')->default(0);
            $table->boolean('stop_sell')->default(false);
            $table->timestamps();
            $table->unique(['room_type_id', 'available_date']);
            $table->index(['property_id', 'available_date']);
            $table->index(['available_rooms', 'stop_sell']);
        });

        Schema::create('occupancy_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->unsignedInteger('max_adults')->default(1);
            $table->unsignedInteger('max_children')->default(0);
            $table->unsignedInteger('max_infants')->default(0);
            $table->unsignedInteger('max_total_guests')->default(1);
            $table->boolean('allow_extra_bed')->default(false);
            $table->timestamps();
            $table->unique(['room_type_id']);
        });

        Schema::create('child_age_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->unsignedInteger('min_age')->default(0);
            $table->unsignedInteger('max_age')->default(12);
            $table->enum('charge_type', ['free', 'fixed', 'percentage'])->default('free');
            $table->decimal('charge_value', 12, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['property_id', 'min_age', 'max_age']);
        });

        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('name');
            $table->enum('tax_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('tax_value', 12, 2)->default(0);
            $table->boolean('is_inclusive')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->index(['country_id', 'status']);
        });

        Schema::create('property_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('name');
            $table->enum('fee_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('fee_value', 12, 2)->default(0);
            $table->enum('applies_per', ['booking', 'night', 'guest', 'room'])->default('booking');
            $table->boolean('is_mandatory')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->index(['property_id', 'status']);
        });

        /* 8. Customer */

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_code', 50)->unique();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('dob')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('nationality')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['phone']);
            $table->index(['email']);
            $table->index(['status']);
        });

        Schema::create('customer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->enum('document_type', ['passport', 'id_card', 'driver_license'])->default('passport');
            $table->string('document_no')->nullable();
            $table->string('issue_country')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['active', 'expired'])->default('active');
            $table->timestamps();
            $table->index(['customer_id', 'document_type', 'status']);
            $table->index(['document_no']);
        });

        /* 9. Booking */

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('booking_no', 50);
            $table->dateTime('booking_date')->nullable();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedInteger('nights')->default(1);
            $table->unsignedInteger('total_rooms')->default(1);
            $table->unsignedInteger('total_adults')->default(1);
            $table->unsignedInteger('total_children')->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('fee_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->string('currency_code', 10)->default('USD');
            $table->enum('booking_source', ['website', 'mobile_app', 'admin', 'partner_api'])->default('website');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            $table->enum('booking_status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'])->default('pending');
            $table->text('special_request')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'booking_no']);
            $table->index(['property_id', 'booking_status', 'payment_status']);
            $table->index(['customer_id']);
            $table->index(['check_in_date', 'check_out_date']);
            $table->index(['booking_date']);
        });

        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->foreignId('rate_plan_id')->nullable()->constrained('rate_plans')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->string('room_name');
            $table->string('rate_plan_name')->nullable();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedInteger('nights')->default(1);
            $table->unsignedInteger('rooms_count')->default(1);
            $table->unsignedInteger('adults')->default(1);
            $table->unsignedInteger('children')->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->enum('status', ['reserved', 'checked_in', 'checked_out', 'cancelled'])->default('reserved');
            $table->timestamps();
            $table->index(['booking_id', 'status']);
            $table->index(['property_id', 'room_type_id']);
            $table->index(['check_in_date', 'check_out_date']);
        });

        Schema::create('booking_item_daily_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_item_id')->constrained('booking_items')->cascadeOnDelete();
            $table->date('stay_date');
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('fee_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['booking_item_id', 'stay_date']);
        });

        Schema::create('booking_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('note')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['booking_id', 'new_status']);
        });

        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->enum('guest_type', ['adult', 'child', 'infant'])->default('adult');
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('nationality')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->index(['booking_id', 'guest_type', 'is_primary']);
        });

        Schema::create('check_in_out_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained('guests')->nullOnDelete();
            $table->dateTime('check_in_at')->nullable();
            $table->dateTime('check_out_at')->nullable();
            $table->string('key_card_no')->nullable();
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->text('note')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['booking_id', 'room_id']);
            $table->index(['check_in_at', 'check_out_at']);
        });

        /* 10. Payment, Invoice, Refund */

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('name');
            $table->string('code', 50);
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'code']);
            $table->index(['status']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('payment_no', 50)->unique();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->dateTime('payment_date')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency_code', 10)->default('USD');
            $table->string('transaction_id')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'void'])->default('pending');
            $table->text('note')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['booking_id', 'status']);
            $table->index(['customer_id']);
            $table->index(['payment_date']);
            $table->index(['transaction_id']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('invoice_no', 50)->unique();
            $table->date('invoice_date')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('fee_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->enum('invoice_status', ['draft', 'issued', 'cancelled'])->default('issued');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['booking_id', 'invoice_status', 'payment_status']);
            $table->index(['invoice_date']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->enum('item_type', ['room', 'tax', 'fee', 'service', 'discount'])->default('room');
            $table->string('description');
            $table->decimal('qty', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();
            $table->index(['invoice_id', 'item_type']);
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('refund_no', 50)->unique();
            $table->dateTime('refund_date')->nullable();
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->string('refund_method')->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['booking_id', 'status']);
            $table->index(['refund_date']);
        });

        /* 11. Promotion & Coupon */

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('properties')->cascadeOnDelete();
            $table->string('promotion_code', 50)->unique();
            $table->string('name');
            $table->enum('promotion_type', ['percentage', 'fixed', 'free_night'])->default('percentage');
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('min_nights')->default(0);
            $table->decimal('min_amount', 12, 2)->default(0);
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['property_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });

        Schema::create('promotion_room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['promotion_id', 'room_type_id']);
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('name');
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_per_customer')->default(1);
            $table->unsignedInteger('used_count')->default(0);
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status']);
            $table->index(['start_date', 'end_date']);
        });

        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->dateTime('used_at')->nullable();
            $table->timestamps();
            $table->index(['coupon_id', 'customer_id']);
            $table->index(['booking_id']);
        });

        /* 12. Review & Wishlist */

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->decimal('rating', 3, 1)->default(0);
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->decimal('cleanliness_score', 3, 1)->default(0);
            $table->decimal('location_score', 3, 1)->default(0);
            $table->decimal('service_score', 3, 1)->default(0);
            $table->decimal('value_score', 3, 1)->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['property_id', 'status']);
            $table->index(['customer_id']);
            $table->index(['rating']);
        });

        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('message');
            $table->dateTime('replied_at')->nullable();
            $table->timestamps();
            $table->index(['review_id']);
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['customer_id', 'property_id']);
        });

        /* 13. Commission & Payout */

        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->decimal('commission_rate', 8, 2)->default(0);
            $table->decimal('booking_amount', 12, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('partner_amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'paid'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['partner_id', 'status']);
            $table->index(['booking_id']);
        });

        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->string('payout_no', 50)->unique();
            $table->date('payout_date')->nullable();
            $table->decimal('total_booking_amount', 12, 2)->default(0);
            $table->decimal('total_commission', 12, 2)->default(0);
            $table->decimal('payout_amount', 12, 2)->default(0);
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['partner_id', 'status']);
            $table->index(['payout_date']);
        });

        Schema::create('payout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_id')->constrained('payouts')->cascadeOnDelete();
            $table->foreignId('commission_id')->constrained('commissions')->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['payout_id', 'commission_id']);
        });

        /* 14. Optional Travel Services */

        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->string('activity_code', 50)->unique();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->text('meeting_point')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['city_id', 'partner_id', 'status']);
        });

        Schema::create('activity_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->date('activity_date');
            $table->time('start_time')->nullable();
            $table->unsignedInteger('available_slots')->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->enum('status', ['active', 'sold_out', 'cancelled'])->default('active');
            $table->timestamps();
            $table->index(['activity_id', 'activity_date', 'status']);
        });

        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->string('transfer_code', 50)->unique();
            $table->string('vehicle_type')->nullable();
            $table->string('pickup_location')->nullable();
            $table->string('dropoff_location')->nullable();
            $table->unsignedInteger('capacity')->default(1);
            $table->decimal('base_price', 12, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['city_id', 'partner_id', 'status']);
        });

        Schema::create('service_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->enum('service_type', ['activity', 'transfer', 'flight'])->default('activity');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->string('booking_no', 50)->unique();
            $table->dateTime('booking_date')->nullable();
            $table->date('service_date')->nullable();
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->enum('booking_status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['customer_id']);
            $table->index(['service_type', 'service_id']);
            $table->index(['service_date', 'booking_status']);
        });

        /* 15. Report, Audit, Setting, Notification */

        Schema::create('report_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('report_type');
            $table->json('filters')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('export_type', ['pdf', 'excel', 'csv'])->default('excel');
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->timestamps();
            $table->index(['user_id', 'report_type', 'status']);
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);
            $table->string('module', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 100)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'user_id']);
            $table->index(['action', 'module']);
            $table->index(['subject_type', 'subject_id']);
            $table->index(['created_at']);
        });

        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('login_at')->nullable();
            $table->dateTime('logout_at')->nullable();
            $table->string('ip_address', 100)->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['login_at']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('key');
            $table->longText('value')->nullable();
            $table->string('type', 50)->default('text');
            $table->string('group', 100)->default('system');
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->unique(['company_id', 'key']);
            $table->index(['group', 'is_public']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['read_at']);
        });

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('title');
            $table->longText('message')->nullable();
            $table->enum('channel', ['database', 'email', 'sms', 'telegram'])->default('database');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->index(['channel', 'status']);
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('login_histories');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('report_exports');

        Schema::dropIfExists('service_bookings');
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('activity_schedules');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('service_categories');

        Schema::dropIfExists('payout_items');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('commissions');

        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('review_replies');
        Schema::dropIfExists('reviews');

        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('promotion_room_types');
        Schema::dropIfExists('promotions');

        Schema::dropIfExists('refunds');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');

        Schema::dropIfExists('check_in_out_logs');
        Schema::dropIfExists('guests');
        Schema::dropIfExists('booking_status_histories');
        Schema::dropIfExists('booking_item_daily_rates');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');

        Schema::dropIfExists('customer_documents');
        Schema::dropIfExists('customers');

        Schema::dropIfExists('property_fees');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('child_age_policies');
        Schema::dropIfExists('occupancy_rules');
        Schema::dropIfExists('availability_calendars');
        Schema::dropIfExists('daily_rates');
        Schema::dropIfExists('rate_plans');
        Schema::dropIfExists('cancellation_policies');

        Schema::dropIfExists('room_blocks');
        Schema::dropIfExists('room_type_images');
        Schema::dropIfExists('room_type_amenity');
        Schema::dropIfExists('room_type_bed_type');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('bed_types');

        Schema::dropIfExists('nearby_places');
        Schema::dropIfExists('property_policies');
        Schema::dropIfExists('property_amenity');
        Schema::dropIfExists('amenities');
        Schema::dropIfExists('property_images');
        Schema::dropIfExists('property_contacts');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('property_types');

        Schema::dropIfExists('partner_contracts');
        Schema::dropIfExists('partners');

        Schema::dropIfExists('destinations');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('countries');

        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

        Schema::dropIfExists('users');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('companies');

        Schema::enableForeignKeyConstraints();
    }
};
