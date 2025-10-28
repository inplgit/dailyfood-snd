@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
    $distributors = $master->get_users_distributors($user->id)->toArray();

@endphp
@extends('layouts.master')
@section('title', 'Edit User')
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Users </h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('users.update', $user->id) }}" id="subm" class="form">
                            @method('PUT')
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ $user->name }}" placeholder="Enter Full Name" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ $user->email }}" placeholder="Enter Email" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="password" class="form-control"
                                            placeholder="*********" />
                                    </div>
                                </div>




                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>User Code</label>
                                        <input type="text" readonly value="{{$user_data->user_code ?? $user_code}}" name="user_code" class="form-control" placeholder="User Code" />
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Type</label>
                                        <select name="user_type" id="user_type" class="form-control">
                                            @foreach (MasterFormsHelper::userType() as $key => $userType)
                                                <option value="{{ $userType->id }}"
                                                    {{ $userType->id == $user->user_type ? 'selected' : '' }}>
                                                    {{ $userType->type }}</option>
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
                                        <option @if($role == $row->id) selected @endif  value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                       </select>
                                    </div>
                                </div>


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
                                                <div class="col-md-3">
                                                    <div class="form-check padtbh">
                                                        <input class="form-check-input {{ $mainModule['main_module'] }}"
                                                            value="{{ $id }}" type="checkbox" {{($user->hasPermissionTo($id))? 'checked' : ''}}
                                                            id="permissions{{ $id }}" name="permissions[]">
                                                        <label class="form-check-label"
                                                            for="permissions{{ $id }}">{{ $permission }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                                <hr>

                                <div class="col-md-12 sperater">
                                    {{-- <strong>Distributor</strong>
                                    <div class="row">
                                        @foreach ($master->get_all_distributors() as $key => $row)
                                            <div class="col-md-3 form-check padtbh">
                                                <input @if (in_array($row->id, $distributors)) checked @endif
                                                    class="form-check-input" value="{{ $row->id }}" type="checkbox"
                                                    id="distributor{{ $row->id }}" name="distributor[]">
                                                <label class="form-check-label"
                                                    for="distributor{{ $row->id }}">{{ $row->distributor_name }}</label>
                                            </div>
                                        @endforeach
                                    </div> --}}

                                    <div class="form-group">
                                        <label>Distributor</label>
                                        <select name="distributor[]" class="form-control" id="distributor" multiple>
                                            <option value="select_all">Select All</option> <!-- Select All Option -->
                                            @foreach ($master->get_all_distributors() as $key => $row)
                                                <option value="{{ $row->id }}" {{in_array($row->id, $distributors) ? 'selected' : ''}}> {{ $row->distributor_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
'


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
                                        <input type="text" name="employee_id" class="form-control" value="{{$user_data->employee_id ?? ''}}" placeholder="Employee ID">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Company Name</label>
                                        <input type="text" name="company_name" class="form-control" placeholder="Enter Company Name" value="{{$user_data->company_name ?? ''}}"/>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone_number" class="form-control" placeholder="Enter Phone Number" value="{{$user_data->phone_number  ??''}}"/>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Alt Phone Number</label>
                                        <input type="text" name="alt_number" class="form-control" placeholder="Enter Alt Number" value="{{$user_data->alt_number?? ''}}"/>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>CNIC</label>
                                        <input type="text" name="cnic" class="form-control" placeholder="Enter CNIC" value="{{$user_data->cnic??''}}"/>
                                    </div>
                                </div>



                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" name="address" class="form-control" placeholder="Enter Address" value="{{$user_data->address??''}}"/>
                                    </div>
                                </div>



                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>City</label>
                                        {{-- <input type="text" name="city" class="form-control" placeholder="Enter CNIC" /> --}}
                                        <select name="city" class="form-control">
                                            <option value="">Select Option</option>
                                            @foreach($cities as $city)
                                            <option value="{{$city->id}}" {{isset($user_data->city) && $user_data->city == $city->id ? 'selected' : ''}} >{{$city->name}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>State<strong>*</strong></label>
                                        <input type="text" name="state" class="form-control" placeholder="Sindh" value="{{$user_data->state ?? ''}}">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Country</label>
                                        <input type="text" name="country" class="form-control" placeholder="Pakistan" value="{{$user_data->country ??''}}">
                                    </div>
                                </div>



                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Manager</label>
                                        <select name="manager" class="select2 form-control form-control-lg">
                                            <option value="">Select Manager</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" {{isset($user_data->manager) && $user_data->manager == $user->id ? 'selected' : ''}}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select name="department_id" class="select2 form-control form-control-lg">
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}" {{ isset($user_data->department_id) && $user_data->department_id == $department->id ? 'selected' : ''}}>{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Designation</label>
                                        <select name="designation_id" class="select2 form-control form-control-lg">
                                            @foreach ($designations as $designation)
                                                <option value="{{ $designation->id }}" {{ isset($user_data->designation_id) && $user_data->designation_id == $designation->id ? 'selected' : ''}}>{{ $designation->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Geography</label>
                                        <select name="geography_id" class="select2 form-control form-control-lg">
                                            <option value="1"  {{ isset($user_data->geography_id) && $user_data->geography_id ==  1 ? 'selected' : ''}}>Karachi</option>
                                            <option value="2"  {{ isset($user_data->geography_id) && $user_data->geography_id ==  2 ? 'selected' : ''}}>Islamabad</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Date Of Join</label>
                                        <input type="date" class="form-control" value="{{$user_data->date_of_join ?? ''}}"  name="date_of_join">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Date of Leaving</label>
                                        <input type="date" class="form-control" value="{{$user_data->date_of_leaving ?? ''}}" name="date_of_leaving">
                                    </div>
                                </div>

                                <div class="col-md-3 col-12 mb-1">
                                    <div class="form-group">
                                        <label>Image</label>
                                        <input type="file" class="form-control"  name="image">
                                    </div>
                                </div>

                                '

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mr-1">Update User</button>
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
