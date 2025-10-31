@extends('admin.layout.app')
@section('title', 'Devices/Products')
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <a class="btn btn-primary mb-3" href="{{ route('product.index') }}">Back</a>

            <form action="{{ url('admin/devices-store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <h4 class="text-center my-4">Create Device/Product</h4>
                    <div class="row px-4">

                        <!-- Name -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name">Name <span style="color: red;">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="Enter device name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="image">Image <span style="color: red;">*</span></label>
                                <input type="file" name="image" id="image"
                                    class="form-control @error('image') is-invalid @enderror" required>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Specification -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="demissions">Specification <span style="color: red;">*</span></label>
                                <input type="text" name="demissions" id="demissions"
                                    class="form-control @error('demissions') is-invalid @enderror"
                                    value="{{ old('demissions') }}" placeholder="Enter specification" required>
                                @error('demissions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Profit Margin -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="profit_margin">Profit Margin (PKR) <span style="color: red;">*</span></label>
                                <input type="number" step="0.01" name="profit_margin" id="profit_margin"
                                    class="form-control @error('profit_margin') is-invalid @enderror"
                                    value="{{ old('profit_margin') }}" placeholder="Enter profit margin in PKR" required>
                                @error('profit_margin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">1 Point = 1 Rupee PKR</small>
                            </div>
                        </div>

                        <!-- Discount -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="discount">Discount (%) <span style="color: red;">*</span></label>
                                <input type="number" step="0.01" name="discount" id="discount"
                                    class="form-control @error('discount') is-invalid @enderror"
                                    value="{{ old('discount') }}" placeholder="Enter discount percentage" required>
                                @error('discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Earn Points -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="points">Earn Points (Auto) <span style="color: red;">*</span></label>
                                <input type="text" name="points" id="points"
                                    class="form-control @error('points') is-invalid @enderror"
                                    value="{{ old('points') }}" placeholder="Auto calculated" readonly required>
                                @error('points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small id="points-status" class="text-success" style="display:none;">✅ Updated</small>
                                <small class="text-muted">Calculated as: (Profit Margin × Discount %) / 100</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Function to calculate points
    function calculatePoints() {
        let profit = parseFloat($('#profit_margin').val()) || 0;
        let discount = parseFloat($('#discount').val()) || 0;
        
        // Show loading state
        $('#points').val('Calculating...');
        $('#points-status').hide();
        
        // ✅ CORRECT CALCULATION:
        // Earn Points = (Profit Margin × Discount %) / 100
        // Example: 100 × 10 / 100 = 10 Points
        let earnPoints = (profit * discount) / 100;
        
        // Update the points field with the calculated value
        $('#points').val(earnPoints.toFixed(0) + ' Points'); // 0 decimal places
        $('#points-status').fadeIn(300).delay(1000).fadeOut(400);
    }
    
    // Calculate points when profit margin or discount changes
    $('#profit_margin, #discount').on('input', function() {
        calculatePoints();
    });
    
    // Also calculate on page load if values exist
    if ($('#profit_margin').val() || $('#discount').val()) {
        calculatePoints();
    }
    
    // Hide validation errors on focus
    $('input, select, textarea').on('focus', function() {
        const $feedback = $(this).parent().find('.invalid-feedback');
        if ($feedback.length) {
            $feedback.hide();
            $(this).removeClass('is-invalid');
        }
    });
});
</script>
@endsection