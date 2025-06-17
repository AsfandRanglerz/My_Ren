@extends('admin.layout.app')
@section('title', 'Create Sub Admin')
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ url('admin/subadmin') }}">Back</a>
                <form id="add_subadmin" action="{{ route('subadmin.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="card">
                                <h4 class="text-center my-4">Add SubAdmin</h4>
                                <div class="row mx-0 px-4">

                                    <!-- Name Field -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}" required
                                                placeholder="Enter name">
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
                                                id="email" name="email" value="{{ old('email') }}" required
                                                placeholder="Enter email">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Phone Field -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                id="phone" name="phone" value="{{ old('phone') }}" required
                                                placeholder="Enter phone number">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Password Field -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group position-relative">
                                            <label for="password">Password</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" id="password"
                                                name="password" required placeholder="Enter password">
                                            <span class="fa fa-eye position-absolute"
                                                style="top: 42px; right: 15px; cursor: pointer;"
                                                onclick="togglePassword()"></span>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Image Upload -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="image">Image</label>
                                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                                id="image" name="image" required>
                                            <small class="text-danger">Note: Maximum image size allowed is 2MB</small>
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Role Field -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3" style="margin-left: 28px;">
                                        <div class="form-group">
                                            <label for="role">Select Role</label>
                                            <select class="form-control @error('role') is-invalid @enderror" id="role"
                                                name="role" required>
                                                <option value="">-- Select Role --</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ old('role') == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="card-footer text-center row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 btn-bg"
                                                id="submit">Save</button>
                                        </div>
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

        // Hide validation errors on focus
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    const feedback = this.parentElement.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.style.display = 'none';
                        this.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
@endsection
