@extends('admin.layout.app')
@section('title', 'Edit User')
@section('content')

    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ url('admin/user') }}">Back</a>

                <form id="edit_farmer" action="{{ route('user.update', $user->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST') <!-- Correct method for updating -->

                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="card">
                                <h4 class="text-center my-4">Edit User</h4>
                                <div class="row mx-0 px-4">

                                    <!-- Name Field -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $user->name) }}"
                                                placeholder="Enter your name" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Email Field -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email" value="{{ old('email', $user->email) }}"
                                                placeholder="example@gmail.com" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Phone Field -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                                placeholder="Enter your phone" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Password Field (optional) -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group position-relative">
                                            <label for="password">Password</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" id="password"
                                                name="password" placeholder="Leave blank to keep current">
                                            <span class="fa fa-eye position-absolute"
                                                style="top: 42px; right: 15px; cursor: pointer;"
                                                onclick="togglePassword()"></span>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                                <!-- Submit Button -->
                                <div class="card-footer text-center row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mr-1 btn-bg" id="submit">Save
                                            Changes</button>
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
    @if (session('message'))
        <script>
            toastr.success('{{ session('message') }}');
        </script>
    @endif

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = event.target;
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Hide validation error on input focus
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    const errorDiv = this.parentElement.querySelector('.invalid-feedback');
                    if (errorDiv) {
                        errorDiv.style.display = 'none';
                        this.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
@endsection
