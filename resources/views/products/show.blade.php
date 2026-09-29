<!DOCTYPE html>
<html>
<head>
<title>Product Details - {{ $product->name }}</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

<div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100">

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(isset($isBot) && $isBot)
        <div class="mb-4 p-3 bg-amber-100 border border-amber-400 text-amber-800 rounded-lg text-xs font-semibold">
            🤖 View Rate-Limited (Potential Bot / Rapid Refresh Detected)
        </div>
    @endif

    <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ $product->name }}</h2>

    <p class="text-xl font-semibold text-indigo-600 mb-4">Price: ₹{{ number_format($product->price) }}</p>

    <div class="my-6 p-4 bg-green-50 rounded-xl border border-green-200">
        <div class="text-3xl font-bold text-green-700">
            👁 {{ views($product)->count() }}
        </div>
        <div class="text-xs text-green-600 font-medium uppercase tracking-wider mt-1">Total Views Tracked</div>
    </div>

    {{-- CTA ACTION BUTTONS --}}
    <div class="space-y-3 mb-6">
        <form action="{{ route('products.action', $product->id) }}" method="POST">
            @csrf
            <input type="hidden" name="action_type" value="add_to_cart">
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl transition shadow">
                🛒 Add to Cart
            </button>
        </form>

        <form action="{{ route('products.action', $product->id) }}" method="POST">
            @csrf
            <input type="hidden" name="action_type" value="buy_now">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition shadow">
                ⚡ Buy Now
            </button>
        </form>
    </div>

    <a href="{{ route('products.index') }}" class="inline-block text-sm font-semibold text-gray-600 hover:text-gray-900 underline">
        ← Back to Products
    </a>

</div>

</body>
</html>