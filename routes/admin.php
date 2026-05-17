<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\PartnerContractController;
use App\Http\Controllers\Admin\PropertyTypeController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\PropertyContactController;
use App\Http\Controllers\Admin\PropertyImageController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\PropertyAmenityController;
use App\Http\Controllers\Admin\PropertyPolicyController;
use App\Http\Controllers\Admin\NearbyPlaceController;
use App\Http\Controllers\Admin\BedTypeController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomTypeBedTypeController;
use App\Http\Controllers\Admin\RoomTypeAmenityController;
use App\Http\Controllers\Admin\RoomTypeImageController;
use App\Http\Controllers\Admin\RoomBlockController;
use App\Http\Controllers\Admin\CancellationPolicyController;
use App\Http\Controllers\Admin\RatePlanController;
use App\Http\Controllers\Admin\DailyRateController;
use App\Http\Controllers\Admin\AvailabilityCalendarController;
use App\Http\Controllers\Admin\OccupancyRuleController;
use App\Http\Controllers\Admin\ChildAgePolicyController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\PropertyFeeController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerDocumentController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\BookingItemController;
use App\Http\Controllers\Admin\BookingItemDailyRateController;
use App\Http\Controllers\Admin\BookingStatusHistoryController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\CheckInOutLogController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\InvoiceItemController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\PayoutItemController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\PromotionRoomTypeController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CouponUsageController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ReviewReplyController;
use App\Http\Controllers\Admin\WishlistController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\ActivityScheduleController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\ServiceBookingController;
use App\Http\Controllers\Admin\ReportExportController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\LoginHistoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\NotificationTemplateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\BranchSwitcherController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('branch/switch', [BranchSwitcherController::class, 'switch'])->name('branch.switch');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{id}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('permissions/{id}', [PermissionController::class, 'show'])->name('permissions.show');
    Route::get('permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('permissions/{id}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('permissions/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('companies/{id}', [CompanyController::class, 'show'])->name('companies.show');
    Route::get('companies/{id}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('companies/{id}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('companies/{id}', [CompanyController::class, 'destroy'])->name('companies.destroy');

    Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
    Route::get('branches/create', [BranchController::class, 'create'])->name('branches.create');
    Route::post('branches', [BranchController::class, 'store'])->name('branches.store');
    Route::get('branches/{id}', [BranchController::class, 'show'])->name('branches.show');
    Route::get('branches/{id}/edit', [BranchController::class, 'edit'])->name('branches.edit');
    Route::put('branches/{id}', [BranchController::class, 'update'])->name('branches.update');
    Route::delete('branches/{id}', [BranchController::class, 'destroy'])->name('branches.destroy');

    Route::get('countries', [CountryController::class, 'index'])->name('countries.index');
    Route::get('countries/create', [CountryController::class, 'create'])->name('countries.create');
    Route::post('countries', [CountryController::class, 'store'])->name('countries.store');
    Route::get('countries/{id}', [CountryController::class, 'show'])->name('countries.show');
    Route::get('countries/{id}/edit', [CountryController::class, 'edit'])->name('countries.edit');
    Route::put('countries/{id}', [CountryController::class, 'update'])->name('countries.update');
    Route::delete('countries/{id}', [CountryController::class, 'destroy'])->name('countries.destroy');

    Route::get('cities', [CityController::class, 'index'])->name('cities.index');
    Route::get('cities/create', [CityController::class, 'create'])->name('cities.create');
    Route::post('cities', [CityController::class, 'store'])->name('cities.store');
    Route::get('cities/{id}', [CityController::class, 'show'])->name('cities.show');
    Route::get('cities/{id}/edit', [CityController::class, 'edit'])->name('cities.edit');
    Route::put('cities/{id}', [CityController::class, 'update'])->name('cities.update');
    Route::delete('cities/{id}', [CityController::class, 'destroy'])->name('cities.destroy');

    Route::get('areas', [AreaController::class, 'index'])->name('areas.index');
    Route::get('areas/create', [AreaController::class, 'create'])->name('areas.create');
    Route::post('areas', [AreaController::class, 'store'])->name('areas.store');
    Route::get('areas/{id}', [AreaController::class, 'show'])->name('areas.show');
    Route::get('areas/{id}/edit', [AreaController::class, 'edit'])->name('areas.edit');
    Route::put('areas/{id}', [AreaController::class, 'update'])->name('areas.update');
    Route::delete('areas/{id}', [AreaController::class, 'destroy'])->name('areas.destroy');

    Route::get('destinations', [DestinationController::class, 'index'])->name('destinations.index');
    Route::get('destinations/create', [DestinationController::class, 'create'])->name('destinations.create');
    Route::post('destinations', [DestinationController::class, 'store'])->name('destinations.store');
    Route::get('destinations/{id}', [DestinationController::class, 'show'])->name('destinations.show');
    Route::get('destinations/{id}/edit', [DestinationController::class, 'edit'])->name('destinations.edit');
    Route::put('destinations/{id}', [DestinationController::class, 'update'])->name('destinations.update');
    Route::delete('destinations/{id}', [DestinationController::class, 'destroy'])->name('destinations.destroy');

    Route::get('partners', [PartnerController::class, 'index'])->name('partners.index');
    Route::get('partners/create', [PartnerController::class, 'create'])->name('partners.create');
    Route::post('partners', [PartnerController::class, 'store'])->name('partners.store');
    Route::get('partners/{id}', [PartnerController::class, 'show'])->name('partners.show');
    Route::get('partners/{id}/edit', [PartnerController::class, 'edit'])->name('partners.edit');
    Route::put('partners/{id}', [PartnerController::class, 'update'])->name('partners.update');
    Route::delete('partners/{id}', [PartnerController::class, 'destroy'])->name('partners.destroy');

    Route::get('partner_contracts', [PartnerContractController::class, 'index'])->name('partner_contracts.index');
    Route::get('partner_contracts/create', [PartnerContractController::class, 'create'])->name('partner_contracts.create');
    Route::post('partner_contracts', [PartnerContractController::class, 'store'])->name('partner_contracts.store');
    Route::get('partner_contracts/{id}', [PartnerContractController::class, 'show'])->name('partner_contracts.show');
    Route::get('partner_contracts/{id}/edit', [PartnerContractController::class, 'edit'])->name('partner_contracts.edit');
    Route::put('partner_contracts/{id}', [PartnerContractController::class, 'update'])->name('partner_contracts.update');
    Route::delete('partner_contracts/{id}', [PartnerContractController::class, 'destroy'])->name('partner_contracts.destroy');

    Route::get('property_types', [PropertyTypeController::class, 'index'])->name('property_types.index');
    Route::get('property_types/create', [PropertyTypeController::class, 'create'])->name('property_types.create');
    Route::post('property_types', [PropertyTypeController::class, 'store'])->name('property_types.store');
    Route::get('property_types/{id}', [PropertyTypeController::class, 'show'])->name('property_types.show');
    Route::get('property_types/{id}/edit', [PropertyTypeController::class, 'edit'])->name('property_types.edit');
    Route::put('property_types/{id}', [PropertyTypeController::class, 'update'])->name('property_types.update');
    Route::delete('property_types/{id}', [PropertyTypeController::class, 'destroy'])->name('property_types.destroy');

    Route::get('properties', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('properties/{id}', [PropertyController::class, 'show'])->name('properties.show');
    Route::get('properties/{id}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('properties/{id}', [PropertyController::class, 'update'])->name('properties.update');
    Route::delete('properties/{id}', [PropertyController::class, 'destroy'])->name('properties.destroy');

    Route::get('property_contacts', [PropertyContactController::class, 'index'])->name('property_contacts.index');
    Route::get('property_contacts/create', [PropertyContactController::class, 'create'])->name('property_contacts.create');
    Route::post('property_contacts', [PropertyContactController::class, 'store'])->name('property_contacts.store');
    Route::get('property_contacts/{id}', [PropertyContactController::class, 'show'])->name('property_contacts.show');
    Route::get('property_contacts/{id}/edit', [PropertyContactController::class, 'edit'])->name('property_contacts.edit');
    Route::put('property_contacts/{id}', [PropertyContactController::class, 'update'])->name('property_contacts.update');
    Route::delete('property_contacts/{id}', [PropertyContactController::class, 'destroy'])->name('property_contacts.destroy');

    Route::get('property_images', [PropertyImageController::class, 'index'])->name('property_images.index');
    Route::get('property_images/create', [PropertyImageController::class, 'create'])->name('property_images.create');
    Route::post('property_images', [PropertyImageController::class, 'store'])->name('property_images.store');
    Route::get('property_images/{id}', [PropertyImageController::class, 'show'])->name('property_images.show');
    Route::get('property_images/{id}/edit', [PropertyImageController::class, 'edit'])->name('property_images.edit');
    Route::put('property_images/{id}', [PropertyImageController::class, 'update'])->name('property_images.update');
    Route::delete('property_images/{id}', [PropertyImageController::class, 'destroy'])->name('property_images.destroy');

    Route::get('amenities', [AmenityController::class, 'index'])->name('amenities.index');
    Route::get('amenities/create', [AmenityController::class, 'create'])->name('amenities.create');
    Route::post('amenities', [AmenityController::class, 'store'])->name('amenities.store');
    Route::get('amenities/{id}', [AmenityController::class, 'show'])->name('amenities.show');
    Route::get('amenities/{id}/edit', [AmenityController::class, 'edit'])->name('amenities.edit');
    Route::put('amenities/{id}', [AmenityController::class, 'update'])->name('amenities.update');
    Route::delete('amenities/{id}', [AmenityController::class, 'destroy'])->name('amenities.destroy');

    Route::get('property_amenity', [PropertyAmenityController::class, 'index'])->name('property_amenity.index');
    Route::get('property_amenity/create', [PropertyAmenityController::class, 'create'])->name('property_amenity.create');
    Route::post('property_amenity', [PropertyAmenityController::class, 'store'])->name('property_amenity.store');
    Route::get('property_amenity/{id}', [PropertyAmenityController::class, 'show'])->name('property_amenity.show');
    Route::get('property_amenity/{id}/edit', [PropertyAmenityController::class, 'edit'])->name('property_amenity.edit');
    Route::put('property_amenity/{id}', [PropertyAmenityController::class, 'update'])->name('property_amenity.update');
    Route::delete('property_amenity/{id}', [PropertyAmenityController::class, 'destroy'])->name('property_amenity.destroy');

    Route::get('property_policies', [PropertyPolicyController::class, 'index'])->name('property_policies.index');
    Route::get('property_policies/create', [PropertyPolicyController::class, 'create'])->name('property_policies.create');
    Route::post('property_policies', [PropertyPolicyController::class, 'store'])->name('property_policies.store');
    Route::get('property_policies/{id}', [PropertyPolicyController::class, 'show'])->name('property_policies.show');
    Route::get('property_policies/{id}/edit', [PropertyPolicyController::class, 'edit'])->name('property_policies.edit');
    Route::put('property_policies/{id}', [PropertyPolicyController::class, 'update'])->name('property_policies.update');
    Route::delete('property_policies/{id}', [PropertyPolicyController::class, 'destroy'])->name('property_policies.destroy');

    Route::get('nearby_places', [NearbyPlaceController::class, 'index'])->name('nearby_places.index');
    Route::get('nearby_places/create', [NearbyPlaceController::class, 'create'])->name('nearby_places.create');
    Route::post('nearby_places', [NearbyPlaceController::class, 'store'])->name('nearby_places.store');
    Route::get('nearby_places/{id}', [NearbyPlaceController::class, 'show'])->name('nearby_places.show');
    Route::get('nearby_places/{id}/edit', [NearbyPlaceController::class, 'edit'])->name('nearby_places.edit');
    Route::put('nearby_places/{id}', [NearbyPlaceController::class, 'update'])->name('nearby_places.update');
    Route::delete('nearby_places/{id}', [NearbyPlaceController::class, 'destroy'])->name('nearby_places.destroy');

    Route::get('bed_types', [BedTypeController::class, 'index'])->name('bed_types.index');
    Route::get('bed_types/create', [BedTypeController::class, 'create'])->name('bed_types.create');
    Route::post('bed_types', [BedTypeController::class, 'store'])->name('bed_types.store');
    Route::get('bed_types/{id}', [BedTypeController::class, 'show'])->name('bed_types.show');
    Route::get('bed_types/{id}/edit', [BedTypeController::class, 'edit'])->name('bed_types.edit');
    Route::put('bed_types/{id}', [BedTypeController::class, 'update'])->name('bed_types.update');
    Route::delete('bed_types/{id}', [BedTypeController::class, 'destroy'])->name('bed_types.destroy');

    Route::get('room_types', [RoomTypeController::class, 'index'])->name('room_types.index');
    Route::get('room_types/create', [RoomTypeController::class, 'create'])->name('room_types.create');
    Route::post('room_types', [RoomTypeController::class, 'store'])->name('room_types.store');
    Route::get('room_types/{id}', [RoomTypeController::class, 'show'])->name('room_types.show');
    Route::get('room_types/{id}/edit', [RoomTypeController::class, 'edit'])->name('room_types.edit');
    Route::put('room_types/{id}', [RoomTypeController::class, 'update'])->name('room_types.update');
    Route::delete('room_types/{id}', [RoomTypeController::class, 'destroy'])->name('room_types.destroy');

    Route::get('rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('rooms/api/room-types', [RoomController::class, 'roomTypesForProperty'])->name('rooms.room_types');
    Route::get('rooms/{id}', [RoomController::class, 'show'])->whereNumber('id')->name('rooms.show');
    Route::get('rooms/{id}/edit', [RoomController::class, 'edit'])->whereNumber('id')->name('rooms.edit');
    Route::put('rooms/{id}', [RoomController::class, 'update'])->whereNumber('id')->name('rooms.update');
    Route::patch('rooms/{id}/status', [RoomController::class, 'updateStatus'])->whereNumber('id')->name('rooms.update_status');
    Route::delete('rooms/{id}', [RoomController::class, 'destroy'])->whereNumber('id')->name('rooms.destroy');

    Route::get('room_type_bed_type', [RoomTypeBedTypeController::class, 'index'])->name('room_type_bed_type.index');
    Route::get('room_type_bed_type/create', [RoomTypeBedTypeController::class, 'create'])->name('room_type_bed_type.create');
    Route::post('room_type_bed_type', [RoomTypeBedTypeController::class, 'store'])->name('room_type_bed_type.store');
    Route::get('room_type_bed_type/{id}', [RoomTypeBedTypeController::class, 'show'])->name('room_type_bed_type.show');
    Route::get('room_type_bed_type/{id}/edit', [RoomTypeBedTypeController::class, 'edit'])->name('room_type_bed_type.edit');
    Route::put('room_type_bed_type/{id}', [RoomTypeBedTypeController::class, 'update'])->name('room_type_bed_type.update');
    Route::delete('room_type_bed_type/{id}', [RoomTypeBedTypeController::class, 'destroy'])->name('room_type_bed_type.destroy');

    Route::get('room_type_amenity', [RoomTypeAmenityController::class, 'index'])->name('room_type_amenity.index');
    Route::get('room_type_amenity/create', [RoomTypeAmenityController::class, 'create'])->name('room_type_amenity.create');
    Route::post('room_type_amenity', [RoomTypeAmenityController::class, 'store'])->name('room_type_amenity.store');
    Route::get('room_type_amenity/{id}', [RoomTypeAmenityController::class, 'show'])->name('room_type_amenity.show');
    Route::get('room_type_amenity/{id}/edit', [RoomTypeAmenityController::class, 'edit'])->name('room_type_amenity.edit');
    Route::put('room_type_amenity/{id}', [RoomTypeAmenityController::class, 'update'])->name('room_type_amenity.update');
    Route::delete('room_type_amenity/{id}', [RoomTypeAmenityController::class, 'destroy'])->name('room_type_amenity.destroy');

    Route::get('room_type_images', [RoomTypeImageController::class, 'index'])->name('room_type_images.index');
    Route::get('room_type_images/create', [RoomTypeImageController::class, 'create'])->name('room_type_images.create');
    Route::post('room_type_images', [RoomTypeImageController::class, 'store'])->name('room_type_images.store');
    Route::get('room_type_images/{id}', [RoomTypeImageController::class, 'show'])->name('room_type_images.show');
    Route::get('room_type_images/{id}/edit', [RoomTypeImageController::class, 'edit'])->name('room_type_images.edit');
    Route::put('room_type_images/{id}', [RoomTypeImageController::class, 'update'])->name('room_type_images.update');
    Route::delete('room_type_images/{id}', [RoomTypeImageController::class, 'destroy'])->name('room_type_images.destroy');

    Route::get('room_blocks', [RoomBlockController::class, 'index'])->name('room_blocks.index');
    Route::get('room_blocks/create', [RoomBlockController::class, 'create'])->name('room_blocks.create');
    Route::post('room_blocks', [RoomBlockController::class, 'store'])->name('room_blocks.store');
    Route::get('room_blocks/{id}', [RoomBlockController::class, 'show'])->name('room_blocks.show');
    Route::get('room_blocks/{id}/edit', [RoomBlockController::class, 'edit'])->name('room_blocks.edit');
    Route::put('room_blocks/{id}', [RoomBlockController::class, 'update'])->name('room_blocks.update');
    Route::delete('room_blocks/{id}', [RoomBlockController::class, 'destroy'])->name('room_blocks.destroy');

    Route::get('cancellation_policies', [CancellationPolicyController::class, 'index'])->name('cancellation_policies.index');
    Route::get('cancellation_policies/create', [CancellationPolicyController::class, 'create'])->name('cancellation_policies.create');
    Route::post('cancellation_policies', [CancellationPolicyController::class, 'store'])->name('cancellation_policies.store');
    Route::get('cancellation_policies/{id}', [CancellationPolicyController::class, 'show'])->name('cancellation_policies.show');
    Route::get('cancellation_policies/{id}/edit', [CancellationPolicyController::class, 'edit'])->name('cancellation_policies.edit');
    Route::put('cancellation_policies/{id}', [CancellationPolicyController::class, 'update'])->name('cancellation_policies.update');
    Route::delete('cancellation_policies/{id}', [CancellationPolicyController::class, 'destroy'])->name('cancellation_policies.destroy');

    Route::get('rate_plans/api/room-types', [RatePlanController::class, 'roomTypesForProperty'])->name('rate_plans.room_types');
    Route::get('rate_plans', [RatePlanController::class, 'index'])->name('rate_plans.index');
    Route::get('rate_plans/create', [RatePlanController::class, 'create'])->name('rate_plans.create');
    Route::post('rate_plans', [RatePlanController::class, 'store'])->name('rate_plans.store');
    Route::get('rate_plans/{id}', [RatePlanController::class, 'show'])->name('rate_plans.show');
    Route::get('rate_plans/{id}/edit', [RatePlanController::class, 'edit'])->name('rate_plans.edit');
    Route::put('rate_plans/{id}', [RatePlanController::class, 'update'])->name('rate_plans.update');
    Route::delete('rate_plans/{id}', [RatePlanController::class, 'destroy'])->name('rate_plans.destroy');

    Route::get('daily_rates', [DailyRateController::class, 'index'])->name('daily_rates.index');
    Route::post('daily_rates/bulk', [DailyRateController::class, 'bulkUpdate'])->name('daily_rates.bulk');
    Route::patch('daily_rates/cell', [DailyRateController::class, 'updateCell'])->name('daily_rates.cell');
    Route::get('daily_rates/create', [DailyRateController::class, 'create'])->name('daily_rates.create');
    Route::post('daily_rates', [DailyRateController::class, 'store'])->name('daily_rates.store');
    Route::get('daily_rates/{id}', [DailyRateController::class, 'show'])->name('daily_rates.show');
    Route::get('daily_rates/{id}/edit', [DailyRateController::class, 'edit'])->name('daily_rates.edit');
    Route::put('daily_rates/{id}', [DailyRateController::class, 'update'])->name('daily_rates.update');
    Route::delete('daily_rates/{id}', [DailyRateController::class, 'destroy'])->name('daily_rates.destroy');

    Route::get('availability_calendars', [AvailabilityCalendarController::class, 'index'])->name('availability_calendars.index');
    Route::post('availability_calendars/bulk', [AvailabilityCalendarController::class, 'bulkUpdate'])->name('availability_calendars.bulk');
    Route::patch('availability_calendars/cell', [AvailabilityCalendarController::class, 'updateCell'])->name('availability_calendars.cell');
    Route::get('availability_calendars/create', [AvailabilityCalendarController::class, 'create'])->name('availability_calendars.create');
    Route::post('availability_calendars', [AvailabilityCalendarController::class, 'store'])->name('availability_calendars.store');
    Route::get('availability_calendars/{id}', [AvailabilityCalendarController::class, 'show'])->name('availability_calendars.show');
    Route::get('availability_calendars/{id}/edit', [AvailabilityCalendarController::class, 'edit'])->name('availability_calendars.edit');
    Route::put('availability_calendars/{id}', [AvailabilityCalendarController::class, 'update'])->name('availability_calendars.update');
    Route::delete('availability_calendars/{id}', [AvailabilityCalendarController::class, 'destroy'])->name('availability_calendars.destroy');

    Route::get('occupancy_rules', [OccupancyRuleController::class, 'index'])->name('occupancy_rules.index');
    Route::get('occupancy_rules/create', [OccupancyRuleController::class, 'create'])->name('occupancy_rules.create');
    Route::post('occupancy_rules', [OccupancyRuleController::class, 'store'])->name('occupancy_rules.store');
    Route::get('occupancy_rules/{id}', [OccupancyRuleController::class, 'show'])->name('occupancy_rules.show');
    Route::get('occupancy_rules/{id}/edit', [OccupancyRuleController::class, 'edit'])->name('occupancy_rules.edit');
    Route::put('occupancy_rules/{id}', [OccupancyRuleController::class, 'update'])->name('occupancy_rules.update');
    Route::delete('occupancy_rules/{id}', [OccupancyRuleController::class, 'destroy'])->name('occupancy_rules.destroy');

    Route::get('child_age_policies', [ChildAgePolicyController::class, 'index'])->name('child_age_policies.index');
    Route::get('child_age_policies/create', [ChildAgePolicyController::class, 'create'])->name('child_age_policies.create');
    Route::post('child_age_policies', [ChildAgePolicyController::class, 'store'])->name('child_age_policies.store');
    Route::get('child_age_policies/{id}', [ChildAgePolicyController::class, 'show'])->name('child_age_policies.show');
    Route::get('child_age_policies/{id}/edit', [ChildAgePolicyController::class, 'edit'])->name('child_age_policies.edit');
    Route::put('child_age_policies/{id}', [ChildAgePolicyController::class, 'update'])->name('child_age_policies.update');
    Route::delete('child_age_policies/{id}', [ChildAgePolicyController::class, 'destroy'])->name('child_age_policies.destroy');

    Route::get('taxes', [TaxController::class, 'index'])->name('taxes.index');
    Route::get('taxes/create', [TaxController::class, 'create'])->name('taxes.create');
    Route::post('taxes', [TaxController::class, 'store'])->name('taxes.store');
    Route::get('taxes/{id}', [TaxController::class, 'show'])->name('taxes.show');
    Route::get('taxes/{id}/edit', [TaxController::class, 'edit'])->name('taxes.edit');
    Route::put('taxes/{id}', [TaxController::class, 'update'])->name('taxes.update');
    Route::delete('taxes/{id}', [TaxController::class, 'destroy'])->name('taxes.destroy');

    Route::get('property_fees', [PropertyFeeController::class, 'index'])->name('property_fees.index');
    Route::get('property_fees/create', [PropertyFeeController::class, 'create'])->name('property_fees.create');
    Route::post('property_fees', [PropertyFeeController::class, 'store'])->name('property_fees.store');
    Route::get('property_fees/{id}', [PropertyFeeController::class, 'show'])->name('property_fees.show');
    Route::get('property_fees/{id}/edit', [PropertyFeeController::class, 'edit'])->name('property_fees.edit');
    Route::put('property_fees/{id}', [PropertyFeeController::class, 'update'])->name('property_fees.update');
    Route::delete('property_fees/{id}', [PropertyFeeController::class, 'destroy'])->name('property_fees.destroy');

    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('customers/{id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    Route::get('customer_documents', [CustomerDocumentController::class, 'index'])->name('customer_documents.index');
    Route::get('customer_documents/create', [CustomerDocumentController::class, 'create'])->name('customer_documents.create');
    Route::post('customer_documents', [CustomerDocumentController::class, 'store'])->name('customer_documents.store');
    Route::get('customer_documents/{id}', [CustomerDocumentController::class, 'show'])->name('customer_documents.show');
    Route::get('customer_documents/{id}/edit', [CustomerDocumentController::class, 'edit'])->name('customer_documents.edit');
    Route::put('customer_documents/{id}', [CustomerDocumentController::class, 'update'])->name('customer_documents.update');
    Route::delete('customer_documents/{id}', [CustomerDocumentController::class, 'destroy'])->name('customer_documents.destroy');

    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('bookings/api/room-types', [BookingController::class, 'roomTypesForProperty'])->name('bookings.room_types');
    Route::get('bookings/api/rate-plans', [BookingController::class, 'ratePlansForRoomType'])->name('bookings.rate_plans');
    Route::get('bookings/api/customers', [BookingController::class, 'searchCustomers'])->name('bookings.customers.search');
    Route::get('bookings/{id}', [BookingController::class, 'show'])->whereNumber('id')->name('bookings.show');
    Route::get('bookings/{id}/edit', [BookingController::class, 'edit'])->whereNumber('id')->name('bookings.edit');
    Route::put('bookings/{id}', [BookingController::class, 'update'])->whereNumber('id')->name('bookings.update');
    Route::delete('bookings/{id}', [BookingController::class, 'destroy'])->whereNumber('id')->name('bookings.destroy');
    Route::post('bookings/{id}/confirm', [BookingController::class, 'confirm'])->whereNumber('id')->name('bookings.confirm');
    Route::post('bookings/{id}/check-in', [BookingController::class, 'checkIn'])->whereNumber('id')->name('bookings.check_in');
    Route::post('bookings/{id}/check-out', [BookingController::class, 'checkOut'])->whereNumber('id')->name('bookings.check_out');
    Route::post('bookings/{id}/cancel', [BookingController::class, 'cancel'])->whereNumber('id')->name('bookings.cancel');

    Route::get('booking_items', [BookingItemController::class, 'index'])->name('booking_items.index');
    Route::get('booking_items/create', [BookingItemController::class, 'create'])->name('booking_items.create');
    Route::post('booking_items', [BookingItemController::class, 'store'])->name('booking_items.store');
    Route::get('booking_items/{id}', [BookingItemController::class, 'show'])->name('booking_items.show');
    Route::get('booking_items/{id}/edit', [BookingItemController::class, 'edit'])->name('booking_items.edit');
    Route::put('booking_items/{id}', [BookingItemController::class, 'update'])->name('booking_items.update');
    Route::delete('booking_items/{id}', [BookingItemController::class, 'destroy'])->name('booking_items.destroy');

    Route::get('booking_item_daily_rates', [BookingItemDailyRateController::class, 'index'])->name('booking_item_daily_rates.index');
    Route::get('booking_item_daily_rates/create', [BookingItemDailyRateController::class, 'create'])->name('booking_item_daily_rates.create');
    Route::post('booking_item_daily_rates', [BookingItemDailyRateController::class, 'store'])->name('booking_item_daily_rates.store');
    Route::get('booking_item_daily_rates/{id}', [BookingItemDailyRateController::class, 'show'])->name('booking_item_daily_rates.show');
    Route::get('booking_item_daily_rates/{id}/edit', [BookingItemDailyRateController::class, 'edit'])->name('booking_item_daily_rates.edit');
    Route::put('booking_item_daily_rates/{id}', [BookingItemDailyRateController::class, 'update'])->name('booking_item_daily_rates.update');
    Route::delete('booking_item_daily_rates/{id}', [BookingItemDailyRateController::class, 'destroy'])->name('booking_item_daily_rates.destroy');

    Route::get('booking_status_histories', [BookingStatusHistoryController::class, 'index'])->name('booking_status_histories.index');
    Route::get('booking_status_histories/create', [BookingStatusHistoryController::class, 'create'])->name('booking_status_histories.create');
    Route::post('booking_status_histories', [BookingStatusHistoryController::class, 'store'])->name('booking_status_histories.store');
    Route::get('booking_status_histories/{id}', [BookingStatusHistoryController::class, 'show'])->name('booking_status_histories.show');
    Route::get('booking_status_histories/{id}/edit', [BookingStatusHistoryController::class, 'edit'])->name('booking_status_histories.edit');
    Route::put('booking_status_histories/{id}', [BookingStatusHistoryController::class, 'update'])->name('booking_status_histories.update');
    Route::delete('booking_status_histories/{id}', [BookingStatusHistoryController::class, 'destroy'])->name('booking_status_histories.destroy');

    Route::get('guests', [GuestController::class, 'index'])->name('guests.index');
    Route::get('guests/create', [GuestController::class, 'create'])->name('guests.create');
    Route::post('guests', [GuestController::class, 'store'])->name('guests.store');
    Route::get('guests/{id}', [GuestController::class, 'show'])->name('guests.show');
    Route::get('guests/{id}/edit', [GuestController::class, 'edit'])->name('guests.edit');
    Route::put('guests/{id}', [GuestController::class, 'update'])->name('guests.update');
    Route::delete('guests/{id}', [GuestController::class, 'destroy'])->name('guests.destroy');

    Route::get('check_in_out_logs', [CheckInOutLogController::class, 'index'])->name('check_in_out_logs.index');
    Route::get('check_in_out_logs/create', [CheckInOutLogController::class, 'create'])->name('check_in_out_logs.create');
    Route::post('check_in_out_logs', [CheckInOutLogController::class, 'store'])->name('check_in_out_logs.store');
    Route::get('check_in_out_logs/{id}', [CheckInOutLogController::class, 'show'])->name('check_in_out_logs.show');
    Route::get('check_in_out_logs/{id}/edit', [CheckInOutLogController::class, 'edit'])->name('check_in_out_logs.edit');
    Route::put('check_in_out_logs/{id}', [CheckInOutLogController::class, 'update'])->name('check_in_out_logs.update');
    Route::delete('check_in_out_logs/{id}', [CheckInOutLogController::class, 'destroy'])->name('check_in_out_logs.destroy');

    Route::get('payment_methods', [PaymentMethodController::class, 'index'])->name('payment_methods.index');
    Route::get('payment_methods/create', [PaymentMethodController::class, 'create'])->name('payment_methods.create');
    Route::post('payment_methods', [PaymentMethodController::class, 'store'])->name('payment_methods.store');
    Route::get('payment_methods/{id}', [PaymentMethodController::class, 'show'])->name('payment_methods.show');
    Route::get('payment_methods/{id}/edit', [PaymentMethodController::class, 'edit'])->name('payment_methods.edit');
    Route::put('payment_methods/{id}', [PaymentMethodController::class, 'update'])->name('payment_methods.update');
    Route::delete('payment_methods/{id}', [PaymentMethodController::class, 'destroy'])->name('payment_methods.destroy');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{id}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('payments/{id}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('payments/{id}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{id}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('invoices/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

    Route::get('invoice_items', [InvoiceItemController::class, 'index'])->name('invoice_items.index');
    Route::get('invoice_items/create', [InvoiceItemController::class, 'create'])->name('invoice_items.create');
    Route::post('invoice_items', [InvoiceItemController::class, 'store'])->name('invoice_items.store');
    Route::get('invoice_items/{id}', [InvoiceItemController::class, 'show'])->name('invoice_items.show');
    Route::get('invoice_items/{id}/edit', [InvoiceItemController::class, 'edit'])->name('invoice_items.edit');
    Route::put('invoice_items/{id}', [InvoiceItemController::class, 'update'])->name('invoice_items.update');
    Route::delete('invoice_items/{id}', [InvoiceItemController::class, 'destroy'])->name('invoice_items.destroy');

    Route::get('refunds', [RefundController::class, 'index'])->name('refunds.index');
    Route::get('refunds/create', [RefundController::class, 'create'])->name('refunds.create');
    Route::post('refunds', [RefundController::class, 'store'])->name('refunds.store');
    Route::get('refunds/{id}', [RefundController::class, 'show'])->name('refunds.show');
    Route::get('refunds/{id}/edit', [RefundController::class, 'edit'])->name('refunds.edit');
    Route::put('refunds/{id}', [RefundController::class, 'update'])->name('refunds.update');
    Route::delete('refunds/{id}', [RefundController::class, 'destroy'])->name('refunds.destroy');

    Route::get('commissions', [CommissionController::class, 'index'])->name('commissions.index');
    Route::get('commissions/create', [CommissionController::class, 'create'])->name('commissions.create');
    Route::post('commissions', [CommissionController::class, 'store'])->name('commissions.store');
    Route::get('commissions/{id}', [CommissionController::class, 'show'])->name('commissions.show');
    Route::get('commissions/{id}/edit', [CommissionController::class, 'edit'])->name('commissions.edit');
    Route::put('commissions/{id}', [CommissionController::class, 'update'])->name('commissions.update');
    Route::delete('commissions/{id}', [CommissionController::class, 'destroy'])->name('commissions.destroy');

    Route::get('payouts', [PayoutController::class, 'index'])->name('payouts.index');
    Route::get('payouts/create', [PayoutController::class, 'create'])->name('payouts.create');
    Route::post('payouts', [PayoutController::class, 'store'])->name('payouts.store');
    Route::get('payouts/{id}', [PayoutController::class, 'show'])->name('payouts.show');
    Route::get('payouts/{id}/edit', [PayoutController::class, 'edit'])->name('payouts.edit');
    Route::put('payouts/{id}', [PayoutController::class, 'update'])->name('payouts.update');
    Route::delete('payouts/{id}', [PayoutController::class, 'destroy'])->name('payouts.destroy');

    Route::get('payout_items', [PayoutItemController::class, 'index'])->name('payout_items.index');
    Route::get('payout_items/create', [PayoutItemController::class, 'create'])->name('payout_items.create');
    Route::post('payout_items', [PayoutItemController::class, 'store'])->name('payout_items.store');
    Route::get('payout_items/{id}', [PayoutItemController::class, 'show'])->name('payout_items.show');
    Route::get('payout_items/{id}/edit', [PayoutItemController::class, 'edit'])->name('payout_items.edit');
    Route::put('payout_items/{id}', [PayoutItemController::class, 'update'])->name('payout_items.update');
    Route::delete('payout_items/{id}', [PayoutItemController::class, 'destroy'])->name('payout_items.destroy');

    Route::get('promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('promotions/create', [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('promotions/{id}', [PromotionController::class, 'show'])->name('promotions.show');
    Route::get('promotions/{id}/edit', [PromotionController::class, 'edit'])->name('promotions.edit');
    Route::put('promotions/{id}', [PromotionController::class, 'update'])->name('promotions.update');
    Route::delete('promotions/{id}', [PromotionController::class, 'destroy'])->name('promotions.destroy');

    Route::get('promotion_room_types', [PromotionRoomTypeController::class, 'index'])->name('promotion_room_types.index');
    Route::get('promotion_room_types/create', [PromotionRoomTypeController::class, 'create'])->name('promotion_room_types.create');
    Route::post('promotion_room_types', [PromotionRoomTypeController::class, 'store'])->name('promotion_room_types.store');
    Route::get('promotion_room_types/{id}', [PromotionRoomTypeController::class, 'show'])->name('promotion_room_types.show');
    Route::get('promotion_room_types/{id}/edit', [PromotionRoomTypeController::class, 'edit'])->name('promotion_room_types.edit');
    Route::put('promotion_room_types/{id}', [PromotionRoomTypeController::class, 'update'])->name('promotion_room_types.update');
    Route::delete('promotion_room_types/{id}', [PromotionRoomTypeController::class, 'destroy'])->name('promotion_room_types.destroy');

    Route::get('coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::get('coupons/create', [CouponController::class, 'create'])->name('coupons.create');
    Route::post('coupons', [CouponController::class, 'store'])->name('coupons.store');
    Route::get('coupons/{id}', [CouponController::class, 'show'])->name('coupons.show');
    Route::get('coupons/{id}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
    Route::put('coupons/{id}', [CouponController::class, 'update'])->name('coupons.update');
    Route::delete('coupons/{id}', [CouponController::class, 'destroy'])->name('coupons.destroy');

    Route::get('coupon_usages', [CouponUsageController::class, 'index'])->name('coupon_usages.index');
    Route::get('coupon_usages/create', [CouponUsageController::class, 'create'])->name('coupon_usages.create');
    Route::post('coupon_usages', [CouponUsageController::class, 'store'])->name('coupon_usages.store');
    Route::get('coupon_usages/{id}', [CouponUsageController::class, 'show'])->name('coupon_usages.show');
    Route::get('coupon_usages/{id}/edit', [CouponUsageController::class, 'edit'])->name('coupon_usages.edit');
    Route::put('coupon_usages/{id}', [CouponUsageController::class, 'update'])->name('coupon_usages.update');
    Route::delete('coupon_usages/{id}', [CouponUsageController::class, 'destroy'])->name('coupon_usages.destroy');

    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('reviews/{id}', [ReviewController::class, 'show'])->name('reviews.show');
    Route::get('reviews/{id}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('reviews/{id}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('review_replies', [ReviewReplyController::class, 'index'])->name('review_replies.index');
    Route::get('review_replies/create', [ReviewReplyController::class, 'create'])->name('review_replies.create');
    Route::post('review_replies', [ReviewReplyController::class, 'store'])->name('review_replies.store');
    Route::get('review_replies/{id}', [ReviewReplyController::class, 'show'])->name('review_replies.show');
    Route::get('review_replies/{id}/edit', [ReviewReplyController::class, 'edit'])->name('review_replies.edit');
    Route::put('review_replies/{id}', [ReviewReplyController::class, 'update'])->name('review_replies.update');
    Route::delete('review_replies/{id}', [ReviewReplyController::class, 'destroy'])->name('review_replies.destroy');

    Route::get('wishlists', [WishlistController::class, 'index'])->name('wishlists.index');
    Route::get('wishlists/create', [WishlistController::class, 'create'])->name('wishlists.create');
    Route::post('wishlists', [WishlistController::class, 'store'])->name('wishlists.store');
    Route::get('wishlists/{id}', [WishlistController::class, 'show'])->name('wishlists.show');
    Route::get('wishlists/{id}/edit', [WishlistController::class, 'edit'])->name('wishlists.edit');
    Route::put('wishlists/{id}', [WishlistController::class, 'update'])->name('wishlists.update');
    Route::delete('wishlists/{id}', [WishlistController::class, 'destroy'])->name('wishlists.destroy');

    Route::get('service_categories', [ServiceCategoryController::class, 'index'])->name('service_categories.index');
    Route::get('service_categories/create', [ServiceCategoryController::class, 'create'])->name('service_categories.create');
    Route::post('service_categories', [ServiceCategoryController::class, 'store'])->name('service_categories.store');
    Route::get('service_categories/{id}', [ServiceCategoryController::class, 'show'])->name('service_categories.show');
    Route::get('service_categories/{id}/edit', [ServiceCategoryController::class, 'edit'])->name('service_categories.edit');
    Route::put('service_categories/{id}', [ServiceCategoryController::class, 'update'])->name('service_categories.update');
    Route::delete('service_categories/{id}', [ServiceCategoryController::class, 'destroy'])->name('service_categories.destroy');

    Route::get('activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('activities/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::get('activities/{id}', [ActivityController::class, 'show'])->name('activities.show');
    Route::get('activities/{id}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::put('activities/{id}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('activities/{id}', [ActivityController::class, 'destroy'])->name('activities.destroy');

    Route::get('activity_schedules', [ActivityScheduleController::class, 'index'])->name('activity_schedules.index');
    Route::get('activity_schedules/create', [ActivityScheduleController::class, 'create'])->name('activity_schedules.create');
    Route::post('activity_schedules', [ActivityScheduleController::class, 'store'])->name('activity_schedules.store');
    Route::get('activity_schedules/{id}', [ActivityScheduleController::class, 'show'])->name('activity_schedules.show');
    Route::get('activity_schedules/{id}/edit', [ActivityScheduleController::class, 'edit'])->name('activity_schedules.edit');
    Route::put('activity_schedules/{id}', [ActivityScheduleController::class, 'update'])->name('activity_schedules.update');
    Route::delete('activity_schedules/{id}', [ActivityScheduleController::class, 'destroy'])->name('activity_schedules.destroy');

    Route::get('transfers', [TransferController::class, 'index'])->name('transfers.index');
    Route::get('transfers/create', [TransferController::class, 'create'])->name('transfers.create');
    Route::post('transfers', [TransferController::class, 'store'])->name('transfers.store');
    Route::get('transfers/{id}', [TransferController::class, 'show'])->name('transfers.show');
    Route::get('transfers/{id}/edit', [TransferController::class, 'edit'])->name('transfers.edit');
    Route::put('transfers/{id}', [TransferController::class, 'update'])->name('transfers.update');
    Route::delete('transfers/{id}', [TransferController::class, 'destroy'])->name('transfers.destroy');

    Route::get('service_bookings', [ServiceBookingController::class, 'index'])->name('service_bookings.index');
    Route::get('service_bookings/create', [ServiceBookingController::class, 'create'])->name('service_bookings.create');
    Route::post('service_bookings', [ServiceBookingController::class, 'store'])->name('service_bookings.store');
    Route::get('service_bookings/{id}', [ServiceBookingController::class, 'show'])->name('service_bookings.show');
    Route::get('service_bookings/{id}/edit', [ServiceBookingController::class, 'edit'])->name('service_bookings.edit');
    Route::put('service_bookings/{id}', [ServiceBookingController::class, 'update'])->name('service_bookings.update');
    Route::delete('service_bookings/{id}', [ServiceBookingController::class, 'destroy'])->name('service_bookings.destroy');

    Route::get('report_exports', [ReportExportController::class, 'index'])->name('report_exports.index');
    Route::get('report_exports/create', [ReportExportController::class, 'create'])->name('report_exports.create');
    Route::post('report_exports', [ReportExportController::class, 'store'])->name('report_exports.store');
    Route::get('report_exports/{id}', [ReportExportController::class, 'show'])->name('report_exports.show');
    Route::get('report_exports/{id}/edit', [ReportExportController::class, 'edit'])->name('report_exports.edit');
    Route::put('report_exports/{id}', [ReportExportController::class, 'update'])->name('report_exports.update');
    Route::delete('report_exports/{id}', [ReportExportController::class, 'destroy'])->name('report_exports.destroy');

    Route::get('activity_logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
    Route::get('activity_logs/create', [ActivityLogController::class, 'create'])->name('activity_logs.create');
    Route::post('activity_logs', [ActivityLogController::class, 'store'])->name('activity_logs.store');
    Route::get('activity_logs/{id}', [ActivityLogController::class, 'show'])->name('activity_logs.show');
    Route::get('activity_logs/{id}/edit', [ActivityLogController::class, 'edit'])->name('activity_logs.edit');
    Route::put('activity_logs/{id}', [ActivityLogController::class, 'update'])->name('activity_logs.update');
    Route::delete('activity_logs/{id}', [ActivityLogController::class, 'destroy'])->name('activity_logs.destroy');

    Route::get('login_histories', [LoginHistoryController::class, 'index'])->name('login_histories.index');
    Route::get('login_histories/create', [LoginHistoryController::class, 'create'])->name('login_histories.create');
    Route::post('login_histories', [LoginHistoryController::class, 'store'])->name('login_histories.store');
    Route::get('login_histories/{id}', [LoginHistoryController::class, 'show'])->name('login_histories.show');
    Route::get('login_histories/{id}/edit', [LoginHistoryController::class, 'edit'])->name('login_histories.edit');
    Route::put('login_histories/{id}', [LoginHistoryController::class, 'update'])->name('login_histories.update');
    Route::delete('login_histories/{id}', [LoginHistoryController::class, 'destroy'])->name('login_histories.destroy');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::get('settings/create', [SettingController::class, 'create'])->name('settings.create');
    Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
    Route::get('settings/{id}', [SettingController::class, 'show'])->name('settings.show');
    Route::get('settings/{id}/edit', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings/{id}', [SettingController::class, 'update'])->name('settings.update');
    Route::delete('settings/{id}', [SettingController::class, 'destroy'])->name('settings.destroy');

    Route::get('notification_templates', [NotificationTemplateController::class, 'index'])->name('notification_templates.index');
    Route::get('notification_templates/create', [NotificationTemplateController::class, 'create'])->name('notification_templates.create');
    Route::post('notification_templates', [NotificationTemplateController::class, 'store'])->name('notification_templates.store');
    Route::get('notification_templates/{id}', [NotificationTemplateController::class, 'show'])->name('notification_templates.show');
    Route::get('notification_templates/{id}/edit', [NotificationTemplateController::class, 'edit'])->name('notification_templates.edit');
    Route::put('notification_templates/{id}', [NotificationTemplateController::class, 'update'])->name('notification_templates.update');
    Route::delete('notification_templates/{id}', [NotificationTemplateController::class, 'destroy'])->name('notification_templates.destroy');

});
