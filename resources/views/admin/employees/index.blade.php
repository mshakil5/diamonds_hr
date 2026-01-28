@extends('admin.layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/css/tempusdominus-bootstrap-4.min.css" />

<style>
    #payslipContent table {
        width: 100%;
        margin-bottom: 20px;
    }
    #payslipContent th, #payslipContent td {
        padding: 10px;
        text-align: left;
    }
    #payslipContent .text-center {
        text-align: center;
    }
    #payslipContent .text-right {
        text-align: right;
    }
    #payslipContent .font-weight-bold {
        font-weight: bold;
    }
    #payslipContent .payslip-input {
        width: 100px;
    }
    @media print {
        #payslipContent {
            font-size: 12px;
        }
        #payslipContent .payslip-input {
            border: none;
            background: transparent;
        }
    }
</style>


<style>
    .customModal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(16, 17, 17, 0.709804);
        z-index: 999999;
        display: none;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        -webkit-box-align: start;
        -ms-flex-align: start;
        align-items: flex-start;
        padding-top: 3%;
    }
    .customModal.show {
        display: flex;
    }
    .customModal .inner {
        border-radius: 3px;
        border-bottom: 5px solid #041e1f70;
        width: 60%;
        background: #fff;
        max-height: 550px;
        overflow-y: auto;
        overflow-x: hidden;
        margin: auto; /* Center horizontally */
        position: relative;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        justify-content: center; /* Center vertically */
        align-items: center;
    }
    .customModal .inner::-webkit-scrollbar {
        background-color: #f5f5f5;
        width: 5px;
    }
    .customModal .inner::-webkit-scrollbar-thumb {
        border-radius: 50px;
        background: -webkit-gradient(linear, left top, right top, from(#fff), to(#e4e4e4));
        background: linear-gradient(to right, #fff, #e4e4e4);
        border: 1px solid #ccc;
    }
    @media (max-width: 768px) {
        .customModal .inner {
            top: 4%;
            width: 90%;
            height: auto;
            position: relative;
        }
    }
    .customModal .inner .header {
        padding: 4px 15px;
        background: #40596E;
        font-size: 14px;
        color: #ededed;
        letter-spacing: 1px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
    }
    .customModal .inner .header .times {
        color: #fff;
        font-size: 16px;
    }
    .customModal .inner .header .times:hover {
        color: red;
        cursor: pointer;
    }
    .reAdjust {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        padding: 15px;
    }
    .reAdjust > div {
        flex: 1;
        min-width: 200px;
    }
    .verGap {
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }
    .paySlip {
        padding: 15px;
    }
    .manipulate {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .manipulate th, .manipulate td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .manipulate th {
        background-color: #f2f2f2;
    }
    .slip-title {
        font-weight: bold;
        margin-bottom: 10px;
    }
    .netPay {
        margin-top: 10px;
        font-weight: bold;
    }
    .payslip-input {
        width: 100px;
    }
    @media print {
        .customModal {
            display: block;
            background: none;
        }
        .customModal .inner {
            width: 100%;
            max-height: none;
            border: none;
        }
        .customModal .inner .header {
            display: none;
        }
        .payslip-input {
            border: none;
            background: transparent;
        }
        .reAdjust {
            display: none;
        }
    }
</style>

@if (auth()->user()->canDo(8))
<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
@endif

<!-- Main content -->
<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <!-- right column -->
            <div class="col-md-12">
                <!-- general form elements disabled -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="header-title">Add new data</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form id="createThisForm">
                            <div class="card mb-2">
                                <div class="card-header">
                                    <h3 class="card-title"> Basic Staff Information</h3>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-body">
                                    <div class="errmsg"></div>
                                    <input type="hidden" id="codeid">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Staff ID <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="employee_id" name="employee_id">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="name" name="name">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Staff Type <span class="text-danger">*</span></label>
                                                <select class="form-control" id="employee_type" name="employee_type">
                                                    <option value="">Select Staff Type</option>
                                                    <option value="full time">Full Time</option>
                                                    <option value="part time">Part Time</option>
                                                    <option value="casual">Casual</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="number" class="form-control" id="phone" name="phone">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Em. Contact Number</label>
                                                <input type="number" class="form-control" id="emergency_contact_number" name="emergency_contact_number">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Em. Contact Person</label>
                                                <input type="text" class="form-control" id="emergency_contact_person" name="emergency_contact_person">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Nation Insurance No</label>
                                                <input type="text" class="form-control" id="ni" name="ni">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Nationality</label>
                                                <input type="text" class="form-control" id="nationality" name="nationality">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Join Date</label>
                                                <div class="input-group date" id="reservationdatetime" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input" data-target="#reservationdatetime" id="join_date" name="join_date" />
                                                    <div class="input-group-append" data-target="#reservationdatetime" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Email <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="email" name="email">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Select Role</label>
                                                <select name="role_id" id="role_id" class="form-control">
                                                    <option value="">Select Role</option>
                                                    @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Image</label>
                                                <input type="file" class="form-control" id="image" name="image">
                                                <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Address</label>
                                                <textarea class="form-control" name="address" id="address" cols="30" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>

                            <div class="card mb-2">
                                <div class="card-header">
                                    <h3 class="card-title"> Payment and holiday info</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Pay Rate <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="pay_rate" name="pay_rate">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Tax Code <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="tax_code" name="tax_code">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Entitled Holiday <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="entitled_holiday" name="entitled_holiday">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Bank Details</label>
                                                <textarea class="form-control" name="bank_details" id="bank_details" cols="30" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>

                            <div class="card mb-2">
                                <div class="card-header">
                                    <h3 class="card-title"> User Login</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Branch <span class="text-danger">*</span></label>
                                                <select name="branch_id" id="branch_id" class="form-control">
                                                    <option value="">Select</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Username <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="username" name="username">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="password" name="password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </form>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                    </div>
                    <!-- /.card-footer -->
                </div>
            </div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Data</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="branchFilter">Filter by Branch:</label>
                                <select id="branchFilter" class="form-control">
                                    <option value="">All Branches</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->name }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Type</th>
                                    <th>Email/Phone</th>
                                    <th>Image</th>
                                    <th>Branch</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($query as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d-m-Y') }}</td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ $data->username }}</td>
                                    <td>{{ $data->employee_type }}</td>
                                    <td>{{ $data->user->email ?? '' }} <br> {{ $data->phone ?? '' }}</td>
                                    <td>
                                        @if ($data->user->photo != null)
                                        <a href="{{ asset('public'.$data->user->photo) }}" target="_blank">
                                            <img src="{{ asset('public'.$data->user->photo) }}" alt="" style="max-width: 100px; width: 100%; height: auto;">
                                        </a>
                                        @endif
                                    </td>
                                    <td>{{ $data->branch->name ?? '' }}</td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus{{ $data->id }}" data-id="{{ $data->id }}" {{ $data->is_active == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitchStatus{{ $data->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a id="DetailsBtn"
                                            rid="{{ $data->id }}"
                                            title="Details"
                                            data-name="{{ $data->name }}"
                                            data-username="{{ $data->username }}"
                                            data-user_id="{{ $data->user_id }}"
                                            data-branch_id="{{ $data->branch_id }}"
                                            data-join_date="{{ $data->join_date }}"
                                            data-employee_id="{{ $data->employee_id }}"
                                            data-email="{{ $data->user->email }}"
                                            data-phone="{{ $data->phone }}"
                                            data-emergency_contact_number="{{ $data->emergency_contact_number }}"
                                            data-emergency_contact_person="{{ $data->emergency_contact_person }}"
                                            data-ni="{{ $data->ni }}"
                                            data-tax_code="{{ $data->tax_code }}"
                                            data-nationality="{{ $data->nationality }}"
                                            data-bank_details="{{ $data->bank_details }}"
                                            data-entitled_holiday="{{ $data->entitled_holiday }}"
                                            data-address="{{ $data->address }}"
                                            data-employee_type="{{ $data->employee_type }}"
                                            data-pay_rate="{{ $data->pay_rate }}"
                                            data-branch_id="{{ $data->branch_id }}"
                                        >
                                            <i class="fa fa-info-circle" style="color: #17a2b8; font-size:16px; margin-right:8px;"></i>
                                        </a>
                                        @if (auth()->user()->canDo(9))
                                        <a id="EditBtn" rid="{{ $data->id }}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px; margin-right:8px;"></i></a>
                                        @endif
                                        @if (auth()->user()->canDo(10))
                                        <a id="deleteBtn" rid="{{ $data->id }}"><i class="fa fa-trash-o" style="color: red;font-size:16px; margin-right:8px;"></i></a>
                                        @endif
                                        <a id="PayslipBtn" class="d-none" rid="{{ $data->id }}" title="Payslip"><i class="fa fa-file-text" style="color: #28a745; font-size:16px;"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- Payslip Modal -->
<div class="customModal" id="payslipModal" style="display: none;">
    <div class="inner">
        <div class="header">
            <div>Pay Report</div>
            <div class="times" data-dismiss="modal">×</div>
        </div>
        <div class="body">
            <div class="slipHead">
                <form id="payslipForm">
                    <div class="reAdjust">
                        <div>
                            <label class="d-block">From Date</label>
                            <div class="input-group date" id="fromDatePicker" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" data-target="#fromDatePicker" id="from_date" name="from_date" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" required />
                                <div class="input-group-append" data-target="#fromDatePicker" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <div class="help is-danger" id="from_date_error" style="display: none;"></div>
                        </div>
                        <div>
                            <label class="d-block">To Date</label>
                            <div class="input-group date" id="toDatePicker" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" data-target="#toDatePicker" id="to_date" name="to_date" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" required />
                                <div class="input-group-append" data-target="#toDatePicker" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <div class="help is-danger" id="to_date_error" style="display: none;"></div>
                        </div>
                        <div class="verGap">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <button type="button" class="btn btn-secondary" id="printPayslip">Print</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="paySlip" id="payslipContent" style="display: none;">
                <div class="infoPrint text-center">
                    <h3>Diamonds Guest House</h3>
                    <h5>Staff Pay Report</h5>
                </div>
                <div class="row_first">
                    <table class="manipulate">
                        <thead>
                            <tr>
                                <th>Staff Id</th>
                                <th>Staff Name</th>
                                <th>Process Date</th>
                                <th>N.I Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td id="paySlipEmployeeID"></td>
                                <td id="paySlipEmployeeName"></td>
                                <td id="process_date"></td>
                                <td id="paySlipEmployeeni"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row_second">
                    <div>
                        <table class="manipulate">
                            <thead>
                                <tr>
                                    <th>Details</th>
                                    <th>Hours</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Working details</td>
                                    <td id="total_hours"></td>
                                    <td id="paySlippay_rate"></td>
                                    <td id="total_amount"></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>Holidays: <span id="paySlipentitled_holiday"></span></td>
                                    <td>Taken: <span id="holiday_taken"></span></td>
                                    <td>Remaining: <span id="holiday_remaining"></span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="row_third">
                    <div>
                        <p class="font-weight-bold">
                            Diamonds Guest House<br>
                            York, Yorkshire, UK
                        </p>
                    </div>
                    <div>
                        <p class="slip-title">Diamonds Guest House</p>
                        <table class="manipulate">
                            <tr>
                                <td><span>Hourly Based Amount</span></td>
                                <td id="hourly_amount"></td>
                            </tr>
                            <tr>
                                <td><span>Overtime</span></td>
                                <td><input type="number" class="form-control payslip-input" id="overtime" name="overtime" step="any" value="0" /></td>
                            </tr>
                            <tr>
                                <td><span>Maternity/Paternity</span></td>
                                <td><input type="number" class="form-control payslip-input" id="maternity" name="maternity" step="any" value="0" /></td>
                            </tr>
                            <tr>
                                <td><span>Sick Pay</span></td>
                                <td><input type="number" class="form-control payslip-input" id="sickPay" name="sickPay" step="any" value="0" /></td>
                            </tr>
                            <tr>
                                <td><span>Holiday Pay</span></td>
                                <td><input type="number" class="form-control payslip-input" id="holidayPay" name="holidayPay" step="any" value="0" /></td>
                            </tr>
                            <tr>
                                <td><span>Bonus</span></td>
                                <td><input type="number" class="form-control payslip-input" id="bonus" name="bonus" step="any" value="0" /></td>
                            </tr>
                            <tr>
                                <td><span>Other</span></td>
                                <td><input type="number" class="form-control payslip-input" id="other" name="other" step="any" value="0" /></td>
                            </tr>
                            <tr>
                                <td><span>Adjustment (-)</span></td>
                                <td><input type="number" class="form-control payslip-input" id="adjustment" name="adjustment" step="any" value="0" /></td>
                            </tr>
                        </table>
                        <div class="netPay text-right">
                            <span>Gross pay: </span>
                            <span id="gross_pay"></span>
                        </div>
                    </div>
                </div>
                <div class="row_forth">
                    <div>
                        <p class="slip-title">Date and Tax Details</p>
                        <table class="manipulate text-center">
                            <tr class="tax">
                                <td><span>Tax code: <span id="paySliptax_code"></span></span></td>
                                <td>From Date: <span id="from_date_display"></span></td>
                                <td>To Date: <span id="to_date_display"></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>






@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/js/tempusdominus-bootstrap-4.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $("#example1").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "searching": true,
        "pageLength": 20,
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
    });

    // Branch filter logic
    $('#branchFilter').on('change', function() {
        var branch = $(this).val();
        if ($.fn.DataTable.isDataTable('#example1')) {
            if (branch) {
                table.column(7).search(branch).draw();
            } else {
                table.column(7).search('').draw();
            }
        }
    });

    // Initialize datetime pickers
    $('#fromDatePicker').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#toDatePicker').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#reservationdatetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
    });

    // CSRF Token Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    // Add/Edit form logic (unchanged)
    $("#addThisFormContainer").hide();
    $("#newBtn").click(function() {
        clearform();
        $("#newBtn").hide(100);
        $("#addThisFormContainer").show(300);
    });
    $("#FormCloseBtn").click(function() {
        $("#addThisFormContainer").hide(200);
        $("#newBtn").show(100);
        clearform();
    });

    
    var url = "{{ URL::to('/admin/employees') }}";
    var upurl = "{{ URL::to('/admin/employees/update') }}";
    
    $("#addBtn").click(function() {
        const btnMode = $(this).val(); // 'Create' or 'Update'
        const isUpdate = (btnMode === 'Update');
        
        // 1. Configuration: Set URL and Required Fields based on mode
        const ajaxUrl = isUpdate ? "{{ URL::to('/admin/employees/update') }}" : "{{ URL::to('/admin/employees') }}";
        
        const requiredFields = [
            '#employee_id', '#name', '#employee_type', '#pay_rate', 
            '#tax_code', '#entitled_holiday', '#branch_id', '#username'
        ];
        
        // Password is only required during creation
        if (!isUpdate) {
            requiredFields.push('#password');
        }

        // 2. Validation Loop
        for (const field of requiredFields) {
            if ($(field).val()?.trim() === '') {
                showError('Please fill all required fields.');
                $(field).focus(); // Professional touch: focus the empty field
                return;
            }
        }

        // 3. Prepare Data
        const formData = new FormData($('#createThisForm')[0]);
        const featureImgInput = document.getElementById('image');
        
        if (featureImgInput.files && featureImgInput.files[0]) {
            formData.append("photo", featureImgInput.files[0]);
        }

        // Add ID if updating
        if (isUpdate) {
            formData.append("codeid", $("#codeid").val());
        }

        // 4. Single AJAX Call
        $.ajax({
            url: ajaxUrl,
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            beforeSend: function() {
                // Optional: Disable button to prevent double-submissions
                $("#addBtn").prop('disabled', true).text('Processing...');
            },
            success: function(response) {
                pagetop();
                showSuccess(`Data ${isUpdate ? 'updated' : 'created'} successfully.`);
                reloadPage(2000);
            },
            error: function(xhr) {
                $("#addBtn").prop('disabled', false).val(btnMode); // Re-enable on error
                
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    const firstError = Object.values(response.errors)[0][0];
                    showError(firstError);
                } else {
                    showError('An error occurred. Please try again.');
                }
            }
        });
    });

function showSuccess(message) {
    pagetop();
    $('.errmsg').html(`<div class="alert alert-success">${message}</div>`);
}

function showError(message) {
    pagetop();
    $('.errmsg').html(`
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `);
}

    // Edit
    $("#contentContainer").on('click', '#EditBtn', function() {
        codeid = $(this).attr('rid');
        info_url = url + '/' + codeid + '/edit';
        $.get(info_url, {}, function(d) {
            populateForm(d);
            pagetop();
        });
    });

    // Delete
    $("#contentContainer").on('click', '#deleteBtn', function() {
        if (!confirm('Sure?')) return;
        codeid = $(this).attr('rid');
        info_url = url + '/' + codeid;
        $.ajax({
            url: info_url,
            method: "GET",
            type: "DELETE",
            data: {},
            success: function(d) {
                showSuccess('Data deleted successfully.');
                reloadPage(2000);
            },
            error: function(xhr, status, error) {
                showError('An error occurred. Please try again.');
                console.error(error);
            }
        });
    });

    // Payslip Modal Trigger
    $("#contentContainer").on('click', '#PayslipBtn', function() {
        var employeeId = $(this).attr('rid');
        $('#payslipForm').data('employee-id', employeeId);
        $('#payslipContent').hide();
        $('#payslipModal').modal('show');
    });

    // Calculate Gross Pay
    function calculateGrossPay() {
        var hourlyAmount = parseFloat($('#hourly_amount').text()) || 0;
        var overtime = parseFloat($('#overtime').val()) || 0;
        var maternity = parseFloat($('#maternity').val()) || 0;
        var sickPay = parseFloat($('#sickPay').val()) || 0;
        var holidayPay = parseFloat($('#holidayPay').val()) || 0;
        var bonus = parseFloat($('#bonus').val()) || 0;
        var other = parseFloat($('#other').val()) || 0;
        var adjustment = parseFloat($('#adjustment').val()) || 0;
        var grossPay = hourlyAmount + overtime + maternity + sickPay + holidayPay + bonus + other - adjustment;
        $('#gross_pay').text(grossPay.toFixed(2));
    }

    // Update Gross Pay on input change
    $('#payslipContent').on('input', '.payslip-input', calculateGrossPay);

    // Payslip Form Submission
    $('#payslipForm').on('submit', function(e) {
        e.preventDefault();
        var employeeId = $(this).data('employee-id');
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();

        if (!fromDate || !toDate) {
            showError('Please select both from and to dates.');
            return;
        }

        $.ajax({
            url: "{{ route('employees.payslip') }}",
            method: "POST",
            data: {
                employee_id: employeeId,
                from_date: fromDate,
                to_date: toDate,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log(response);
                if (response.payslip && response.employee && response.holiday) {
                    $('#payslipContent').show();
                    $('#paySlipEmployeeID').text(response.employee.employee_id);
                    $('#paySlipEmployeeName').text(response.employee.name);
                    $('#process_date').text('{{ \Carbon\Carbon::today()->format('Y-m-d') }}');
                    $('#paySlipEmployeeni').text(response.employee.ni || 'N/A');
                    $('#total_hours').text(response.workingHours);
                    $('#paySlippay_rate').text(response.employee.pay_rate);
                    $('#total_amount').text((response.employee.pay_rate * response.workingHours).toFixed(2));
                    $('#hourly_amount').text(parseFloat(response.employee.pay_rate).toFixed(2));
                    $('#paySlipentitled_holiday').text(response.employee.entitled_holiday);
                    $('#holiday_taken').text(response.holiday.holidayDataCount);
                    $('#holiday_remaining').text((response.employee.entitled_holiday - response.holiday.holidayDataCount));
                    $('#sick_days').text(response.holiday.sickDays);
                    $('#absence_days').text(response.holiday.absenceDays);
                    $('#paySliptax_code').text(response.employee.tax_code);
                    $('#from_date_display').text(fromDate);
                    $('#to_date_display').text(toDate);
                    // Reset additional inputs
                    $('#overtime, #maternity, #sickPay, #holidayPay, #bonus, #other, #adjustment').val(0);
                    calculateGrossPay();
                } else {
                    showError('Failed to fetch payslip data.');
                }
            },
            error: function(xhr) {
                showError('An error occurred while fetching payslip data.');
            }
        });
    });

    // Print Payslip
    // $('#printPayslip').on('click', function() {
    //     var printContents = $('#payslipContent').html();
    //     var originalContents = $('body').html();
    //     $('body').html(printContents);
    //     window.print();
    //     $('body').html(originalContents);
    //     location.reload();
    // });

    $('.customModal .times').on('click', function() {
        $('#payslipModal').removeClass('show');
        location.reload();
    });

    function populateForm(data) {
        $("#employee_id").val(data.employee_id);
        $("#name").val(data.name);
        $("#username").val(data.username);
        $("#phone").val(data.phone);
        $("#email").val(data.email);
        $("#employee_type").val(data.employee_type);
        $("#emergency_contact_number").val(data.emergency_contact_number);
        $("#emergency_contact_person").val(data.emergency_contact_person);
        $("#ni").val(data.ni);
        $("#role_id").val(data.user.role_id);
        $("#nationality").val(data.nationality);
        $("#join_date").val(data.join_date);
        $("#address").val(data.address);
        $("#pay_rate").val(data.pay_rate);
        $("#tax_code").val(data.tax_code);
        $("#branch_id").val(data.branch_id);
        $("#entitled_holiday").val(data.entitled_holiday);
        $("#bank_details").val(data.bank_details);
        var image = document.getElementById('preview-image');
        if (data.photo) {
            image.src = data.photo;
        } else {
            image.src = "#";
        }
        $("#codeid").val(data.id);
        $("#addBtn").val('Update');
        $("#addBtn").html('Update');
        $("#header-title").html('Update new data');
        $("#addThisFormContainer").show(300);
        $("#newBtn").hide(100);
    }

    function clearform() {
        $('#createThisForm')[0].reset();
        $("#addBtn").val('Create');
        $('#preview-image').attr('src', '#');
        $("#header-title").html('Add new data');
    }

    $("#image").change(function(e) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $("#preview-image").attr("src", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    });

    $(document).on('change', '.toggle-status', function() {
        var userId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        $.ajax({
            url: '{{ route("employees.updateStatus") }}',
            method: 'POST',
            data: {
                userId: userId,
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status === 200) {
                    showSuccess(response.message);
                } else {
                    showError('Failed to update status.');
                }
            },
            error: function(xhr) {
                showError('An error occurred. Please try again.');
            }
        });
    });

    $("#contentContainer").on('click', '#DetailsBtn', function() {
        var attrs = {};
        $.each(this.attributes, function() {
            if (this.specified && this.name.startsWith('data-')) {
                var key = this.name.replace('data-', '');
                attrs[key] = this.value;
            }
        });
        let modalHtml = `
        <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalLabel">Employee Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <tbody>
                                ${Object.entries(attrs).map(([key, value]) => `
                                    <tr>
                                        <th>${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</th>
                                        <td>${value}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        `;
        $('#detailsModal').remove();
        $('body').append(modalHtml);
        $('#detailsModal').modal('show');
    });


    function reloadPage(timeout) {
        setTimeout(() => location.reload(), timeout);
    }

    function pagetop() {
        $('html, body').animate({ scrollTop: 0 }, 'fast');
    }

    $('.customModal .times').on('click', function() {
        $('#payslipModal').removeClass('show');
    });
});
</script>

<script>
    $(document).ready(function() {
        $('#printPayslip').on('click', function() {
            // Create a new style element for print-specific styles
            const printStyles = `
                <style>
                    body {
                        font-family: Arial, Helvetica, sans-serif;
                        font-size: 12pt;
                        color: #000;
                        margin: 0;
                        background: #fff;
                    }
                    .paySlip {
                        width: 100%;
                        max-width: 210mm;
                        margin: 0 auto;
                        padding: 15mm;
                    }
                    .infoPrint {
                        text-align: center;
                        margin-bottom: 20mm;
                    }
                    .infoPrint h3 {
                        font-size: 18pt;
                        margin: 0;
                        font-weight: bold;
                    }
                    .infoPrint h5 {
                        font-size: 14pt;
                        margin: 5mm 0 0;
                        font-weight: normal;
                    }
                    .row_first, .row_second, .row_third, .row_forth {
                        margin-bottom: 15mm;
                    }
                    .manipulate {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 10mm;
                    }
                    .manipulate th, .manipulate td {
                        border: 1px solid #000;
                        padding: 3mm;
                        text-align: left;
                    }
                    .manipulate th {
                        background-color: #f2f2f2;
                        font-weight: bold;
                    }
                    .manipulate td {
                        font-size: 11pt;
                    }
                    .row_second table tfoot td {
                        border: none;
                        font-size: 10pt;
                        padding: 2mm;
                    }
                    .row_third .slip-title, .row_forth .slip-title {
                        font-size: 14pt;
                        font-weight: bold;
                        margin-bottom: 5mm;
                    }
                    .row_third p {
                        margin: 0 0 5mm;
                    }
                    .row_third table tr td {
                        border: none;
                        padding: 2mm;
                    }
                    .netPay {
                        text-align: right;
                        font-weight: bold;
                        font-size: 12pt;
                        margin-top: 5mm;
                    }
                    .payslip-input {
                        display: none;
                    }
                    .row_forth table {
                        width: 100%;
                    }
                    .row_forth .tax td {
                        border: none;
                        text-align: center;
                        font-size: 11pt;
                    }
                    .slipHead, .verGap, button {
                        display: none;
                    }
                    @page {
                        size: A4;
                        margin: 10mm;
                    }
                </style>
            `;

            // Get the payslip content
            const printContents = $('#payslipContent').html();
            const originalContents = $('body').html();

            // Set the body to the print content with styles
            $('body').html(`
                <div class="paySlip">
                    ${printContents}
                </div>
                ${printStyles}
            `);

            // Trigger print
            window.print();

            // Restore original content
            $('body').html(originalContents);
            location.reload();
        });
    });
</script>
@endsection