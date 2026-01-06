<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommendations for {{ $event->event_name }}</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .event-header { background: #f4f4f4; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .facilitator-card { border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 5px; display: flex; align-items: center; }
        .score { font-weight: bold; color: green; font-size: 1.2em; margin-right: 15px; min-width: 60px; }
        .details h3 { margin: 0 0 5px 0; }
        .details p { margin: 0; color: #666; }
        .skills { margin-top: 5px; font-size: 0.9em; color: #333; }
    </style>
</head>
<body>

    <div class="event-header">
        <h1>{{ $event->event_name }}</h1>
        <p><strong>Venue:</strong> {{ $event->venue }}</p>
        <p><strong>Required Skills:</strong> {{ $event->required_skill_tag }}</p>
        <p><strong>Date:</strong> {{ $event->start_date_time }}</p>
    </div>

    <h2>Top Recommended Facilitators</h2>

    @if(empty($recommendations))
        <p>No suitable facilitators found based on current data.</p>
    @else
        @foreach($recommendations as $rec)
            <div class="facilitator-card">
                <div class="score">{{ $rec['match_score'] }}%</div>
                <div class="details">
                    <h3>{{ $rec['name'] }}</h3>
                    <p><strong>Experience:</strong> {{ Str::limit($rec['experience'], 100) }}</p>
                    <div class="skills"><strong>Skills:</strong> {{ $rec['skills'] }}</div>
                </div>
            </div>
        @endforeach
    @endif

</body>
</html>
