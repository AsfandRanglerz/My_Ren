@extends('admin.layout.app')
@section('title', 'Signup Reward Settings')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Signup Reward Settings <small class="font-weight-bold text-danger">(Points will be awarded to users on their first sign-up as specified below.)</small></h4>
                            </div>

                            <div class="card-body table-striped table-bordered table-responsive">
                                <div class="clearfix mb-3">
                                    <!-- @if (Auth::guard('admin')->check() ||
                                            ($sideMenuPermissions->has('Voucher Settings') && $sideMenuPermissions['Voucher Settings']->contains('create')))
                                        <a class="btn btn-primary text-white"
                                            href="{{ route('voucher.create') }}">Create</a>
                                    @endif -->
                                </div>

                                <table class="table" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Required Signup Reward Points</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($setting as $setting)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $setting->points }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Voucher Settings') && $sideMenuPermissions['Voucher Settings']->contains('edit')))
                                                            <a href="{{ route('signup_reward_setting.edit', $setting->id) }}"
                                                                class="btn btn-primary me-2">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
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
                                title: "Success!",
                                text: "Record deleted successfully!",
                                icon: "success",
                                timer: 1000,
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
