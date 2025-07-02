@extends('admin.layout.app')
@section('title', 'Withdrawal Requests')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">

                        <!-- Card Starts -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Withdrawal Requests</h4>
                            </div>
                            <div class="card-body table-responsive">
                                {{-- Optional "Create" button --}}
                                {{-- Uncomment if needed --}}
                                {{-- 
                                @if (Auth::guard('admin')->check() ||
                                        ($sideMenuPermissions->has('Withdraw Request') && $sideMenuPermissions['Withdraw Request']->contains('create')))
                                    <a class="btn btn-primary mb-3 text-white"
                                        href="{{ url('/admin/user-create') }}">Create</a>
                                @endif 
                                --}}

                                <table class="table table-striped" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Name</th>
                                            <th>Requested Amount</th>
                                            <th>Total Amount</th>
                                            <th>Payment Method</th>
                                            <th>Account Number</th>
                                            <th>Bank Details</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($withdrawRequests as $withdrawRequest)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $withdrawRequest->name }}</td>
                                                <td>{{ $withdrawRequest->withdrawal_amount }}</td>
                                                <td>{{ $withdrawRequest->total_amount }}</td>
                                                <td>{{ $withdrawRequest->withdrawal_method }}</td>
                                                <td>{{ $withdrawRequest->account_number }}</td>
                                                <td>{{ $withdrawRequest->withdrawal_details }}</td>
                                                {{-- <td>
                                                    @if ($withdrawRequest->attachment)
                                                        <a href="{{ asset('storage/' . $withdrawRequest->attachment) }}"
                                                            target="_blank" class="btn btn-info btn-sm">View</a>
                                                    @else
                                                        <span class="text-muted">No Attachment</span>
                                                    @endif
                                                </td> --}}
                                                <td>    
                                                    @if (Auth::guard('admin')->check() ||
                                                            ($sideMenuPermissions->has('Withdraw Request') && $sideMenuPermissions['Withdraw Request']->contains('edit')))
                                                        <button type="button"
                                                            class="btn btn-sm btn-primary open-edit-modal"
                                                            data-id="{{ $withdrawRequest->id }}"
                                                            data-name="{{ $withdrawRequest->name }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editWithdrawModal">
                                                            <span>Pay</span>
                                                        </button>
                                                    @endif
                                                    {{-- Delete button (optional) --}}
                                                    
                                                    {{-- @if (Auth::guard('admin')->check() ||
                                                            ($sideMenuPermissions->has('Withdraw Request') && $sideMenuPermissions['Withdraw Request']->contains('delete')))
                                                        <form id="delete-form-{{ $withdrawRequest->id }}"
                                                        action="{{ route('withdrawRequest.delete', $withdrawRequest->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="btn btn-sm show_confirm"
                                                            style="background-color: #cb84fe;"
                                                            data-form="delete-form-{{ $withdrawRequest->id }}">
                                                            <i class="fa fa-trash" ></i>
                                                        </button>
                                                    </form>

                                                    @endif  --}}
                                                   
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> <!-- /.card-body -->
                        </div> <!-- /.card -->

                    </div> <!-- /.col-12 -->
                </div> <!-- /.row -->
            </div> <!-- /.section-body -->
        </section>
    </div>



    <!-- Deactivation Reason Modal -->
    <div class="modal fade" id="deactivationModal" tabindex="-1" role="dialog" aria-labelledby="deactivationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deactivationModalLabel">Deactivation Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="deactivationForm">
                        @csrf
                        <input type="hidden" name="user_id" id="deactivatingUserId">
                        <div class="form-group">
                            <label for="deactivationReason">Please specify the reason for deactivation:</label>
                            <textarea class="form-control" id="deactivationReason" name="reason" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmDeactivation">Submit</button>
                </div>
            </div>
        </div>
    </div>

<!-- Edit Withdraw Request Modal -->
<div class="modal fade" id="editWithdrawModal" tabindex="-1" role="dialog" aria-labelledby="editWithdrawModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editWithdrawForm" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Withdraw Request</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="editWithdrawId">

            <div class="form-group">
                <label for="editName">Name</label>
                <input type="text" class="form-control" name="name" id="editName" readonly>
            </div>

            <div class="form-group">
                <label for="attachment">Upload New Attachment</label>
                <input type="file" class="form-control" name="attachment" id="attachment" accept="image/*">
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Changes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('js')
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
        event.preventDefault();
        var formId = $(this).data("form");
        var form = document.getElementById(formId);

        if (!form) {
            console.error("Form not found for ID:", formId);
            return;
        }

        swal({
            title: "Are you sure you want to delete this record?",
            text: "If you delete this record, it will be gone forever.",
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
                        
                        // toastr.success('Withdraw request deleted successfully');
                        swal({
                            title: "Success!",
                            text: "Record deleted successfully",
                            icon: "success",
                            button: false,
                            timer: 2000
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
        
    $('.open-edit-modal').click(function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        $('#editWithdrawId').val(id);
        $('#editName').val(name);

        // Set the form action dynamically
        const actionUrl = "{{ url('/withdrawrequest') }}/" + id;
        $('#editWithdrawForm').attr('action', actionUrl);
    });


           </script>
@endsection
