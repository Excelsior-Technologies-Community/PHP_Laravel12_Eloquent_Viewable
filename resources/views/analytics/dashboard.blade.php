<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Product View Analytics</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>


<body class="bg-light">


<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a class="navbar-brand fw-bold"
           href="{{ route('products.index') }}">

            Eloquent Viewable

        </a>


        <div>

            <a href="{{ route('products.index') }}"
               class="btn btn-outline-light btn-sm">

                Products

            </a>

            <a href="{{ route('analytics.products') }}"
               class="btn btn-outline-light btn-sm">

                Search & Filter

            </a>

            <a href="{{ route('analytics.history') }}"
               class="btn btn-outline-light btn-sm">

                View History

            </a>

        </div>

    </div>

</nav>


<div class="container py-4">


    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">

                📊 Product View Analytics

            </h2>

            <p class="text-muted mb-0">

                Eloquent Viewable statistics and product popularity

            </p>

        </div>


        <a href="{{ route('products.create') }}"
           class="btn btn-success">

            + Add Product

        </a>

    </div>



    {{-- MAIN STATISTICS --}}

    <div class="row g-4 mb-4">


        <div class="col-md-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Total Products
                    </div>

                    <h2 class="fw-bold mt-2">

                        {{ $totalProducts }}

                    </h2>

                    <small class="text-primary">
                        📦 Products
                    </small>

                </div>

            </div>

        </div>



        <div class="col-md-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Total Views
                    </div>

                    <h2 class="fw-bold mt-2">

                        {{ $totalViews }}

                    </h2>

                    <small class="text-success">
                        👁 All recorded views
                    </small>

                </div>

            </div>

        </div>



        <div class="col-md-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Unique Visitors
                    </div>

                    <h2 class="fw-bold mt-2">

                        {{ $uniqueVisitors }}

                    </h2>

                    <small class="text-info">
                        👤 Unique visitors
                    </small>

                </div>

            </div>

        </div>



        <div class="col-md-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Average Views
                    </div>

                    <h2 class="fw-bold mt-2">

                        {{ $averageViews }}

                    </h2>

                    <small class="text-warning">
                        📈 Per product
                    </small>

                </div>

            </div>

        </div>

    </div>



    {{-- TIME STATISTICS --}}

    <div class="row g-4 mb-4">


        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted">
                        Today's Views
                    </div>

                    <h2 class="fw-bold text-primary">

                        {{ $todayViews }}

                    </h2>

                    <small class="text-muted">
                        Since midnight
                    </small>

                </div>

            </div>

        </div>



        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted">
                        This Week
                    </div>

                    <h2 class="fw-bold text-success">

                        {{ $weekViews }}

                    </h2>

                    <small class="text-muted">
                        Current week
                    </small>

                </div>

            </div>

        </div>



        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted">
                        This Month
                    </div>

                    <h2 class="fw-bold text-warning">

                        {{ $monthViews }}

                    </h2>

                    <small class="text-muted">
                        Current month
                    </small>

                </div>

            </div>

        </div>

    </div>



    {{-- TOP PRODUCTS --}}

    <div class="card border-0 shadow-sm mb-4">


        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">

                🏆 Top 5 Most Viewed Products

            </h5>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-dark">

                    <tr>

                        <th>#</th>

                        <th>Product</th>

                        <th>Price</th>

                        <th>Views</th>

                        <th>Percentage</th>

                    </tr>

                    </thead>


                    <tbody>

                    @forelse($topProducts as $index => $product)

                        @php

                            $percentage = $totalViews > 0
                                ? min(
                                    100,
                                    ($product->view_count / $totalViews) * 100
                                )
                                : 0;

                        @endphp


                        <tr>

                            <td>

                                {{ $index + 1 }}

                            </td>


                            <td class="fw-semibold">

                                {{ $product->name }}

                            </td>


                            <td>

                                ₹{{ number_format($product->price, 2) }}

                            </td>


                            <td>

                                <span class="badge bg-success">

                                    👁 {{ $product->view_count }}

                                </span>

                            </td>


                            <td style="width:250px">

                                <div class="progress">

                                    <div
                                        class="progress-bar"
                                        style="width: {{ $percentage }}%">

                                        {{ round($percentage, 1) }}%

                                    </div>

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="5"
                                class="text-center text-muted py-4">

                                No product view data available.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>



    {{-- MOST / LEAST --}}

    <div class="row g-4 mb-4">


        <div class="col-md-6">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <h5 class="fw-bold">

                        🔥 Most Viewed Product

                    </h5>


                    @if($mostViewedProduct)

                        <h3 class="mt-3">

                            {{ $mostViewedProduct->name }}

                        </h3>

                        <p class="text-muted">

                            ₹{{ number_format($mostViewedProduct->price, 2) }}

                        </p>

                        <span class="badge bg-success">

                            👁 {{ $mostViewedProduct->view_count }} Views

                        </span>

                    @else

                        <p class="text-muted">

                            No views available.

                        </p>

                    @endif

                </div>

            </div>

        </div>



        <div class="col-md-6">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <h5 class="fw-bold">

                        📉 Least Viewed Product

                    </h5>


                    @if($leastViewedProduct)

                        <h3 class="mt-3">

                            {{ $leastViewedProduct->name }}

                        </h3>

                        <p class="text-muted">

                            ₹{{ number_format($leastViewedProduct->price, 2) }}

                        </p>

                        <span class="badge bg-secondary">

                            👁 {{ $leastViewedProduct->view_count }} Views

                        </span>

                    @else

                        <p class="text-muted">

                            No products available.

                        </p>

                    @endif

                </div>

            </div>

        </div>

    </div>



    {{-- RECENT ACTIVITY --}}

    <div class="card border-0 shadow-sm">


        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">

                🕒 Recent Product View Activity

            </h5>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-light">

                    <tr>

                        <th>Product</th>

                        <th>Price</th>

                        <th>Visitor</th>

                        <th>Collection</th>

                        <th>Viewed At</th>

                    </tr>

                    </thead>


                    <tbody>

                    @forelse($recentViews as $view)

                        <tr>

                            <td class="fw-semibold">

                                {{ $view->name }}

                            </td>

                            <td>

                                ₹{{ number_format($view->price, 2) }}

                            </td>

                            <td>

                                {{ $view->visitor ?? 'Guest' }}

                            </td>

                            <td>

                                {{ $view->collection ?? '-' }}

                            </td>

                            <td>

                                {{ \Carbon\Carbon::parse($view->viewed_at)->format('d M Y, h:i A') }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="text-center text-muted py-4">

                                No view activity found.

                            </td>

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