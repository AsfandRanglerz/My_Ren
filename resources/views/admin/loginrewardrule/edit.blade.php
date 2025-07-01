@extends('admin.layout.app')
@section('title', 'Edit Reward Settings')
@section('content')

    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ route('login-reward-rules.index') }}">Back</a>

                <form action="{{ route('login-reward-rules.update', $data->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="card">
                        <h4 class="text-center my-4">Reward Rule Edit</h4>
                        <div class="row px-4">

                            <!-- Day Field -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="day">Day</label>
                                    <input type="text" name="day" id="day" value="{{ $data->day }}"
                                        class="form-control @error('day') is-invalid @enderror" placeholder="Enter day"
                                        readonly required>
                                    @error('day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Points Field -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="points">Points <span style="color: red;">*</span></label>
                                    <input type="number" name="points" id="points" max="999"
                                        class="form-control @error('points') is-invalid @enderror"
                                        value="{{ old('points', $data->points ?? '') }}" placeholder="Enter points"
                                        required>

                                    @error('points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <div id="custom-error" style="color: red; display: none;">
                                        Please enter a number between 1 and 999
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="card-footer text-center w-100">
                                <button type="submit" class="btn btn-primary" id="submitBtn">Save Changes</button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

    {{-- Custom jQuery Validation --}}
    <script>
        $(document).ready(function() {
            function validatePoints() {
                let value = $('#points').val();
                let numericValue = parseInt(value);
                let isValid = value && numericValue >= 1 && numericValue <= 999;

                if (!isValid) {
                    $('#custom-error').show().text('Please enter a number between 1 and 999');
                    $('#points').addClass('is-invalid');
                    $('#submitBtn').prop('disabled', true);
                } else {
                    $('#custom-error').hide();
                    $('#points').removeClass('is-invalid');
                    $('#submitBtn').prop('disabled', false);
                }

                return isValid;
            }

            $('#points').on('input', validatePoints);
            $('#points').on('focus', function() {
                $('#custom-error').hide();
                $('#points').removeClass('is-invalid');
                $('#submitBtn').prop('disabled', false);
            });
            $('#points').on('blur', validatePoints);

            // Initial validation on page load
            validatePoints();
        });
    </script>
@endsection
