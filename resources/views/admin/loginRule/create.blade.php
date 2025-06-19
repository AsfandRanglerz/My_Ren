@extends('admin.layout.app')
@section('title', 'Create Login points Rule')
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
                                <h4 class="text-center my-4">Create Login points Rule</h4>
                                <div class="row mx-0 px-4">
                                    <!-- Consecutive days -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="Consecutive_days">Consecutive days</label>
                                            <input type="text" class="form-control" id="Consecutive_days"
                                                name="Consecutive_days" value="{{ old('Consecutive_days') }}" required
                                                placeholder="Enter Consecutive days">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <!-- Points -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="points">Points</label>
                                            <input type="text" class="form-control" id="points" name="points"
                                                value="{{ old('points') }}" required placeholder="Enter points value">
                                            <div class="invalid-feedback"></div>
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
    @if (\Illuminate\Support\Facades\Session::has('message'))
        <script>
            toastr.success('{{ \Illuminate\Support\Facades\Session::get('message') }}');
        </script>
    @endif
@endsection
