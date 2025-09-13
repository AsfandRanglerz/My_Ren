@extends('admin.layout.app')

@section('title', 'Create SN Code')

@section('content')

    <div class="main-content">

        <section class="section">

            <div class="section-body">

                <a class="btn btn-primary mb-3" href="{{ route('product.detail', $product->id) }}">Back</a>

                <form id="add_department" action="{{ route('product.batch.store') }}" method="POST"

                    enctype="multipart/form-data">

                    @csrf

                    <div class="row">

                        <div class="col-12 col-md-12 col-lg-12">

                            <div class="card">

                                <h4 class="text-center my-4"> Create SN Code</h4>

                                <div class="row mx-0 px-4">

                                    <!-- Name Field -->

                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">

                                        <input type="hidden" name="product_id" value="{{ $id }}">



                                        <div class="form-group">

                                            <label for="name">SN Code <span style="color: red;">*</span></label>

                                            <input type="text"

                                                class="form-control  @error('scan_code') is-invalid @enderror "

                                                id="scan_code" name="scan_code" value="{{ old('name') }}" required

                                                placeholder="Enter SN Code">

                                            @error('scan_code')

                                                <div class="invalid-feedback">{{ $message }}</div>

                                            @enderror

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



    <script>

        // Hide validation errors on focus

        document.addEventListener('DOMContentLoaded', function() {

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

        });

    </script>

@endsection

