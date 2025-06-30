@extends('admin.layout.app')
@section('title', 'Create Product')
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ route('product.index') }}">Back</a>

                <form action="{{ url('admin/products-store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <h4 class="text-center my-4">Create Product</h4>
                        <div class="row px-4">

                            <!-- Name -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name">Name <span style="color: red;">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        placeholder="Enter product name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Points Per Sale -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="points">Points Per Sale <span style="color: red;">*</span></label>
                                    <input type="number" name="points" id="points"
                                        class="form-control @error('points') is-invalid @enderror"
                                        value="{{ old('points') }}" placeholder="Enter points" required>
                                    @error('points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Dimensions -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="demissions">Dimension <span style="color: red;">*</span></label>
                                    <input type="text" name="demissions" id="demissions"
                                        class="form-control @error('demissions') is-invalid @enderror"
                                        value="{{ old('demissions') }}" placeholder="Enter dimensions" required>
                                    @error('demissions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Image -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="image">Image <span style="color: red;">*</span></label>
                                    <input type="file" name="image" id="image"
                                        class="form-control @error('image') is-invalid @enderror" required>
                                    @error('image')
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
