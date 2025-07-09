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
                                    <form action="{{ route('notifications.deleteAll') }}" method="POST" class="d-inline-block float-right">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-primary mb-3 delete_all">
                                        Delete All
                                    </button>
                                </form>
                                <table class="table" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Message</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($notifications as $notification)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><img src="{{ asset($notification->image) }}" alt="Notification Image" width="60" height="60"></td>
                                                <td>{{ $notification->title }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($notification->description), 150, '...') }}
                                                </td>
                                                <td>{{ $notification->created_at->format('d M Y') }}</td>
                                                <td>
                                                    {{-- <a href="#" class="btn btn-primary me-2"><i
                                                            class="fa fa-edit"></i></a> --}}
                                                    <form id="delete-form-{{ $notification->id }}" 
                                                    action="{{ route('notification.destroy', $notification->id) }}" 
                                                    method="POST" style="display:inline-block; margin-left: 10px">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button class="show_confirm btn" 
                                                            data-form="delete-form-{{ $notification->id }}" 
                                                            style="background-color: #cb84fe;" 
                                                            type="submit">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
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
              <form id="createUserForm" method="POST" action="{{ route('notification.store') }}" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create Notification</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <!-- User Type Dropdown with Select All -->
                       <input type="hidden" name="user_type" value="user">


                         <div class="form-group" id="user_field">
                            <label><strong>Sellers <span style="color: red;">*</span></strong></label>
                            <div class="form-check mb-2">
                                <input type="checkbox" id="select_all_users" class="form-check-input">
                                <label class="form-check-label" for="select_all_users">Select All</label>
                            </div>
                            <select name="users[]" id="users" class="form-control select2" multiple>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('users') && in_array($user->id, old('users')) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('users')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div class="form-group">
                            <label for="userImage">Image <span style="color: red;">*</span></label>
                            <input type="file" class="form-control-file" id="userImage" name="image" accept="image/*"
                                required>
                            <small class="text-danger">Max 2MB image size allowed.</small>
                        </div>

                         <!-- Title Input -->
                        <div class="form-group">
                            <label><strong>Title <span style="color:red;">*</span></strong></label>
                            <input type="text" name="title" class="form-control" placeholder="Title" required>
                            @error('title')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                       <div class="form-group">
                            <label><strong>Description <span style="color:red;">*</span></strong></label>
                            <textarea name="description" class="form-control" placeholder="Type your message here..." rows="4" required></textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="createBtn">
                            <span id="createBtnText">Create Notification</span>
                            <span id="createSpinner" style="display: none;">
                                    <i class="fa fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#table_id_events').DataTable();

            
             // Initialize Select2
             $('.select2').select2({
                 placeholder: "Select sellers",
                 allowClear: true,
             });
    
              
    
            //  "Select All Sellers" functionality
             $('#select_all_users').on('change', function () {
                 $('#users > option').prop('selected', this.checked).trigger('change');
             });
    
             // Ensure "Select All" is unchecked if any individual option is deselected
             $('#users').on('change', function () {
                 $('#select_all_users').prop('checked', $('#users option:selected').length === $('#users option').length);
             });
             $('form').submit(function () {
            // Show spinner and disable button
            $("#createSpinner").show();
            $("#createBtnText").hide();
            $("#createBtn").prop("disabled", true);
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
         $(document).on('click', '.delete_all', function(event) {
        var form = $(this).closest("form");
        event.preventDefault();
        swal({
            title: "Are you sure you want to delete all records?",
            text: "This will permanently remove all records and cannot be undone.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });
    });
    </script>
    
@endsection
