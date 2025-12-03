<!DOCTYPE html>
<html>
<head>
    <title>Import Shops</title>
</head>
<body>

<h2>Upload Shop Excel File</h2>

@if(session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

<form action="{{ route('shops.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="excel_file" required />
    <br><br>
    <button type="submit">Import & Update</button>
</form>

</body>
</html>
