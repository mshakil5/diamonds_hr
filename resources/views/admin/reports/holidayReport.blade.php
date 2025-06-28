@extends('admin.layouts.admin')

@section('content')



<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                {{-- <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button> --}}
            </div>
        </div>
    </div>
</section>

<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
         <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="header-title">Holiday report</h3>
                    </div>
                    <div class="card-body">
                        <div class="errmsg"></div>
                        <form action="{{ route('holidayReport.search')}}" method="POST">
                            @csrf
                            
                            <div class="row">
                                
                                <div class="col-sm-9">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>Employee *</label>
                                        <select class="form-control select2" id="employee_id" name="employee_id">
                                            <option value="">Select Employee</option>
                                            @foreach ($employees as $employee)
                                            <option value="{{$employee->id}}">{{$employee->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                
                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>Action</label> <br>
                                        <button type="submit" id="searchBtn" class="btn btn-secondary">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                                
                                
                                

                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@if ($employeeName)
 <section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Name: {{$employeeName->name ?? ''}}</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Staff ID</th>
                                    <th>Name </th>
                                    <th>Holiday Ent</th>
                                    <th>Holiday Start date </th>
                                    <th>Duration (Days) </th>
                                    <th> Status </th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td>1</td>
                                    <td>{{ $employeeName->id }}</td>
                                    <td>{{ $employeeName->name }}</td>
                                    <td>Not clear</td>
                                    <td>{{ $employeeName->entitled_holiday + $holidayDataCount }}</td>
                                    <td>Not clear</td>
                                    <td>1</td>
                                </tr>


                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>   
@endif


@endsection

@section('script')


<script>
 
    $(document).ready(function() {
        

        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });


    });
</script>



@endsection