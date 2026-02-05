<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Facilitator Portal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .container-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 207px;
            background: linear-gradient(180deg, #2a7a4f 0%, #256541 100%);
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-logo {
            padding: 0 16px;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-logo-icon {
            width: 32px;
            height: 32px;
            background-color: #d4af37;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #2a7a4f;
            font-size: 14px;
        }

        .sidebar-logo-text {
            display: flex;
            flex-direction: column;
            font-size: 12px;
        }

        .sidebar-logo-text .main {
            font-weight: 600;
            font-size: 13px;
        }

        .sidebar-logo-text .sub {
            font-size: 11px;
            opacity: 0.8;
        }

        .sidebar-nav {
            list-style: none;
            margin-bottom: 40px;
        }

        .sidebar-nav li {
            margin: 8px 0;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 0 8px;
            border-radius: 8px;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .sidebar-nav a.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-left: 3px solid white;
            padding-left: 13px;
        }

        .sidebar-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .sidebar-user {
            padding: 0 16px;
            margin-top: auto;
            padding-top: 32px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-user-label {
            font-size: 11px;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sidebar-user-name {
            font-size: 14px;
            font-weight: 600;
            margin-top: 4px;
        }

        .sidebar-user-role {
            font-size: 12px;
            opacity: 0.7;
            margin-top: 2px;
        }

        .sidebar-buttons {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar-btn {
            padding: 10px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .sidebar-btn.primary {
            background-color: white;
            color: #2a7a4f;
        }

        .sidebar-btn.primary:hover {
            background-color: #f0f0f0;
        }

        .sidebar-btn.secondary {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-btn.secondary:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 207px;
            flex: 1;
            padding: 40px;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .page-subtitle {
            font-size: 14px;
            color: #666;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .stat-content h3 {
            font-size: 13px;
            color: #666;
            font-weight: 500;
            margin-bottom: 8px;
            text-transform: capitalize;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        /* Quick Actions */
        .quick-actions-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1a1a1a;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .action-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .action-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .action-icon {
            width: 56px;
            height: 56px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 16px;
            color: white;
        }

        .action-icon.blue { background-color: #4a90e2; }
        .action-icon.purple { background-color: #b84bdb; }
        .action-icon.orange { background-color: #ff8c00; }
        .action-icon.green { background-color: #2ecc71; }

        .action-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1a1a1a;
        }

        .action-subtitle {
            font-size: 12px;
            color: #999;
        }

        /* Section */
        .section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 24px;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
            font-size: 14px;
        }

        /* Event Item */
        .event-item {
            padding: 20px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .event-item:last-child {
            border-bottom: none;
        }

        .event-info {
            flex: 1;
        }

        .event-title {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 12px;
        }

        .event-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 13px;
            color: #666;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .event-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge.pending {
            background-color: #ffe8cc;
            color: #d97706;
        }

        .badge.completed {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .badge.reviewed {
            background-color: #d1fae5;
            color: #059669;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #2a7a4f;
            color: white;
        }

        .btn-primary:hover {
            background-color: #1f5a38;
        }

        .btn-secondary {
            background-color: #f0f0f0;
            color: #1a1a1a;
        }

        .btn-secondary:hover {
            background-color: #e0e0e0;
        }

        /* Facilitator Item */
        .facilitator-item {
            display: flex;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
        }

        .facilitator-item:last-child {
            border-bottom: none;
        }

        .facilitator-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            font-size: 16px;
            flex-shrink: 0;
        }

        .facilitator-details {
            flex: 1;
        }

        .facilitator-name {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .facilitator-role {
            font-size: 12px;
            color: #666;
            margin-top: 2px;
        }

        .facilitator-email {
            font-size: 12px;
            color: #999;
            margin-top: 2px;
        }

        /* Search and Filter */
        .search-filter-bar {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            align-items: center;
        }

        .search-box {
            flex: 1;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 10px 16px 10px 36px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 13px;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            border-color: #2a7a4f;
            box-shadow: 0 0 0 3px rgba(42, 122, 79, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .filter-btn {
            padding: 10px 16px;
            border: 1px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            border-color: #2a7a4f;
            color: #2a7a4f;
        }

        /* Rating */
        .rating {
            font-size: 14px;
            color: #ffc107;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
            }

            .main-content {
                margin-left: 0;
            }

            .stats-container,
            .quick-actions {
                grid-template-columns: 1fr;
            }

            .event-item,
            .facilitator-item {
                flex-direction: column;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <div class="sidebar-logo-icon">FM</div>
                <div class="sidebar-logo-text">
                    <span class="main">Facilitator</span>
                    <span class="sub">Portal</span>
                </div>
            </div>

            <ul class="sidebar-nav">
                <li><a href="{{ route('dashboard') }}" class="@if(Route::currentRouteName() === 'dashboard') active @endif"><span class="sidebar-icon">🏠</span> Dashboard</a></li>
                <li><a href="{{ route('clock-in') }}" class="@if(Route::currentRouteName() === 'clock-in') active @endif"><span class="sidebar-icon">⏰</span> Clock In/Out</a></li>
                <li><a href="{{ route('events') }}" class="@if(Route::currentRouteName() === 'events') active @endif"><span class="sidebar-icon">📅</span> Assigned Events</a></li>
                <li><a href="{{ route('performance-reviews') }}" class="@if(Route::currentRouteName() === 'performance-reviews') active @endif"><span class="sidebar-icon">⭐</span> Performance Reviews</a></li>
                <li><a href="{{ route('leave-request') }}" class="@if(Route::currentRouteName() === 'leave-request') active @endif"><span class="sidebar-icon">📋</span> Leave Request</a></li>
                <li><a href="{{ route('allowance-request') }}" class="@if(Route::currentRouteName() === 'allowance-request') active @endif"><span class="sidebar-icon">📋</span> Allowance Request</a></li>
                <li><a href="{{ route('past-events') }}" class="@if(Route::currentRouteName() === 'past-events') active @endif"><span class="sidebar-icon">⏱️</span> Past Events</a></li>
            </ul>

            <div class="sidebar-user">
                <div class="sidebar-user-label">Logged in as</div>
                <div class="sidebar-user-name">Sarah Johnson</div>
                <div class="sidebar-user-role">Facilitator</div>

                <div class="sidebar-buttons">
                    <button class="sidebar-btn primary">👤 Update Profile</button>
                    <button class="sidebar-btn secondary">🚪 Logout</button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
