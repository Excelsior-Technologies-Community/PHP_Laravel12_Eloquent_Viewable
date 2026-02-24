<!DOCTYPE html>
<html>
<head>
<title>Product Details</title>

<style>

body{
    font-family: Arial, sans-serif;
    background:#eef2f7;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0;
}

/* CARD */
.card{
    width:420px;
    background:white;
    padding:35px;
    border-radius:12px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
    text-align:center;
}

h2{
    margin-bottom:10px;
}

.price{
    font-size:20px;
    margin-bottom:15px;
}

.views{
    font-size:22px;
    font-weight:bold;
    color:#16a34a;
    margin:20px 0;
}

/* BUTTON */
.back{
    display:inline-block;
    margin-top:15px;
    padding:10px 18px;
    background:#2563eb;
    color:white;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
}

.back:hover{
    background:#1d4ed8;
}

</style>
</head>

<body>

<div class="card">

<h2>{{ $product->name }}</h2>

<p class="price">Price: ₹{{ $product->price }}</p>

<div class="views">
    👁 {{ views($product)->count() }} Views
</div>

<a class="back" href="{{ route('products.index') }}">
    ← Back to Products
</a>

</div>

</body>
</html>