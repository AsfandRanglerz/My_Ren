@extends('admin.layout.app')
@section('title', 'Voucher Settings')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Voucher Settings</h4>
                            </div>

                            <div class="card-body table-striped table-bordered table-responsive">
                                <div class="clearfix">
                                    <div class="create-btn">
                                        @if (Auth::guard('admin')->check() ||
                                                ($sideMenuPermissions->has('Products') && $sideMenuPermissions['Products']->contains('create')))
                                            <a class="btn btn-primary mb-3 text-white"
                                                href="{{ route('voucher.create') }}">Create</a>
                                        @endif
                                    </div>
                                </div>

                                <table class="table responsive" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Required Points</th>
                                            <th>Amount</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($vouchers as $voucher)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $voucher->required_points }}</td>
                                                <td>{{ $voucher->amount }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Products') && $sideMenuPermissions['Products']->contains('edit')))
                                                            <a href="{{ url('admin/products-edit', $voucher->id) }}"
                                                                class="btn btn-primary me-2">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        @endif

                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Products') && $sideMenuPermissions['Products']->contains('delete')))
                                                            <form id="delete-form-{{ $voucher->id }}"
                                                                action="{{ route('product.delete', $voucher->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>

                                                            <button class="show_confirm btn d-flex"
                                                                style="background-color: #cb84fe;"
                                                                data-form="delete-form-{{ $voucher->id }}" type="button">
                                                                <i class="fa fa-trash"></i>
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
    <script type="text/javascript">
        $(document).ready(function() {
            $('#table_id_events').DataTable();
        });

        // delete confirmation with AJAX
        $(document).on('click', '.show_confirm', function(event) {
            event.preventDefault();
            var formId = $(this).data("form");
            var form = document.getElementById(formId);

            swal({
                title: "Are you sure?",
                text: "If you delete this Product record, it will be gone forever.",
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
