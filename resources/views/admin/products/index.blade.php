@extends('admin.layout.app')
@section('title', 'Devices')
@section('content')

    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-12">
                                    <h4>Devices</h4>
                                </div>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">

                                <div class="clearfix">
                                    <div class="create-btn">

                                        @if (Auth::guard('admin')->check() ||
                                                ($sideMenuPermissions->has('Devices') && $sideMenuPermissions['Devices']->contains('create')))
                                            <a class="btn btn-primary mb-3 text-white"
                                                href="{{ url('admin/products-create') }}">Create</a>
                                        @endif
                                    </div>
                                </div>
                                <table class="table" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Dimension</th>
                                            <th>Points Per Sale</th>
                                            <th scope="col">View Detail</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $product)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>

                                                <td>
                                                    <img src="{{ !empty($product->image) && file_exists(public_path($product->image)) ? asset('public/' . $product->image) : asset('public/admin/assets/images/default.png') }}"
                                                        alt="device image"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                </td>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $product->demissions }}</td>
                                                <td>{{ $product->points_per_sale }}</td>
                                                <td>
                                                    <a href="{{ route('product.detail', $product->id) }}"
                                                        class="btn btn-info me-2"
                                                        style="float: left; margin-right: 8px;"><span><i
                                                                class="fa fa-eye"></i></span></a>
                                                </td>

                                                <td>
                                                    <div class="d-flex ">



                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Devices') && $sideMenuPermissions['Devices']->contains('edit')))
                                                            <a href="{{ url('admin/products-edit', $product->id) }}"
                                                                class="btn btn-primary me-2"
                                                                style="float: left; margin-right: 8px;"><span><i
                                                                        class="fa fa-edit"></i></span></a>
                                                        @endif

                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Devices') && $sideMenuPermissions['Devices']->contains('delete')))
                                                            <form id="delete-form-{{ $product->id }}"
                                                                action="{{ route('product.delete', $product->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>

                                                            <button class="show_confirm btn d-flex "
                                                                style="background-color: #cb84fe;"
                                                                data-form="delete-form-{{ $product->id }}" type="button">
                                                                <span><i class="fa fa-trash"></i></span>
                                                            </button>
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


        //delete alert
        $(document).on('click', '.show_confirm', function(event) {
            var formId = $(this).data("form");
            var form = document.getElementById(formId);
            event.preventDefault();
            swal({
                    title: "Are you sure?",
                    text: "If you delete this Device record, it will be gone forever.",
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
