@extends('admin.layout.app')
@section('title', 'Edit Sub Admin')

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ url()->previous() }}">Back</a>
                <form id="edit_subadmin" action="{{ route('subadmin.update', $subAdmin->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="card">
                                <h4 class="text-center my-4">Edit SubAdmin</h4>
                                <div class="row mx-0 px-4">
                                    {{-- Name --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">
                                                Name <span style="color: red;">*</span>
                                            </label>

                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                name="name" id="name" value="{{ old('name', $subAdmin->name) }}"
                                                placeholder="Enter name">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email">Email <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control @error('email') is-invalid @enderror"
                                                name="email" id="email" value="{{ old('email', $subAdmin->email) }}"
                                                placeholder="Enter email">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Phone --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="phone">Phone <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                name="phone" id="phone" value="{{ old('phone', $subAdmin->phone) }}"
                                                placeholder="Enter phone">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>

                                    {{-- Role --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="role">Role <span style="color: red;">*</span></label>
                                            <select name="role" id="role"
                                                class="form-control @error('role') is-invalid @enderror">
                                                <option value="">-- Select Role --</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ old('role', $currentRoleId) == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Image --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="image">Image<span style="color: red;">*</span></label>
                                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                                name="image" id="image">
                                            {{-- <small class="text-danger">Note: Maximum size is 2MB</small> --}}
                                            {{-- @if ($subAdmin->image)
                                                <div class="mt-2">
                                                    <img src="{{ asset($subAdmin->image) }}" width="100">
                                                </div>
                                            @endif --}}
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Password Field -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group position-relative">
                                            <label for="password">Password (Optional)</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" id="password"
                                                name="password" placeholder="Password">
                                            <span class="fa fa-eye position-absolute"
                                                style="top: 42px; right: 15px; cursor: pointer;"
                                                onclick="togglePassword()"></span>

                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer text-center">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
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

        // Automatically hide error message on focus
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
