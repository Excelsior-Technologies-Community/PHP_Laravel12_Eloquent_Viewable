<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewAnalyticsController extends Controller
{
    /**
     * Product View Analytics Dashboard
     */
    public function dashboard(Request $request)
    {
        $productType = Product::class;

        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();

        // Total products
        $totalProducts = Product::count();

        // Total views
        $totalViews = DB::table('views')
            ->where('viewable_type', $productType)
            ->count();

        // Today's views
        $todayViews = DB::table('views')
            ->where('viewable_type', $productType)
            ->where('viewed_at', '>=', $today)
            ->count();

        // This week's views
        $weekViews = DB::table('views')
            ->where('viewable_type', $productType)
            ->where('viewed_at', '>=', $weekStart)
            ->count();

        // This month's views
        $monthViews = DB::table('views')
            ->where('viewable_type', $productType)
            ->where('viewed_at', '>=', $monthStart)
            ->count();

        // Unique visitors
        $uniqueVisitors = DB::table('views')
            ->where('viewable_type', $productType)
            ->whereNotNull('visitor')
            ->distinct('visitor')
            ->count('visitor');

        // Average views per product
        $averageViews = $totalProducts > 0
            ? round($totalViews / $totalProducts, 2)
            : 0;

        // --- MODULE 1: SPIKE ALERT BANNER & BOT SPAM STATS ---
        $recentSpikeViews = DB::table('views')
            ->where('viewable_type', $productType)
            ->where('viewed_at', '>=', now()->subMinutes(10))
            ->count();

        $hasTrafficSpike = $recentSpikeViews >= 10;
        $totalBotViews = DB::table('views')
            ->where('viewable_type', $productType)
            ->where('is_bot', true)
            ->count();

        // --- MODULE 2: DEVICE & BROWSER BREAKDOWN ---
        $deviceBreakdown = DB::table('views')
            ->where('viewable_type', $productType)
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();

        $desktopCount = $deviceBreakdown['Desktop'] ?? 0;
        $mobileCount = $deviceBreakdown['Mobile'] ?? 0;
        $tabletCount = $deviceBreakdown['Tablet'] ?? 0;
        $denomViews = max(1, $totalViews);

        $devicePercentages = [
            'Desktop' => round(($desktopCount / $denomViews) * 100, 1),
            'Mobile' => round(($mobileCount / $denomViews) * 100, 1),
            'Tablet' => round(($tabletCount / $denomViews) * 100, 1),
        ];

        $browserBreakdown = DB::table('views')
            ->where('viewable_type', $productType)
            ->select('browser_name', DB::raw('COUNT(*) as count'))
            ->groupBy('browser_name')
            ->pluck('count', 'browser_name')
            ->toArray();

        // --- MODULE 3: VIEW-TO-ACTION & CONVERSION ENGINE ---
        $totalActions = ProductAction::count();
        $overallConversionRate = $totalViews > 0
            ? round(($totalActions / $totalViews) * 100, 2)
            : 0;

        $productsWithConversion = Product::query()
            ->select('products.*')
            ->selectSub(function ($query) use ($productType) {
                $query->from('views')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('views.viewable_id', 'products.id')
                    ->where('views.viewable_type', $productType);
            }, 'view_count')
            ->selectSub(function ($query) {
                $query->from('product_actions')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('product_actions.product_id', 'products.id');
            }, 'action_count')
            ->get()
            ->map(function ($product) {
                $v = $product->view_count ?: 0;
                $a = $product->action_count ?: 0;
                $product->conversion_rate = $v > 0 ? round(($a / $v) * 100, 1) : 0;
                $product->engagement_score = round(($v * 0.4) + ($a * 2.5), 1);

                return $product;
            })
            ->sortByDesc('conversion_rate')
            ->values();

        // --- MODULE 4: HOURLY PEAK TRENDS & WEEKLY HEATMAP ---
        $driverName = DB::connection()->getDriverName();
        if ($driverName === 'sqlite') {
            $hourlyViewsRaw = DB::table('views')
                ->where('viewable_type', $productType)
                ->select(DB::raw("CAST(strftime('%H', viewed_at) AS INTEGER) as hour"), DB::raw('COUNT(*) as count'))
                ->groupBy('hour')
                ->pluck('count', 'hour')
                ->toArray();
        } else {
            $hourlyViewsRaw = DB::table('views')
                ->where('viewable_type', $productType)
                ->select(DB::raw('HOUR(viewed_at) as hour'), DB::raw('COUNT(*) as count'))
                ->groupBy('hour')
                ->pluck('count', 'hour')
                ->toArray();
        }

        $hourlyTrends = [];
        for ($h = 0; $h < 24; $h++) {
            $formattedHour = sprintf('%02d:00', $h);
            $hourlyTrends[$formattedHour] = $hourlyViewsRaw[$h] ?? 0;
        }

        $mostViewedProduct = Product::query()
            ->select('products.*')
            ->selectSub(function ($query) use ($productType) {
                $query->from('views')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('views.viewable_id', 'products.id')
                    ->where('views.viewable_type', $productType);
            }, 'view_count')
            ->orderByDesc('view_count')
            ->first();

        $leastViewedProduct = Product::query()
            ->select('products.*')
            ->selectSub(function ($query) use ($productType) {
                $query->from('views')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('views.viewable_id', 'products.id')
                    ->where('views.viewable_type', $productType);
            }, 'view_count')
            ->orderBy('view_count')
            ->first();

        $topProducts = Product::query()
            ->select('products.*')
            ->selectSub(function ($query) use ($productType) {
                $query->from('views')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('views.viewable_id', 'products.id')
                    ->where('views.viewable_type', $productType);
            }, 'view_count')
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();

        $recentViews = DB::table('views')
            ->join('products', function ($join) use ($productType) {
                $join->on('products.id', '=', 'views.viewable_id')
                    ->where('views.viewable_type', '=', $productType);
            })
            ->select(
                'views.id',
                'products.name',
                'products.price',
                'views.visitor',
                'views.collection',
                'views.device_type',
                'views.browser_name',
                'views.is_bot',
                'views.viewed_at'
            )
            ->orderByDesc('views.viewed_at')
            ->limit(5)
            ->get();

        return view('analytics.dashboard', compact(
            'totalProducts',
            'totalViews',
            'todayViews',
            'weekViews',
            'monthViews',
            'uniqueVisitors',
            'averageViews',
            'hasTrafficSpike',
            'recentSpikeViews',
            'totalBotViews',
            'desktopCount',
            'mobileCount',
            'tabletCount',
            'devicePercentages',
            'browserBreakdown',
            'totalActions',
            'overallConversionRate',
            'productsWithConversion',
            'hourlyTrends',
            'mostViewedProduct',
            'leastViewedProduct',
            'topProducts',
            'recentViews'
        ));
    }

    /**
     * Product Search / Filtering / Pagination
     */
    public function products(Request $request)
    {
        $productType = Product::class;

        $search = $request->input('search');
        $viewFilter = $request->input('view_filter', 'all');
        $sort = $request->input('sort', 'oldest');
        $perPage = (int) $request->input('per_page', 8);

        if (!in_array($perPage, [5, 8, 10, 25, 50], true)) {
            $perPage = 8;
        }

        $products = Product::query()
            ->select('products.*')
            ->selectSub(function ($query) use ($productType) {
                $query->from('views')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('views.viewable_id', 'products.id')
                    ->where('views.viewable_type', $productType);
            }, 'view_count');

        if ($search) {
            $products->where('products.name', 'like', '%' . $search . '%');
        }

        if ($viewFilter === 'popular') {
            $products->having('view_count', '>=', 10);
        }
        if ($viewFilter === 'medium') {
            $products->havingBetween('view_count', [3, 9]);
        }
        if ($viewFilter === 'low') {
            $products->havingBetween('view_count', [0, 2]);
        }

        switch ($sort) {
            case 'views_high':
                $products->orderByDesc('view_count');
                break;
            case 'views_low':
                $products->orderBy('view_count');
                break;
            case 'price_high':
                $products->orderByDesc('price');
                break;
            case 'price_low':
                $products->orderBy('price');
                break;
            case 'name_asc':
                $products->orderBy('name');
                break;
            case 'name_desc':
                $products->orderByDesc('name');
                break;
            default:
                $products->oldest('products.created_at');
                break;
        }

        $products = $products->paginate($perPage)->withQueryString();

        return view('analytics.products', compact(
            'products',
            'search',
            'viewFilter',
            'sort',
            'perPage'
        ));
    }

    /**
     * View History
     */
    public function history(Request $request)
    {
        $productType = Product::class;

        $productId = $request->input('product_id');
        $search = $request->input('search');

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $minViews = $request->input('min_views');
        $maxViews = $request->input('max_views');

        $sort = $request->input('sort', 'oldest');
        $perPage = (int) $request->input('per_page', 15);

        if (!in_array($perPage, [5, 10, 25, 50], true)) {
            $perPage = 15;
        }

        $viewsQuery = DB::table('views')
            ->join('products', function ($join) use ($productType) {
                $join->on('products.id', '=', 'views.viewable_id')
                    ->where('views.viewable_type', '=', $productType);
            })
            ->select(
                'views.id',
                'views.viewable_id',
                'products.name as product_name',
                'products.price',
                'views.visitor',
                'views.collection',
                'views.device_type',
                'views.browser_name',
                'views.is_bot',
                'views.viewed_at'
            );

        if ($productId) {
            $viewsQuery->where('views.viewable_id', $productId);
        }
        if ($search) {
            $viewsQuery->where('products.name', 'like', '%' . $search . '%');
        }
        if ($dateFrom) {
            $viewsQuery->whereDate('views.viewed_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $viewsQuery->whereDate('views.viewed_at', '<=', $dateTo);
        }

        switch ($sort) {
            case 'oldest':
                $viewsQuery->orderBy('views.viewed_at', 'asc');
                break;
            case 'product_asc':
                $viewsQuery->orderBy('products.name', 'asc');
                break;
            case 'product_desc':
                $viewsQuery->orderBy('products.name', 'desc');
                break;
            default:
                $viewsQuery->orderBy('views.viewed_at', 'desc');
                break;
        }

        $viewHistory = $viewsQuery->paginate($perPage)->withQueryString();

        $filteredViewsQuery = DB::table('views')
            ->join('products', function ($join) use ($productType) {
                $join->on('products.id', '=', 'views.viewable_id')
                    ->where('views.viewable_type', '=', $productType);
            });

        if ($productId) {
            $filteredViewsQuery->where('views.viewable_id', $productId);
        }
        if ($search) {
            $filteredViewsQuery->where('products.name', 'like', '%' . $search . '%');
        }
        if ($dateFrom) {
            $filteredViewsQuery->whereDate('views.viewed_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $filteredViewsQuery->whereDate('views.viewed_at', '<=', $dateTo);
        }

        $filteredViews = $filteredViewsQuery->count();

        $popularProducts = Product::query()
            ->select('products.*')
            ->selectSub(function ($query) use ($productType) {
                $query->from('views')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('views.viewable_id', 'products.id')
                    ->where('views.viewable_type', $productType);
            }, 'view_count');

        if ($minViews !== null && $minViews !== '') {
            $popularProducts->having('view_count', '>=', (int) $minViews);
        }
        if ($maxViews !== null && $maxViews !== '') {
            $popularProducts->having('view_count', '<=', (int) $maxViews);
        }

        $popularProducts = $popularProducts->orderByDesc('view_count')->limit(10)->get();
        $products = Product::orderBy('name')->get();

        return view('analytics.history', compact(
            'viewHistory',
            'popularProducts',
            'products',
            'productId',
            'search',
            'dateFrom',
            'dateTo',
            'minViews',
            'maxViews',
            'sort',
            'perPage',
            'filteredViews'
        ));
    }

    /**
     * Export Filtered View History CSV
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $productType = Product::class;

        $productId = $request->input('product_id');
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $sort = $request->input('sort', 'oldest');

        $query = DB::table('views')
            ->join('products', function ($join) use ($productType) {
                $join->on('products.id', '=', 'views.viewable_id')
                    ->where('views.viewable_type', '=', $productType);
            })
            ->select(
                'views.id',
                'products.name as product_name',
                'products.price',
                'views.visitor',
                'views.device_type',
                'views.browser_name',
                'views.is_bot',
                'views.viewed_at'
            );

        if ($productId) {
            $query->where('views.viewable_id', $productId);
        }
        if ($search) {
            $query->where('products.name', 'like', '%' . $search . '%');
        }
        if ($dateFrom) {
            $query->whereDate('views.viewed_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('views.viewed_at', '<=', $dateTo);
        }

        if ($sort === 'oldest') {
            $query->orderBy('views.viewed_at', 'asc');
        } elseif ($sort === 'product_asc') {
            $query->orderBy('products.name', 'asc');
        } elseif ($sort === 'product_desc') {
            $query->orderBy('products.name', 'desc');
        } else {
            $query->orderBy('views.viewed_at', 'desc');
        }

        $views = $query->get();
        $fileName = 'product-view-history-' . now()->format('Y-m-d-H-i-s') . '.csv';

        return response()->streamDownload(function () use ($views) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'View ID',
                'Product',
                'Price',
                'Visitor',
                'Device Type',
                'Browser',
                'Bot Flag',
                'Viewed At',
            ]);

            foreach ($views as $view) {
                fputcsv($file, [
                    $view->id,
                    $view->product_name,
                    $view->price,
                    $view->visitor ?? 'Guest',
                    $view->device_type ?? 'Desktop',
                    $view->browser_name ?? 'Chrome',
                    $view->is_bot ? 'YES' : 'NO',
                    $view->viewed_at,
                ]);
            }
            fclose($file);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    /**
     * Delete Individual View History
     */
    public function deleteView($id)
    {
        DB::table('views')->where('id', $id)->delete();

        return back()->with('success', 'View history record deleted successfully.');
    }
}