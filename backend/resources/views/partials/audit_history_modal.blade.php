<!-- Generic Audit History Modal -->
<div id="globalAuditHistoryModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-clock-rotate-left me-2"></i>Audit History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Changes</th>
                            </tr>
                        </thead>
                        <tbody id="globalAuditHistoryBody">
                            <tr>
                                <td colspan="4" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Generic History Button Click Handler
    // Simply attach the class '.show-audit-history-btn' and 'data-url' attribute to any button
    $(document).on('click', '.show-audit-history-btn', function(e) {
        e.preventDefault();
        
        var url = $(this).data('url');
        
        if (!url) {
            console.error('Audit History: No data-url provided on the button.');
            return;
        }
        
        $('#globalAuditHistoryBody').html('<tr><td colspan="4" class="text-center">Loading history...</td></tr>');
        $('#globalAuditHistoryModal').modal('show');

        $.ajax({
            url: url,
            type: "GET",
            success: function(response) {
                if (response.success) {
                    var html = '';
                    if (response.data.length > 0) {
                        $.each(response.data, function(index, log) {
                            var badgeClass = 'primary';
                            if (log.action === 'Created') badgeClass = 'success';
                            if (log.action === 'Updated') badgeClass = 'info';
                            if (log.action === 'Deleted') badgeClass = 'danger';

                            html += '<tr>' +
                                '<td><span class="badge bg-inverse-' + badgeClass + '">' + log.action + '</span></td>' +
                                '<td>' + log.user + '</td>' +
                                '<td>' + log.date + '</td>' +
                                '<td>' + log.changes + '</td>' +
                                '</tr>';
                        });
                    } else {
                        html = '<tr><td colspan="4" class="text-center">No history found for this record.</td></tr>';
                    }
                    $('#globalAuditHistoryBody').html(html);
                } else {
                    $('#globalAuditHistoryBody').html('<tr><td colspan="4" class="text-center text-danger">Failed to load history.</td></tr>');
                }
            },
            error: function(xhr) {
                $('#globalAuditHistoryBody').html('<tr><td colspan="4" class="text-center text-danger">Failed to load history.</td></tr>');
                showToast('Something went wrong loading history', 'error');
            }
        });
    });
});
</script>
@endpush
