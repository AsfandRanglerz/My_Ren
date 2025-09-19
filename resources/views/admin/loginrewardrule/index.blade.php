@extends('admin.layout.app')

@section('title', 'Rewards')

@section('content')





<div class="main-content" style="min-height: 562px;">

    <section class="section">

        <div class="section-body">

            <div class="row">

                <div class="col-12 col-md-12 col-lg-12">

                    <div class="card">

                        <div class="card-header">

                            <div class="col-12">

                                <h4>Rewards <small class="font-weight-bold text-danger">(Points will be awarded to users who achieve the sales target mentioned below.)</small></h4>

                            </div>

                        </div>

                        <div class="card-body table-striped table-bordered table-responsive">

                            @if (Auth::guard('admin')->check() ||
                                        ($sideMenuPermissions->has('Install Rewards') && $sideMenuPermissions['Install Rewards']->contains('create')))
                                    <a class="btn btn-primary mb-3 text-white"
                                        href="{{ url('admin/lntall-rewards-create') }}">Create</a>
                                @endif



                            <table class="table" id="table_id_events">
                                <thead>

                                    <tr>

                                        <th>Sr.</th>

                                        <th>Sale Targets</th>

                                        <th>Points</th>

                                        <th scope="col">Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($data as $data)
                                    <tr>

                                        <td>{{ $loop->iteration }}</td>

                                        <td>{{ $data->target_sales}}</td>

                                        <td>{{ $data->points }}</td>
                                        <td>
                                            <div class="d-flex ">
                                                @if (Auth::guard('admin')->check() ||
                                                ($sideMenuPermissions->has('Install Rewards') && $sideMenuPermissions['Install Rewards']->contains('edit')))
                                                <a href="{{ route('lntall-rewards.edit', $data->id) }}"
                                                    class="btn btn-primary me-2"
                                                    style="float: left; margin-right: 8px;"><span><i
                                                            class="fa fa-edit"></i></span></a>
                                                @endif
                                                @if (Auth::guard('admin')->check() || ($sideMenuPermissions->has('Install Rewards') && $sideMenuPermissions['Install Rewards']->contains('delete')))

                                                            <form id="delete-form-{{ $data->id }}"

                                                action="{{ route('lntall-rewards.destroy', $data->id) }}"

                                                method="POST">

                                                @csrf

                                                @method('DELETE')

                                                </form>
                                                <button class="show_confirm btn d-flex "

                                                    style="background-color: #cb84fe;"

                                                    data-form="delete-form-{{ $data->id }}" type="button">

                                                    <span><i class="fa fa-trash"></i></span>

                                                </button>

                                                @endif

                                        </td>

                        </div>

                        </td>

                        </tr>
                        @endforeach

                        </tbody>

                        </table>

                    </div>



                </div>

            </div>

        </div>

</div>

</section>

</div>



@endsection



@section('js')



<script type="text/javascript">
    $(document).ready(function() {

        $('#table_id_events').DataTable()

    })



    /// delete sweet Alert

    $('.show_confirm').click(function(event) {

        var formId = $(this).data("form");

        var form = document.getElementById(formId);

        event.preventDefault();

        swal({

                title: "Are you sure you want to delete this record?",

                text: "If you delete this Install Reward, it will be gone forever.",

                icon: "warning",

                buttons: true,

                dangerMode: true,

            })

            .then((willDelete) => {

                if (willDelete) {

                    // Send AJAX request

                    $.ajax({

                        url: form.action,

                        type: 'POST',

                        data: {

                            _method: 'DELETE',

                            _token: '{{ csrf_token() }}'

                        },

                        success: function(response) {



                            swal({

                                title: "Success!",

                                text: "Record deleted successfully!",

                                icon: "success",

                                button: false,

                                timer: 1000

                            }).then(() => {

                                location.reload();

                            });

                        },

                        error: function(xhr) {

                            swal("Error!", "Failed to delete record.", "error");

                        }

                    });

                }

            });

    });
</script>



@endsection