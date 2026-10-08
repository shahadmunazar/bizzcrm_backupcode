@extends('layouts.main')

@section('title', 'System Debug & Logs')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="page-title">System Debug & Scheduler Logs</h4>
                        </div>
                        <div class="col-auto">
                            <button onclick="location.reload()" class="btn btn-secondary"><i class="ti ti-refresh"></i> Refresh Page</button>
                        </div>
                    </div>
                </div>

                <!-- Schedule List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Scheduled Tasks Status</h5>
                    </div>
                    <div class="card-body">
                        <pre class="bg-dark text-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">{{ $scheduleList }}</pre>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title">Quick Actions (Run Manually)</h5>
                    </div>
                    <div class="card-body">
                        <button onclick="runCustomCommand('api:fetch')" class="btn btn-primary me-2">Run API Fetch</button>
                        <button onclick="runCustomCommand('attendance:check-logs')" class="btn btn-info me-2">Sync Attendance Logs</button>
                        <button onclick="runCustomCommand('queue:restart')" class="btn btn-warning">Restart Queue</button>
                        
                        <div id="commandOutput" class="mt-3" style="display:none;">
                            <h6>Command Output:</h6>
                            <pre id="outputPre" class="bg-light p-3 border rounded"></pre>
                        </div>
                    </div>
                </div>

                <!-- Cache Locks -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title">Active Database Locks (Mutex)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Key</th>
                                        <th>Owner</th>
                                        <th>Expires At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($locks as $lock)
                                    <tr>
                                        <td><code>{{ $lock->key }}</code></td>
                                        <td>{{ $lock->owner }}</td>
                                        <td>{{ date('Y-m-d H:i:s', $lock->expiration) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No active locks found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Laravel Logs -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Recent Laravel Logs (Last 100 lines)</h5>
                        <button onclick="clearLog()" class="btn btn-sm btn-outline-danger">Clear Logs</button>
                    </div>
                    <div class="card-body">
                        <pre id="logArea" class="bg-dark text-success p-3 rounded" style="font-size: 12px; height: 500px; overflow-y: auto;">{{ $logs }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function runCustomCommand(cmd) {
        if(!confirm('Are you sure you want to run ' + cmd + '?')) return;
        
        const btn = event.target;
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Running...';
        
        fetch("{{ url('/system-debug/run') }}/" + cmd)
            .then(res => res.json())
            .then(data => {
                document.getElementById('commandOutput').style.display = 'block';
                document.getElementById('outputPre').innerText = data.output || data.message;
                btn.disabled = false;
                btn.innerText = originalText;
                if(data.status === 'success') {
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message, 'danger');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerText = originalText;
                alert('Error running command');
            });
    }

    function clearLog() {
        if(!confirm('This will truncate the log file. Continue?')) return;
        // Simplified: point to the clear-all-caches which we updated
        window.location.href = "{{ url('/clear-all-caches') }}";
    }

    // Auto-scroll logs to bottom
    document.addEventListener('DOMContentLoaded', function() {
        const logArea = document.getElementById('logArea');
        logArea.scrollTop = logArea.scrollHeight;
    });
</script>
@endsection
