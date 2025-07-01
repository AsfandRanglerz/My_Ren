@extends('admin.layout.app')

@section('title', 'Create Voucher')

@section('content')

    <div class="main-content">

        <section class="section">

            <div class="section-body">

                <a class="btn btn-primary mb-3" href="{{ url('admin/voucher-index') }}">Back</a>



                <form action="{{ route('voucher.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    <div class="card">

                        <h4 class="text-center my-4">Create Voucher</h4>

                        <div class="row px-4">



                            <!-- Required Points -->

                            <div class="col-sm-6">

                                <div class="form-group">

                                    <label for="points">Required Points <span style="color: red;">*</span></label>

                                    <input type="number" name="points" id="points"
                                        class="form-control @error('points') is-invalid @enderror"
                                        value="{{ old('points') }}" placeholder="Enter required points" required>

                                    @error('points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                </div>

                            </div>



                            <!-- Amount -->

                            <div class="col-sm-6">

                                <div class="form-group">

                                    <label for="amount">Amount <span style="color: red;">*</span></label>

                                    <input type="number" name="amount" id="amount"
                                        class="form-control @error('amount') is-invalid @enderror"
                                        value="{{ old('amount') }}" placeholder="Enter Amount" required>

                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
    <script>
        // Hide validation errors on focus

        $(document).ready(function() {

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
