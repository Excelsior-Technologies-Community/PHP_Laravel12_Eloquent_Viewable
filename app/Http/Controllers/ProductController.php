<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // LIST
    public function index()
    {
        $products = Product::latest()->get();

        return view('products.index', compact('products'));
    }

    // CREATE PAGE
    public function create()
    {
        return view('products.create');
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    // SHOW + VIEW COUNT WITH BOT DETECTION & DEVICE METADATA
    public function show(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $ip = $request->ip() ?: '127.0.0.1';
        $userAgent = $request->userAgent() ?: 'Unknown';

        // 1. Rate Limiting Cooldown Check (10 seconds per IP per product)
        $recentViewExists = DB::table('views')
            ->where('viewable_id', $product->id)
            ->where('viewable_type', Product::class)
            ->where('ip_address', $ip)
            ->where('viewed_at', '>=', now()->subSeconds(10))
            ->exists();

        // 2. Bot Spam Detection (If > 5 views in 1 min from same IP)
        $minuteViewsCount = DB::table('views')
            ->where('ip_address', $ip)
            ->where('viewed_at', '>=', now()->subMinute())
            ->count();

        $isBot = $minuteViewsCount >= 5;

        // Record view if not rate-limited
        if (!$recentViewExists) {
            views($product)->record();

            // Extract Device & Browser Metadata
            $deviceType = $this->detectDevice($userAgent);
            $browserName = $this->detectBrowser($userAgent);

            // Update the latest recorded view entry with metadata
            $latestViewId = DB::table('views')
                ->where('viewable_id', $product->id)
                ->where('viewable_type', Product::class)
                ->orderByDesc('id')
                ->value('id');

            if ($latestViewId) {
                DB::table('views')->where('id', $latestViewId)->update([
                    'device_type' => $deviceType,
                    'browser_name' => $browserName,
                    'ip_address' => $ip,
                    'is_bot' => $isBot,
                ]);
            }
        }

        return view('products.show', compact('product', 'isBot'));
    }

    // RECORD CTA ACTION (Add to Cart / Buy Now)
    public function recordAction(Request $request, $id)
    {
        $request->validate([
            'action_type' => 'required|string|in:add_to_cart,buy_now,favorite',
        ]);

        $product = Product::findOrFail($id);

        ProductAction::create([
            'product_id' => $product->id,
            'action_type' => $request->action_type,
            'ip_address' => $request->ip() ?: '127.0.0.1',
        ]);

        $actionLabel = $request->action_type === 'buy_now' ? 'Buy Now' : ($request->action_type === 'add_to_cart' ? 'Add to Cart' : 'Favorite');

        return redirect()->back()->with('success', "Action '{$actionLabel}' recorded successfully for {$product->name}!");
    }

    // EDIT PAGE
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        return view('products.edit', compact('product'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    // DELETE
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }

    // Helper: Detect Device Type
    private function detectDevice(string $ua): string
    {
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
            return 'Tablet';
        }
        if (preg_match('/(mobile|ipod|iphone|android|blackberry|opera mini|windows phone)/i', $ua)) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    // Helper: Detect Browser
    private function detectBrowser(string $ua): string
    {
        if (preg_match('/MSIE|Trident/i', $ua)) {
            return 'Internet Explorer';
        }
        if (preg_match('/Edge/i', $ua)) {
            return 'Edge';
        }
        if (preg_match('/Firefox/i', $ua)) {
            return 'Firefox';
        }
        if (preg_match('/Safari/i', $ua) && !preg_match('/Chrome/i', $ua)) {
            return 'Safari';
        }
        if (preg_match('/Chrome/i', $ua)) {
            return 'Chrome';
        }

        return 'Other';
    }
}