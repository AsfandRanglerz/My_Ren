@extends('admin.layout.app')

@section('title', 'Rankings')

@section('content')
    @php
        $type = $type ?? 'month'; // Default to 'month' if not set
    @endphp

    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div
                                class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                <h4 class="mb-2 mb-md-0">
                                    User Rankings - <span class="text-capitalize">{{ $type }}</span>
                                </h4>

                                <div class="mt-2 mt-md-0 d-flex align-items-center">
                                    <label for="rankingTypeSelect" class="me-2 mb-0" style="white-space: nowrap;"> <b>Filter
                                            By : </b></label>
                                    <select class="form-control form-control-sm" id="rankingTypeSelect"
                                        style="border-radius: 0;">
                                        <option value="monthly" {{ $type == 'monthly' ? 'selected' : '' }}>Monthly Rankings
                                        </option>
                                        <option value="yearly" {{ $type == 'yearly' ? 'selected' : '' }}>Yearly Rankings
                                        </option>
                                    </select>
                                </div>
                            </div>


                            <div class="card-body table-responsive table-bordered">
                                @if ($rankings->isEmpty())
                                    <div class="alert alert-info mb-0">
                                        No ranking data available for this {{ $type }}.
                                    </div>
                                @else
                                    <table class="table" id="table_id_rankings">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Name</th>
                                                <th>Installs (Products)</th>
                                                <th>Total Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rankings as $index => $user)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->products_count }}</td>
                                                    <td>{{ $user->points }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#table_id_rankings').DataTable({
                responsive: true,
                pageLength: 10,
                ordering: true,
                autoWidth: false
            });


            const urlParams = new URLSearchParams(window.location.search);
            if (!urlParams.has('type')) {
                window.location.href = "{{ route('ranking.index') }}?type=month";
            }


            $('#rankingTypeSelect').on('change', function() {
                const selected = $(this).val();
                window.location.href = "{{ route('ranking.index') }}?type=" + selected;
            });
        });
    </script>
@endsection
