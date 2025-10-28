{{-- @extends('layouts.master')
@section('title', "Product")
@section('content')






@endsection --}}


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" type="text/css" href="{{ url('/public/assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{url('/public/assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/public/assets/css/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/public/assets/css/components.css')}}">

    <link rel="stylesheet" type="text/css" href="{{url('/public/assets/css/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/public/assets/css/themes/bordered-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/public/assets/css/themes/semi-dark-layout.css')}}">

</head>
<body>

    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Product  List</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Product Name</th>
                            <th>Price</th>
                        </tr>
                        </thead>
                        <tbody id="data">
                            @foreach ($products as $key => $row)
                                <tr class="text-center">
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $row->product_name ?? '' }}</td>
                                    <td>{{ $row->sales_price ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
