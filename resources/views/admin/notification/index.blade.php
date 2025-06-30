@extends('admin.layout.app')
@section('title', 'Notifications')
@php
    if (auth('subadmin')->check()) {
        $userType = 'subadmin';
    } elseif (auth('web')->check()) {
        $userType = 'web';
    } else {
        $userType = 'guest';
    }
@endphp

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Notifications</h4>
                            </div>
                            <div class="card-body table-responsive">

                                @if (Auth::guard('admin')->check() ||
                                        ($sideMenuPermissions->has('Notifications') && $sideMenuPermissions['Notifications']->contains('create')))
                                    <a class="btn mb-3 text-white" data-toggle="modal" style="background-color: #cb84fe;"
                                        data-target="#createUserModal">
                                        Create
                                    </a>
                                @endif

                                <table class="table table-striped" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Image</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($notifications as $notification)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $notification->Image }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($notification->description), 150, '...') }}
                                                </td>
                                                <td>
                                                    @if (Auth::guard('admin')->check() ||
                                                            ($sideMenuPermissions->has('Notifications') && $sideMenuPermissions['Notifications']->contains('edit')))
                                                        <a href="#" class="btn btn-primary me-2"
                                                            style="float: left; margin-right: 8px;">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif

                                                    @if (Auth::guard('admin')->check() ||
                                                            ($sideMenuPermissions->has('Notifications') && $sideMenuPermissions['Notifications']->contains('delete')))
                                                        <form id="delete-form-{{ $notification->id }}" action="#"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>

                                                        <button class="show_confirm btn d-flex gap-4"
                                                            style="background-color: #cb84fe
;"
                                                            data-form="delete-form-{{ $notification->id }}" type="button">
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

    <!-- Create Notification Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="createUserForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Notification</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">

                        <!-- User Type Dropdown -->
                        <div class="form-group">
                            <label for="user_type">Select User Type<span style="color: red;">*</span></label>
                            <select class="form-control" id="user_type" name="user_type" required>
                                <option value="" selected disabled>-- Select User Type --</option>
                                <option value="subadmin">Sub Admin</option>
                                <option value="web">User</option>
                            </select>
                        </div>

                        <!-- Users Dropdown with Select All and Select2 -->
                        <div class="form-group">
                            <label for="user_ids">Select User(s) <span style="color: red;">*</span></label>
                            <div class="d-flex mb-2">
                                <button type="button" class="btn btn-sm btn-success" id="select_all_users_btn">Select All
                                    Users</button>
                            </div>
                            <select class="form-control" id="user_ids" name="user_ids[]" multiple="multiple" required
                                style="width: 100%;">

                                <option disabled>Select a user type first</option>
                            </select>
                        </div>

                        <!-- Image -->
                        <div class="form-group">
                            <label for="userImage">Image<span style="color: red;">*</span></label>
                            <input type="file" class="form-control-file" id="userImage" name="image" accept="image/*"
                                required>

                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-danger">Max 2MB image size allowed.</small>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="userDescription">Description<span style="color: red;">*</span></label>
                            <textarea class="form-control" id="userDescription" name="description" rows="3" required></textarea>

                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Notification</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Include DataTable -->
    <script type="text/javascript">
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#table_id_events')) {
                $('#table_id_events').DataTable().destroy();
            }
            $('#table_id_events').DataTable();
        });

        ///////////////////////////

        $(document).ready(function() {
            // ✅ Initialize Select2
            $('#user_ids').select2({
                placeholder: "Select User(s)",
                width: '100%',
                dropdownParent: $('#createUserModal') // Use only if Select2 is inside a modal
            });

            // ✅ Select All Users Button
            $('#select_all_users_btn').click(function() {
                $('#user_ids option').prop('selected', true);
                $('#user_ids').trigger('change');
            });

            // ✅ SweetAlert Delete Confirmation
            $('.show_confirm').click(function(event) {
                event.preventDefault();
                var formId = $(this).data("form");
                var form = document.getElementById(formId);

                swal({
                    title: "Are you sure you want to delete this record?",
                    text: "If you delete this User record, it will be gone forever.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
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
                            error: function() {
                                swal("Error!", "Failed to delete record.", "error");
                            }
                        });
                    }
                });
            });

            // ✅ Load users dynamically on user_type change
            $('#user_type').change(function() {
                var userType = $(this).val();
                $('#user_ids').html('<option disabled selected>Loading...</option>');
                $('#user_ids').trigger('change'); // Clear display in UI

                $.ajax({
                    url: '{{ url('admin/get-users-by-type') }}',
                    method: 'GET',
                    data: {
                        type: userType
                    },
                    success: function(response) {
                        $('#user_ids').empty();
                        if (response.length === 0) {
                            $('#user_ids').append('<option disabled>No users found</option>');
                        } else {
                            response.forEach(function(user) {
                                $('#user_ids').append(
                                    `<option value="${user.id}">${user.name} (${user.email})</option>`
                                );
                            });
                        }
                        $('#user_ids').trigger('change'); // Refresh Select2 UI
                    },
                    error: function() {
                        $('#user_ids').html('<option disabled>Error loading users</option>');
                        $('#user_ids').trigger('change');
                    }
                });
            });
        });
    </script>

@endsection
