@extends('admin.layout.app')
@section('title', 'Privacy Policy')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <form id="privacyPolicyForm" action="{{ url('admin/privacy-policy-update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Privacy Policy</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Description<span style="color: red;">*</span></label>
                                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">
                                            {{ old('description', $data->description ?? '') }}
                                        </textarea>
                                        @error('description')
                                            <div class="invalid-feedback d-block" id="description-error">{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary mr-1">Save Changes</button>
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
    <!-- Toastr Alerts -->
    @if (session('success'))
        <script>
            toastr.success('{{ session('success') }}');
        </script>
    @endif

    @if (session('message'))
        <script>
            toastr.success('{{ session('message') }}');
        </script>
    @endif

    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script>
        const editor = CKEDITOR.replace('description');

        // Hide validation error when focusing CKEditor
        editor.on('focus', function() {
            const errorEl = document.getElementById('description-error');
            if (errorEl) {
                errorEl.style.display = 'none';
            }
        });
    </script>
@endsection

@section('css')
    <style>
        .ck-editor__editable {
            min-height: 300px;
        }

        .text-danger,
        .invalid-feedback {
            font-size: 0.875em;
        }
    </style>
@endsection
