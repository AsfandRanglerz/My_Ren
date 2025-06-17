@extends('admin.layout.app')
@section('title', 'Edit Blogs')
@section('content')

    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ url('admin/blogs-index') }}">Back</a>

                <form id="edit_blog" action="{{ route('blog.update', $data->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST') {{-- Important for PUT request --}}

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <h4 class="text-center my-4">Edit Blog</h4>
                                <div class="row mx-0 px-4">

                                    {{-- Title --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" id="title" name="title"
                                                value="{{ $data->title }}" placeholder="Enter title" required autofocus>
                                        </div>
                                    </div>

                                    {{-- Slug --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="slug">Slug</label>
                                            <input type="text" class="form-control" id="slug" name="slug"
                                                value="{{ $data->slug }}" placeholder="Slug" required>
                                        </div>
                                    </div>

                                    {{-- Image --}}
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="image">Image</label>
                                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                                id="image" name="image">

                                            <!-- 2MB size note in red -->
                                            <small class="text-danger" style="display: block;">Note: Maximum image size
                                                allowed is 2MB</small>

                                            @error('image')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror

                                            @if ($data->image)
                                                <img src="{{ asset('public/' . $data->image) }}" alt="Blog Image"
                                                    width="150" height="100" class="mt-3">
                                            @endif
                                        </div>
                                    </div>
                                    {{-- Description --}}
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="content">Description</label>
                                            <textarea class="form-control" id="content" name="content" rows="5" required>{{ $data->content }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Submit Button --}}
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
    {{-- CKEditor --}}
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('content');
    </script>

    {{-- Slug Generator --}}
    <script>
        function slugify(text) {
            return text
                .toString()
                .toLowerCase()
                .trim()
                .replace(/\s+/g, '-') // Replace spaces with -
                .replace(/[^\w\-]+/g, '') // Remove non-word chars
                .replace(/\-\-+/g, '-') // Replace multiple - with single -
                .replace(/^-+/, '') // Trim - from start
                .replace(/-+$/, ''); // Trim - from end
        }

        document.getElementById('title').addEventListener('input', function() {
            const title = this.value;
            document.getElementById('slug').value = slugify(title);
        });
    </script>
@endsection
