<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Products</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">


<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <span class="navbar-brand fw-bold">
            Eloquent Viewable
        </span>

        <div>

            <a href="{{ route('analytics.dashboard') }}"
               class="btn btn-outline-light btn-sm">
                📊 Analytics
            </a>

            <a href="{{ route('analytics.products') }}"
               class="btn btn-outline-light btn-sm">
                🔎 Search & Filter
            </a>

            <a href="{{ route('analytics.history') }}"
               class="btn btn-outline-light btn-sm">
                📈 View History
            </a>

        </div>

    </div>

</nav>


<div class="container py-5">


    <div class="card border-0 shadow-sm">

        <div class="card-body">


            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Product List
                    </h2>

                    <p class="text-muted mb-0">
                        Products with Eloquent Viewable tracking
                    </p>

                </div>

                <a
                    href="{{ route('products.create') }}"
                    class="btn btn-success">

                    + Add Product

                </a>

            </div>


            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-dark">

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Price</th>

                        <th>Views</th>

                        <th>Actions</th>

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

                                    👁 {{ views($product)->count() }}

                                </span>

                            </td>

                            <td>

                                <a
                                    class="btn btn-sm btn-primary"
                                    href="{{ route('products.show', $product->id) }}">

                                    View

                                </a>


                                <a
                                    class="btn btn-sm btn-warning"
                                    href="{{ route('products.edit', $product->id) }}">

                                    Edit

                                </a>


                                <form
                                    action="{{ route('products.destroy', $product->id) }}"
                                    method="POST"
                                    style="display:inline;">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this product?')">

                                        Delete

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="text-center text-muted py-4">

                                No products found.

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