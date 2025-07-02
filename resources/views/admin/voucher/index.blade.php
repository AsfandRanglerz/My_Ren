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
                                <h4>Voucher Settings <small class="font-weight-bold text-danger">(Each voucher is created by
                                        you, and based on the points and amount you set, users can redeem the voucher to
                                        withdraw that amount.)</small></h4>
                            </div>

                            <div class="card-body table-responsive">
                                <div class="clearfix mb-3">
                                    @if (Auth::guard('admin')->check() ||
                                            ($sideMenuPermissions->has('Voucher Settings') && $sideMenuPermissions['Voucher Settings']->contains('create')))
                                        <a class="btn btn-primary text-white"
                                            href="{{ route('voucher.create') }}">Create</a>
                                    @endif
                                </div>

                                <table class="table table-bordered table-striped table-hover" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Required Points</th>
                                            <th>Amount</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($vouchers as $voucher)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $voucher->required_points }}</td>
                                                <td>{{ $voucher->amount }} PKR</td>
                                                <td>
                                                    <div class="d-flex">
                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Voucher Settings') && $sideMenuPermissions['Voucher Settings']->contains('edit')))
                                                            <a href="{{ url('admin/voucher-edit', $voucher->id) }}"
                                                                class="btn btn-primary me-2">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        @endif

                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Voucher Settings') && $sideMenuPermissions['Voucher Settings']->contains('delete')))
                                                            <form id="delete-form-{{ $voucher->id }}"
                                                                action="{{ route('voucher.destroy', $voucher->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>

                                                            <button class="show_confirm btn text-white"
                                                                style="background-color: #cb84fe;"
                                                                data-form="delete-form-{{ $voucher->id }}" type="button">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No vouchers found.</td>
                                            </tr>
                                        @endforelse
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

        // delete confirmation with SweetAlert and AJAX
        $(document).on('click', '.show_confirm', function(event) {
            event.preventDefault();
            var formId = $(this).data("form");
            var form = document.getElementById(formId);

            swal({
                title: "Are you sure?",
                text: "If you delete this Voucher record, it will be gone forever.",
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
                                title: "Deleted!",
                                text: "Voucher deleted successfully!",
                                icon: "success",
                                timer: 3000,
                                buttons: false
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
