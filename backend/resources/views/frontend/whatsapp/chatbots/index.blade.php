@extends('layouts.main')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <h4 class="page-title">WhatsApp Chatbots</h4>
                        </div>
                        <div class="col-8 text-end d-flex justify-content-end align-items-center gap-2">
                            <select class="form-select" id="project_filter" aria-label="Filter by Project" style="max-width: 250px;">
                                <option value="">All Projects</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                            <a href="{{ box_route('whatsapp.chatbots.create') }}{{ request('project_id') ? '?project_id='.request('project_id') : '' }}" class="btn btn-primary" id="btn-create-chatbot">
                                <i class="ti ti-square-rounded-plus me-2"></i>Create New Chatbot
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="card">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="table-responsive custom-table">
                            <table class="table table-bordered table-striped" id="chatbots-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Project</th>
                                        <th>Trigger Type</th>
                                        <th>Trigger Value</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let table = $('#chatbots-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ box_route('whatsapp.chatbots.index') }}",
            data: function (d) {
                d.project_id = $('#project_filter').val();
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'project_name', name: 'project_name', orderable: false, searchable: false},
            {data: 'trigger_badge', name: 'trigger_type'},
            {data: 'trigger_value', name: 'trigger_value'},
            {data: 'status_badge', name: 'is_active', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    $('#project_filter').change(function() {
        table.ajax.reload();
        
        // Update the Create button URL with the selected project_id
        let projectId = $(this).val();
        let createUrl = "{{ box_route('whatsapp.chatbots.create') }}";
        if (projectId) {
            createUrl += "?project_id=" + projectId;
        }
        $('#btn-create-chatbot').attr('href', createUrl);
    });
});
</script>
@endpush
