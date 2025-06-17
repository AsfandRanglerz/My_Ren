@extends('admin.layout.app')
@section('title', 'Sub Admins')

@section('content')
    {{-- Assign Permissions Modal --}}
    @foreach ($subAdmins as $subAdmin)
        <div class="modal fade" id="createSubadminModal-{{ $subAdmin->id }}" tabindex="-1" role="dialog"
            aria-labelledby="permissionModalLabel-{{ $subAdmin->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Permissions</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('update.permissions', $subAdmin->id) }}" method="POST"
                            id="createSubadminForm-{{ $subAdmin->id }}">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="sub_admin_id" value="{{ $subAdmin->id }}">
                            <div class="form-group">
                                @foreach ($sideMenus as $sideMenu)
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input parent-checkbox"
                                            id="menu-{{ $subAdmin->id }}-{{ $sideMenu->id }}"
                                            onclick="toggleNestedPermissions(this, '{{ $subAdmin->id }}-{{ $sideMenu->id }}')"
                                            {{ $subAdmin->permissions->where('side_menu_id', $sideMenu->id)->isNotEmpty() ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="menu-{{ $subAdmin->id }}-{{ $sideMenu->id }}">
                                            {{ $sideMenu->name }}
                                        </label>
                                    </div>
                                    <div class="ml-4 nested-permissions"
                                        id="nested-permissions-{{ $subAdmin->id }}-{{ $sideMenu->id }}"
                                        style="{{ $subAdmin->permissions->where('side_menu_id', $sideMenu->id)->isNotEmpty() ? 'display:flex;' : 'display:none;' }}">
                                        @foreach (['view', 'create', 'edit', 'delete'] as $perm)
                                            <div class="form-check mr-2">
                                                <input type="checkbox" class="form-check-input"
                                                    id="{{ $perm }}-{{ $subAdmin->id }}-{{ $sideMenu->id }}"
                                                    name="side_menu_id[{{ $sideMenu->id }}][]" value="{{ $perm }}"
                                                    {{ $subAdmin->permissions->where('side_menu_id', $sideMenu->id)->pluck('permissions')->contains($perm) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="{{ $perm }}-{{ $subAdmin->id }}-{{ $sideMenu->id }}">
                                                    {{ ucfirst($perm) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-primary">Save Permissions</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Sub Admins Table --}}
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Sub Admins</h4>
                            </div>
                            <div class="card-body table-responsive">
                                @if (Auth::guard('admin')->check() ||
                                        ($sideMenuPermissions->has('Sub Admins') && $sideMenuPermissions['Sub Admins']->contains('create')))
                                    <a class="btn btn-primary mb-3" href="{{ route('subadmin.create') }}">Create</a>
                                @endif

                                <table class="table table-bordered" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Contact</th>
                                            <th>Role</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subAdmins as $subAdmin)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $subAdmin->name }}</td>
                                                <td>{{ $subAdmin->email }}</td>
                                                <td>{{ $subAdmin->phone }}</td>
                                                <td>{{ $subAdmin->roles->pluck('name')->join(', ') ?: 'No Role' }}</td>
                                                <td>
                                                    @if ($subAdmin->image && file_exists($subAdmin->image))
                                                        <img src="{{ asset($subAdmin->image) }}" width="50"
                                                            height="50" alt="Image">
                                                    @else
                                                        <img src="{{ asset('public/admin/assets/images/avator.png') }}"
                                                            width="50" height="50" alt="Default Image">
                                                    @endif
                                                </td>
                                                <td>
                                                    <label class="custom-switch">
                                                        <input type="checkbox" class="custom-switch-input toggle-status"
                                                            data-id="{{ $subAdmin->id }}"
                                                            {{ $subAdmin->status ? 'checked' : '' }}>
                                                        <span class="custom-switch-indicator"></span>
                                                        <span class="custom-switch-description">
                                                            {{ $subAdmin->status ? 'Activated' : 'Deactivated' }}
                                                        </span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Sub Admins') && $sideMenuPermissions['Sub Admins']->contains('edit')))
                                                            <a href="{{ route('subadmin.edit', $subAdmin->id) }}"
                                                                class="btn btn-primary mr-1">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        @endif

                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Sub Admins') && $sideMenuPermissions['Sub Admins']->contains('delete')))
                                                            <form id="delete-form-{{ $subAdmin->id }}"
                                                                action="{{ route('subadmin.destroy', $subAdmin->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>

                                                            <button class="show_confirm btn d-flex gap-4"
                                                                style="background-color: #ff5608;"
                                                                data-form="delete-form-{{ $subAdmin->id }}" type="button">
                                                                <span><i class="fa fa-trash"></i></span>
                                                            </button>
                                                        @endif
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

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $('.show_confirm').click(function(event) {
            var formId = $(this).data("form");
            var form = document.getElementById(formId);
            event.preventDefault();

            swal({
                    title: "Are you sure you want to delete this record?",
                    text: "If you delete this Sub-Admin record, it will be gone forever.",
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


        // Toggle nested permissions visibility

        function toggleNestedPermissions(parentCheckbox, targetId) {
            const container = document.getElementById('nested-permissions-' + targetId);
            if (parentCheckbox.checked) {
                container.style.display = 'flex';
            } else {
                container.style.display = 'none';
                const checkboxes = container.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(cb => cb.checked = false);
            }
        }

        //togle functionality for status
        $(document).ready(function() {
            $('.toggle-status').on('change', function() {
                var subAdminId = $(this).data('id');
                var status = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('admin.subadmin.toggleStatus') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: subAdminId,
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    },
                    error: function() {
                        toastr.error('Failed to update status.');
                    }
                });
            });
        });
    </script>
@endsection
