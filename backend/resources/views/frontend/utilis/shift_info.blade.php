
    <style>
        :root {
            --primary-color: #3b7ddd;
            --secondary-color: #6c757d;
            --success-color: #1cbb8c;
            --warning-color: #f7b84b;
            --danger-color: #f06548;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }

        body {
            background-color: #f8f9fa;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(to right, #2c6cc3, #3b7ddd);
            border: none;
            padding: 12px 24px;
            font-size: 1.1rem;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #2561b8, #3472d0);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(to right, #2c6cc3, #3b7ddd);
            color: white;
            padding: 20px;
            border-bottom: none;
        }

        .modal-title {
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-close {
            filter: invert(1);
        }

        .modal-body {
            padding: 0;
            max-height: 70vh;
            overflow-y: auto;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            padding: 20px;
        }

        .info-card {
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .info-card-header {
            background-color: #f8f9fa;
            padding: 12px 15px;
            border-bottom: 1px solid #e1e5eb;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px 8px 0 0;
            font-size: 0.95rem;
        }

        .info-card-body {
            padding: 15px;
            flex-grow: 1;
        }

        .info-item {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .info-label {
            font-weight: 600;
            min-width: 120px;
            color: var(--secondary-color);
            font-size: 0.85rem;
        }

        .info-value {
            flex-grow: 1;
            font-size: 0.85rem;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-enabled {
            background-color: rgba(28, 187, 140, 0.15);
            color: var(--success-color);
        }

        .status-disabled {
            background-color: rgba(108, 117, 125, 0.15);
            color: var(--secondary-color);
        }

        .time-badge {
            background-color: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: monospace;
            font-weight: 500;
            font-size: 0.8rem;
            border-left: 2px solid var(--primary-color);
        }

        .day-badge {
            background-color: rgba(59, 125, 221, 0.1);
            color: var(--primary-color);
            padding: 4px 8px;
            border-radius: 4px;
            margin-right: 5px;
            margin-bottom: 5px;
            display: inline-block;
            font-weight: 500;
            font-size: 0.75rem;
            border: 1px solid rgba(59, 125, 221, 0.2);
        }

        .modal-footer {
            border-top: 1px solid #e1e5eb;
            padding: 15px 20px;
        }

        /* Custom scrollbar for modal body */
        .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #c5c5c5;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        @media (max-width: 992px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <!-- Button to trigger modal -->
    {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#shiftInfoModal">
        <i class="bi bi-clock-history me-2"></i> View Shift Information
    </button> --}}

    <!-- Modal -->
    <div class="modal fade" id="shiftInfoModal" tabindex="-1" aria-labelledby="shiftInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shiftInfoModalLabel">
                        <i class="bi bi-clock-history"></i> Shift Information
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="info-grid">
                        <!-- Basic Information -->
                        <div class="info-card">
                            <div class="info-card-header">
                                <span><i class="bi bi-info-circle me-1"></i> Basic Information</span>
                            </div>
                            <div class="info-card-body">
                                <div class="info-item">
                                    <span class="info-label">Shift ID:</span>
                                    <span class="info-value">SH2023-001</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Code:</span>
                                    <span class="info-value">G-SHIFT</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Type:</span>
                                    <span class="info-value">General Shift</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Status:</span>
                                    <span class="info-value"><span class="status-badge status-enabled">Enabled</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Shift Time -->
                        <div class="info-card">
                            <div class="info-card-header">
                                <span><i class="bi bi-clock me-1"></i> Shift Time</span>
                            </div>
                            <div class="info-card-body">
                                <div class="info-item">
                                    <span class="info-label">In Time:</span>
                                    <span class="info-value"><span class="time-badge">09:00 AM</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Out Time:</span>
                                    <span class="info-value"><span class="time-badge">06:00 PM</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Total Hours:</span>
                                    <span class="info-value"><span class="time-badge">09:00 Hrs</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Rest Time -->
                        <div class="info-card">
                            <div class="info-card-header">
                                <span><i class="bi bi-egg-fried me-1"></i> Rest Time</span>
                                <span class="status-badge status-enabled">Enabled</span>
                            </div>
                            <div class="info-card-body">
                                <div class="info-item">
                                    <span class="info-label">Lunch Out:</span>
                                    <span class="info-value"><span class="time-badge">01:00 PM</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Lunch In:</span>
                                    <span class="info-value"><span class="time-badge">02:00 PM</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Duration:</span>
                                    <span class="info-value"><span class="time-badge">01:00 Hr</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Short Breaks -->
                        <div class="info-card">
                            <div class="info-card-header">
                                <span><i class="bi bi-cup-hot me-1"></i> Short Breaks</span>
                                <span class="status-badge status-enabled">Enabled</span>
                            </div>
                            <div class="info-card-body">
                                <div class="info-item">
                                    <span class="info-label">Break 1 Out:</span>
                                    <span class="info-value"><span class="time-badge">11:00 AM</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Break 1 In:</span>
                                    <span class="info-value"><span class="time-badge">11:15 AM</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Break 2 Out:</span>
                                    <span class="info-value"><span class="time-badge">04:00 PM</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Break 2 In:</span>
                                    <span class="info-value"><span class="time-badge">04:15 PM</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Total Break:</span>
                                    <span class="info-value"><span class="time-badge">00:30 Hrs</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Half Day -->
                        <div class="info-card">
                            <div class="info-card-header">
                                <span><i class="bi bi-calendar-day me-1"></i> Half Day</span>
                                <span class="status-badge status-disabled">Disabled</span>
                            </div>
                            <div class="info-card-body">
                                <div class="info-item">
                                    <span class="info-label">Start Time:</span>
                                    <span class="info-value"><span class="time-badge">-</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">End Time:</span>
                                    <span class="info-value"><span class="time-badge">-</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Duration:</span>
                                    <span class="info-value"><span class="time-badge">-</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Half Day From:</span>
                                    <span class="info-value">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Deduction Details -->
                        <div class="info-card">
                            <div class="info-card-header">
                                <span><i class="bi bi-exclamation-circle me-1"></i> Deduction Rules</span>
                            </div>
                            <div class="info-card-body">
                                <div class="info-item">
                                    <span class="info-label">Absent If LT:</span>
                                    <span class="info-value"><span class="time-badge">04:00 Hrs</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Half Day If LT:</span>
                                    <span class="info-value"><span class="time-badge">06:00 Hrs</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Short Leave If LT:</span>
                                    <span class="info-value"><span class="time-badge">08:00 Hrs</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Overtime -->
                        <div class="info-card">
                            <div class="info-card-header">
                                <span><i class="bi bi-alarm me-1"></i> Overtime</span>
                                <span class="status-badge status-enabled">Enabled</span>
                            </div>
                            <div class="info-card-body">
                                <div class="info-item">
                                    <span class="info-label">Max Allowed:</span>
                                    <span class="info-value"><span class="time-badge">03:00 Hrs</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Weekly Off -->
                        <div class="info-card">
                            <div class="info-card-header">
                                <span><i class="bi bi-calendar-week me-1"></i> Weekly Off</span>
                            </div>
                            <div class="info-card-body">
                                <div class="info-item">
                                    <span class="info-label">Off Days:</span>
                                    <span class="info-value">
                                        <span class="day-badge">Sunday</span>
                                        <span class="day-badge">Saturday</span>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Sat Offs:</span>
                                    <span class="info-value">
                                        <span class="day-badge">2nd</span>
                                        <span class="day-badge">4th</span>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Sat Half Days:</span>
                                    <span class="info-value">
                                        <span class="day-badge">1st</span>
                                        <span class="day-badge">3rd</span>
                                        <span class="day-badge">5th</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
