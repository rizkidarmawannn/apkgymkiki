@extends('layouts.app')

@section('title', 'Progres')

@section('content')
<div class="page-header mb-4">
    <h1>Progres</h1>
</div>

<div class="progress-grid mb-4">
    <!-- Weight Input Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="m-0">Catat Berat Badan</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('progress.store') }}" method="POST" class="d-flex flex-gap align-end">
                @csrf
                <div class="form-group mb-0 flex-1">
                    <label>Berat Badan (kg)</label>
                    <input type="number" step="0.1" name="weight" class="form-control" required placeholder="cth. 75.5">
                </div>
                <div class="form-group mb-0 flex-1">
                    <label>Tanggal <small class="text-muted">(dd/mm/yyyy)</small></label>
                    <input type="text" name="date" class="form-control" required placeholder="dd/mm/yyyy cth. {{ date('d/m/Y') }}" value="{{ date('d/m/Y') }}" pattern="\d{1,2}/\d{1,2}/\d{4}" title="Format: dd/mm/yyyy (contoh: {{ date('d/m/Y') }})">
                </div>
                <button type="submit" class="btn btn-primary h-100">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Weight Stats Card -->
    <div class="card mb-4 bg-primary text-white text-center p-4 rounded-xl">
        <div class="d-flex justify-content-around">
            <div>
                <small class="text-white-50 d-block">Awal</small>
                <strong class="text-xl">{{ $startingWeight ?? '0.0' }} kg</strong>
            </div>
            <div>
                <small class="text-white-50 d-block">Sekarang</small>
                <strong class="text-xl">{{ $currentWeight ?? '0.0' }} kg</strong>
            </div>
            <div>
                <small class="text-white-50 d-block">Target</small>
                <strong class="text-xl">{{ $targetWeight ?? '0.0' }} kg</strong>
            </div>
        </div>
        <div class="mt-4 pt-3 border-top border-white-25">
            <small class="text-white-50 d-block">Perubahan</small>
            @php 
                $change = ($currentWeight ?? 0) - ($startingWeight ?? 0); 
                $goal = $profile?->goal ?? $user->profile?->goal ?? 'lose_weight';
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
        <h3 class="m-0">Grafik Berat Badan</h3>
        <div class="chart-toggles btn-group">
            <button class="btn btn-small btn-outline active chart-toggle" data-period="7">7 Hari</button>
            <button class="btn btn-small btn-outline chart-toggle" data-period="30">30 Hari</button>
        </div>
    </div>
    <div class="card-body">
        <div class="chart-container" style="position: relative; height:300px; width:100%">
            <canvas id="weightChart"></canvas>
        </div>
    </div>
</div>

<!-- Weight History Table -->
<div class="card mb-4">
    <div class="card-header flex-between align-center">
        <h3 class="m-0">Riwayat Catatan Berat</h3>
        <span class="text-muted text-xs font-semibold uppercase">Format: dd/mm/yyyy</span>
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            @forelse($weightLogs->take(8) ?? [] as $wl)
                <li class="list-group-item flex-between align-center p-3">
                    <div class="d-flex align-center flex-gap-sm">
                        <span class="text-muted">📅</span>
                        <strong>{{ \Carbon\Carbon::parse($wl->date)->format('d/m/Y') }}</strong>
                    </div>
                    <span class="badge badge-primary font-semibold" style="font-size: 0.95rem;">
                        {{ number_format($wl->weight, 1) }} kg
                    </span>
                </li>
            @empty
                <li class="list-group-item p-3 text-center text-muted">Belum ada riwayat timbangan berat.</li>
            @endforelse
        </ul>
    </div>
</div>

<!-- Weekly Summaries -->
<div class="stats-grid mb-4">
    <div class="card stat-card stat-workouts border-left-info">
        <h3>Latihan Minggu Ini</h3>
        <p class="stat-value text-primary">{{ $workoutsThisWeek ?? 0 }}</p>
    </div>
    <div class="card stat-card stat-calories border-left-warning">
        <h3>Rata-rata Kalori (7hr)</h3>
        <p class="stat-value text-warning">{{ number_format($avgCalories ?? 0) }} <span class="text-sm">kkal/hari</span></p>
    </div>
</div>

<!-- Exercise Progress -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="m-0">Progres Gerakan Latihan</h3>
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            @forelse($exerciseProgress ?? [] as $ep)
                @php
                    $epObj = is_array($ep) ? (object)$ep : $ep;
                    $exName = $epObj->exercise_name ?? ($epObj->exercise->name ?? 'Exercise');
                    $curWeight = (float)($epObj->current_weight ?? 0);
                    $prevWeight = (float)($epObj->previous_weight ?? 0);
                    $diff = $curWeight - $prevWeight;
                @endphp
                <li class="list-group-item d-flex justify-content-between align-center p-3">
                    <div>
                        <strong>{{ $exName }}</strong>
                        <small class="d-block text-muted">Sebelumnya: {{ $prevWeight }} kg</small>
                    </div>
                    <div class="text-right">
                        <strong>{{ $curWeight }} kg</strong>
                        <small class="d-block {{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $diff > 0 ? '+' : '' }}{{ $diff }} kg
                        </small>
                    </div>
                </li>
            @empty
                <li class="list-group-item p-3 text-center text-muted">Belum ada data progres latihan.</li>
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
        
        const initialData = @json($weightLogs ?? []);
        
        function formatDDMMYYYY(dateStr) {
            if (!dateStr) return '';
            const clean = String(dateStr).substring(0, 10);
            const parts = clean.split('-');
            if (parts.length === 3) {
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            }
            return dateStr;
        }

        function formatChartData(data) {
            return {
                labels: data.map(item => formatDDMMYYYY(item.date)),
                datasets: [{
                    label: 'Berat Badan (kg)',
                    data: data.map(item => item.weight),
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            };
        }

        if (initialData.length > 0) {
            weightChart = new Chart(ctx, {
                type: 'line',
                data: formatChartData(initialData),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });
        }

        document.querySelectorAll('.chart-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.chart-toggle').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const days = this.dataset.period;
                
                fetch(`/progress/weight-data?days=${days}`)
                    .then(res => res.json())
                    .then(data => {
                        if (weightChart) {
                            const formatted = formatChartData(data);
                            weightChart.data.labels = formatted.labels;
                            weightChart.data.datasets[0].data = formatted.datasets[0].data;
                            weightChart.update();
                        }
                    });
            });
        });
    });
</script>
@endpush
@endsection
