@extends('admin.layout.app')

@section('title', 'Points Conversion')

@section('content')



    <div class="main-content" style="min-height: 562px;">

        <section class="section">

            <div class="section-body">

                <div class="row">

                    <div class="col-12 col-md-12 col-lg-12">

                        <div class="card">

                            <div class="card-header">

                                <div class="col-12">

                                    <h4>Points Conversion</h4>

                                </div>

                            </div>

                            <div class="card-body table-striped table-bordered table-responsive">



                                <div class="clearfix">

                                    <div class="create-btn">



                                        {{-- @if (Auth::guard('admin')->check() || ($sideMenuPermissions->has('Points Conversion') && $sideMenuPermissions['Points Conversion']->contains('create')))

                                            <a class="btn btn-primary mb-3 text-white"

                                                href="{{ url('admin/point-conversions-create') }}">Create</a>

                                        @endif --}}

                                    </div>

                                </div>

                                <table class="table" id="table_id_events">
                                    <thead>

                                        <tr>

                                            <th>Sr.</th>

                                            <th>Points</th>

                                            <th>Price</th>

                                            <th scope="col">Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        @foreach ($conversions as $conversion)
                                            <tr>

                                                <td>{{ $loop->iteration }}</td>

                                                <td>{{ $conversion->points }}</td>

                                                <td>{{ $conversion->price }} PKR </td>

                                                <td>

                                                    <div class="d-flex ">

                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Points Conversion') && $sideMenuPermissions['Points Conversion']->contains('edit')))
                                                            <a href="{{ url('admin/point-conversions-edit', $conversion->id) }}"
                                                                class="btn btn-primary me-2"
                                                                style="float: left; margin-right: 8px;"><span><i
                                                                        class="fa fa-edit"></i></span></a>
                                                        @endif

                                                </td>

                            </div>

                            </td>

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

    </div>



@endsection



@section('js')



    <script type="text/javascript">
        $(document).ready(function() {

            $('#table_id_events').DataTable()

        })





        // delete alert



        $('.show_confirm').click(function(event) {

            var formId = $(this).data("form");

            var form = document.getElementById(formId);

            event.preventDefault();

            swal({

                    title: "Are you sure?",

                    text: "If you delete this product record, it will be gone forever.",

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

                                    timer: 3000

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
