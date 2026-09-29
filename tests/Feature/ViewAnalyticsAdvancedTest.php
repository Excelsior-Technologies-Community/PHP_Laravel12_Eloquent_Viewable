<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewAnalyticsAdvancedTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_product_and_record_metadata(): void
    {
        $product = Product::create([
            'name' => 'Smart Watch Pro',
            'price' => 4999,
        ]);

        $response = $this->get(route('products.show', $product->id), [
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Smart Watch Pro');
        $response->assertSee('Add to Cart');

        $this->assertDatabaseHas('views', [
            'viewable_id' => $product->id,
            'device_type' => 'Mobile',
            'browser_name' => 'Safari',
        ]);
    }

    public function test_can_record_product_cta_action(): void
    {
        $product = Product::create([
            'name' => 'Wireless Headphones',
            'price' => 2999,
        ]);

        $response = $this->post(route('products.action', $product->id), [
            'action_type' => 'add_to_cart',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_actions', [
            'product_id' => $product->id,
            'action_type' => 'add_to_cart',
        ]);
    }

    public function test_dashboard_renders_device_breakdown_and_conversion_engine(): void
    {
        $product = Product::create([
            'name' => 'Gaming Mouse',
            'price' => 1500,
        ]);

        // Record a view
        $this->get(route('products.show', $product->id));

        // Record a CTA action
        $this->post(route('products.action', $product->id), [
            'action_type' => 'buy_now',
        ]);

        $dashboard = $this->get(route('analytics.dashboard'));
        $dashboard->assertStatus(200);
        $dashboard->assertSee('Device Type Share Analytics');
        $dashboard->assertSee('Product View-to-Action Conversion Ratios');
        $dashboard->assertSee('Hourly Traffic Peak Hours');
    }
}
