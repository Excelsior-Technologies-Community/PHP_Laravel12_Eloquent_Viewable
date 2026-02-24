<!DOCTYPE html>
<html>
<head>
<title>Products</title>

<style>

body{
    font-family: Arial, sans-serif;
    background:#eef2f7;
    margin:0;
    padding:40px;
}

/* CONTAINER */
.container{
    max-width:900px;
    margin:auto;
    background:white;
    padding:30px;
    border-radius:12px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

h2{
    text-align:center;
    margin-bottom:25px;
}

/* ADD BUTTON */
.add-btn{
    background:#22c55e;
    color:white;
    padding:10px 16px;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
}

.add-btn:hover{
    background:#16a34a;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

th{
    background:#111827;
    color:white;
    padding:12px;
}

td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #eee;
}

tr:hover{
    background:#f9fafb;
}

/* BUTTONS */
.btn{
    padding:6px 12px;
    border-radius:6px;
    color:white;
    text-decoration:none;
    font-size:14px;
    border:none;
    cursor:pointer;
}

.view{ background:#2563eb; }
.edit{ background:#f59e0b; }
.delete{ background:#dc2626; }

.view:hover{ background:#1d4ed8; }
.edit:hover{ background:#d97706; }
.delete:hover{ background:#b91c1c; }

.views{
    font-weight:bold;
    color:#16a34a;
}

</style>
</head>

<body>

<div class="container">

<h2>Product List</h2>

<a href="{{ route('products.create') }}" class="add-btn">+ Add Product</a>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Price</th>
    <th>Views</th>
    <th>Actions</th>
</tr>

@foreach($products as $product)
<tr>
    <td>{{ $product->id }}</td>
    <td>{{ $product->name }}</td>
    <td>₹{{ $product->price }}</td>

    <td class="views">
        👁 {{ views($product)->count() }}
    </td>

    <td>
        <a class="btn view"
           href="{{ route('products.show',$product->id) }}">View</a>

        <a class="btn edit"
           href="{{ route('products.edit',$product->id) }}">Edit</a>

        <form action="{{ route('products.destroy',$product->id) }}"
              method="POST"
              style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn delete">Delete</button>
        </form>
    </td>
</tr>
@endforeach

</table>

</div>

</body>
</html>