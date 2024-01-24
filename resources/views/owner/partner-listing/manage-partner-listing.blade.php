@extends('owner.layouts.master')
@section('content')
<main id="content" class="bg-gray-01">
    <div class="px-3 px-lg-6 px-xxl-13 py-5 py-lg-10 invoice-listing">
        <div class="mb-6">
            <h2 class="mb-0 text-heading fs-22 lh-15">Manage Partner Listing</h2>
        </div>
        <div class="row">
            <div class="col-md-12 mb-5">
                <div class="text-right">
                    <a href="{{ route('owner.create.partner.listing') }}" class="btn btn-lg btn-primary next-button property_information" type="button">Create Partner Listing
                    <span class="d-inline-block ml-2 fs-16"><i class="fal fa-long-arrow-right"></i></span>
                    </a>
                </div>
            </div>
        </div>
        <div class="row mb-12">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="partnerListing" class="table table-hover bg-white border rounded-lg" style="width:100%">
                                <thead>
                                    <tr role="row">
                                        <th>Sr No.</th>
                                        <th>Title</th>
                                        <th>Address</th>
                                        <th>Email</th>
                                        <th>Phone </th>
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
</main>
@endsection
@push('js')
<script>
    $(function () {
    var table = $('#partnerListing').DataTable({
        "language": {
        "zeroRecords": "No record(s) found.",
         searchPlaceholder: "Search records"
      },
      "bDestroy": true,
      searching: false,
       ordering: false,
       paging: true,
       processing: true,
       serverSide: true,
       lengthChange: true,
       "bSearchable":false,
       bStateSave: true,
       scrollX: true,
        ajax:{
            url:"{{route('owner.manage.partner.listing')}}",
        },
        dataType: 'html',
        columns: [
            {data: 'DT_RowIndex' ,name:'DT_RowIndex',searching: false,orderable: false},
            {data: 'title', name: 'title',orderable: false},
            {data: 'address', name: 'address',orderable: false},
            {data: 'email', name: 'email',orderable: false,defaultContent:0},
            {data: 'phone', name: 'phone',orderable: false,defaultContent:"07 June 2023"},
            {data: 'action', name: 'action',orderable: false,defaultContent:"07 June 2023"},
        ]
    });
    $.fn.dataTable.ext.errMode = 'none';
    $('#paymentTransaction').on('error.dt', function(e, settings, techNote, message) {
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
})


partnerListingDelete = (id) =>{
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
            showLoader();
            $.ajax({
                url: "{{ route('owner.partner.listing.delete') }}",
                type: 'POST',
                dataType: "json",
                data:{'id':id,'_token': '{{ csrf_token()}}'},
                cache:false,
                success:function (res) {
                    hideLoader();
                    Swal.fire(
                        'Confirmed!',
                        res.msg,
                        ).then((res)=>{
                            setTimeout(function() {
                                location.reload();
                            },500);
                    })
                }
            });
        }
    });
}
</script>
    
@endpush