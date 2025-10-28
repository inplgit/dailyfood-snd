
@extends('layouts.master')
@section('title', 'Attendence')
@section('content')


    <form id="list_data" method="get" action="{{ route('attendenceList') }}"></form>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Attendence List</h4>
                   
                </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-3">Top 4 TSO

                        <label for="">From</label>
                        <input type="date" value="{{date('Y-m-d')}}" class="form-control" name="from" id="from">
                    </div>
                    <div class="col-3">
                        <label for="">To</label>
                        <input type="date" value="{{date('Y-m-d')}}" class="form-control" name="to" id="to">
                    </div>
                    <div class="col-3">
                        <label for="">&nbsp;</label>
                        <input type="button" onclick="get_data()" class="btn btn-primary" value="Search">
                    </div>
                </div>
            </div>
            <br>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="data">
                          
                        </tbody>
                    </table>
                </div>
               
            </div>
        </div>
    </div>
    <!-- Basic Floating Label Form section end -->



@endsection
@section('script')
<script>
    $(document).ready(function(){
        get_data();
    })

    function get_data()
    {
       var from =  $('#from').val();
       var to = $('#to').val();
        $.ajax({
            type: "get",
            url: '{{ route('attendenceList') }}',
            data: {
                from: from,to:to
            },
            success: function(data) {
              
                $('#data').empty(data);
               $('#data').append(data);
            }
        });
    }
    </script>
@endsection
