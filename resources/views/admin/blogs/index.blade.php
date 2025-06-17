@extends('admin.layout.app')
@section('title', 'Blogs')
@section('content')

    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-12">
                                    <h4>Blogs</h4>
                                </div>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">
                                <div class="clearfix">
                                    <div class="create-btn">
                                        @if (Auth::guard('admin')->check() ||
                                                ($sideMenuPermissions->has('Blogs') && $sideMenuPermissions['Blogs']->contains('create')))
                                            <a class="btn btn-primary mb-3 text-white"
                                                href="{{ url('admin/blogs-create') }}">Create</a>
                                        @endif
                                    </div>
                                </div>

                                <table class="table responsive" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Sr.</th>
                                            <th>Title</th>
                                            <th>Slug</th>
                                            <th>Image</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-faqs">
                                        @foreach ($blogs as $blog)
                                            <tr data-id="{{ $blog->id }}">
                                                <td class="sort-handler" style="cursor: move; text-align: center;">
                                                    <i class="fas fa-th"></i>
                                                </td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $blog->title }}</td>
                                                <td>{{ $blog->slug }}</td>
                                                <td>
                                                    @if ($blog->image)
                                                        <img src="{{ asset('public/' . $blog->image) }}" alt="blog image"
                                                            width="80" height="60">
                                                    @else
                                                        <img src="{{ asset('public/admin/assets/images/default.png') }}"
                                                            alt="blog image" width="80" height="60">
                                                    @endif
                                                </td>

                                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($blog->content), 150, '...') }}
                                                </td>

                                                <td>
                                                    <label class="custom-switch">
                                                        <input type="checkbox" class="custom-switch-input toggle-status"
                                                            data-id="{{ $blog->id }}"
                                                            {{ $blog->toggle ? 'checked' : '' }}>
                                                        <span class="custom-switch-indicator"></span>
                                                        <span class="custom-switch-description">
                                                            {{ $blog->toggle ? 'Activated' : 'Deactivated' }}
                                                        </span>
                                                    </label>
                                                </td>

                                                <td>
                                                    <div class="d-flex gap-4">
                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Blogs') && $sideMenuPermissions['Blogs']->contains('edit')))
                                                            <a href="{{ route('blog.edit', $blog->id) }}"
                                                                class="btn btn-primary" style="margin-right: 10px">
                                                                <span><i class="fa fa-edit"></i></span>
                                                            </a>
                                                        @endif

                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Blogs') && $sideMenuPermissions['Blogs']->contains('delete')))
                                                            <form id="delete-form-{{ $blog->id }}"
                                                                action="{{ route('blog.destroy', $blog->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>

                                                            <button class="show_confirm btn d-flex gap-4"
                                                                data-form="delete-form-{{ $blog->id }}" type="button"
                                                                style="background: #ff5608;">
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
    <!-- DataTables -->
    <script>
        $(document).ready(function() {
            $('#table_id_events').DataTable({
                paging: false,
                info: false
            });
        });
    </script>

    <!-- SweetAlert -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>

    <script>
        window.addEventListener('load', () => {
            const message = localStorage.getItem('toastMessage');
            if (message) {
                toastr.success(message);
                localStorage.removeItem('toastMessage');
            }
        });
    </script>

    <!-- Toggle Status Script -->
    <script>
        $(document).ready(function() {
            $('.toggle-status').change(function() {
                let status = $(this).prop('checked') ? 1 : 0;
                let blogId = $(this).data('id');
                let $descriptionSpan = $(this).siblings('.custom-switch-description');

                $.ajax({
                    url: "{{ route('blog.toggle-status') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: blogId,
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            $descriptionSpan.text(response.new_status);
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error updating status');
                    }
                });
            });
        });
    </script>

    <!-- Delete Confirmation Script -->
    <script type="text/javascript">
        $('.show_confirm').click(function(event) {
            let formId = $(this).data("form");
            let form = document.getElementById(formId);
            event.preventDefault();
            swal({
                    title: "Are you sure you want to delete this record?",
                    text: "If you delete this blog, it will be gone forever.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    </script>

    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        new Sortable(document.getElementById('sortable-faqs'), {
            animation: 150,
            handle: '.sort-handler',
            onEnd: function() {
                let order = [];
                document.querySelectorAll('#sortable-faqs tr').forEach((row, index) => {
                    order.push({
                        id: row.getAttribute('data-id'),
                        position: index + 1
                    });
                });

                fetch("{{ route('blog.reorder') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            order: order
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        localStorage.setItem('toastMessage', 'Alignment has been updated successfully');
                        window.location.reload();
                    });
            }
        });
    </script>
@endsection
