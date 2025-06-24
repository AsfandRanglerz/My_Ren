@extends('admin.layout.app')
@section('title', 'Edit Reward Settings')
@section('content')

    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ route('login-reward-rules.index') }}">Back</a>

                <form action="{{ route('login-reward-rules.update', $data->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <h4 class="text-center my-4">Reward Rule Edit</h4>
                        <div class="row px-4">

                            <!-- day -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="day">Day</label>
                                    <input type="text" name="day" id="day" value="{{ $data->day }}"
                                        class="form-control @error('day') is-invalid @enderror" value="{{ old('day') }}"
                                        placeholder="Enter day" required readonly>
                                    @error('day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            <!-- Points -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="points">Points <span style="color: red;">*</span></label>
                                    <input type="number" name="points" id="points" value="{{ $data->points }}"
                                        class="form-control @error('points') is-invalid @enderror"
                                        value="{{ old('points') }}" placeholder="Enter points" required>
                                    @error('points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
