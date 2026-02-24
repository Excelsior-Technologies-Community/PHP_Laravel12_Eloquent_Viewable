<!DOCTYPE html>
<html>
<head>
<title>Create Product</title>

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
.form-box{
    width:420px;
    background:#ffffff;
    padding:35px;
    border-radius:12px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

h2{
    margin-bottom:25px;
    text-align:center;
}

/* LABEL */
label{
    font-weight:600;
    display:block;
    margin-bottom:6px;
}

/* INPUT */
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
    border-color:#22c55e;
    box-shadow:0 0 0 2px rgba(34,197,94,.2);
}

/* BUTTON */
button{
    width:100%;
    background:#22c55e;
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
    background:#16a34a;
}

</style>
</head>

<body>

<div class="form-box">

<h2>Create Product</h2>

<form action="{{ route('products.store') }}" method="POST">
@csrf

<label>Name</label>
<input type="text" name="name" placeholder="Enter product name">

<label>Price</label>
<input type="number" name="price" placeholder="Enter price">

<button type="submit">Save Product</button>

</form>

</div>

</body>
</html>