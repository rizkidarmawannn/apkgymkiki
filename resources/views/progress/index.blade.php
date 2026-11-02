@extends('layouts.app')

@section('title', 'Progres')

@section('content')
<div class="page-header mb-4">
    <h1>Progres</h1>
</div>

<!-- Weight Tracker Sheet (Matching Excel Weight Tracker Template) -->
<div class="weight-tracker-sheet">
    <div class="weight-tracker-header">
        <svg class="wt-bars-icon" viewBox="0 0 24 24" fill="currentColor">
            <rect x="2" y="10" width="3.5" height="12" rx="1" fill="#E05D52" opacity="0.6"/>
            <rect x="7.5" y="6" width="3.5" height="16" rx="1" fill="#E05D52" opacity="0.8"/>
            <rect x="13" y="2" width="3.5" height="20" rx="1" fill="#E05D52"/>
            <rect x="18.5" y="7" width="3.5" height="15" rx="1" fill="#E05D52" opacity="0.9"/>
        </svg>
        <div class="wt-brand-text">
            <span class="wt-group-title">FITNESS TRACKER</span>
            <h2 class="wt-main-title">Weight Tracker</h2>
            <span class="wt-month-subtitle">BULAN {{ $monthName }}</span>
        </div>
    </div>

    <div class="weight-tracker-grid">
        <!-- Card 1: User's Overall Progress (Matching Column 1) -->
        <div class="wt-card">
            <div class="wt-card-header">
                <h3 class="wt-user-title">{{ $user->name }}</h3>
                <span class="wt-badge-scope">Semua Waktu</span>
            </div>

            <div class="wt-readout-wrap">
                <div class="wt-readout-labels">
                    <span>STARTING</span>
                    <span>GOAL</span>
                </div>
                <div class="wt-readout-boxes">
                    <div class="wt-display-box">{{ $startingWeight > 0 ? number_format($startingWeight, ($startingWeight == (int)$startingWeight ? 0 : 1)) : '-' }}</div>
                    <div class="wt-arrow">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </div>
                    <div class="wt-display-box">{{ $targetWeight > 0 ? number_format($targetWeight, ($targetWeight == (int)$targetWeight ? 0 : 1)) : '-' }}</div>
                </div>
                <div class="wt-sublabel">
                    <span>WEIGHT (KG)</span>
                </div>
            </div>

            <div class="wt-table">
                <div class="wt-row">
                    <span class="wt-cell-label">MAXIMUM</span>
                    <span class="wt-cell-val">{{ number_format($maxWeight, 1) }}</span>
                </div>
                <div class="wt-row">
                    <span class="wt-cell-label">MINIMUM</span>
                    <span class="wt-cell-val">{{ number_format($minWeight, 1) }}</span>
                </div>
                <div class="wt-row">
                    <span class="wt-cell-label">NAIK / TURUN BB</span>
                    @if($totalWeightChange < 0)
                        <span class="wt-cell-val" style="color: #10b981; font-weight: 800;">↓ Turun {{ number_format(abs($totalWeightChange), 1) }} kg</span>
                    @elseif($totalWeightChange > 0)
                        <span class="wt-cell-val" style="color: #ef4444; font-weight: 800;">↑ Naik {{ number_format($totalWeightChange, 1) }} kg</span>
                    @else
                        <span class="wt-cell-val text-muted">Tetap (0.0 kg)</span>
                    @endif
                </div>
                <div class="wt-row">
                    <span class="wt-cell-label">LOST %</span>
                    <span class="wt-cell-val">{{ ($lostPercent > 0 ? '+' : '') . number_format($lostPercent, 1) }}%</span>
                </div>
                <div class="wt-row">
                    <span class="wt-cell-label">AVERAGE</span>
                    <span class="wt-cell-val">{{ number_format($avgWeight, 1) }}</span>
                </div>
                <div class="wt-row">
                    <span class="wt-cell-label">KALORI TERBAKAR</span>
                    <span class="wt-cell-val" style="color: #f97316; font-weight: 800;">
                        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 14px; height: 14px; vertical-align: -2px; margin-right: 3px; display: inline-block;"><path d="M12 2c-.5 2-2 4-3.5 5.5C6.5 9.5 5 12.5 5 16c0 3.87 3.13 7 7 7s7-3.13 7-7c0-3-1.5-5.5-3-7.5-.5 1-1.2 2-2 2.5.5-2.5 0-5.5-2-9z"/></svg>{{ number_format($totalCaloriesBurned) }} kkal
                    </span>
                </div>
                <div class="wt-row">
                    <span class="wt-cell-label">TO GOAL</span>
                    <span class="wt-cell-val">{{ $targetWeight > 0 ? number_format($toGoal, 1) : '-' }}</span>
                </div>
                <div class="wt-banner-row">
                    <span class="wt-banner-label">TOTAL LOST</span>
                    <span class="wt-banner-val">{{ abs($totalLost) > 0 ? number_format(abs($totalLost), 1) : '0' }}</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Current Month Progress (Matching Column 2) -->
        <div class="wt-card">
            <div class="wt-card-header">
                <h3 class="wt-user-title">Bulan Ini</h3>
                <span class="wt-badge-scope">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
            </div>

            <div class="wt-readout-wrap">
                <div class="wt-readout-labels">
                    <span>STARTING</span>
                    <span>CURRENT</span>
                </div>
                <div class="wt-readout-boxes">
                    <div class="wt-display-box">{{ $monthFirstWeight > 0 ? number_format($monthFirstWeight, ($monthFirstWeight == (int)$monthFirstWeight ? 0 : 1)) : '-' }}</div>
                    <div class="wt-arrow">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </div>
                    <div class="wt-display-box">{{ $currentWeight > 0 ? number_format($currentWeight, ($currentWeight == (int)$currentWeight ? 0 : 1)) : '-' }}</div>
                </div>
                <div class="wt-sublabel">
                    <span>WEIGHT (KG)</span>
                </div>
            </div>

            <div class="wt-table">
                <div class="wt-row">
                    <span class="wt-cell-label">MAXIMUM</span>
                    <span class="wt-cell-val">{{ number_format($monthMaxWeight, 1) }}</span>
                </div>
                <div class="wt-row">
                    <span class="wt-cell-label">MINIMUM</span>
                    <span class="wt-cell-val">{{ number_format($monthMinWeight, 1) }}</span>
                </div>
                <div class="wt-row">
                    <span class="wt-cell-label">NAIK / TURUN BULAN INI</span>
                    @if($monthWeightChange < 0)
                        <span class="wt-cell-val" style="color: #10b981; font-weight: 800;">↓ Turun {{ number_format(abs($monthWeightChange), 1) }} kg</span>
                    @elseif($monthWeightChange > 0)
                        <span class="wt-cell-val" style="color: #ef4444; font-weight: 800;">↑ Naik {{ number_format($monthWeightChange, 1) }} kg</span>
                    @else
                        <span class="wt-cell-val text-muted">Tetap (0.0 kg)</span>
                    @endif
                </div>
                <div class="wt-row">
                    <span class="wt-cell-label">LOST %</span>
                    <span class="wt-cell-val">{{ ($monthLostPercent > 0 ? '+' : '') . number_format($monthLostPercent, 1) }}%</span>
                </div>
                <div class="wt-row">
                    <span class="wt-cell-label">AVERAGE</span>
                    <span class="wt-cell-val">{{ number_format($monthAvgWeight, 1) }}</span>
                </div>
                <div class="wt-row">
                    <span class="wt-cell-label">KALORI TERBAKAR</span>
                    <span class="wt-cell-val" style="color: #f97316; font-weight: 800;">
                        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 14px; height: 14px; vertical-align: -2px; margin-right: 3px; display: inline-block;"><path d="M12 2c-.5 2-2 4-3.5 5.5C6.5 9.5 5 12.5 5 16c0 3.87 3.13 7 7 7s7-3.13 7-7c0-3-1.5-5.5-3-7.5-.5 1-1.2 2-2 2.5.5-2.5 0-5.5-2-9z"/></svg>{{ number_format($monthCaloriesBurned) }} kkal
                    </span>
                </div>
                <div class="wt-row">
                    <span class="wt-cell-label">TO GOAL</span>
                    <span class="wt-cell-val">{{ $targetWeight > 0 ? number_format($toGoal, 1) : '-' }}</span>
                </div>
                <div class="wt-banner-row">
                    <span class="wt-banner-label">TOTAL LOST</span>
                    <span class="wt-banner-val">{{ abs($monthTotalLost) > 0 ? number_format(abs($monthTotalLost), 1) : '0' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Weight Input Card -->
<div class="card mb-4">
    <div class="card-header flex-between align-center">
        <h3 class="m-0" style="font-size: 1.05rem; font-weight: 800;">Catat Berat Badan & Target</h3>
        <span class="text-xs text-muted font-semibold">Timbangan Rutin</span>
    </div>
    <div class="card-body">
        <form action="{{ route('progress.store') }}" method="POST" class="d-flex flex-wrap flex-gap align-end">
            @csrf
            <div class="form-group mb-0 flex-1" style="min-width: 150px;">
                <label style="font-weight: 700; font-size: 0.85rem;">Berat Hari Ini (kg)</label>
                <input type="number" step="0.1" name="weight" class="form-control" required placeholder="cth. 75.5" value="{{ $currentWeight > 0 ? $currentWeight : '' }}">
            </div>
            <div class="form-group mb-0 flex-1" style="min-width: 150px;">
                <label style="font-weight: 700; font-size: 0.85rem;">Target Berat (kg) <small class="text-muted font-normal">(opsional)</small></label>
                <input type="number" step="0.1" name="target_weight" class="form-control" placeholder="Target kg" value="{{ $targetWeight > 0 ? $targetWeight : '' }}">
            </div>
            <div class="form-group mb-0 flex-1" style="min-width: 150px;">
                <label style="font-weight: 700; font-size: 0.85rem;">Tanggal <small class="text-muted font-normal">(dd/mm/yyyy)</small></label>
                <input type="text" name="date" class="form-control" required placeholder="dd/mm/yyyy" value="{{ date('d/m/Y') }}" pattern="\d{1,2}/\d{1,2}/\d{4}" title="Format: dd/mm/yyyy">
            </div>
            <button type="submit" class="btn btn-primary" style="height: 42px; font-weight: 700; padding: 0 24px;">
                Simpan
            </button>
        </form>
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
                        <span class="text-muted" style="display: inline-flex; align-items: center;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        </span>
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
        const canvas = document.getElementById('weightChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let weightChart;
        
        const initialData = @json($weightLogs ?? []);
        
        function formatDDMMYYYY(dateStr) {
            if (!dateStr) return '';
            const clean = String(dateStr).substring(0, 10);
            const parts = clean.split('-');
            if (parts.length === 3) {
                return `${parts[2]}/${parts[1]}`;
            }
            return dateStr;
        }

        // Custom plugin to draw weight numbers cleanly above each bar like in Image 5
        const barTopLabelsPlugin = {
            id: 'barTopLabelsPlugin',
            afterDatasetsDraw(chart) {
                const { ctx, data } = chart;
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                ctx.save();
                ctx.font = '700 11px Inter, system-ui, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillStyle = isDark ? '#94a3b8' : '#475569';

                chart.getDatasetMeta(0).data.forEach((bar, index) => {
                    const val = data.datasets[0].data[index];
                    if (val !== undefined && val !== null && !isNaN(val)) {
                        ctx.fillText(Number(val).toFixed(1), bar.x, Math.max(16, bar.y - 7));
                    }
                });
                ctx.restore();
            }
        };

        function calculateYBounds(dataValues) {
            const numVals = dataValues.map(v => Number(v)).filter(v => !isNaN(v) && v > 0);
            if (numVals.length === 0) return { min: 40, max: 100 };
            const min = Math.min(...numVals);
            const max = Math.max(...numVals);
            // Give subtle headroom so bar values don't get cut off and heights show realistic contrast
            return {
                min: Math.max(0, Math.floor(min - 2.5)),
                max: Math.ceil(max + 2.5)
            };
        }

        function createBarGradient(chartCtx, chartArea) {
            if (!chartArea) return '#0ea5e9';
            const gradient = chartCtx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
            gradient.addColorStop(0, '#0284c7');
            gradient.addColorStop(0.5, '#0ea5e9');
            gradient.addColorStop(1, '#38bdf8');
            return gradient;
        }

        function formatChartData(data) {
            const rawWeights = data.map(item => Number(item.weight));
            return {
                labels: data.map(item => formatDDMMYYYY(item.date)),
                datasets: [{
                    label: 'Berat Badan (kg)',
                    data: rawWeights,
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const { ctx, chartArea } = chart;
                        return createBarGradient(ctx, chartArea);
                    },
                    hoverBackgroundColor: '#0284c7',
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 38,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                }]
            };
        }

        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const initialWeights = initialData.map(item => item.weight);
        const bounds = calculateYBounds(initialWeights);

        if (initialData.length > 0) {
            weightChart = new Chart(ctx, {
                type: 'bar',
                data: formatChartData(initialData),
                plugins: [barTopLabelsPlugin],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 20,
                            bottom: 5,
                            left: 5,
                            right: 5
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#f8fafc',
                            bodyColor: '#38bdf8',
                            titleFont: { size: 12, weight: '700' },
                            bodyFont: { size: 13, weight: '700' },
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `Berat: ${context.parsed.y} kg`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: isDark ? '#94a3b8' : '#64748b',
                                font: { weight: '600', size: 11 }
                            }
                        },
                        y: {
                            min: bounds.min,
                            max: bounds.max,
                            grid: {
                                color: isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)'
                            },
                            ticks: {
                                color: isDark ? '#94a3b8' : '#64748b',
                                font: { weight: '600', size: 11 },
                                callback: function(val) {
                                    return val + ' kg';
                                }
                            }
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
                
                fetch(`/progress/weight-data?period=${days}&days=${days}`)
                    .then(res => res.json())
                    .then(data => {
                        if (weightChart) {
                            const formatted = formatChartData(data);
                            const newWeights = data.map(item => item.weight);
                            const newBounds = calculateYBounds(newWeights);
                            weightChart.data.labels = formatted.labels;
                            weightChart.data.datasets[0].data = formatted.datasets[0].data;
                            weightChart.options.scales.y.min = newBounds.min;
                            weightChart.options.scales.y.max = newBounds.max;
                            weightChart.update();
                        }
                    });
            });
        });
    });
</script>
@endpush
@endsection
