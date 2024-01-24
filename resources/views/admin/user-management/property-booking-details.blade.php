@extends('admin.layouts.master')
@push('title')
    Property Booking Details
@endpush
@section('content')
<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="row page-titles mx-0">
                <div class="col p-md-0">
                    @include('flash-message.flash-message')
                    <div class="row">
                        <div class="col-md-6"><h4 style="color:black">Property Booking Details</h4></div>                             
                    </div>
                </div>
            </div> 
            <!-- row -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-body">
                            <div class="customfilter">
                                <ul>
                                    <li><a href="javascript:void(0)" class="active">Upcoming Bookings : <span>(20)</span></a></li>
                                    <li><a href="javascript:void(0)">Ongoing Bookings : <span>(05)</span></a></li>
                                    <li><a href="javascript:void(0)">Payment Due Pending : <span>(10)</span></a></li>
                                </ul>
                            </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration display nowrap" style="width:100%" id="property_booking">
                                        <thead>
                                            <tr>
                                                <th>Sr No.</th>
                                                <th>Owner Id</th>
                                                <th>Check In Date</th>
                                                <th>Check Out Date</th>
                                                <th>Total Booking Fess</th>
                                                <th>Paid Amount</th>
                                                <th>Due Amount</th>
                                                <th>Due Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #/ container -->
        </div>
@endsection
@push('js')
<script>
   $(function () {
    var table = $('#property_booking').DataTable({
        "language": {
        "zeroRecords": "No record(s) found.",
         searchPlaceholder: "Search records"
      },
      "bDestroy": true,
      searching: true,
       ordering: false,
       paging: true,
       processing: true,
       serverSide: true,
       lengthChange: true,
       "bSearchable":true,
       bStateSave: true,
       scrollX: true,
        ajax:{
            url:"{{route('admin.property.booking_details')}}",
        },
        dataType: 'html',
        columns: [
            {data: 'DT_RowIndex' ,name:'DT_RowIndex',searchable: false,orderable: false},
            {data: 'user_id' ,name:'user_id',searchable: false,orderable: false},
            {data: 'check_in', name: 'check_in',orderable: false},
            {data: 'check_out', name: 'check_out',orderable: false},
            {data: 'total_amount', name: 'total_amount',orderable: false,defaultContent:919786123454},
            {data: 'paid_amount', name: 'paid_amount',orderable: false},
            {data: 'dues_amount', name: 'dues_amount',orderable: false},
            {data: 'next_payment_date', name: 'next_payment_date',searchable: false,orderable: false},
            {data: 'action', name: 'action',orderable: false},
        ]
    });
    $.fn.dataTable.ext.errMode = 'none';
    $('#amenites').on('error.dt', function(e, settings, techNote, message) {
       console.log( 'An error has been reported by DataTables: ', message);
    })
    $('.mega-menu').on('click',function(){
        try {
            table.state.clear();
        }
        catch(err) {
            console.log(err.message);
        }
    })
    $(".search").on('click',function(){
        table.draw();
    })
  });

</script>
    
@endpush