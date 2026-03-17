@if ($errors->any())
<div>
    @foreach ($errors->all() as $err)

    <p style="color:red">{{ $err }}</p>
    
    @endforeach
</div>
@endif

<form action="/product/store" method="POST">
@csrf

<label>Name</label>
<input type="text" name="name">

<label>Price</label>
<input type="number" name="price">

<label>Description</label>
<textarea name="description"></textarea>

<label>Category</label>
<input type="text" name="category">

<button type="submit">Add Product</button>

</form>