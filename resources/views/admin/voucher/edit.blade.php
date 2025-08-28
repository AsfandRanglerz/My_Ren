@extends('admin.layout.app')

@section('title', 'Edit Voucher')

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ url('admin/voucher-index') }}">Back</a>

                <form action="{{ route('voucher.update', $voucher->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST') <!-- Using POST method as requested -->

                    <div class="card">
                        <h4 class="text-center my-4">Edit Voucher</h4>
                        <div class="row px-4">
                            <!-- Required Points -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="points">Required Points <span class="text-danger">*</span></label>
                                    <input type="number" name="points" id="points"
                                        class="form-control @error('points') is-invalid @enderror"
                                        value="{{ old('points', $voucher->required_points) }}"
                                        placeholder="Enter required points" required
                                        onfocus="this.nextElementSibling.style.display='none'; this.classList.remove('is-invalid')">
                                    @error('points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="amount">Rupees Off <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="rupees" id="amount"
                                            class="form-control @error('amount') is-invalid @enderror"
                                            value="{{ old('amount', $voucher->rupees) }}" placeholder="Enter Rupees"
                                            required
                                            onfocus="this.closest('.form-group').querySelector('.invalid-feedback').style.display='none'; this.classList.remove('is-invalid')">
                                        <div class="input-group-append">
                                            <span class="input-group-text">PKR</span>
                                        </div>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
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

@section('scripts')
    <script>
        // Alternative jQuery solution if needed
        $(document).ready(function() {
            $('input, select, textarea').on('focus', function() {
                const $input = $(this);
                // For points field
                if ($input.attr('name') === 'points') {
                    $input.next('.invalid-feedback').hide();
                }
                // For amount field
                if ($input.attr('name') === 'amount') {
                    $input.closest('.form-group').find('.invalid-feedback').hide();
                }
                $input.removeClass('is-invalid');
            });
        });
    </script>
@endsection
