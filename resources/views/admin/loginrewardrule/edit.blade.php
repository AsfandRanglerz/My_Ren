@extends('admin.layout.app')

@section('title', 'Edit Install Rewards')

@section('content')



    <div class="main-content">

        <section class="section">

            <div class="section-body">

                <a class="btn btn-primary mb-3" href="{{ route('intall-rewards.index') }}">Back</a>



                <form action="{{ route('lntall-rewards.update', $data->id) }}" method="POST"

                    enctype="multipart/form-data">

                    @csrf

                    @method('POST')



                    <div class="card">

                        <h4 class="text-center my-4">Install Reward Edit</h4>

                        <div class="row px-4">



                            <!-- Day Field -->

                            <div class="col-sm-6">

                                <div class="form-group">

                                    <label for="target">Sale Target <span style="color: red;">*</span></label>

                                    <input type="text" name="target_sales" id="target_sales" value="{{ $data->target_sales }}"

                                        
                                    class="form-control @error('target_sales') is-invalid @enderror" placeholder="Enter target for sale"

                                         required>

                                    @error('target_sales')

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>



                            <!-- Points Field -->

                            <div class="col-sm-6">

                                <div class="form-group">

                                    <label for="points">Points <span style="color: red;">*</span></label>

                                    <input type="number" name="points" id="points" 

                                        class="form-control @error('points') is-invalid @enderror"

                                        value="{{ old('points', $data->points ?? '') }}" placeholder="Enter points"

                                        required>



                                    @error('points')

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror



                                    <div id="custom-error" style="color: red; display: none;">


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

       $(document).ready(function () {

    
    $('#target_sales').on('focus input', function () {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').hide(); 
    });

    $('#points').on('focus input', function () {
        $(this).removeClass('is-invalid'); // red border ختم
        $(this).siblings('.invalid-feedback').hide(); // error text hide
    });

});

    </script>

@endsection

