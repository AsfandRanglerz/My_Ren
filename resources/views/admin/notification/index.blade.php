@extends('admin.layout.app')
@section('title', 'Notifications')

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
                            <div class="card-body table-striped table-bordered table-responsive"> <a
                                    class="btn mb-3 text-white" data-toggle="modal" style="background-color: #cb84fe;"
                                    data-target="#createUserModal">Create</a>
                                <table class="table" id="table_id_events">
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
                                                    <a href="#" class="btn btn-primary me-2"><i
                                                            class="fa fa-edit"></i></a>
                                                    <form id="delete-form-{{ $notification->id }}" action="#"
                                                        method="POST" style="display:inline;">
                                                        @csrf @method('DELETE')
                                                        <button class="show_confirm btn" style="background-color: #cb84fe;"
                                                            data-form="delete-form-{{ $notification->id }}"
                                                            type="button"><i class="fa fa-trash"></i></button>
                                                    </form>
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
                        <h5 class="modal-title">Create Notification</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <!-- User Type Dropdown with Select All -->
                        <div class="form-group">
                            <label for="user_type">Select User Type <span style="color: red;">*</span></label>
                            <button type="button" class="btn btn-sm btn-primary mb-2" id="select_all_user_types_btn">Select
                                All</button>
                            <select class="form-control" id="user_type" name="user_type[]" multiple required>
                                <option value="subadmin">Sub Admin</option>
                                <option value="web">User</option>
                            </select>
                            <small class="form-text text-muted">Hold Ctrl or Cmd to select multiple.</small>
                        </div>

                        <!-- Image Upload -->
                        <div class="form-group">
                            <label for="userImage">Image <span style="color: red;">*</span></label>
                            <input type="file" class="form-control-file" id="userImage" name="image" accept="image/*"
                                required>
                            <small class="text-danger">Max 2MB image size allowed.</small>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="userDescription">Description <span style="color: red;">*</span></label>
                            <textarea class="form-control" id="userDescription" name="description" rows="3" required></textarea>
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
    <script>
        $(document).ready(function() {
            $('#table_id_events').DataTable();

            // Select All User Types
            $('#select_all_user_types_btn').click(function() {
                $('#user_type option').prop('selected', true);
                $('#user_type').trigger('change');
            });

            // SweetAlert Delete Confirmation
            $('.show_confirm').click(function(event) {
                event.preventDefault();
                var formId = $(this).data("form");
                var form = document.getElementById(formId);

                swal({
                    title: "Are you sure you want to delete this record?",
                    text: "This action cannot be undone.",
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
                                swal("Deleted!", "Record has been deleted.", "success")
                                    .then(() => {
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
        });
    </script>
@endsection
