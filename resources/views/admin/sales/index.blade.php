@extends('admin.layout.app')
@section('title', 'Reward Details')

@section('content')
<div class="main-content" style="min-height: 562px;">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <!-- Header -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">{{ $data->name ?? 'N/A' }} - Reward Detail</h4>
                            <div class="text-end">

                                <!-- Total Points Button (Gross) -->
                                <div class="btn btn-success mx-1">
                                    Total Points: {{ $grossTotalPoints }}
                                </div>

                                <!-- Remaining Points Button -->
                                <div class="btn btn-warning mx-1">
                                    Remaining Points: {{ $remainingPoints }}
                                </div>
                                <!-- Deduct Points Button -->
                                <button class="btn btn-danger mx-1" data-bs-toggle="modal" data-bs-target="#deductModal">
                                    Deduct Points
                                </button>
                            </div>
                        </div>

                        <!-- Sales Table -->
                        <div class="card-body table-striped table-bordered table-responsive">
                            <table class="table" id="table_id_events">
                                <thead>
                                    <tr>
                                        <th>Sr.</th>
                                        <th>Product</th>
                                        <th>SN Code</th>
                                        <th>Earn Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->sales as $sale)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $sale->product->name ?? 'N/A' }}</td>
                                            <td>{{ $sale->scan_code }}</td>
                                            <td>{{ $sale->points_earned }}</td>
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

<!-- Deduct Points Modal -->
<div class="modal fade" id="deductModal" tabindex="-1" aria-labelledby="deductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="deductPointsForm" method="POST" action="{{ route('admin.deduct.points') }}">
        @csrf
        <input type="hidden" name="user_id" value="{{ $data->id }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deductModalLabel">Deduct Points</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Enter Points to Deduct</label>
                    <input type="number" class="form-control" name="deduct_points" id="deduct_points">
                    <small class="text-danger d-none" id="deduct_points_error"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
  </div>
</div>

@endsection

@section('js')
<script>
$(document).ready(function() {
    $('#table_id_events').DataTable();
	  // ✅ Reset modal on close (clear input & error)
    $('#deductModal').on('hidden.bs.modal', function () {
        $('#deductPointsForm')[0].reset();
        $('#deduct_points_error').addClass('d-none').text('');
    });

    // ✅ Hide error when user focuses input again
    $('#deduct_points').on('focus input', function() {
        $('#deduct_points_error').addClass('d-none').text('');
    });

    // ✅ Form submit validation
    $('#deductPointsForm').on('submit', function(e) {
        e.preventDefault();
        let points = $('#deduct_points').val();
        let maxPoints = parseInt($('#deduct_points').attr('max'));
        
        // frontend validation
        if (points === '' || points <= 0) {
            $('#deduct_points_error').removeClass('d-none').text('Please enter a valid number of points.');
            return;
        }
        if (points > maxPoints) {
            $('#deduct_points_error').removeClass('d-none').text('You cannot deduct more than remaining points.');
            return;
        }

        // ✅ Submit via AJAX
        $.ajax({
            url: $(this).attr('action'),
            method: "POST",
            data: $(this).serialize(),
            success: function(res) {
                alert(res.message);
                $('#deductModal').modal('hide');
                $('#deductPointsForm')[0].reset();
                $('#deduct_points_error').addClass('d-none').text('');
                $('.btn-warning').text('Remaining Points: ' + res.remainingPoints);
            },
            error: function(err) {
                const errorMsg = err.responseJSON?.message ?? 'Something went wrong';
                $('#deduct_points_error').removeClass('d-none').text(errorMsg);
            }
        });
    });

});
</script>
@endsection
