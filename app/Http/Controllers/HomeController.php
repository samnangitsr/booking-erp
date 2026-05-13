<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Welcome', [
            'appName' => config('app.name'),
            'locale' => app()->getLocale(),
            'apiUrl' => route('home.data'),
        ]);
    }

    public function data(): JsonResponse
    {
        $priceSubquery = DB::table('room_types')
            ->selectRaw('property_id, MIN(base_price) as starting_price, MAX(max_occupancy) as max_occupancy')
            ->whereNull('deleted_at')
            ->where('status', 'active')
            ->groupBy('property_id');

        $reviewSubquery = DB::table('reviews')
            ->selectRaw('property_id, AVG(rating) as review_score, COUNT(*) as review_count')
            ->whereNull('deleted_at')
            ->where('status', 'approved')
            ->groupBy('property_id');

        $imageSubquery = DB::table('property_images')
            ->selectRaw('property_id, MAX(CASE WHEN is_cover = 1 THEN image_path END) as cover_image, MIN(image_path) as fallback_image')
            ->where('status', 'active')
            ->groupBy('property_id');

        $stats = [
            'approvedProperties' => DB::table('properties')
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->where('approval_status', 'approved')
                ->count(),
            'featuredStays' => DB::table('properties')
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->where('approval_status', 'approved')
                ->where('is_featured', true)
                ->count(),
            'activeDestinations' => DB::table('destinations')
                ->where('status', 'active')
                ->count(),
            'approvedReviews' => DB::table('reviews')
                ->whereNull('deleted_at')
                ->where('status', 'approved')
                ->count(),
        ];

        $destinations = DB::table('destinations as d')
            ->leftJoin('cities as c', 'c.id', '=', 'd.city_id')
            ->leftJoin('countries as country', 'country.id', '=', 'd.country_id')
            ->leftJoin('properties as p', function ($join) {
                $join->on('p.city_id', '=', 'd.city_id')
                    ->whereNull('p.deleted_at')
                    ->where('p.status', 'active')
                    ->where('p.approval_status', 'approved');
            })
            ->where('d.status', 'active')
            ->groupBy('d.id', 'd.name', 'd.slug', 'd.image', 'c.name', 'country.name')
            ->orderByDesc(DB::raw('COUNT(DISTINCT p.id)'))
            ->orderBy('d.name')
            ->limit(6)
            ->get([
                'd.id',
                'd.name',
                'd.slug',
                'd.image',
                'c.name as city_name',
                'country.name as country_name',
                DB::raw('COUNT(DISTINCT p.id) as property_count'),
            ])
            ->map(function ($destination) {
                return [
                    'id' => $destination->id,
                    'name' => $destination->name,
                    'slug' => $destination->slug,
                    'city' => $destination->city_name,
                    'country' => $destination->country_name,
                    'propertyCount' => (int) $destination->property_count,
                    'imageUrl' => $this->assetUrl($destination->image),
                ];
            })
            ->values();

        $featuredProperties = DB::table('properties as p')
            ->leftJoin('cities as c', 'c.id', '=', 'p.city_id')
            ->leftJoin('countries as country', 'country.id', '=', 'p.country_id')
            ->leftJoin('property_types as pt', 'pt.id', '=', 'p.property_type_id')
            ->leftJoinSub($priceSubquery, 'prices', function ($join) {
                $join->on('prices.property_id', '=', 'p.id');
            })
            ->leftJoinSub($reviewSubquery, 'reviews', function ($join) {
                $join->on('reviews.property_id', '=', 'p.id');
            })
            ->leftJoinSub($imageSubquery, 'images', function ($join) {
                $join->on('images.property_id', '=', 'p.id');
            })
            ->whereNull('p.deleted_at')
            ->where('p.status', 'active')
            ->where('p.approval_status', 'approved')
            ->orderByDesc('p.is_featured')
            ->orderByDesc(DB::raw('COALESCE(reviews.review_score, 0)'))
            ->orderBy('p.name')
            ->limit(8)
            ->get([
                'p.id',
                'p.name',
                'p.slug',
                'p.description',
                'p.address',
                'p.star_rating',
                'p.is_featured',
                'c.name as city_name',
                'country.name as country_name',
                'pt.name as property_type_name',
                'prices.starting_price',
                'prices.max_occupancy',
                'reviews.review_score',
                'reviews.review_count',
                'images.cover_image',
                'images.fallback_image',
            ])
            ->map(function ($property) {
                return [
                    'id' => $property->id,
                    'name' => $property->name,
                    'slug' => $property->slug,
                    'description' => Str::limit(strip_tags((string) $property->description), 120),
                    'location' => collect([$property->city_name, $property->country_name])->filter()->implode(', '),
                    'address' => Str::limit((string) $property->address, 80),
                    'propertyType' => $property->property_type_name ?: 'Stay',
                    'starRating' => (float) $property->star_rating,
                    'reviewScore' => round((float) ($property->review_score ?? 0), 1),
                    'reviewCount' => (int) ($property->review_count ?? 0),
                    'startingPrice' => $property->starting_price !== null ? (float) $property->starting_price : null,
                    'maxOccupancy' => (int) ($property->max_occupancy ?? 0),
                    'isFeatured' => (bool) $property->is_featured,
                    'imageUrl' => $this->assetUrl($property->cover_image ?: $property->fallback_image),
                ];
            })
            ->values();

        $propertyTypes = DB::table('property_types as pt')
            ->leftJoin('properties as p', function ($join) {
                $join->on('p.property_type_id', '=', 'pt.id')
                    ->whereNull('p.deleted_at')
                    ->where('p.status', 'active')
                    ->where('p.approval_status', 'approved');
            })
            ->where('pt.status', 'active')
            ->groupBy('pt.id', 'pt.name')
            ->orderByDesc(DB::raw('COUNT(DISTINCT p.id)'))
            ->orderBy('pt.name')
            ->limit(4)
            ->get([
                'pt.id',
                'pt.name',
                DB::raw('COUNT(DISTINCT p.id) as property_count'),
            ])
            ->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'propertyCount' => (int) $type->property_count,
                ];
            })
            ->values();

        $reviews = DB::table('reviews as r')
            ->leftJoin('properties as p', 'p.id', '=', 'r.property_id')
            ->leftJoin('cities as c', 'c.id', '=', 'p.city_id')
            ->whereNull('r.deleted_at')
            ->where('r.status', 'approved')
            ->whereNull('p.deleted_at')
            ->orderByDesc('r.created_at')
            ->limit(3)
            ->get([
                'r.id',
                'r.rating',
                'r.title',
                'r.comment',
                'r.created_at',
                'p.name as property_name',
                'c.name as city_name',
            ])
            ->map(function ($review, int $index) {
                return [
                    'id' => $review->id,
                    'rating' => round((float) $review->rating, 1),
                    'title' => $review->title ?: 'Guest highlight',
                    'comment' => Str::limit((string) $review->comment, 160),
                    'propertyName' => $review->property_name,
                    'city' => $review->city_name,
                    'guestName' => 'Guest '.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'date' => $review->created_at ? Carbon::parse($review->created_at)->format('M Y') : null,
                ];
            })
            ->values();

        $promotions = DB::table('promotions as pr')
            ->leftJoin('properties as p', 'p.id', '=', 'pr.property_id')
            ->leftJoin('cities as c', 'c.id', '=', 'p.city_id')
            ->leftJoinSub($imageSubquery, 'images', function ($join) {
                $join->on('images.property_id', '=', 'p.id');
            })
            ->whereNull('pr.deleted_at')
            ->where('pr.status', 'active')
            ->where(function ($query) {
                $query->whereNull('pr.end_date')
                    ->orWhere('pr.end_date', '>=', now()->toDateString());
            })
            ->orderByDesc('pr.start_date')
            ->limit(6)
            ->get([
                'pr.id',
                'pr.promotion_code',
                'pr.name',
                'pr.promotion_type',
                'pr.discount_value',
                'pr.start_date',
                'pr.end_date',
                'pr.min_nights',
                'p.name as property_name',
                'c.name as city_name',
                'images.cover_image',
                'images.fallback_image',
            ])
            ->map(function ($promotion) {
                return [
                    'id' => $promotion->id,
                    'code' => $promotion->promotion_code,
                    'name' => $promotion->name,
                    'type' => $promotion->promotion_type,
                    'discountValue' => (float) $promotion->discount_value,
                    'discountLabel' => $promotion->promotion_type === 'percentage'
                        ? rtrim(rtrim(number_format((float) $promotion->discount_value, 2), '0'), '.').'% off'
                        : ($promotion->promotion_type === 'fixed'
                            ? '$'.number_format((float) $promotion->discount_value, 0).' off'
                            : 'Free night'),
                    'startDate' => $promotion->start_date ? Carbon::parse($promotion->start_date)->format('d M') : null,
                    'endDate' => $promotion->end_date ? Carbon::parse($promotion->end_date)->format('d M Y') : null,
                    'minNights' => (int) $promotion->min_nights,
                    'propertyName' => $promotion->property_name,
                    'city' => $promotion->city_name,
                    'imageUrl' => $this->assetUrl($promotion->cover_image ?: $promotion->fallback_image),
                ];
            })
            ->values();

        return response()->json([
            'hero' => [
                'headline' => 'See the world for less',
                'subheadline' => 'Hotels, homes, activities and airport transfers — all in one booking flow.',
            ],
            'stats' => $stats,
            'destinations' => $destinations,
            'featuredProperties' => $featuredProperties,
            'propertyTypes' => $propertyTypes,
            'reviews' => $reviews,
            'promotions' => $promotions,
        ]);
    }

    private function assetUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }
}
