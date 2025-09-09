@extends('admin.layout.app')

@section('title', 'Install Reward Create')

@section('content')



    <div class="main-content">

        <section class="section">

            <div class="section-body">

                <!-- <a class="btn btn-primary mb-3" href="{{ route('lntall-rewards.store') }}">Back</a> -->



                <form action="{{ route('lntall-rewards.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    <div class="card">

                        <h4 class="text-center my-4">Install Reward Create</h4>

                        <div class="row px-4">



                            <!-- day -->

                            <div class="col-sm-6">

                                <div class="form-group">

                                    <label for="target">Sale Target <span style="color: red;">*</span></label>

                                    <input type="number" name="target_sales" id="target"

                                        class="form-control @error('target_sales') is-invalid @enderror" value="{{ old('target_sales') }}"

                                        placeholder="Enter target for sale" required>

                                    @error('target_sales')

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>
                            <!-- Points -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="points">Points <span style="color: red;">*</span></label>
                                    <input type="number" name="points" id="points"
                                        class="form-control @error('points') is-invalid @enderror" value="{{ old('points') }}"
                                        placeholder="Enter points" required>
                                    @error('points')
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
        $(document).ready(function () {
    // Jb input, select, textarea pr dobara click (focus) ho
    $('input, select, textarea').on('focus', function () {
        const $feedback = $(this).siblings('.invalid-feedback'); // sibling lelo instead of parent().find()
        
        if ($feedback.length) {
            $feedback.fadeOut(200); // smoothly hide
            $(this).removeClass('is-invalid'); // red border hata do
        }
    });
});
    </script>
@endsection

