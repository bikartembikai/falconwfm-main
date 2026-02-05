@extends('layouts.facilitator')

@section('title', 'Assigned Events')

@section('styles')
<style>
    /* Assignment Stats */
    .assignment-stats {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 32px;
    }

    .stat-card-compact {
        background: white;
        border-radius: 8px;
        padding: 16px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .stat-card-compact h4 {
        font-size: 12px;
        color: #999;
        margin-bottom: 8px;
        font-weight: 500;
        text-transform: capitalize;
    }

    .stat-card-compact .value {
        font-size: 32px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .stat-card-compact.pending .value { color: #f59e0b; }
    .stat-card-compact.accepted .value { color: #10b981; }
    .stat-card-compact.rejected .value { color: #ef4444; }
    .stat-card-compact.completed .value { color: #3b82f6; }

    /* Event Cards */
    .event-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }

    .event-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border-left: 5px solid #ddd;
        transition: all 0.3s ease;
    }

    .event-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .event-card.accepted {
        border-left-color: #10b981;
        background-color: #f0fdf4;
    }

    .event-card.pending {
        border-left-color: #f59e0b;
        background-color: #fffbf0;
    }

    .event-card.rejected {
        border-left-color: #ef4444;
        background-color: #fef2f2;
    }

    .event-card.completed {
        border-left-color: #3b82f6;
        background-color: #f0f9ff;
    }

    .event-card-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 12px;
    }

    .event-card-title {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
        flex: 1;
    }

    .event-card-status {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .event-card-status.accepted {
        color: #10b981;
    }

    .event-card-status.pending {
        color: #f59e0b;
    }

    .event-card-status.rejected {
        color: #ef4444;
    }

    .event-card-details {
        font-size: 13px;
        color: #666;
        margin-bottom: 12px;
        line-height: 1.6;
    }

    .event-card-detail-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .event-card-detail-row:last-child {
        margin-bottom: 0;
    }

    .event-card-detail-label {
        display: inline-block;
        width: 60px;
        color: #999;
        font-weight: 500;
    }

    .event-card-detail-value {
        color: #1a1a1a;
        font-weight: 500;
    }

    .event-card-dates {
        background-color: rgba(0, 0, 0, 0.03);
        padding: 12px;
        border-radius: 6px;
        margin: 12px 0;
        font-size: 12px;
        color: #666;
    }

    .event-card-actions {
        display: flex;
        gap: 8px;
        margin-top: 16px;
    }

    .event-card-actions .btn {
        flex: 1;
        padding: 10px 12px;
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

    .modal-description {
        font-size: 13px;
        line-height: 1.6;
        color: #666;
    }

    .modal-skills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .modal-skill-tag {
        background-color: #dbeafe;
        color: #1e40af;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .modal-footer .btn {
        padding: 10px 20px;
    }

    .modal-close-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #999;
        padding: 0;
    }

    /* Confirm Modal */
    .modal-confirm {
        text-align: center;
    }

    .modal-confirm .modal-header {
        text-align: center;
    }

    .modal-confirm .modal-body {
        text-align: center;
    }

    .modal-confirm .modal-footer {
        justify-content: center;
    }

    @media (max-width: 768px) {
        .assignment-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .event-cards-grid {
            grid-template-columns: 1fr;
        }

        .modal-content {
            width: 95%;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Event Assignments</h1>
    <p class="page-subtitle">View and respond to your event assignments</p>
</div>

<!-- Stats -->
<div class="assignment-stats">
    <div class="stat-card-compact">
        <h4>Total</h4>
        <div class="value">2</div>
    </div>
    <div class="stat-card-compact pending">
        <h4>Pending</h4>
        <div class="value">1</div>
    </div>
    <div class="stat-card-compact accepted">
        <h4>Accepted</h4>
        <div class="value">1</div>
    </div>
    <div class="stat-card-compact rejected">
        <h4>Rejected</h4>
        <div class="value">0</div>
    </div>
    <div class="stat-card-compact completed">
        <h4>Completed</h4>
        <div class="value">0</div>
    </div>
</div>

<!-- Search -->
<div style="margin-bottom: 24px;">
    <div class="search-box" style="max-width: 100%;">
        <span class="search-icon">🔍</span>
        <input type="text" placeholder="Search event...">
    </div>
</div>

<!-- Event Cards -->
<div class="event-cards-grid">
    <!-- Accepted Event -->
    <div class="event-card accepted">
        <div class="event-card-header">
            <div class="event-card-title">Annual Leadership Summit</div>
            <div class="event-card-status accepted">✓ Accepted</div>
        </div>
        <div class="event-card-details">
            <div class="event-card-detail-row">
                <span class="event-card-detail-label">📅 Date:</span>
                <span class="event-card-detail-value">2024-02-15</span>
            </div>
            <div class="event-card-detail-row">
                <span class="event-card-detail-label">🕐 Time:</span>
                <span class="event-card-detail-value">09:00</span>
            </div>
            <div class="event-card-detail-row">
                <span class="event-card-detail-label">📍 Location:</span>
                <span class="event-card-detail-value">Convention Center, Hall A</span>
            </div>
        </div>
        <div class="event-card-dates">
            <div>Assigned: 2024-01-01</div>
            <div>Responded: 2024-02-02</div>
        </div>
        <div class="event-card-actions">
            <button class="btn btn-secondary" onclick="showEventDetailsModal()">Details</button>
        </div>
    </div>

    <!-- Pending Event -->
    <div class="event-card pending">
        <div class="event-card-header">
            <div class="event-card-title">Digital Transformation Workshop</div>
            <div class="event-card-status pending">⏱ Pending</div>
        </div>
        <div class="event-card-details">
            <div class="event-card-detail-row">
                <span class="event-card-detail-label">📅 Date:</span>
                <span class="event-card-detail-value">2024-02-20</span>
            </div>
            <div class="event-card-detail-row">
                <span class="event-card-detail-label">🕐 Time:</span>
                <span class="event-card-detail-value">10:00</span>
            </div>
            <div class="event-card-detail-row">
                <span class="event-card-detail-label">📍 Location:</span>
                <span class="event-card-detail-value">Tech Hub Building, Room 301</span>
            </div>
        </div>
        <div class="event-card-dates">
            <div>Assigned: 2024-02-10</div>
        </div>
        <div class="event-card-actions">
            <button class="btn btn-secondary" onclick="showEventDetailsModal()">Details</button>
            <button class="btn btn-primary" onclick="showAcceptModal()">Accept</button>
            <button class="btn" style="background-color: #ef4444; color: white;" onclick="alert('Reject functionality')">Reject</button>
        </div>
    </div>
</div>

<!-- Accept Assignment Modal -->
<div id="acceptModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Accept Assignment</div>
        <div class="modal-body">
            <div class="modal-field">
                <label class="modal-label">Event:</label>
                <div class="modal-value">Digital Transformation Workshop</div>
            </div>
            <div class="modal-field">
                <label class="modal-label">Date & Time:</label>
                <div class="modal-value">2024-02-20 at 10:00</div>
            </div>
            <div class="modal-field">
                <label class="modal-label" style="margin-bottom: 12px;">Are you sure you want to <strong>accept</strong> this assignment?</label>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeAcceptModal()">Cancel</button>
            <button class="btn btn-primary" onclick="acceptAssignment()">Accept Assignment</button>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px;">
            <div class="modal-header" style="margin-bottom: 0;">Event Details</div>
            <button class="modal-close-btn" onclick="closeDetailsModal()">×</button>
        </div>
        <div class="modal-body">
            <p style="font-size: 13px; color: #666; margin-bottom: 20px;">Complete information about this event</p>
            
            <div class="modal-field">
                <label class="modal-label">Annual Leadership Summit</label>
            </div>

            <div class="modal-field">
                <label class="modal-label">📅 Date</label>
                <div class="modal-value">2024-02-15</div>
            </div>

            <div class="modal-field">
                <label class="modal-label">🕐 Time</label>
                <div class="modal-value">09:00</div>
            </div>

            <div class="modal-field">
                <label class="modal-label">📍 Venue</label>
                <div class="modal-value">Convention Center, Hall A</div>
            </div>

            <div class="modal-field">
                <label class="modal-label">Description</label>
                <div class="modal-description">
                    A comprehensive summit bringing together industry leaders to discuss emerging trends and best practices in modern workplace management.
                </div>
            </div>

            <div class="modal-field">
                <label class="modal-label">Skills Required</label>
                <div class="modal-skills">
                    <span class="modal-skill-tag">Leadership</span>
                    <span class="modal-skill-tag">Public Speaking</span>
                    <span class="modal-skill-tag">Strategic Planning</span>
                    <span class="modal-skill-tag">Industry Expertise</span>
                </div>
            </div>

            <div class="modal-field">
                <label class="modal-label">👔 Role Assigned</label>
                <div class="modal-value">Leadership Coach / Speaker</div>
            </div>

            <div class="modal-field">
                <label class="modal-label">Your Response</label>
                <div style="color: #10b981; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <span>✓</span> Accepted
                </div>
            </div>
        </div>

        <div class="modal-footer" style="justify-content: flex-end;">
            <button class="btn btn-secondary" onclick="closeDetailsModal()">Close</button>
        </div>
    </div>
</div>

<script>
function showAcceptModal() {
    document.getElementById('acceptModal').classList.add('active');
}

function closeAcceptModal() {
    document.getElementById('acceptModal').classList.remove('active');
}

function acceptAssignment() {
    alert('Assignment accepted!');
    closeAcceptModal();
}

function showEventDetailsModal() {
    document.getElementById('detailsModal').classList.add('active');
}

function closeDetailsModal() {
    document.getElementById('detailsModal').classList.remove('active');
}

// Close modal when clicking outside
window.onclick = function(event) {
    let acceptModal = document.getElementById('acceptModal');
    let detailsModal = document.getElementById('detailsModal');
    
    if (event.target === acceptModal) {
        acceptModal.classList.remove('active');
    }
    if (event.target === detailsModal) {
        detailsModal.classList.remove('active');
    }
}
</script>
@endsection
