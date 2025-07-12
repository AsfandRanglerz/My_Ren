@extends('admin.layout.app')
@section('title', 'Withdrawal Requests')

@section('content')
    <style>
        .dimmed-paid {
            opacity: 0.6;
            pointer-events: none;
            cursor: not-allowed;
        }
    </style>
    <script>
        const baseWithdrawUpdateUrl = "{{ url('/admin/withdrawrequest') }}";
    </script>
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
                                @if (Auth::guard('admin')->check() || ($sideMenuPermissions->has('Withdraw Request') && $sideMenuPermissions['Withdraw Request']->contains('create')))
                                    <a class="btn btn-primary mb-3 text-white"
                                        href="{{ url('/admin/user-create') }}">Create</a>
                                @endif 
                                --}}

                                <table class="table table-striped" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>User Name</th>
                                            <th>Requested Amount</th>
                                            <th>Payment Method</th>
                                            <th>Account Holder</th>
                                            <th>Account Number</th>
                                            {{-- <th>Total Amount</th> --}}
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($withdrawRequests as $withdrawRequest)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $withdrawRequest->name }}</td>
                                                <td>{{ $withdrawRequest->withdrawal_amount }}</td>
                                                <td>{{ $withdrawRequest->withdrawal_method }}</td>
                                                <td>{{ $withdrawRequest->withdrawal_details }}</td>
                                                <td>{{ $withdrawRequest->account_number }}</td>
                                                {{-- <td>{{ $withdrawRequest->total_amount }}</td> --}}
                                                {{-- <td>
                                                    @if ($withdrawRequest->attachment)
                                                        <a href="{{ asset('storage/' . $withdrawRequest->attachment) }}"
                                                            target="_blank" class="btn btn-info btn-sm">View</a>
                                                    @else
                                                        <span class="text-muted">No Attachment</span>
                                                    @endif
                                                </td> --}}
                                                <td>
                                                    @php
                                                        $isApproved = $withdrawRequest->status == 0;
                                                        $isNotApproved = $withdrawRequest->status == 1;
                                                        $hasAttachment = !empty($withdrawRequest->attachment);
                                                    @endphp
                                                    @if (Auth::guard('admin')->check() ||
                                                            ($sideMenuPermissions->has('Withdraw Request') && $sideMenuPermissions['Withdraw Request']->contains('edit')))
                                                        <button type="button"
                                                            class="btn btn-sm open-edit-modal btn-success 
                                                        {{ $isApproved && $hasAttachment ? 'dimmed-paid' : ($isNotApproved ? 'btn-danger' : 'btn-primary') }}"
                                                            data-id="{{ $withdrawRequest->id }}"
                                                            data-status="{{ $withdrawRequest->status }}"
                                                            data-attachment="{{ $withdrawRequest->attachment }}"
                                                            @if (!$isApproved || !$hasAttachment) data-bs-toggle="modal"
                                                        data-bs-target="#editWithdrawModal" @endif>
                                                            <span>{{ $isApproved && $hasAttachment ? 'Paid' : 'Pay' }}</span>
                                                        </button>
                                                    @endif
                                                    @if ($isApproved && $hasAttachment)
                                                    <a href="{{ asset('public/' . $withdrawRequest->attachment) }}"
                                                        target="_blank" class="btn btn-info btn-sm mt-0">
                                                        <i class="fa fa-paperclip"></i> 
                                                    </a>
                                                @endif
                                                    {{-- Delete button (optional) --}}

                                                    {{-- @if (Auth::guard('admin')->check() || ($sideMenuPermissions->has('Withdraw Request') && $sideMenuPermissions['Withdraw Request']->contains('delete')))
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





    <!-- Edit Withdraw Request Modal -->
    <div class="modal fade" id="editWithdrawModal" tabindex="-1" role="dialog" aria-labelledby="editWithdrawModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="editWithdrawForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Process Withdrawal Request</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="id" id="editWithdrawId">

                        {{-- Editable Fields --}}
                        <div id="editModeFields">
                            <div class="form-group">
                                <label for="attachment">Upload Attachment <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="attachment" id="attachment"
                                    accept="image/*">
                                <div id="attachment-error" class="text-danger" style="display:none;"></div>
                            </div>
                            <div class="form-group" id="viewAttachmentSection" style="display: none;">
                                <a id="viewAttachmentLink" href="#" target="_blank" class="btn btn-info btn-sm">View
                                    Attachment</a>
                            </div>

                        </div>

                        <div class="form-group">
                            <label>Approval Status <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="approved"
                                    value="approved">
                                <label class="form-check-label" for="approved">Approved</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="not_approved"
                                    value="not_approved">
                                <label class="form-check-label" for="not_approved">Not Approved</label>
                            </div>
                            <div id="status-error" class="text-danger" style="display:none;"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="saveChangesBtn">
                            <span id="saveChangeBtnText">Save Changes</span>
                            <span id="saveChangeSpinner" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i>
                            </span>
                        </button>
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

        $('#editWithdrawForm').submit(function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const id = $('#editWithdrawId').val();
            console.log("Editing withdraw request ID:", id);

            $("#saveChangeSpinner").show();
            $("#saveChangeBtnText").hide();
            $("#saveChangesBtn").prop("disabled", true);

            const actionUrl = `${baseWithdrawUpdateUrl}/${id}`;
            formData.append('_method', 'PUT');

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#editWithdrawModal').modal('hide');
                    toastr.success("Withdrawal request updated successfully");
                    location.reload();
                    const button = $(`.open-edit-modal[data-id="${id}"]`);
                    const status = formData.get("status");
                    const attachmentFile = formData.get("attachment");

                    if (status === 'approved' && hasAttachment) {
                        button
                            .removeClass('btn-primary btn-danger')
                            .addClass('btn-success')
                            .html('<span>Paid</span>');
                    } else if (status === 'not_approved') {
                        button
                            .removeClass('btn-primary btn-success')
                            .addClass('btn-danger')
                            .html('<span>Pay</span>');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        // Show attachment error
                        if (errors.attachment) {
                            $('#attachment-error').text(errors.attachment[0]).show();
                        } else {
                            $('#attachment-error').hide();
                        }
                        // Show status error
                        if (errors.status) {
                            $('#status-error').text(errors.status[0]).show();
                        } else {
                            $('#status-error').hide();
                        }
                        // Show other errors as toast
                        for (const key in errors) {
                            if (errors.hasOwnProperty(key) && key !== 'attachment' && key !==
                                'status') {
                                // If you have other fields, add similar blocks above and show here
                                toastr.error(errors[key][0]);
                            }
                        }
                    } else {
                        $('#attachment-error').hide();
                        $('#status-error').hide();
                        toastr.error("An error occurred while updating.");
                    }
                },
                complete: function() {
                    // Hide spinner, show text, and enable button
                    $("#saveChangeSpinner").hide();
                    $("#saveChangeBtnText").show();
                    $("#saveChangesBtn").prop("disabled", false);
                }
            });
        });


        $(document).ready(function() {
            $('.open-edit-modal').click(function() {

                const id = $(this).data('id');
                const status = $(this).data('status');
                const attachment = $(this).data('attachment');

                // Set hidden field
                $('#editWithdrawId').val(id);

                // Reset form inputs and visibility
                $('#attachment').val('');
                $('#approved, #not_approved').prop('checked', false).prop('disabled', false);
                $('#attachment').prop('disabled', false);
                $('#viewAttachmentSection').hide();
                $('#attachment-error').hide();
                $('#status-error').hide();

                // Set radio status
                if (status !== '') {
                    if (status == 0) {
                        $('#approved').prop('checked', true);
                    } else if (status == 1) {
                        $('#not_approved').prop('checked', true);
                    }
                }
                // Set attachment view
                // if (attachment && attachment !== '') {
                //     $('#viewAttachmentLink').attr('href', '/My_Ren/public/' + attachment);
                //     $('#viewAttachmentSection').show();
                // }

                // ✅ Disable fields if status is approved (0) AND attachment exists
                if (status == 0 && attachment && attachment !== '') {
                    $('#approved, #not_approved').prop('disabled', true);
                    $('#attachment').prop('disabled', true);
                    $('#saveChangesBtn').prop('disabled', true);
                } else {
                    $('#saveChangesBtn').prop('disabled', false);
                }
            });
        });
    </script>
@endsection
