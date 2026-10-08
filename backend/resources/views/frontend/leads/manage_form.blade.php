@extends('layouts.main')
@section('title', 'Manage Leads Form')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Manage Leads Form Fields</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Drag and drop rows to reorder fields. Toggle the visibility checkbox to show/hide fields on the leads list.</p>
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Field Label</th>
                                    <th>Database Column</th>
                                    <th>Visibility</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-fields">
                                @forelse($fields as $field)
                                    <tr data-id="{{ $field->id }}" class="sortable-row">
                                        <td>
                                            <i class="fas fa-grip-vertical text-muted handle" style="cursor: move;"></i>
                                            <input type="hidden" name="fields[]" value="{{ $field->id }}">
                                        </td>
                                        <td>{{ $field->label }}</td>
                                        <td>{{ $field->column_name }}</td>
                                        <td>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="field_{{ $field->id }}" class="check change-status" data-id="{{ $field->id }}" {{ $field->is_visible ? 'checked' : '' }}>
                                                <label for="field_{{ $field->id }}" class="checktoggle">checkbox</label>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No fields found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Sortable
            $("#sortable-fields").sortable({
                handle: ".handle",
                update: function(event, ui) {
                    var fields = [];
                    $("#sortable-fields tr").each(function(index) {
                        var id = $(this).data('id');
                        // Sequence starts from 1
                        fields.push({
                            id: id,
                            sequence: index + 1
                        });
                    });

                    // Send AJAX request to reorder
                    $.ajax({
                        url: "{{ route('admin.system-fields.reorder') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            fields: fields
                        },
                        success: function(response) {
                            if (response.status) {
                                // Optional: Show toast
                                // toastr.success(response.message);
                            }
                        },
                        error: function(xhr) {
                            alert('Error updating order');
                        }
                    });
                }
            });

            // Toggle Visibility
            $(".change-status").change(function() {
                var id = $(this).data('id');
                var isChecked = $(this).is(':checked');
                var status = isChecked ? 1 : 0;

                $.ajax({
                    url: "{{ route('fields.changefromStatus') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        system_id: id,
                        is_visible: status,
                        type: 'system'
                    },
                    success: function(response) {
                        if (response.status) {
                            // Optional: Show toast
                        } else {
                            // Revert checkbox if failed
                            // $(this).prop('checked', !isChecked);
                        }
                    },
                    error: function() {
                        alert('Error updating status');
                    }
                });
            });
        });
    </script>
@endpush
