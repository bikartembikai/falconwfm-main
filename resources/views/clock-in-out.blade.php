@extends('layouts.facilitator')

@section('title', 'Clock In / Clock Out')

@section('styles')
<style>
    /* Active Events Section */
    .active-events-section {
        margin-bottom: 40px;
    }

    .section-header {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #1a1a1a;
    }

    .event-card-clock {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .event-details-list {
        margin-bottom: 20px;
    }

    .event-detail-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        font-size: 13px;
        color: #666;
    }

    .event-detail-item strong {
        color: #1a1a1a;
    }

    .clock-status {
        background-color: #f0f0f0;
        padding: 12px 16px;
        border-radius: 8px;
        text-align: center;
        margin: 20px 0;
        font-size: 14px;
        font-weight: 500;
        color: #666;
    }

    .clock-status.clocked-in {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .clock-status.not-clocked {
        background-color: #f0f0f0;
        color: #666;
    }

    .clock-button {
        width: 100%;
        padding: 14px 20px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .clock-button.clock-in {
        background-color: #10b981;
        color: white;
    }

    .clock-button.clock-in:hover {
        background-color: #059669;
    }

    .clock-button.clock-out {
        background-color: #ef4444;
        color: white;
    }

    .clock-button.clock-out:hover {
        background-color: #dc2626;
    }

    /* Attendance History Table */
    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .attendance-table thead {
        background-color: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    .attendance-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #666;
    }

    .attendance-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 13px;
        color: #1a1a1a;
    }

    .attendance-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .attendance-table tbody tr:last-child td {
        border-bottom: none;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.completed {
        background-color: #dcfce7;
        color: #15803d;
    }

    .status-badge.clocked-in {
        background-color: #dbeafe;
        color: #0284c7;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.3s ease;
    }

    .modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-content {
        background-color: white;
        padding: 32px;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: slideUp 0.3s ease;
        text-align: center;
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 24px;
        color: #1a1a1a;
    }

    .modal-body {
        margin-bottom: 24px;
    }

    .modal-field {
        margin-bottom: 20px;
        text-align: left;
    }

    .modal-field:last-child {
        margin-bottom: 0;
    }

    .modal-label {
        font-size: 13px;
        font-weight: 600;
        color: #666;
        margin-bottom: 8px;
        display: block;
    }

    .modal-value {
        font-size: 14px;
        color: #1a1a1a;
    }

    .modal-question {
        font-size: 14px;
        color: #1a1a1a;
        line-height: 1.6;
    }

    .modal-footer {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .modal-footer .btn {
        padding: 10px 20px;
    }

    @media (max-width: 768px) {
        .attendance-table {
            font-size: 12px;
        }

        .attendance-table th,
        .attendance-table td {
            padding: 10px 8px;
        }

        .modal-content {
            width: 95%;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Clock In / Clock Out</h1>
    <p class="page-subtitle">Record your attendance for assigned events</p>
</div>

<!-- Active Events -->
<div class="active-events-section">
    <h2 class="section-header">Active Events</h2>
    
    <div class="event-card-clock">
        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: #1a1a1a;">Annual Leadership Summit</h3>
        
        <div class="event-details-list">
            <div class="event-detail-item">
                <span>📅 Date:</span>
                <strong>2024-02-15</strong>
            </div>
            <div class="event-detail-item">
                <span>🕐 Time:</span>
                <strong>09:00</strong>
            </div>
            <div class="event-detail-item">
                <span>📍 Location:</span>
                <strong>Convention Center, Hall A</strong>
            </div>
        </div>

        <div class="clock-status not-clocked">
            Not clocked in today
        </div>

        <button class="clock-button clock-in" onclick="showClockInModal()">
            🕐 Clock In
        </button>
    </div>
</div>

<!-- Attendance History -->
<div class="section">
    <h2 class="section-header">Attendance History</h2>

    <div style="overflow-x: auto;">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>EVENT</th>
                    <th>DATE</th>
                    <th>CLOCK IN</th>
                    <th>CLOCK OUT</th>
                    <th>HOURS</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Annual Leadership Summit</td>
                    <td>2024-02-15</td>
                    <td>09:15</td>
                    <td>17:30</td>
                    <td>8.3h</td>
                    <td><span class="status-badge completed">✓ Completed</span></td>
                </tr>
                <tr>
                    <td>Annual Leadership Summit</td>
                    <td>2026-01-27</td>
                    <td>09:23</td>
                    <td>-</td>
                    <td>-</td>
                    <td><span class="status-badge clocked-in">Clocked in</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Clock In Modal -->
<div id="clockInModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Confirm Clock In</div>
        <div class="modal-body">
            <div class="modal-field">
                <label class="modal-label">Event:</label>
                <div class="modal-value">Annual Leadership Summit</div>
            </div>
            <div class="modal-field">
                <label class="modal-label">Time:</label>
                <div class="modal-value" id="clockInTime">02:22 am</div>
            </div>
            <div class="modal-field">
                <p class="modal-question">Are you sure you want to in now?</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeClockInModal()">Cancel</button>
            <button class="btn btn-primary" style="background-color: #3b82f6;" onclick="confirmClockIn()">Confirm Clock In</button>
        </div>
    </div>
</div>

<!-- Clock Out Modal -->
<div id="clockOutModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Confirm Clock Out</div>
        <div class="modal-body">
            <div class="modal-field">
                <label class="modal-label">Event:</label>
                <div class="modal-value">Annual Leadership Summit</div>
            </div>
            <div class="modal-field">
                <label class="modal-label">Time:</label>
                <div class="modal-value" id="clockOutTime">05:45 pm</div>
            </div>
            <div class="modal-field">
                <p class="modal-question">Are you sure you want to out now?</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeClockOutModal()">Cancel</button>
            <button class="btn" style="background-color: #ef4444; color: white;" onclick="confirmClockOut()">Confirm Clock Out</button>
        </div>
    </div>
</div>

<script>
function showClockInModal() {
    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    document.getElementById('clockInTime').textContent = time;
    document.getElementById('clockInModal').classList.add('active');
}

function closeClockInModal() {
    document.getElementById('clockInModal').classList.remove('active');
}

function confirmClockIn() {
    alert('Successfully clocked in!');
    closeClockInModal();
    // Update UI to show "Clocked in at XX:XX"
    document.querySelector('.clock-status').textContent = 'Clocked in at 02:23';
    document.querySelector('.clock-status').classList.remove('not-clocked');
    document.querySelector('.clock-status').classList.add('clocked-in');
    document.querySelector('.clock-button').textContent = '🕐 Clock Out';
    document.querySelector('.clock-button').classList.remove('clock-in');
    document.querySelector('.clock-button').classList.add('clock-out');
    document.querySelector('.clock-button').onclick = showClockOutModal;
}

function showClockOutModal() {
    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    document.getElementById('clockOutTime').textContent = time;
    document.getElementById('clockOutModal').classList.add('active');
}

function closeClockOutModal() {
    document.getElementById('clockOutModal').classList.remove('active');
}

function confirmClockOut() {
    alert('Successfully clocked out!');
    closeClockOutModal();
}

// Close modal when clicking outside
window.onclick = function(event) {
    let clockInModal = document.getElementById('clockInModal');
    let clockOutModal = document.getElementById('clockOutModal');
    
    if (event.target === clockInModal) {
        clockInModal.classList.remove('active');
    }
    if (event.target === clockOutModal) {
        clockOutModal.classList.remove('active');
    }
}
</script>
@endsection
