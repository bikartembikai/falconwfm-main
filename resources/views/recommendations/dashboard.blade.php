<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommender System Dashboard</title>
    <!-- Simple inline CSS for dashboard feel -->
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 30px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
        .event-title { font-size: 1.4em; color: #34495e; margin: 0 0 10px 0; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .event-meta { font-size: 0.9em; color: #7f8c8d; margin-bottom: 15px; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; background: #e0e0e0; font-size: 0.85em; margin-right: 5px; }
        .match-list { list-style: none; padding: 0; margin: 0; }
        .match-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f1f1; }
        .match-item:last-child { border-bottom: none; }
        .score-box { background: #e8f5e9; color: #2e7d32; font-weight: bold; padding: 5px 10px; border-radius: 6px; font-size: 0.9em; }
        .facilitator-info { flex-grow: 1; margin-left: 12px; }
        .facilitator-name { font-weight: 600; display: block; }
        .facilitator-skills { font-size: 0.8em; color: #999; display: block; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; max-width: 200px; }
        .no-matches { color: #999; font-style: italic; text-align: center; padding: 10px; }
        .actions { margin-top: 15px; text-align: right; }
        .btn { text-decoration: none; color: #3498db; font-weight: 600; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="container">
    <h1>🎯 Content-Based Recommender Validation</h1>
    
    <h2>Events looking for Facilitators</h2>
    <div class="grid">
        @foreach($dashboardData as $item)
            <div class="card">
                <h2 class="event-title">{{ $item['event']->event_name }}</h2>
                
                <div class="event-meta">
                    <div>
                        <strong>Required:</strong> 
                        @foreach(explode(' ', $item['event']->required_skill_tag) as $skill)
                            @if(!empty($skill))
                                <span class="badge">{{ $skill }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>

                <h3>Top Matches</h3>
                <ul class="match-list">
                    @forelse($item['matches'] as $match)
                        <li class="match-item">
                            <div class="score-box">{{ $match['match_score'] }}%</div>
                            <div class="facilitator-info">
                                <span class="facilitator-name">{{ $match['name'] }}</span>
                                <span class="facilitator-skills" title="{{ $match['skills'] }}">{{ $match['skills'] }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="no-matches">No clear matches found.</li>
                    @endforelse
                </ul>

                <div class="actions">
                    <a href="{{ url('/events/' . $item['event']->id . '/recommendations') }}" class="btn">View Full Details →</a>
                </div>
            </div>
        @endforeach
    </div>

    <h2 style="margin-top: 50px;">Facilitators looking for Events</h2>
    <div class="grid">
        @foreach($facilitatorData as $item)
            <div class="card" style="border-top: 4px solid #3498db;">
                <h2 class="event-title">{{ $item['facilitator']->user->name }}</h2>
                <div class="event-meta">
                    <p><strong>Skills:</strong> {{ Str::limit($item['facilitator']->skills, 50) }}</p>
                    <p><strong>Exp:</strong> {{ Str::limit($item['facilitator']->experience, 50) }}</p>
                </div>
                
                <h3>Recommended Events</h3>
                <ul class="match-list">
                    @forelse($item['matches'] as $match)
                        <li class="match-item">
                            <div class="score-box" style="background:#e3f2fd; color:#1565c0;">{{ $match['match_score'] }}%</div>
                            <div class="facilitator-info">
                                <span class="facilitator-name">{{ $match['name'] }}</span>
                                <span class="facilitator-skills">{{ $match['required_skills'] }}</span>
                            </div>
                        </li>
                    @empty
                         <li class="no-matches">No events match profile.</li>
                    @endforelse
                </ul>
            </div>
        @endforeach
    </div>

</div>

</body>
</html>
