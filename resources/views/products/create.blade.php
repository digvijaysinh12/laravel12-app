<h2>Create Product</h2>

<form method="POST" action="{{ route('products.store') }}">

@csrf

<input type="text" name="name" placeholder="Product Name">

<br><br>

<input type="number" name="price" placeholder="Price">

<br><br>

<button type="submit">Save</button>

</form>