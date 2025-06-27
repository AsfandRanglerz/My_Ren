@extends('admin.layout.app')
@section('title', 'Create Role')
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ url('admin/roles') }}">Back</a>
                <form id="add_department" action="{{ route('store.role') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="card">
                                <h4 class="text-center my-4">Create Role</h4>
                                <div class="row mx-0 px-4">
                                    <!-- Name Field -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="name">Name <span style="color: red;">*</span></label>
                                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                                required placeholder="Enter Role Name"
                                                class="form-control @error('name') is-invalid @enderror">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="card-footer text-center row" style="margin-top: 1%;">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 btn-bg"
                                                id="submit">Save</button>
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
