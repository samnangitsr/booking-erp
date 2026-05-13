<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Property;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today();
        $stats = [
            'today_bookings' => Booking::whereDate('booking_date', $today)->count(),
            'total_customers' => Customer::count(),
            'total_properties' => Property::where('status', 'active')->count(),
            'total_revenue' => (float) Payment::where('status', 'paid')->sum('amount'),
        ];

        $recentBookings = Booking::with(['customer', 'property'])
            ->latest('id')
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recentBookings'));
    }
}
