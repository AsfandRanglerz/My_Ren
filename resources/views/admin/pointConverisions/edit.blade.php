@extends('admin.layout.app')
@section('title', 'Edit Points Conversion')
@section('content')

    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ route('point-conversions.index') }}">Back</a>

                <form action="{{ url('admin/point-conversions-update', $conversion->id) }}" method="POST">
                    @csrf
                    <div class="card">
                        <h4 class="text-center my-4">Edit Points Conversion</h4>
                        <div class="row px-4">
                            <!-- Points -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="points">Points <span style="color: red;">*</span></label>
                                    <input type="number" name="points" id="points"
                                        class="form-control @error('points') is-invalid @enderror"
                                        value="{{ $conversion->points }}" placeholder="Enter points" required>
                                    @error('points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="price">Price <span style="color: red;">*</span></label>
                                    <div class="input-group">

                                        <input type="number" step="0.01" name="price" id="price"
                                            class="form-control @error('price') is-invalid @enderror"
                                            value="{{ $conversion->price }}" placeholder="Enter price" required>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">PKR</span>
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card-footer text-center">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

@endsection
