@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp
@extends('layouts.master')
@section('title', 'Add User')
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">ADD NEW USER</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('users.store') }}" id="subm" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter Full Name" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control reset" placeholder="Enter Email" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="*********" />
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>User Code</label>
                                        <input type="text" readonly value="{{$user_code}}" name="user_code" class="form-control" placeholder="User Code" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Type</label>
                                        <select onchange="get_distributor()" name="user_type" id="user_type"
                                            class="form-control">
                                            @foreach (MasterFormsHelper::userType() as $key => $userType)
                                                <option value="{{ $userType->id }}">{{ $userType->type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Role</label>
                                        <select class="form-control" name="role" id="role">
                                        <option value="">select</option>
                                        @foreach ( $master->get_all_role() as $key =>$row )
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>

                                <hr>

                                <div style="display: none" class="col-md-12 form-check sperater2">
                                    <strong>Permissions</strong>
                                    <div class="row padl">
                                        @foreach ($master->getAllPermissionList() as $mainModule)
                                            {{-- @dd($mainModule); --}}
                                            <div class="col-md-12">
                                                <input class="form-check-input" type="checkbox"
                                                    id="{{ $mainModule['main_module'] }}" onclick="checkboxChecked(this)">
                                                <strong>{{ $mainModule['main_module'] }}</strong>


                                            </div>
                                            @foreach ($mainModule['permissions'] as $id => $permission)
                                                {{-- @dd($id); --}}
                                                    <div class="col-md-3 form-check padtbh">
                                                        <input class="form-check-input {{ $mainModule['main_module'] }}"
                                                            value="{{ $id }}" type="checkbox"
                                                            id="permissions{{ $id }}" name="permissions[]">
                                                        <label class="form-check-label"
                                                            for="permissions{{ $id }}">{{ $permission }}</label>
                                                    </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-md-12 form-check ">
                                    {{-- <strong>Distributor</strong> --}}
                                    {{-- <div class="row">
                                        @foreach ($master->get_all_distributors() as $key => $row)
                                            <div class="col-md-3 form-check">
                                                <div class="form-check padtbh">
                                                    <input class="form-check-input" value="{{ $row->id }}" type="checkbox" id="distributor{{ $row->id }}" name="distributor[]">
                                                    <label class="form-check-label" for="distributor{{ $row->id }}"> {{ $row->distributor_name }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div> --}}
                                    <div class="form-group">
                                        <label>Distributor</label>
                                        {{-- <input type="password" name="password" class="form-control" placeholder="*********" /> --}}
                                        <select name="distributor[]" class="form-control" id="distributor" multiple>
                                            <option value="select_all">Select All</option> <!-- Select All Option -->
                                            @foreach ($master->get_all_distributors() as $key => $row)
                                                <option value="{{ $row->id }}"> {{ $row->distributor_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {{-- <div class="col-md-12 seprator">
                                    <hr>
                                </div> --}}
                                <div class="col-md-12">
                                    <div class="main_head">
                                        <h2>Additional Detail</h2>
                                    </div>
                                </div>
                                <div class="col-md-12 seprator">
                                    <hr>
                                </div>

                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Employee ID</label>
                                        <input type="text" name="employee_id" class="form-control" placeholder="Employee ID">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Company Name</label>
                                        <input type="text" name="company_name" class="form-control" placeholder="Enter Company Name" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone_number" class="form-control" placeholder="Enter Phone Number" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Alt Phone Number</label>
                                        <input type="text" name="alt_number" class="form-control" placeholder="Enter Alt Number" />
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>CNIC</label>
                                        <input type="text" name="cnic" class="form-control" placeholder="Enter CNIC" />
                                    </div>
                                </div>



                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" name="address" class="form-control" placeholder="Enter Address" />
                                    </div>
                                </div>



                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>City</label>
                                        {{-- <input type="text" name="city" class="form-control" placeholder="Enter CNIC" /> --}}
                                        <select name="city" class="form-control">
                                            <option value="">Select Option</option>
                                            @foreach($cities as $city)
                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>State<strong>*</strong></label>
                                        <input type="text" name="state" class="form-control" placeholder="Sindh">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Country</label>
                                        <input type="text" name="country" class="form-control" placeholder="Pakistan">
                                    </div>
                                </div>



                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Manager</label>
                                        <select name="manager" class="select2 form-control form-control-lg">
                                            <option value="">Select Manager</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select name="department_id" class="select2 form-control form-control-lg">
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Designation</label>
                                        <select name="designation_id" class="select2 form-control form-control-lg">
                                            @foreach ($designations as $designation)
                                                <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Geography</label>
                                        <select name="geography_id" class="select2 form-control form-control-lg">
                                            <option value="1">Karachi</option>
                                            <option value="2">Islamabad</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Date Of Join</label>
                                        <input type="date" class="form-control"  name="date_of_join">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Date of Leaving</label>
                                        <input type="date" class="form-control"  name="date_of_leaving">
                                    </div>
                                </div>

                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Image</label>
                                        <input type="file" class="form-control"  name="image">
                                    </div>
                                </div>

                                <div class="col-12 text-right">
                                    <button type="submit" class="btn btn-primary mr-1">Create User</button>
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Floating Label Form section end -->

@endsection

@section('script')

    <script>
        function checkboxChecked(id) {
            // alert(id.id);
            // $('#select-all').click(function(event) {
                if (id.checked) {
                    // Iterate each checkbox
                    $('.'+id.id).each(function() {
                        this.checked = true;
                    });
                } else {
                    $('.'+id.id).each(function() {
                        this.checked = false;
                    });
                }
            // });
        }

        function get_distributor() {
            var user_type = $('#user_type').val();
            if (user_type != 5) {
                return;
            }

            $.ajax({
                type: "get",
                url: '{{ url('distributor/get_distributor') }}',
                data: {
                    user_type: user_type
                },
                async: true,
                cache: false,
                success: function(data) {
                    $('#data').html(data);

                }
            });
        }

        $(document).ready(function() {
            $('#distributor').select2({
                closeOnSelect: false,
                placeholder: "Select distributor", // Add a placeholder if needed
                allowClear: true
            });

            $('#user_category').select2();

            select_all();
        });

        // $('#city').on('change' , function(){
        //     getStoreByCity();
        //     $('#distributor').prepend('<option value="select_all">Select All</option>');
        // });

        function select_all()
        {
            // Implement Select All functionality
            $('#distributor').on('select2:select', function(e) {
                if (e.params.data.id === 'select_all') {
                    // Select all options except the 'Select All' option
                    $('#distributor').find('option').not('[value="select_all"]').prop('selected', true);
                    $('#distributor').trigger('change'); // Update the selection
                }
            });

            // Implement Deselect All functionality when 'Select All' is unselected
            $('#distributor').on('select2:unselect', function(e) {
                if (e.params.data.id === 'select_all') {
                    // Deselect all options except the 'Select All' option
                    $('#distributor').find('option').prop('selected', false);
                    $('#distributor').trigger('change'); // Update the selection
                }
            });
        }
   </script>
@endsection
