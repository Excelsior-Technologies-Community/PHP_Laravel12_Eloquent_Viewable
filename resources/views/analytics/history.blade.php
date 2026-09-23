<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>View History & Popularity Report</title>

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

            <a href="{{ route('analytics.dashboard') }}"
               class="btn btn-outline-light btn-sm">
                Dashboard
            </a>

            <a href="{{ route('analytics.products') }}"
               class="btn btn-outline-light btn-sm">
                Search & Filter
            </a>

        </div>

    </div>

</nav>


<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                📈 View History & Popularity Report
            </h2>

            <p class="text-muted mb-0">
                Track individual product view activity.
            </p>

        </div>

        <a
            href="{{ route('analytics.history.export', ['product_id' => $productId]) }}"
            class="btn btn-success">

            📥 Export CSV

        </a>

    </div>


    <!-- PRODUCT FILTER -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('analytics.history') }}">

                <div class="row g-3 align-items-end">

                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Filter by Product
                        </label>

                        <select name="product_id"
                                class="form-select">

                            <option value="">
                                All Products
                            </option>

                            @foreach($products as $product)

                                <option
                                    value="{{ $product->id }}"
                                    {{ (string) $productId === (string) $product->id ? 'selected' : '' }}>

                                    {{ $product->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-3">

                        <button type="submit"
                                class="btn btn-primary w-100">

                            🔎 Filter History

                        </button>

                    </div>


                    <div class="col-md-3">

                        <a href="{{ route('analytics.history') }}"
                           class="btn btn-outline-secondary w-100">

                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- POPULARITY REPORT -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">
                🏆 Product Popularity Report
            </h5>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-dark">

                    <tr>

                        <th>Rank</th>

                        <th>Product</th>

                        <th>Price</th>

                        <th>Total Views</th>

                        <th>Status</th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($popularProducts as $index => $product)

                        <tr>

                            <td>

                                <span class="badge bg-dark">
                                    #{{ $index + 1 }}
                                </span>

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

                            <td>

                                @if($product->view_count >= 10)

                                    <span class="badge bg-danger">
                                        🔥 Popular
                                    </span>

                                @elseif($product->view_count >= 3)

                                    <span class="badge bg-warning text-dark">
                                        📈 Growing
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        New / Low Activity
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="text-center text-muted py-4">

                                No popularity data available.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- VIEW HISTORY -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">
                🕒 Detailed View History
            </h5>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-light">

                    <tr>

                        <th>ID</th>

                        <th>Product</th>

                        <th>Price</th>

                        <th>Visitor</th>

                        <th>Collection</th>

                        <th>Viewed At</th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($viewHistory as $view)

                        <tr>

                            <td>
                                {{ $view->id }}
                            </td>

                            <td class="fw-semibold">
                                {{ $view->product_name }}
                            </td>

                            <td>
                                ₹{{ number_format($view->price, 2) }}
                            </td>

                            <td>

                                @if($view->visitor)

                                    {{ $view->visitor }}

                                @else

                                    <span class="text-muted">
                                        Guest
                                    </span>

                                @endif

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

                            <td colspan="6"
                                class="text-center text-muted py-4">

                                No view history found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <!-- PAGINATION -->

        @if($viewHistory->hasPages())

            <div class="card-footer bg-white">

                {{ $viewHistory->links('pagination::bootstrap-5') }}

            </div>

        @endif

    </div>

</div>


</body>
</html>