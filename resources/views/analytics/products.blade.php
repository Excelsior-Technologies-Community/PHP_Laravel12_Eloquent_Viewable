<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Product Search & View Filtering</title>

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

            <a href="{{ route('analytics.history') }}"
               class="btn btn-outline-light btn-sm">

                View History

            </a>

        </div>

    </div>

</nav>


<div class="container py-4">


    <div class="mb-4">

        <h2 class="fw-bold">

            🔎 Product Search & View Filtering

        </h2>

        <p class="text-muted">

            Search, filter, sort and paginate products by view activity.

        </p>

    </div>



    {{-- FILTER --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('analytics.products') }}">

                <div class="row g-3">


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



                    <div class="col-md-3">

                        <label class="form-label fw-semibold">

                            View Filter

                        </label>

                        <select name="view_filter"
                                class="form-select">

                            <option value="all"
                                {{ $viewFilter === 'all' ? 'selected' : '' }}>

                                All Products

                            </option>

                            <option value="popular"
                                {{ $viewFilter === 'popular' ? 'selected' : '' }}>

                                Popular (10+ Views)

                            </option>

                            <option value="medium"
                                {{ $viewFilter === 'medium' ? 'selected' : '' }}>

                                Medium (3-9 Views)

                            </option>

                            <option value="low"
                                {{ $viewFilter === 'low' ? 'selected' : '' }}>

                                Low (0-2 Views)

                            </option>

                        </select>

                    </div>



                    <div class="col-md-3">

                        <label class="form-label fw-semibold">

                            Sort By

                        </label>

                        <select name="sort"
                                class="form-select">

                            <option value="latest"
                                {{ $sort === 'latest' ? 'selected' : '' }}>

                                Latest Products

                            </option>

                            <option value="views_high"
                                {{ $sort === 'views_high' ? 'selected' : '' }}>

                                Highest Views

                            </option>

                            <option value="views_low"
                                {{ $sort === 'views_low' ? 'selected' : '' }}>

                                Lowest Views

                            </option>

                            <option value="price_high"
                                {{ $sort === 'price_high' ? 'selected' : '' }}>

                                Highest Price

                            </option>

                            <option value="price_low"
                                {{ $sort === 'price_low' ? 'selected' : '' }}>

                                Lowest Price

                            </option>

                            <option value="name_asc"
                                {{ $sort === 'name_asc' ? 'selected' : '' }}>

                                Name A-Z

                            </option>

                            <option value="name_desc"
                                {{ $sort === 'name_desc' ? 'selected' : '' }}>

                                Name Z-A

                            </option>

                        </select>

                    </div>



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


                <div class="row mt-3">

                    <div class="col-md-6">

                        <button
                            type="submit"
                            class="btn btn-primary">

                            🔎 Apply Filters

                        </button>


                        <a
                            href="{{ route('analytics.products') }}"
                            class="btn btn-outline-secondary">

                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>



    {{-- RESULTS --}}

    <div class="card border-0 shadow-sm">


        <div class="card-header bg-white d-flex justify-content-between">

            <h5 class="mb-0 fw-bold">

                Product Results

            </h5>


            <span class="badge bg-primary">

                {{ $products->total() }} Products

            </span>

        </div>



        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-dark">

                    <tr>

                        <th>ID</th>

                        <th>Product</th>

                        <th>Price</th>

                        <th>Views</th>

                        <th>Popularity</th>

                        <th>Action</th>

                    </tr>

                    </thead>


                    <tbody>

                    @forelse($products as $product)

                        <tr>

                            <td>

                                {{ $product->id }}

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

                                        📈 Medium

                                    </span>

                                @else

                                    <span class="badge bg-secondary">

                                        Low

                                    </span>

                                @endif

                            </td>


                            <td>

                                <a
                                    href="{{ route('products.show', $product->id) }}"
                                    class="btn btn-sm btn-primary">

                                    View

                                </a>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="6"
                                class="text-center text-muted py-4">

                                No products found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>



        {{-- PAGINATION --}}

        @if($products->hasPages())

            <div class="card-footer bg-white">

                {{ $products->links('pagination::bootstrap-5') }}

            </div>

        @endif


    </div>

</div>

</body>

</html>