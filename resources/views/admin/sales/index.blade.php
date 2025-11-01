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
                                <div class="btn btn-info mx-1">
                                    Remaining Points: {{ $remainingPoints }}
                                </div>

                                <!-- Deducted Points Button -->
                                <div class="btn btn-warning mx-1">
                                    Deducted Points: {{ $deductedPoints }}
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
    <form id="deductPointsForm">
        @csrf
        <input type="hidden" name="user_id" value="{{ $data->id }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deductModalLabel">Deduct Points</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label>Enter Points to Deduct</label>
                <input type="number" class="form-control" name="deduct_points" min="1" max="{{ $remainingPoints }}">
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

    $('#deductPointsForm').on('submit', function(e) {
        e.preventDefault();

        let user_id = $('input[name="user_id"]').val();
        let points = $('input[name="deduct_points"]').val();

        if(points <= 0){
            alert('Points must be greater than 0');
            return;
        }

        $.ajax({
            url: "{{ route('admin.deduct.points') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(res) {
                $('#deductModal').modal('hide');
                alert(res.message);

                // Blade top buttons update
                $('.btn-info').text('Remaining Points: ' + res.remainingPoints);
                $('.btn-warning').text('Deducted Points: ' + res.deductedPoints);
            },
            error: function(err) {
                alert(err.responseJSON.message ?? 'Something went wrong');
            }
        });
    });
});
</script>
@endsection
