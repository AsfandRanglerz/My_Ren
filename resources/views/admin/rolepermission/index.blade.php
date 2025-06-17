@extends('admin.layout.app')
@section('title', 'Roles')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Roles</h4>
                            </div>
                            <div class="card-body table-responsive">
                                @if (Auth::guard('admin')->check() ||
                                        ($sideMenuPermissions->has('Roles') && $sideMenuPermissions['Roles']->contains('create')))
                                    <a class="btn btn-primary mb-3 text-white"
                                        href="{{ url('admin/roles-create') }}">Create</a>
                                @endif

                                <table class="table table-striped" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Name</th>
                                            {{-- <th>Status</th> --}}
                                            <th>Permissions</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($roles as $role)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    {{ $role->name }}
                                                </td>
                                                {{-- <td>
                                                    <button class="btn btn-success">Active</button>
                                                </td> --}}
                                                <td>
                                                    @if (Auth::guard('admin')->check() ||
                                                            ($sideMenuPermissions->has('Roles') && $sideMenuPermissions['Roles']->contains('edit')))
                                                        <a class="btn btn-success"
                                                            href="{{ route('role.permissions', $role->id) }}">Permissions</a>
                                                    @endif
                                                </td>

                                                <td>
                                                    @if (Auth::guard('admin')->check() ||
                                                            ($sideMenuPermissions->has('Roles') && $sideMenuPermissions['Roles']->contains('delete')))
                                                        <!-- Delete Form -->
                                                        <form id="delete-form-{{ $role->id }}"
                                                            action="{{ route('delete.role', $role->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE') <!-- Spoof DELETE method -->
                                                        </form>


                                                        <!-- Delete Button -->
                                                        <button class="show_confirm btn d-flex gap-4"
                                                            style="background-color: #ff5608;"
                                                            data-form="delete-form-{{ $role->id }}" type="button">
                                                            <span><i class="fa fa-trash"></i></span>

                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> <!-- /.card-body -->
                        </div> <!-- /.card -->
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div> <!-- /.section-body -->
        </section>
    </div>
@endsection

@section('js')

    @if (\Illuminate\Support\Facades\Session::has('message'))
        <script>
            toastr.success('{{ \Illuminate\Support\Facades\Session::get('message') }}');
        </script>
    @endif
    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#table_id_events')) {
                $('#table_id_events').DataTable().destroy();
            }
            $('#table_id_events').DataTable();
        });
    </script>
    <!-- Include SweetAlert -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
    <script type="text/javascript">
        $('.show_confirm').click(function(event) {
            var formId = $(this).data("form");
            var form = document.getElementById(formId);
            event.preventDefault();

            swal({
                    title: "Are you sure you want to delete this record?",
                    text: "If you delete this Role record, it will be gone forever.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        // Send AJAX request to delete
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
                                    text: "Record deleted successfully",
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
