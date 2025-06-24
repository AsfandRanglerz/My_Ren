@extends('admin.layout.app')
@section('title', 'Edit FAQ')
@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <form action="{{ url('admin/faq-update', $data->id) }}" method="POST">
                    @csrf

                    <a href="{{ url('/admin/faq-index') }}" class="btn mb-3" style="background: #cb84fe;">Back</a>
                    <div class="row">

                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Edit FAQ</h4>
                                </div>

                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Question <span style="color: red;">*</span></label>
                                        <input name="questions" class="form-control" value="{{ $data->questions }}">
                                        {{-- <input name="questions" class="form-control"> --}}
                                        @error('question')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Answer <span style="color: red;">*</span></label>
                                        <textarea name="description" class="form-control">
                                            @if ($data)
{{ $data->description }}
@endif
                                            
                                        </textarea>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary mr-1" type="submit">Save Changes</button>
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
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('description');
    </script>
@endsection
