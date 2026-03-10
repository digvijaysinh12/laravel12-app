<h2>Products</h2>

<a href="{{ route('products.create') }}">Add Product</a>

<table border="1" cellpadding="10">
<tr>
<th>ID</th>
<th>Name</th>
<th>Price</th>
<th>Actions</th>
</tr>

@foreach($products as $product)

<tr>
<td>{{ $product->id }}</td>
<td>{{ $product->name }}</td>
<td>{{ $product->price }}</td>

<td>

<a href="{{ route('products.show',$product->id) }}">View</a>

<a href="{{ route('products.edit',$product->id) }}">Edit</a>

<form action="{{ route('products.destroy',$product->id) }}" method="POST" style="display:inline">

@csrf
@method('DELETE')

<button type="submit">Delete</button>

</form>

</td>

</tr>

@endforeach

</table>