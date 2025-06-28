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
                        <h3 class="card-title" id="header-title">Stock report</h3>
                    </div>
                    <div class="card-body">
                        <div class="errmsg"></div>
                        <form action="{{ route('stockReport.search')}}" method="POST" class="d-none">
                            @csrf
                            
                            <div class="row">
                                
                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <input type="date" class="form-control" id="from_date" name="from_date" value="">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="date" class="form-control" id="to_date" name="to_date" value="">
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




 <section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Stock report</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Initial Stock</th>
                                    <th>Dirty</th>
                                    <th>Bed</th>
                                    <th>Arrived</th>
                                    <th>Lost/Missed</th>
                                    <th>Marks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->initial_stock ?? 0 }}</td>
                                        <td>{{ $product->dirty ?? 0 }}</td>
                                        <td>{{ $product->bed ?? 0 }}</td>
                                        <td>{{ $product->arrived ?? 0 }}</td>
                                        <td>{{ $product->lost ?? 0 }}</td>
                                        <td>{{ $product->marks ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>   



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