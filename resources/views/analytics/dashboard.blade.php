<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product View Analytics Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light pb-5">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('products.index') }}">
            👁️ Eloquent Viewable Studio
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm">Products</a>
            <a href="{{ route('analytics.products') }}" class="btn btn-outline-light btn-sm">Search & Filter</a>
            <a href="{{ route('analytics.history') }}" class="btn btn-outline-light btn-sm">View History</a>
        </div>
    </div>
</nav>

<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">📊 Product View & Conversion Analytics</h2>
            <p class="text-muted mb-0">Bot protection, device metadata, conversion ratios, and hourly peak traffic</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-success font-semibold">
            + Add Product
        </a>
    </div>

    {{-- MODULE 1: SPIKE ALERT BANNER --}}
    @if(isset($hasTrafficSpike) && $hasTrafficSpike)
        <div class="alert alert-warning border-warning d-flex items-center justify-content-between shadow-sm mb-4">
            <div class="d-flex items-center gap-3">
                <span class="fs-3">⚡</span>
                <div>
                    <strong class="d-block">Traffic Surge Detected!</strong>
                    <span class="small">Received {{ $recentSpikeViews }} views in the last 10 minutes. View rate-limiting active to suppress bot spam.</span>
                </div>
            </div>
            <span class="badge bg-danger fs-6">High Traffic Spike</span>
        </div>
    @endif

    {{-- MAIN STATISTICS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3">
                <div class="text-muted small">Total Products</div>
                <h2 class="fw-bold text-dark mt-1 mb-0">{{ $totalProducts }}</h2>
                <span class="text-primary small">📦 Inventory Count</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3">
                <div class="text-muted small">Total Tracked Views</div>
                <h2 class="fw-bold text-success mt-1 mb-0">{{ $totalViews }}</h2>
                <span class="text-muted small">👁️ Unique: {{ $uniqueVisitors }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3">
                <div class="text-muted small">Total Conversion Actions</div>
                <h2 class="fw-bold text-purple text-indigo mt-1 mb-0" style="color: #6f42c1;">{{ $totalActions }}</h2>
                <span class="text-purple small" style="color: #6f42c1;">🛒 Cart & Buy Clicks</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3">
                <div class="text-muted small">Overall Conversion Rate</div>
                <h2 class="fw-bold text-warning mt-1 mb-0">{{ $overallConversionRate }}%</h2>
                <span class="text-warning small">🎯 Action / View Ratio</span>
            </div>
        </div>
    </div>

    {{-- MODULE 2: DEVICE & BROWSER BREAKDOWN --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white font-bold">
                    📱 Device Type Share Analytics
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>🖥️ Desktop</span>
                            <span class="fw-bold">{{ $desktopCount }} views ({{ $devicePercentages['Desktop'] }}%)</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-primary" style="width: {{ $devicePercentages['Desktop'] }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>📱 Mobile</span>
                            <span class="fw-bold">{{ $mobileCount }} views ({{ $devicePercentages['Mobile'] }}%)</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-success" style="width: {{ $devicePercentages['Mobile'] }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>タブ Tablet</span>
                            <span class="fw-bold">{{ $tabletCount }} views ({{ $devicePercentages['Tablet'] }}%)</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-info" style="width: {{ $devicePercentages['Tablet'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white font-bold">
                    🌐 Browser Distribution & Bot Suppressions
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="p-2 bg-light rounded">
                                <div class="text-muted small">Chrome</div>
                                <div class="fw-bold text-primary fs-5">{{ $browserBreakdown['Chrome'] ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded">
                                <div class="text-muted small">Firefox / Safari</div>
                                <div class="fw-bold text-success fs-5">{{ ($browserBreakdown['Firefox'] ?? 0) + ($browserBreakdown['Safari'] ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded">
                                <div class="text-muted small">Edge / Other</div>
                                <div class="fw-bold text-dark fs-5">{{ ($browserBreakdown['Edge'] ?? 0) + ($browserBreakdown['Other'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 bg-red-50 text-danger rounded border border-danger-subtle text-center">
                        🛡️ <strong>Bot Spam Suppressed:</strong> {{ $totalBotViews }} suspicious views rate-limited & flagged
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODULE 3: CONVERSION & ENGAGEMENT SCORE TABLE --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">🛒 Product View-to-Action Conversion Ratios</h5>
            <span class="badge bg-primary">Engagement Engine</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Total Views</th>
                            <th>Actions Recorded</th>
                            <th>Conversion Rate</th>
                            <th>Engagement Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productsWithConversion as $product)
                            <tr>
                                <td class="fw-bold text-dark">{{ $product->name }}</td>
                                <td>₹{{ number_format($product->price, 2) }}</td>
                                <td><span class="badge bg-secondary">👁️ {{ $product->view_count ?: 0 }}</span></td>
                                <td><span class="badge bg-purple" style="background:#6f42c1;">🛒 {{ $product->action_count ?: 0 }}</span></td>
                                <td>
                                    <span class="fw-bold {{ $product->conversion_rate >= 20 ? 'text-success' : 'text-primary' }}">
                                        {{ $product->conversion_rate }}%
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        ⭐ {{ $product->engagement_score }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No product conversion data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODULE 4: HOURLY PEAK TRAFFIC TRENDS CHART --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="fw-bold mb-0">📅 Hourly Traffic Peak Hours (00:00 - 23:00)</h5>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-end gap-1 overflow-x-auto pt-4 pb-2" style="height: 180px;">
                @php
                    $maxHourly = max(1, max($hourlyTrends));
                @endphp
                @foreach($hourlyTrends as $hourStr => $count)
                    @php
                        $heightPct = max(5, round(($count / $maxHourly) * 100));
                    @endphp
                    <div class="text-center flex-grow-1" style="min-width: 30px;">
                        <div class="small fw-bold mb-1 text-primary">{{ $count }}</div>
                        <div class="bg-primary rounded-top mx-auto" style="height: {{ $heightPct }}%; width: 18px;" title="{{ $hourStr }}: {{ $count }} views"></div>
                        <div class="text-muted" style="font-size: 10px; margin-top: 4px;">{{ substr($hourStr, 0, 2) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- RECENT ACTIVITY --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="fw-bold mb-0">🕒 Recent Product View Activity & Metadata</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Device</th>
                            <th>Browser</th>
                            <th>Bot Status</th>
                            <th>Viewed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentViews as $view)
                            <tr>
                                <td class="fw-semibold">{{ $view->name }}</td>
                                <td>₹{{ number_format($view->price, 2) }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $view->device_type ?? 'Desktop' }}</span></td>
                                <td><span class="badge bg-light text-dark border">{{ $view->browser_name ?? 'Chrome' }}</span></td>
                                <td>
                                    <span class="badge {{ $view->is_bot ? 'bg-danger' : 'bg-success' }}">
                                        {{ $view->is_bot ? '🤖 Bot' : '👤 Human' }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($view->viewed_at)->format('d M Y, h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No view activity found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>