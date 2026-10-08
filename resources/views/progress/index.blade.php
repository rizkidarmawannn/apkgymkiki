@extends('layouts.app')

@section('title', 'Progress')

@section('content')
<div class="page-header mb-4">
    <h1>Progress</h1>
</div>

<div class="progress-grid mb-4">
    <!-- Weight Input Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="m-0">Log Weight</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('progress.store') }}" method="POST" class="d-flex flex-gap align-end">
                @csrf
                <div class="form-group mb-0 flex-1">
                    <label>Weight (kg)</label>
                    <input type="number" step="0.1" name="weight" class="form-control" required placeholder="e.g. 75.5">
                </div>
                <div class="form-group mb-0 flex-1">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                </div>
                <button type="submit" class="btn btn-primary h-100">Save</button>
            </form>
        </div>
    </div>

    <!-- Weight Stats Card -->
    <div class="card mb-4 bg-primary text-white text-center p-4 rounded-xl">
        <div class="d-flex justify-content-around">
            <div>
                <small class="text-white-50 d-block">Starting</small>
                <strong class="text-xl">{{ $startingWeight ?? '0.0' }} kg</strong>
            </div>
            <div>
                <small class="text-white-50 d-block">Current</small>
                <strong class="text-xl">{{ $currentWeight ?? '0.0' }} kg</strong>
            </div>
            <div>
                <small class="text-white-50 d-block">Target</small>
                <strong class="text-xl">{{ $targetWeight ?? '0.0' }} kg</strong>
            </div>
        </div>
        <div class="mt-4 pt-3 border-top border-white-25">
            <small class="text-white-50 d-block">Change</small>
            @php 
                $change = ($currentWeight ?? 0) - ($startingWeight ?? 0); 
                $goal = $profile->goal ?? 'lose_weight';
                $isGood = (str_contains($goal, 'lose') && $change <= 0) || (str_contains($goal, 'gain') && $change >= 0);
            @endphp
            <strong class="text-2xl d-flex align-center justify-content-center flex-gap">
                {{ abs($change) }} kg 
                @if($change < 0)
                    <span style="color: #6ee7b7">↓</span>
                @elseif($change > 0)
                    <span style="color: #fca5a5">↑</span>
                @else
                    <span>-</span>
                @endif
            </strong>
        </div>
    </div>
</div>

<!-- Chart Card -->
<div class="card mb-4">
    <div class="card-header flex-between align-center">
        <h3 class="m-0">Weight Trend</h3>
        <div class="chart-toggles btn-group">
            <button class="btn btn-small btn-outline active chart-toggle" data-period="7">7 Days</button>
            <button class="btn btn-small btn-outline chart-toggle" data-period="30">30 Days</button>
        </div>
    </div>
    <div class="card-body">
        <div class="chart-container" style="position: relative; height:300px; width:100%">
            <canvas id="weightChart"></canvas>
        </div>
    </div>
</div>

<!-- Weekly Summaries -->
<div class="stats-grid mb-4">
    <div class="card stat-card stat-workouts border-left-info">
        <h3>Workouts This Week</h3>
        <p class="stat-value text-primary">{{ $workoutsThisWeek ?? 0 }}</p>
    </div>
    <div class="card stat-card stat-calories border-left-warning">
        <h3>Avg Calories (7d)</h3>
        <p class="stat-value text-warning">{{ number_format($avgCalories ?? 0) }} <span class="text-sm">kcal/day</span></p>
    </div>
</div>

<!-- Exercise Progress -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="m-0">Exercise Progress</h3>
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            @forelse($exerciseProgress ?? [] as $ep)
                <li class="list-group-item d-flex justify-content-between align-center p-3">
                    <div>
                        <strong>{{ $ep->exercise_name }}</strong>
                        <small class="d-block text-muted">Prev: {{ $ep->previous_weight }} kg</small>
                    </div>
                    <div class="text-right">
                        <strong>{{ $ep->current_weight }} kg</strong>
                        @php $diff = $ep->current_weight - $ep->previous_weight; @endphp
                        <small class="d-block {{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $diff > 0 ? '+' : '' }}{{ $diff }} kg
                        </small>
                    </div>
                </li>
            @empty
                <li class="list-group-item p-3 text-center text-muted">No exercise data available yet.</li>
            @endforelse
        </ul>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('weightChart').getContext('2d');
        let weightChart;
        
        // Initial Dummy data to be replaced by AJAX if API exists
        // In Blade, we could inject data directly here:
        const initialData = @json($weightLogs ?? []);
        
        function initChart(data) {
            if(weightChart) weightChart.destroy();
            
            const labels = data.map(d => d.date);
            const weights = data.map(d => d.weight);
            
            weightChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Weight (kg)',
                        data: weights,
                        borderColor: '#2A4B7C',
                        backgroundColor: 'rgba(42, 75, 124, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#2A4B7C',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: '#f3f4f6' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
        
        if(initialData.length > 0) {
            initChart(initialData);
        } else {
            // Placeholder empty chart setup
            initChart([
                {date:'Mon', weight: 70}, {date:'Tue', weight: 69.8}, {date:'Wed', weight: 69.5}
            ]);
        }
        
        // Toggle period (Mockup for AJAX)
        document.querySelectorAll('.chart-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.chart-toggle').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const period = this.dataset.period;
                // Fetch data via AJAX
                fetch(`/progress/weight-data?period=${period}`)
                    .then(res => res.json())
                    .then(data => initChart(data))
                    .catch(err => console.log('Fetch error (API might not exist):', err));
            });
        });
    });
</script>
@endpush
@endsection
