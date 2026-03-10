<h2>Test Form</h2>

<form method="POST" action="/submit-form">
    
    @csrf

    <input type="text" name="name" placeholder="Enter Name">

    <br><br>

    <button type="submit">Submit</button>

</form>