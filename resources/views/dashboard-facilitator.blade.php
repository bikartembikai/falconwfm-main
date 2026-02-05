@extends('layouts.facilitator')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Welcome, Sarah Johnson!</h1>
    <p class="page-subtitle">Here's your facilitator portal dashboard</p>
</div>

<!-- Stats -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-content">
            <h3>Total Assignments</h3>
            <div class="stat-value">0</div>
        </div>
        <div class="stat-icon">📅</div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <h3>Pending Response</h3>
            <div class="stat-value">0</div>
        </div>
        <div class="stat-icon">⏰</div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <h3>Pending Allowance</h3>
            <div class="stat-value">1</div>
        </div>
        <div class="stat-icon">📋</div>
    </div>
</div>

<!-- Quick Actions -->
<h2 class="quick-actions-title">Quick Actions</h2>
<div class="quick-actions">
    <a href="#" class="action-card">
        <div class="action-icon blue">⏱️</div>
        <div class="action-title">Clock In/Out</div>
        <div class="action-subtitle">Record your attendance</div>
    </a>
    <a href="#" class="action-card">
        <div class="action-icon purple">📅</div>
        <div class="action-title">View Assignments</div>
        <div class="action-subtitle">View event assignments</div>
    </a>
    <a href="#" class="action-card">
        <div class="action-icon orange">📄</div>
        <div class="action-title">Request Leave</div>
        <div class="action-subtitle">Apply for leave</div>
    </a>
    <a href="#" class="action-card">
        <div class="action-icon green">💰</div>
        <div class="action-title">Request Allowance</div>
        <div class="action-subtitle">Request expense allowance</div>
    </a>
</div>

<!-- Recent Assignments & Allowances -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- Recent Assignments -->
    <div class="section">
        <div class="section-title">
            <span class="section-icon">📅</span>
            Recent Assignments
        </div>
        <div class="empty-state">
            No assignments yet
        </div>
    </div>

    <!-- Recent Allowance Requests -->
    <div class="section">
        <div class="section-title">
            <span class="section-icon">💰</span>
            Recent Allowance Requests
        </div>
        <div class="event-item">
            <div class="event-info">
                <div class="event-title">Travel</div>
                <div class="event-meta">
                    <span class="meta-item">₱ 500</span>
                </div>
            </div>
            <div class="event-actions">
                <span class="badge pending">Pending</span>
            </div>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <button class="btn btn-primary">View All Allowances</button>
        </div>
    </div>
</div>
@endsection
