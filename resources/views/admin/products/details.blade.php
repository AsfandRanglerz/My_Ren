@extends('admin.layout.app')
@section('title', 'Device/Product Details')
@section('content')

    <style>
        /* Toastr text color to black */
        .toast-message {
            color: #000000 !important;
        }
    </style>

    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-12">
                                    <h4>{{ $product->name }} - Scan Codes</h4>
                                </div>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">
                                <div class="clearfix mb-3">
                                    <div class="create-btn">
                                        <a class="btn btn-primary mb-0" href="{{ url('admin/devices') }}">Back</a>
                                        @if (Auth::guard('admin')->check() ||
                                                ($sideMenuPermissions->has('Devices/Products') && $sideMenuPermissions['Devices/Products']->contains('create')))
                                            <a class="btn  text-white" href="{{ route('product.createdetails', $id) }}"
                                                style="background-color: #cb84fe;
">Create</a>
                                        @endif


                                    </div>
                                </div>
                                <table class="table" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Scan Code</th>
                                            <th scope="col">Action</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @if ($details->batches && $details->batches->isNotEmpty()) --}}
                                        @foreach ($details as $data)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>

                                                <td>{{ $data->scan_code }}</td>
                                                <td>

                                                    @if (Auth::guard('admin')->check() ||
                                                            ($sideMenuPermissions->has('Devices/Products') && $sideMenuPermissions['Devices/Products']->contains('delete')))
                                                        <form id="delete-form-{{ $data->id }}"
                                                            action="{{ route('bactches.delete', $data->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>

                                                        <button class="show_confirm btn d-flex "
                                                            style="background-color: #cb84fe;"
                                                            data-form="delete-form-{{ $data->id }}" type="button">
                                                            <span><i class="fa fa-trash"></i></span>
                                                        </button>
                                                    @endif

                                                </td>

                                            </tr>
                                        @endforeach
                                        {{-- @else --}}
                                        {{-- <tr>
                                                <td colspan="2">No scan codes found.</td>
                                            </tr> --}}
                                        {{-- @endif --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@section('js')
    <!-- CSS -->

    <!-- JS -->



    <script>
        @if (Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
        @endif

        @if (Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    </script>

    <script type="text/javascript">
        //datatables
        $(document).ready(function() {
            $('#table_id_events').DataTable();
        });


        //delete sweetalert
        $(document).on('click', '.show_confirm', function(event) {
            var formId = $(this).data("form");
            var form = document.getElementById(formId);
            event.preventDefault();
            swal({
                    title: "Are you sure?",
                    text: "If you delete this Device/Product Scan Code, it will be gone forever.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        // Send AJAX request
                        $.ajax({
                            url: form.action,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {

                                swal({
                                    title: "Success!",
                                    text: "Record deleted successfully!",
                                    icon: "success",
                                    button: false,
                                    timer: 1000
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                swal("Error!", "Failed to delete record.", "error");
                            }
                        });
                    }
                });
        });
    </script>
@endsection
