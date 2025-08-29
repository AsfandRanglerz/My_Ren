@extends('admin.layout.app')

@section('title', 'Create User')

@section('content')



    <div class="main-content">

        <section class="section">

            <div class="section-body">

                <a class="btn btn-primary mb-3" href="{{ route('user.index') }}">Back</a>


             <!-- form  -->
                <form id="edit_farmer" action="{{ route('user.create') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    <div class="row">

                        <div class="col-12 col-md-12 col-lg-12">

                            <div class="card">

                                <h4 class="text-center my-4">Create User</h4>

                                <div class="row mx-0 px-4">



                                    <!-- Name -->

                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">

                                        <div class="form-group">

                                            <label for="name">Name <span style="color: red;">*</span></label>

                                            <input type="text" class="form-control @error('name') is-invalid @enderror"

                                                id="name" name="name" value="{{ old('name') }}"

                                                placeholder="Enter name" required autofocus>

                                            @error('name')

                                                <div class="invalid-feedback">{{ $message }}</div>

                                            @enderror

                                        </div>

                                    </div>



                                    <!-- Email -->

                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">

                                        <div class="form-group">

                                            <label for="email">Email <span style="color: red;">*</span></label>

                                            <input type="email" class="form-control @error('email') is-invalid @enderror"

                                                id="email" name="email" value="{{ old('email') }}"

                                                placeholder="example@gmail.com" required autofocus>

                                            @error('email')

                                                <div class="invalid-feedback">{{ $message }}</div>

                                            @enderror

                                        </div>

                                    </div>



                                    <!-- Phone -->

                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">

                                        <div class="form-group">

                                            <label for="phone">Phone <span style="color: red;">*</span></label>

                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"

                                                id="phone" name="phone" value="{{ old('phone') }}"

                                                placeholder="Enter phone" required autofocus>

                                            @error('phone')

                                                <div class="invalid-feedback">{{ $message }}</div>

                                            @enderror

                                        </div>

                                    </div>



                                    <!-- Image -->

                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">

                                        <div class="form-group position-relative">

                                            <label for="image">Image <span style="color: red;">*</span></label>

                                            <input type="file" class="form-control @error('image') is-invalid @enderror"

                                                id="image" name="image" placeholder="Enter image" required>

                                            @error('image')

                                                <div class="invalid-feedback">{{ $message }}</div>

                                            @enderror

                                        </div>

                                    </div>

                                    <!-- Password -->

                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">

                                        <div class="form-group position-relative">

                                            <label for="password">Password (Optional)</label>

                                            <input type="password"

                                                class="form-control @error('password') is-invalid @enderror" id="password"

                                                name="password" placeholder="Password">



                                            <span class="fa fa-eye position-absolute toggle-password"

                                                style="top: 42px; right: 15px; cursor: pointer;"></span>

                                        </div>



                                    </div>

                                </div>



                                <!-- Submit Button -->

                                <div class="card-footer text-center row">

                                    <div class="col-12">

                                        <button type="submit" class="btn btn-primary mr-1 btn-bg">Save</button>

                                    </div>

                                </div>



                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </section>

    </div>



@endsection



@section('js')

    @if (session('success'))

        <script>

            toastr.success('{{ session('success') }}');

        </script>

    @endif



    <script>

        // Toggle password visibility

        $(document).ready(function() {

            $('.toggle-password').on('click', function() {

                const $passwordInput = $('#password');

                const $icon = $(this);



                if ($passwordInput.attr('type') === 'password') {

                    $passwordInput.attr('type', 'text');

                    $icon.removeClass('fa-eye').addClass('fa-eye-slash');

                } else {

                    $passwordInput.attr('type', 'password');

                    $icon.removeClass('fa-eye-slash').addClass('fa-eye');

                }

            });

        });



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

