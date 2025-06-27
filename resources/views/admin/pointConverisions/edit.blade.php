@extends('admin.layout.app')
@section('title', 'Edit Points Conversion')
@section('content')

    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ route('point-conversions.index') }}">Back</a>

                <form action="{{ url('admin/point-conversions-update', $conversion->id) }}" method="POST">
                    @csrf
                    <div class="card">
                        <h4 class="text-center my-4">Edit Points Conversion</h4>
                        <div class="row px-4">
                            <!-- Points -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="points">Points <span style="color: red;">*</span></label>
                                    <input type="number" name="points" id="points"
                                        class="form-control @error('points') is-invalid @enderror"
                                        value="{{ $conversion->points }}" placeholder="Enter points" required>
                                    @error('points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="points-error" class="text-danger small" style="display: none;">
                                        Maximum 10 digits allowed
                                    </div>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="price">Price <span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="price" id="price"
                                            class="form-control @error('price') is-invalid @enderror"
                                            value="{{ $conversion->price }}" placeholder="Enter price" required>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">PKR</span>
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div id="price-error" class="text-danger small" style="display: none;">
                                            Maximum 10 digits allowed
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            <button type="submit" class="btn btn-primary" id="submitBtn">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <script>
        $(document).ready(function() {
            // Hide validation errors on focus
            $('input, select, textarea').on('focus', function() {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').hide();
            });

            // Validate points input (max 10 digits)
            $('#points').on('input', function() {
                validateNumberInput($(this), $('#points-error'));
            });

            // Validate price input (max 10 digits before decimal)
            $('#price').on('input', function() {
                validateDecimalInput($(this), $('#price-error'));
            });

            // Initial validation on page load
            validateNumberInput($('#points'), $('#points-error'));
            validateDecimalInput($('#price'), $('#price-error'));
        });

        function validateNumberInput(input, errorElement) {
            let value = input.val().toString();

            if (value.length > 10) {
                errorElement.show();
                input.addClass('is-invalid');
                $('#submitBtn').prop('disabled', true);
                input.val(value.slice(0, 10)); // Trim to 10 digits
            } else {
                errorElement.hide();
                input.removeClass('is-invalid');
                $('#submitBtn').prop('disabled', false);
            }
        }

        function validateDecimalInput(input, errorElement) {
            let value = input.val().toString();
            let parts = value.split('.');
            let integerPart = parts[0];

            if (integerPart.length > 10) {
                errorElement.show();
                input.addClass('is-invalid');
                $('#submitBtn').prop('disabled', true);
                input.val(value.slice(0, 10) + (parts[1] ? '.' + parts[1] : '')); // Trim integer part
            } else {
                errorElement.hide();
                input.removeClass('is-invalid');
                $('#submitBtn').prop('disabled', false);
            }
        }
    </script>
@endsection
