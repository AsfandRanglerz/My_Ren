@extends('admin.layout.app')
@section('title', 'Create Blogs')
@section('content')

    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ url('admin/blogs-index') }}">Back</a>

                <!-- Corrected POST route to user.create -->
                <form id="edit_farmer" action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="card">
                                <h4 class="text-center my-4">Create Blog</h4>
                                <div class="row mx-0 px-4">

                                    <!-- Name -->
                                    <!-- Title -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" id="title" name="title"
                                                value="{{ old('title') }}" placeholder="Enter title" required autofocus>
                                            @error('title')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Slug -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="slug">Slug</label>
                                            <input type="text" class="form-control" id="slug" name="slug"
                                                value="{{ old('slug') }}" placeholder="slug" required>
                                            @error('slug')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>


                                    <!-- Image -->
                                    <div class="col-sm-12 pl-sm-0 pr-sm-3 w-100">
                                        <div class="form-group">
                                            <label for="Image">Image</label>
                                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                                id="conent" name="image" placeholder="Enter Image" required autofocus>

                                            <!-- Small red note about 2MB limit -->
                                            <small class="text-danger">Note: Maximum image size allowed is 2MB</small>

                                            @error('image')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>


                                    <!-- Description -->
                                    <div class="col-sm-12 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="email">Description</label>
                                            <textarea type="text" class="form-control" id="content" name="content" placeholder="Enter Description" required
                                                autofocus>
                                            </textarea>
                                            @error('description')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="card-footer text-center row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mr-1 btn-bg">Save</button>
                                    </div>
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
        CKEDITOR.replace('content');
    </script>
    <script>
        function slugify(text) {
            return text
                .toString()
                .toLowerCase()
                .replace(/\s+/g, '-') // Replace spaces with -
                .replace(/[^\w\-]+/g, '') // Remove all non-word chars
                .replace(/\-\-+/g, '-') // Replace multiple - with single -
                .replace(/^-+/, '') // Trim - from start of text
                .replace(/-+$/, ''); // Trim - from end of text
        }

        document.getElementById('title').addEventListener('input', function() {
            const title = this.value;
            document.getElementById('slug').value = slugify(title);
        });
    </script>
@endsection
