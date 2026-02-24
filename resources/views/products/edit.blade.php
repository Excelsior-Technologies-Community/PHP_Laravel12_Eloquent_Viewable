<!DOCTYPE html>
<html>
<head>
<title>Edit Product</title>

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

.form-box{
    width:420px;
    background:white;
    padding:35px;
    border-radius:12px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

h2{
    text-align:center;
    margin-bottom:25px;
}

label{
    font-weight:600;
    display:block;
    margin-bottom:6px;
}

input{
    width:100%;
    padding:12px;
    border:1px solid #d1d5db;
    border-radius:8px;
    margin-bottom:18px;
    font-size:15px;
    transition:.3s;
}

input:focus{
    outline:none;
    border-color:#f59e0b;
    box-shadow:0 0 0 2px rgba(245,158,11,.2);
}

button{
    width:100%;
    background:#f59e0b;
    color:white;
    padding:12px;
    border:none;
    border-radius:8px;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:#d97706;
}

</style>
</head>

<body>

<div class="form-box">

<h2>Edit Product</h2>

<form action="{{ route('products.update',$product->id) }}" method="POST">
@csrf
@method('PUT')

<label>Name</label>
<input type="text" name="name" value="{{ $product->name }}">

<label>Price</label>
<input type="number" name="price" value="{{ $product->price }}">

<button type="submit">Update Product</button>

</form>

</div>

</body>
</html>