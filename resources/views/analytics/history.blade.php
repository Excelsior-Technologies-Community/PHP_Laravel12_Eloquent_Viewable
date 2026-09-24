<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>View History & Analytics</title>

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


    {{-- SUCCESS MESSAGE --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif



    {{-- HEADER --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">

                📈 View History & Popularity Report

            </h2>

            <p class="text-muted mb-0">

                Search, filter and manage product view activity.

            </p>

        </div>


        {{-- FILTERED CSV EXPORT --}}

        <a
            href="{{ route('analytics.history.export', request()->query()) }}"
            class="btn btn-success">

            📥 Export Filtered CSV

        </a>

    </div>



    {{-- FILTER FORM --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">

                🔎 View History Filters

            </h5>

        </div>


        <div class="card-body">

            <form method="GET"
                  action="{{ route('analytics.history') }}">


                <div class="row g-3">


                    {{-- PRODUCT --}}

                    <div class="col-md-4">

                        <label class="form-label fw-semibold">

                            Product

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



                    {{-- SEARCH --}}

                    <div class="col-md-4">

                        <label class="form-label fw-semibold">

                            Search Product

                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="Search product name...">

                    </div>



                    {{-- SORT --}}

                    <div class="col-md-4">

                        <label class="form-label fw-semibold">

                            Sort History

                        </label>

                        <select name="sort"
                                class="form-select">

                            <option value="latest"
                                {{ $sort === 'latest' ? 'selected' : '' }}>

                                Newest First

                            </option>

                            <option value="oldest"
                                {{ $sort === 'oldest' ? 'selected' : '' }}>

                                Oldest First

                            </option>

                            <option value="product_asc"
                                {{ $sort === 'product_asc' ? 'selected' : '' }}>

                                Product A-Z

                            </option>

                            <option value="product_desc"
                                {{ $sort === 'product_desc' ? 'selected' : '' }}>

                                Product Z-A

                            </option>

                        </select>

                    </div>



                    {{-- DATE FROM --}}

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">

                            Date From

                        </label>

                        <input
                            type="date"
                            name="date_from"
                            value="{{ $dateFrom }}"
                            class="form-control">

                    </div>



                    {{-- DATE TO --}}

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">

                            Date To

                        </label>

                        <input
                            type="date"
                            name="date_to"
                            value="{{ $dateTo }}"
                            class="form-control">

                    </div>



                    {{-- MIN VIEWS --}}

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">

                            Min Views

                        </label>

                        <input
                            type="number"
                            name="min_views"
                            value="{{ $minViews }}"
                            min="0"
                            class="form-control"
                            placeholder="0">

                    </div>



                    {{-- MAX VIEWS --}}

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">

                            Max Views

                        </label>

                        <input
                            type="number"
                            name="max_views"
                            value="{{ $maxViews }}"
                            min="0"
                            class="form-control"
                            placeholder="100">

                    </div>



                    {{-- PER PAGE --}}

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">

                            Per Page

                        </label>

                        <select name="per_page"
                                class="form-select">

                            <option value="5"
                                {{ $perPage == 5 ? 'selected' : '' }}>

                                5

                            </option>

                            <option value="10"
                                {{ $perPage == 10 ? 'selected' : '' }}>

                                10

                            </option>

                            <option value="25"
                                {{ $perPage == 25 ? 'selected' : '' }}>

                                25

                            </option>

                            <option value="50"
                                {{ $perPage == 50 ? 'selected' : '' }}>

                                50

                            </option>

                        </select>

                    </div>

                </div>



                <div class="mt-4">


                    <button
                        type="submit"
                        class="btn btn-primary">

                        🔎 Apply Filters

                    </button>


                    <a
                        href="{{ route('analytics.history') }}"
                        class="btn btn-outline-secondary">

                        Reset

                    </a>

                </div>


            </form>

        </div>

    </div>



    {{-- QUICK FILTERS --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">


            <strong class="me-3">

                Quick Filters:

            </strong>


            <a
                href="{{ route('analytics.history', [
                    'date_from' => now()->format('Y-m-d'),
                    'date_to' => now()->format('Y-m-d')
                ]) }}"
                class="btn btn-sm btn-outline-primary">

                Today

            </a>


            <a
                href="{{ route('analytics.history', [
                    'date_from' => now()->startOfWeek()->format('Y-m-d'),
                    'date_to' => now()->format('Y-m-d')
                ]) }}"
                class="btn btn-sm btn-outline-success">

                This Week

            </a>


            <a
                href="{{ route('analytics.history', [
                    'date_from' => now()->startOfMonth()->format('Y-m-d'),
                    'date_to' => now()->format('Y-m-d')
                ]) }}"
                class="btn btn-sm btn-outline-warning">

                This Month

            </a>


            <a
                href="{{ route('analytics.history') }}"
                class="btn btn-sm btn-outline-secondary">

                All Time

            </a>

        </div>

    </div>



    {{-- FILTERED COUNT --}}

    <div class="row mb-4">


        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted">

                        Filtered Views

                    </div>

                    <h2 class="fw-bold text-primary">

                        {{ $filteredViews }}

                    </h2>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted">

                        Current Page

                    </div>

                    <h2 class="fw-bold text-success">

                        {{ $viewHistory->count() }}

                    </h2>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted">

                        Per Page

                    </div>

                    <h2 class="fw-bold text-warning">

                        {{ $perPage }}

                    </h2>

                </div>

            </div>

        </div>

    </div>



    {{-- POPULARITY REPORT --}}

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



    {{-- VIEW HISTORY --}}

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

                        <th>Action</th>

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

                                {{ $view->visitor ?? 'Guest' }}

                            </td>


                            <td>

                                {{ $view->collection ?? '-' }}

                            </td>


                            <td>

                                {{ \Carbon\Carbon::parse($view->viewed_at)->format('d M Y, h:i A') }}

                            </td>


                            <td>

                                <form
                                    action="{{ route('analytics.history.delete', $view->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this view history record?')">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger">

                                        Delete

                                    </button>

                                </form>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="7"
                                class="text-center text-muted py-4">

                                No view history found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>



        {{-- PAGINATION --}}

        @if($viewHistory->hasPages())

            <div class="card-footer bg-white">

                {{ $viewHistory->links('pagination::bootstrap-5') }}

            </div>

        @endif


    </div>


</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>