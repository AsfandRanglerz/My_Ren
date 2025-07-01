@extends('admin.layout.app')
@section('title', 'Login Reward Rule Create')
@section('content')

    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ route('login-reward-rules.index') }}">Back</a>

                <form action="{{ route('loginrewardrules.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <h4 class="text-center my-4">Reward Rule Create</h4>
                        <div class="row px-4">

                            <!-- day -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="day">Day</label>
                                    <input type="text" name="day" id="day"
                                        class="form-control @error('day') is-invalid @enderror" value="{{ old('day') }}"
                                        placeholder="Enter day" required>
                                    @error('day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            <!-- Points -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="points">Points</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror"
                                        id="image" name="image">
                                    {{-- <small class="text-danger">Note: Maximum image size allowed is 2MB</small> --}}
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
