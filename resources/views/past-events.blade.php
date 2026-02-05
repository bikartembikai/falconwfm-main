@extends('layouts.facilitator')

@section('title', 'Past Events')

@section('content')
<div class="page-header">
    <h1 class="page-title">Past Events</h1>
    <p class="page-subtitle">View your event history and performance</p>
</div>

<!-- Stats -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-content">
            <h3>Total Events</h3>
            <div class="stat-value">5</div>
        </div>
        <div class="stat-icon">📅</div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <h3>Average Rating</h3>
            <div class="stat-value">4.8</div>
        </div>
        <div class="stat-icon">⭐</div>
    </div>
</div>

<!-- Search and Filter -->
<div class="section">
    <div class="search-filter-bar">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" placeholder="Search events by name or location...">
        </div>
        <button class="filter-btn">
            <span>⋯</span>
            All Types
        </button>
        <button class="filter-btn">
            <span>⋯</span>
            All Status
        </button>
    </div>

    <!-- Event 1 -->
    <div class="event-item">
        <div class="event-info">
            <div class="event-title">Leadership Workshop 2024</div>
            <div class="event-meta">
                <span class="meta-item">📅 Dec 15, 2024</span>
                <span class="meta-item">📍 Makati Business Center</span>
                <span class="meta-item">⏱️ 8 hours</span>
                <span class="meta-item">👥 45 attendees</span>
            </div>
            <div style="margin-top: 12px; font-style: italic; color: #666; font-size: 13px;">
                "Excellent facilitation skills and engagement with participants."
            </div>
        </div>
        <div class="event-actions">
            <div class="rating">
                <span>⭐</span> 4.8
            </div>
        </div>
    </div>

    <div style="height: 1px; background-color: #f0f0f0; margin: 20px 0;"></div>

    <!-- Event 2 -->
    <div class="event-item">
        <div class="event-info">
            <div class="event-title">Team Building Adventure</div>
            <div class="event-meta">
                <span class="meta-item">📅 Nov 28, 2024</span>
                <span class="meta-item">📍 Tagaytay Highlands</span>
                <span class="meta-item">⏱️ 2 days</span>
                <span class="meta-item">👥 60 attendees</span>
            </div>
            <div style="margin-top: 12px; font-style: italic; color: #666; font-size: 13px;">
                "Outstanding performance! Very professional and organized."
            </div>
        </div>
        <div class="event-actions">
            <div class="rating">
                <span>⭐</span> 5.0
            </div>
        </div>
    </div>

    <div style="height: 1px; background-color: #f0f0f0; margin: 20px 0;"></div>

    <!-- Event 3 -->
    <div class="event-item">
        <div class="event-info">
            <div class="event-title">Product Launch Event</div>
            <div class="event-meta">
                <span class="meta-item">📅 Nov 10, 2024</span>
                <span class="meta-item">📍 BGC Convention Center</span>
                <span class="meta-item">⏱️ 6 hours</span>
                <span class="meta-item">👥 120 attendees</span>
            </div>
            <div style="margin-top: 12px; font-style: italic; color: #666; font-size: 13px;">
                "Great content delivery and audience interaction."
            </div>
        </div>
        <div class="event-actions">
            <div class="rating">
                <span>⭐</span> 4.5
            </div>
        </div>
    </div>

    <div style="height: 1px; background-color: #f0f0f0; margin: 20px 0;"></div>

    <!-- Event 4 -->
    <div class="event-item">
        <div class="event-info">
            <div class="event-title">Skills Development Workshop</div>
            <div class="event-meta">
                <span class="meta-item">📅 Oct 22, 2024</span>
                <span class="meta-item">📍 Quezon City Training Center</span>
                <span class="meta-item">⏱️ 4 hours</span>
                <span class="meta-item">👥 35 attendees</span>
            </div>
            <div style="margin-top: 12px; font-style: italic; color: #666; font-size: 13px;">
                "Effective teaching methods and good classroom management."
            </div>
        </div>
        <div class="event-actions">
            <div class="rating">
                <span>⭐</span> 4.6
            </div>
        </div>
    </div>

    <div style="height: 1px; background-color: #f0f0f0; margin: 20px 0;"></div>

    <!-- Event 5 -->
    <div class="event-item">
        <div class="event-info">
            <div class="event-title">Corporate Retreat 2024</div>
            <div class="event-meta">
                <span class="meta-item">📅 Sep 15, 2024</span>
                <span class="meta-item">📍 Subic Bay Resort</span>
                <span class="meta-item">⏱️ 3 days</span>
                <span class="meta-item">👥 85 attendees</span>
            </div>
            <div style="margin-top: 12px; font-style: italic; color: #666; font-size: 13px;">
                "Engaging activities and good time management throughout the event."
            </div>
        </div>
        <div class="event-actions">
            <div class="rating">
                <span>⭐</span> 4.7
            </div>
        </div>
    </div>
</div>
@endsection
