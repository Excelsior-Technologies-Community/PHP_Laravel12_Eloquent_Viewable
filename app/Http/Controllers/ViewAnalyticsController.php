<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewAnalyticsController extends Controller
{
    /**
     * Product View Analytics Dashboard
     */
    public function dashboard()
    {
        $productType = Product::class;

        // Total products
        $totalProducts = Product::count();

        // Total recorded views
        $totalViews = DB::table('views')
            ->where('viewable_type', $productType)
            ->count();

        // Average views per product
        $averageViews = $totalProducts > 0
            ? round($totalViews / $totalProducts, 2)
            : 0;

        // Most viewed product
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

        // Least viewed product
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

        // Top 5 most viewed products
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

        // Recent view activity
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
                'views.viewed_at'
            )
            ->orderByDesc('views.viewed_at')
            ->limit(10)
            ->get();

        return view('analytics.dashboard', compact(
            'totalProducts',
            'totalViews',
            'averageViews',
            'mostViewedProduct',
            'leastViewedProduct',
            'topProducts',
            'recentViews'
        ));
    }

    /**
     * Product Search, View Filtering and Pagination
     */
    public function products(Request $request)
    {
        $productType = Product::class;

        $search = $request->input('search');
        $viewFilter = $request->input('view_filter', 'all');
        $sort = $request->input('sort', 'latest');

        $products = Product::query()
            ->select('products.*')
            ->selectSub(function ($query) use ($productType) {
                $query->from('views')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('views.viewable_id', 'products.id')
                    ->where('views.viewable_type', $productType);
            }, 'view_count');

        // Search by product name
        if ($search) {
            $products->where('products.name', 'like', '%' . $search . '%');
        }

        // View filtering
        if ($viewFilter === 'popular') {
            $products->having('view_count', '>=', 10);
        }

        if ($viewFilter === 'medium') {
            $products->havingBetween('view_count', [3, 9]);
        }

        if ($viewFilter === 'low') {
            $products->havingBetween('view_count', [0, 2]);
        }

        // Sorting
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
                $products->latest('products.created_at');
                break;
        }

        $products = $products
            ->paginate(8)
            ->withQueryString();

        return view('analytics.products', compact(
            'products',
            'search',
            'viewFilter',
            'sort'
        ));
    }

    /**
     * View History and Popularity Report
     */
    public function history(Request $request)
    {
        $productType = Product::class;

        $productId = $request->input('product_id');

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
                'views.viewed_at'
            );

        if ($productId) {
            $viewsQuery->where('views.viewable_id', $productId);
        }

        $viewHistory = $viewsQuery
            ->orderByDesc('views.viewed_at')
            ->paginate(15)
            ->withQueryString();

        // Popularity report
        $popularProducts = Product::query()
            ->select('products.*')
            ->selectSub(function ($query) use ($productType) {
                $query->from('views')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('views.viewable_id', 'products.id')
                    ->where('views.viewable_type', $productType);
            }, 'view_count')
            ->orderByDesc('view_count')
            ->limit(10)
            ->get();

        $products = Product::orderBy('name')->get();

        return view('analytics.history', compact(
            'viewHistory',
            'popularProducts',
            'products',
            'productId'
        ));
    }

    /**
     * Export View History as CSV
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $productType = Product::class;

        $productId = $request->input('product_id');

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
                'views.collection',
                'views.viewed_at'
            );

        if ($productId) {
            $query->where('views.viewable_id', $productId);
        }

        $views = $query
            ->orderByDesc('views.viewed_at')
            ->get();

        $fileName = 'product-view-history-' . now()->format('Y-m-d-H-i-s') . '.csv';

        return response()->streamDownload(function () use ($views) {

            $file = fopen('php://output', 'w');

            // CSV heading
            fputcsv($file, [
                'View ID',
                'Product',
                'Price',
                'Visitor',
                'Collection',
                'Viewed At'
            ]);

            foreach ($views as $view) {
                fputcsv($file, [
                    $view->id,
                    $view->product_name,
                    $view->price,
                    $view->visitor,
                    $view->collection,
                    $view->viewed_at
                ]);
            }

            fclose($file);

        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }
}