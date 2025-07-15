@extends('admin.layout.app')

@section('title', 'Edit Device/Product')

@section('content')



    <div class="main-content">

        <section class="section">

            <div class="section-body">

                <a class="btn btn-primary mb-3" href="{{ url('admin/devices') }}">Back</a>



                <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    @method('POST') <!-- Correct method -->



                    <div class="card">

                        <h4 class="text-center my-4">Edit Device/Product</h4>

                        <div class="row px-4">



                            <!-- Name -->

                            <div class="col-sm-6">

                                <div class="form-group">

                                    <label for="name">Name <span style="color: red;">*</span></label>

                                    <input type="text" name="name" id="name"

                                        class="form-control @error('name') is-invalid @enderror"

                                        value="{{ old('name', $product->name) }}" required placeholder="Enter product name">

                                    @error('name')

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>







                            <!-- Points per Sale -->

                            <div class="col-sm-6">

                                <div class="form-group">

                                    <label for="points">Points per Sale <span style="color: red;">*</span></label>

                                    <input type="number" name="points" id="points"

                                        class="form-control @error('points') is-invalid @enderror"

                                        value="{{ old('points', $product->points_per_sale) }}" placeholder="Enter points"

                                        required>

                                    @error('points')

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>



                            <!-- Demissions -->

                            <div class="col-sm-6">

                                <div class="form-group">

                                    <label for="demissions">Dimension <span style="color: red;">*</span></label>

                                    <input type="text" name="demissions" id="demissions"

                                        class="form-control @error('demissions') is-invalid @enderror"

                                        value="{{ old('demissions', $product->demissions) }}" placeholder="Enter dimensions"

                                        required>

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

                                        class="form-control @error('image') is-invalid @enderror"

                                        placeholder="Upload image">

                                    @error('image')

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror



                                    <img src="{{ !empty($product->image) && file_exists(public_path($product->image)) ? asset('public/' . $product->image) : asset('public/admin/assets/images/default.png') }}"

                                        alt="product image" style="width: 100px; height: 100px; object-fit: cover;"

                                        class="mt-2">

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



@section('js')

    @if (session('message'))

        <script>

            toastr.success('{{ session('message') }}');

        </script>

    @endif



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

