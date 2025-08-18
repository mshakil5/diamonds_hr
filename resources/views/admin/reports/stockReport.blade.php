@extends('admin.layouts.admin')

@section('content')

<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="header-title">Stock Report</h3>
                    </div>
                    <div class="card-body">
                        <div class="errmsg"></div>
                        <form action="{{ route('stockReport.search') }}" method="POST" class="">
                            @csrf
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <input type="date" class="form-control" id="from_date" name="from_date" value="{{ old('from_date') }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="date" class="form-control" id="to_date" name="to_date" value="{{ old('to_date') }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
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
                    <!-- Report Header for PDF -->
                    <div id="report-header" style="text-align: center; margin-bottom: 20px;">
                        <h2>Stock Report</h2>
                        <p>Date: <span id="date-range">{{ request()->from_date ? (request()->from_date . ' to ' . request()->to_date) : 'All Dates' }}</span></p>
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
                                    <th>Missed</th>
                                    <th>Rejected</th>
                                    <th>Shelve Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{ $product->name }} - {{ $product->id }}</td>
                                        <td>{{ $product->initial_stock ?? 0 }}</td>
                                        <td>{{ $product->dirty ?? 0 }}</td>
                                        <td>{{ $product->bed ?? 0 }}</td>
                                        <td>{{ $product->arrived ?? 0 }}</td>
                                        <td>{{ $product->lost ?? 0 }}</td>
                                        <td>{{ $product->marks ?? 0 }}</td>
                                        <td>{{ ($product->initial_stock + $product->arrived - $product->dirty - $product->lost - $product->bed - $product->marks) ?? 0 }}</td>
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
            "pageLength": 30,
            "buttons": [
                "copy", 
                "csv", 
                "excel", 
                {
                    extend: 'pdf',
                    title: '', // Remove default title
                    messageTop: function() {
                        // Get the header content and strip HTML tags
                        let header = $('#report-header').html();
                        // Return as an object for DataTables PDF export with center alignment
                        return {
                            text: header.replace(/<[^>]+>/g, ''),
                            alignment: 'center',
                            margin: [0, 0, 0, 10]
                        };
                    },
                    customize: function(doc) {
                        // Ensure messageTop is properly formatted
                        if (doc.content[1] && typeof doc.content[1].text === 'string') {
                            doc.content[1].text = doc.content[1].text.trim();
                            doc.content[1].alignment = 'center';
                            doc.content[1].margin = [0, 0, 0, 10];
                        }
                        // Add spacing after header
                        doc.content.splice(2, 0, {
                            text: '\n',
                            alignment: 'center'
                        });
                    }
                }, 
                "print"
            ]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
});
</script>

@endsection