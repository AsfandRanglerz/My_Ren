@extends('admin.layout.app')
@section('title', 'Edit Terms & Conditions')
@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <form action="{{ url('admin/term-condition-update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Edit Terms & Conditions</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Description <span style="color: red;">*</span></label>
                                        <textarea name="description" class="form-control">
                                            @if ($data)
{{ $data->description }}
@endif

                                            
                                        </textarea>
                                        @error('description')
                                            <div class="invalid-feedback d-block" id="description-error">{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary mr-1" type="submit">
                                        Save Changes
                                    </button>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Normal input, select, and textarea fields
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

            // CKEditor handling
            if (typeof CKEDITOR === '') {
                for (const instanceName in CKEDITOR.instances) {
                    if (CKEDITOR.instances.hasOwnProperty(instanceName)) {
                        const editor = CKEDITOR.instances[instanceName];
                        editor.on('focus', function() {
                            const textarea = document.getElementById(editor.name);
                            const feedback = textarea.parentElement.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.style.display = 'none';
                                textarea.classList.remove('is-invalid');
                            }
                        });
                    }
                }
            }
        });
    </script>

@endsection
