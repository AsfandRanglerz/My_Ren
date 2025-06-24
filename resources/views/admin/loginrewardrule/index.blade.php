@extends('admin.layout.app')
@section('title', 'Login Reward Rule')
@section('content')

    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-12">
                                    <h4>Reward Settings(According to the details provided on the index page below, customers
                                        will receive points for staying consistently active on the app.)</h4>
                                </div>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">
                                {{-- @if (Auth::guard('admin')->check() || $sideMenuPermissions->contains(fn($permission) => $permission['side_menu_name'] === 'ContactUs' && $permission['permissions']->contains('create')))
                                    <a class="btn btn-primary mb-3 text-white"
                                        href="{{ url('admin/login-reward-rules-create') }}">Create</a>
                                @endif --}}

                                <table class="table responsive" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Day</th>
                                            <th>Points</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $data)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>



                                                <td>{{ $data->day }}</td>
                                                <td>{{ $data->points }}</td>


                                                <td>
                                                    <div class="d-flex ">



                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Login Reward Rule') && $sideMenuPermissions['Login Reward Rule']->contains('edit')))
                                                            <a href="{{ route('login-reward-rules.edit', $data->id) }}"
                                                                class="btn btn-primary me-2"
                                                                style="float: left; margin-right: 8px;"><span><i
                                                                        class="fa fa-edit"></i></span></a>
                                                        @endif

                                                        {{-- @if (Auth::guard('admin')->check() || ($sideMenuPermissions->has('Products') && $sideMenuPermissions['Products']->contains('delete')))
                                                            <form id="delete-form-{{ $data->id }}"
                                                                action="{{ route('login-reward-rules.destroy', $data->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>

                                                            <button class="show_confirm btn d-flex "
                                                                style="background-color: #cb84fe;"
                                                                data-form="delete-form-{{ $data->id }}" type="button">
                                                                <span><i class="fa fa-trash"></i></span>
                                                            </button>
                                                        @endif --}}


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

    <script>
        $(document).ready(function() {
            $('#table_id_events').DataTable()
        })
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
    <script type="text/javascript">
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
