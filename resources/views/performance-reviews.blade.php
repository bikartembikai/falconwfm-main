@extends('layouts.facilitator')

@section('title', 'Performance Reviews')

@section('content')
<div class="page-header">
    <h1 class="page-title">Performance Reviews</h1>
    <p class="page-subtitle">Rate and provide feedback for co-facilitators from recent events</p>
</div>

<!-- Stats -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-content">
            <h3>Total Reviews</h3>
            <div class="stat-value">4</div>
        </div>
        <div class="stat-icon">👤</div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <h3>Pending Reviews</h3>
            <div class="stat-value">3</div>
        </div>
        <div class="stat-icon">⏰</div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <h3>Completed Reviews</h3>
            <div class="stat-value">1</div>
        </div>
        <div class="stat-icon">✓</div>
    </div>
</div>

<!-- Events -->
<div class="section">
    <!-- Event 1 -->
    <div class="event-item" style="padding-bottom: 24px; border-bottom: 1px solid #f0f0f0;">
        <div class="event-info">
            <div class="event-title">Leadership Development Workshop</div>
            <div class="event-meta">
                <span class="meta-item">📅 January 28, 2024</span>
                <span class="meta-item">📍 Makati Business Center</span>
            </div>
        </div>
    </div>

    <!-- Facilitator 1 -->
    <div class="facilitator-item">
        <div class="facilitator-avatar" style="background-color: #4a7c59;">MC</div>
        <div class="facilitator-details">
            <div class="facilitator-name">Michael Chen</div>
            <div class="facilitator-role">Lead Facilitator</div>
            <div class="facilitator-email">michael.chen@example.com</div>
        </div>
        <div class="event-actions">
            <span class="badge pending">Pending</span>
            <button class="btn btn-primary">Submit Review</button>
        </div>
    </div>

    <!-- Facilitator 2 -->
    <div class="facilitator-item">
        <div class="facilitator-avatar" style="background-color: #6b5b95;">ED</div>
        <div class="facilitator-details">
            <div class="facilitator-name">Emma Davis</div>
            <div class="facilitator-role">Co-Facilitator</div>
            <div class="facilitator-email">emma.davis@example.com</div>
        </div>
        <div class="event-actions">
            <span class="badge pending">Pending</span>
            <button class="btn btn-primary">Submit Review</button>
        </div>
    </div>

    <div style="height: 1px; background-color: #f0f0f0; margin: 20px 0;"></div>

    <!-- Event 2 -->
    <div class="event-item" style="padding-bottom: 24px; border-bottom: 1px solid #f0f0f0;">
        <div class="event-info">
            <div class="event-title">Team Building Activity</div>
            <div class="event-meta">
                <span class="meta-item">📅 January 25, 2024</span>
                <span class="meta-item">📍 Tagaytay Highlands Resort</span>
            </div>
        </div>
    </div>

    <!-- Facilitator 3 -->
    <div class="facilitator-item">
        <div class="facilitator-avatar" style="background-color: #2a7a4f;">AR</div>
        <div class="facilitator-details">
            <div class="facilitator-name">Alex Rodriguez</div>
            <div class="facilitator-role">Support Facilitator</div>
            <div class="facilitator-email">alex.rodriguez@example.com</div>
        </div>
        <div class="event-actions">
            <span class="badge reviewed">Reviewed</span>
            <button class="btn btn-secondary" disabled style="opacity: 0.5; cursor: not-allowed;">Review Submitted</button>
        </div>
    </div>

    <!-- Facilitator 4 -->
    <div class="facilitator-item">
        <div class="facilitator-avatar" style="background-color: #c97c7c;">LW</div>
        <div class="facilitator-details">
            <div class="facilitator-name">Lisa Wang</div>
            <div class="facilitator-role">Co-Facilitator</div>
            <div class="facilitator-email">lisa.wang@example.com</div>
        </div>
        <div class="event-actions">
            <span class="badge pending">Pending</span>
            <button class="btn btn-primary">Submit Review</button>
        </div>
    </div>
</div>
@endsection
