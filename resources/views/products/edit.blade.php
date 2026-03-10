<h2>Edit Product</h2>

<form method="POST" action="{{ route('products.update',$product->id) }}">

@csrf
@method('PUT')

<input type="text" name="name" value="{{ $product->name }}">

<br><br>

<input type="number" name="price" value="{{ $product->price }}">

<br><br>

<button type="submit">Update</button>

</form>