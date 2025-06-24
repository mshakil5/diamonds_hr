@extends('admin.layouts.admin')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    @if ($errors->any())
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (Session::has('success'))
        <div class="alert alert-success">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <p>{{ Session::get('success') }}</p>
        </div>
        {{ Session::forget('success') }}
    @endif

    <style>
        .form-check-input {
            position: absolute;
            opacity: 0;
        }

        .form-check-input + .form-check-label {
            position: relative;
            padding-left: 50px;
            cursor: pointer;
            font-size: 1rem;
            user-select: none;
        }

        .form-check-input + .form-check-label::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 20px;
            background-color: #ccc;
            border-radius: 20px;
            transition: background-color 0.3s;
        }

        .form-check-input + .form-check-label::after {
            content: '';
            position: absolute;
            left: 2px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .form-check-input:checked + .form-check-label::before {
            background-color: #007bff;
        }

        .form-check-input:checked + .form-check-label::after {
            transform: translate(20px, -50%);
        }
    </style>
    

    <section class="content pt-3" id="contentContainer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h3 class="card-title">Roles and Permissions</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover table-responsive">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h3 class="card-title">Create New Role</h3>
                        </div>
                        <div class="card-body">
                            <div class="ermsg"></div>
                            <form action="" method="post" id="permissionForm" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="name" class="control-label">Role Name</label>
                                    <input name="name" id="name" type="text" class="form-control" maxlength="50" required  placeholder="Enter Role Name"/>
                                </div>
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Dashboard</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p1" name="permission[]" value="1">
                                                    <label class="form-check-label" for="p1">Dashboard Content</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                              <legend class="w-auto px-2">Products</legend>
                                              
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p2" name="permission[]" value="2">
                                                  <label class="form-check-label" for="p2">Product Module</label>
                                              </div>

                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p73" name="permission[]" value="73">
                                                  <label class="form-check-label" for="p73">All Products Only</label>
                                              </div>

                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p3" name="permission[]" value="3">
                                                  <label class="form-check-label" for="p3">Create New Product</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p4" name="permission[]" value="4">
                                                  <label class="form-check-label" for="p4">Edit Product</label>
                                              </div>
                                              <div class="form-check d-none">
                                                  <input class="form-check-input" type="checkbox" id="p5" name="permission[]" value="5">
                                                  <label class="form-check-label" for="p5">Delete Product</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p6" name="permission[]" value="6">
                                                  <label class="form-check-label" for="p6">Categories</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p7" name="permission[]" value="7">
                                                  <label class="form-check-label" for="p7">Sub Categories</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p8" name="permission[]" value="8">
                                                  <label class="form-check-label" for="p8">Sub Sub Categories</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p9" name="permission[]" value="9">
                                                  <label class="form-check-label" for="p9">Brands</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p10" name="permission[]" value="10">
                                                  <label class="form-check-label" for="p10">Models</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p11" name="permission[]" value="11">
                                                  <label class="form-check-label" for="p11">Units</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p12" name="permission[]" value="12">
                                                  <label class="form-check-label" for="p12">Groups</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p74" name="permission[]" value="74">
                                                  <label class="form-check-label" for="p74">Tags</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p13" name="permission[]" value="13">
                                                  <label class="form-check-label" for="p13">Warranty</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p14" name="permission[]" value="14">
                                                  <label class="form-check-label" for="p14">Upload Products</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p15" name="permission[]" value="15">
                                                  <label class="form-check-label" for="p15">Promition & Discount</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p16" name="permission[]" value="16">
                                                  <label class="form-check-label" for="p16">Product Reviews</label>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" id="p17" name="permission[]" value="17">
                                                  <label class="form-check-label" for="p17">Product Queries</label>
                                              </div>
                                              <div class="form-check d-none">
                                                  <input class="form-check-input" type="checkbox" id="p18" name="permission[]" value="18">
                                                  <label class="form-check-label" for="p18">Bundle Product</label>
                                              </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Orders</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p19" name="permission[]" value="19">
                                                    <label class="form-check-label" for="p19">Order Module</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p20" name="permission[]" value="20">
                                                    <label class="form-check-label" for="p20">Order Status Change</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p21" name="permission[]" value="21">
                                                    <label class="form-check-label" for="p21">Send Confirmation Mail</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p22" name="permission[]" value="22">
                                                    <label class="form-check-label" for="p22">Send To Stock/ System Loss</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Stock</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p23" name="permission[]" value="23">
                                                    <label class="form-check-label" for="p23">Stock Module</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p24" name="permission[]" value="24">
                                                    <label class="form-check-label" for="p24">Purchase</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p25" name="permission[]" value="25">
                                                    <label class="form-check-label" for="p25">Stock List</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p26" name="permission[]" value="26">
                                                    <label class="form-check-label" for="p26">Purchase History</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p27" name="permission[]" value="27">
                                                    <label class="form-check-label" for="p27">Send To System Loss</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p28" name="permission[]" value="28">
                                                    <label class="form-check-label" for="p28">System Loses</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p29" name="permission[]" value="29">
                                                    <label class="form-check-label" for="p29">Supplier</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p30" name="permission[]" value="30">
                                                    <label class="form-check-label" for="p30">Warehouse</label>
                                                </div>
                                                <div class="form-check d-none">
                                                    <input class="form-check-input" type="checkbox" id="p31" name="permission[]" value="31">
                                                    <label class="form-check-label" for="p31">Supplier Pay</label>
                                                </div>
                                                <div class="form-check d-none">
                                                    <input class="form-check-input" type="checkbox" id="p32" name="permission[]" value="32">
                                                    <label class="form-check-label" for="p32">Supplier Transaction</label>
                                                </div>
                                                <div class="form-check d-none">
                                                    <input class="form-check-input" type="checkbox" id="p33" name="permission[]" value="33">
                                                    <label class="form-check-label" for="p33">Purchase Return</label>
                                                </div>
                                                <div class="form-check d-none">
                                                    <input class="form-check-input" type="checkbox" id="p34" name="permission[]" value="34">
                                                    <label class="form-check-label" for="p34">Return History</label>
                                                </div>
                                            </fieldset>        
                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Customer</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p35" name="permission[]" value="35">
                                                    <label class="form-check-label" for="p35">Create Customer</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p36" name="permission[]" value="36">
                                                    <label class="form-check-label" for="p36">Edit Customer</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p37" name="permission[]" value="37">
                                                    <label class="form-check-label" for="p37">Delete Customer</label>
                                                </div>
                                            </fieldset>           
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">    

                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">In House Sale</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p38" name="permission[]" value="38">
                                                    <label class="form-check-label" for="p38">In House Sale Module</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p39" name="permission[]" value="39">
                                                    <label class="form-check-label" for="p39">In House Sale</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p40" name="permission[]" value="40">
                                                    <label class="form-check-label" for="p40">Quotations</label>
                                                </div>
                                            </fieldset>                                     
                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Additional Permissions</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p41" name="permission[]" value="41">
                                                    <label class="form-check-label" for="p41">Reports</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p42" name="permission[]" value="42">
                                                    <label class="form-check-label" for="p42">Create Admin</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p43" name="permission[]" value="43">
                                                    <label class="form-check-label" for="p43">Edit Admin</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p44" name="permission[]" value="44">
                                                    <label class="form-check-label" for="p44">Delete Admin</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p45" name="permission[]" value="45">
                                                    <label class="form-check-label" for="p45">Role & Permission</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p46" name="permission[]" value="46">
                                                    <label class="form-check-label" for="p46">Slider</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p47" name="permission[]" value="47">
                                                    <label class="form-check-label" for="p47">Company Details</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p48" name="permission[]" value="48">
                                                    <label class="form-check-label" for="p48">Contact Email</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p49" name="permission[]" value="49">
                                                    <label class="form-check-label" for="p49">Mail Content</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p50" name="permission[]" value="50">
                                                    <label class="form-check-label" for="p50">Contact Message</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input d-none" type="checkbox" id="p51" name="permission[]" value="51">
                                                    <label class="form-check-label" for="p51">Section Status</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p52" name="permission[]" value="52">
                                                    <label class="form-check-label" for="p52">Ad</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p53" name="permission[]" value="53">
                                                    <label class="form-check-label" for="p53">Coupon</label>
                                                </div>
                                                <div class="form-check d-none">
                                                    <input class="form-check-input" type="checkbox" id="p54" name="permission[]" value="54">
                                                    <label class="form-check-label" for="p54">Create Special Offer</label>
                                                </div>
                                                <div class="form-check d-none">
                                                    <input class="form-check-input" type="checkbox" id="p55" name="permission[]" value="55">
                                                    <label class="form-check-label" for="p55">Edit Special Offer</label>
                                                </div>
                                                <div class="form-check d-none">
                                                    <input class="form-check-input" type="checkbox" id="p56" name="permission[]" value="56">
                                                    <label class="form-check-label" for="p56">Create Flash Sale</label>
                                                </div>
                                                <div class="form-check d-none">
                                                    <input class="form-check-input" type="checkbox" id="p57" name="permission[]" value="57">
                                                    <label class="form-check-label" for="p57">Edit Flash Sale</label>
                                                </div>
                                                <div class="form-check d-none">
                                                    <input class="form-check-input" type="checkbox" id="p58" name="permission[]" value="58">
                                                    <label class="form-check-label" for="p58">In House Sale</label>
                                                </div>
                                                <div class="form-check d-none">
                                                    <input class="form-check-input" type="checkbox" id="p59" name="permission[]" value="59">
                                                    <label class="form-check-label" for="p59">Delivery Man</label>
                                                </div>
                                            </fieldset>
                                            <fieldset class="border p-2">
                                                <legend class="w-auto px-2">Accounting</legend>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p60" name="permission[]" value="60">
                                                    <label class="form-check-label" for="p60">Chart Of Accounts</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p61" name="permission[]" value="61">
                                                    <label class="form-check-label" for="p61">Income</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p62" name="permission[]" value="62">
                                                    <label class="form-check-label" for="p62">Expense</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p63" name="permission[]" value="63">
                                                    <label class="form-check-label" for="p63">Assets</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p64" name="permission[]" value="64">
                                                    <label class="form-check-label" for="p64">Liabilities</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p65" name="permission[]" value="65">
                                                    <label class="form-check-label" for="p65">Equity</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p66" name="permission[]" value="66">
                                                    <label class="form-check-label" for="p66">Equity Holders</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p67" name="permission[]" value="67">
                                                    <label class="form-check-label" for="p67">Ledger</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p68" name="permission[]" value="68">
                                                    <label class="form-check-label" for="p68">Cashflow</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p69" name="permission[]" value="69">
                                                    <label class="form-check-label" for="p69">Bank Book</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p70" name="permission[]" value="70">
                                                    <label class="form-check-label" for="p70">Cash Book</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p71" name="permission[]" value="71">
                                                    <label class="form-check-label" for="p71">Income Statement</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="p72" name="permission[]" value="72">
                                                    <label class="form-check-label" for="p72">Balance Sheet</label>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-success btn-md" id="submitBtn" type="submit" disabled>
                                            <i class="fa fa-plus-circle"></i> Submit
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
    
@section('script')


@endsection