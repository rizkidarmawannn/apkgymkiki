@props([
    'tier' => 0,
    'status' => 'extinguished',
    'size' => 36,
])

@php
    $tier = (int)$tier;
    $width = is_numeric($size) ? $size : 36;
    $height = is_numeric($size) ? $size : 36;
@endphp

@if($status === 'rest')
    <!-- Rest Recovery Shield Shape (Kuning / Amber) -->
    <svg class="flame-vector-svg flame-vector-rest" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 32 32" fill="none" aria-label="Hari Istirahat">
        <path d="M16 3l10 4.5v9c0 6-4.5 11-10 13.5C10.5 27.5 6 22.5 6 16.5v-9L16 3z" fill="rgba(245, 158, 11, 0.2)" stroke="#f59e0b" stroke-width="2" stroke-linejoin="round"/>
        <path d="M11 16h10M16 11v10" stroke="#f59e0b" stroke-width="2.2" stroke-linecap="round"/>
    </svg>
@elseif($tier === 0 || $status === 'extinguished')
    <!-- Extinguished Flame Shape (Api Padam - Bukan Lilin, Ada Asap Halus) -->
    <svg class="flame-vector-svg flame-vector-dead" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 32 32" fill="none" aria-label="Api Padam">
        <!-- Smoke Wisps drifting upward -->
        <path d="M13.5 5c-.5 1.5.5 2.5 0 4s-1.2 1.5-.7 3" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" opacity="0.6"/>
        <path d="M18.5 3c-.5 1.5.5 2.5 0 4s-1.2 1.5-.7 3" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" opacity="0.4"/>
        <!-- Extinguished Flame Contour (Dashed Charcoal Outline) -->
        <path d="M16 9c-2.5 3.5-5.5 7-5.5 12a5.5 5.5 0 0 0 11 0c0-5-3-8.5-5.5-12z" 
              stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="3.5 2.5" fill="rgba(100, 116, 139, 0.12)"/>
        <!-- Extinguished Core Wick -->
        <path d="M16 21v-4" stroke="#475569" stroke-width="2.2" stroke-linecap="round"/>
        <circle cx="16" cy="22" r="1.5" fill="#64748b"/>
    </svg>
@elseif($tier === 1)
    <!-- Tier 1: Api Membara (Oranye Menyala) -->
    <svg class="flame-vector-svg flame-vector-tier-1" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 32 32" fill="none" aria-label="Api Membara">
        <defs>
            <linearGradient id="flameGradTier1" x1="16" y1="3" x2="16" y2="28" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#fbbf24"/>
                <stop offset="45%" stop-color="#f97316"/>
                <stop offset="100%" stop-color="#dc2626"/>
            </linearGradient>
            <linearGradient id="flameCoreTier1" x1="16" y1="13" x2="16" y2="26" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#fef08a"/>
                <stop offset="100%" stop-color="#f97316"/>
            </linearGradient>
        </defs>
        <path d="M16 2.5c-.8 2.5-4.5 6.5-5.5 11.5a7 7 0 0 0 13.5 3.5c.5-1.5.5-2.5.5-3.5-1.5 1.5-3.2 2-4.5 1.5-2.5-.8-3.5-3-2-5.5 1-1.7 1-3.5-.5-5.5-.3-.7-.5-1.5-.5-2z" fill="url(#flameGradTier1)"/>
        <path d="M16 13c-.7 1.8-2.3 3.5-2.8 5.8a3.5 3.5 0 0 0 6.2 2.2c-.7 0-1.5-.4-1.8-1.2-.4-1.3.4-2.2.4-3.5-.4-.7-1.3-2.2-2-3.3z" fill="url(#flameCoreTier1)"/>
    </svg>
@elseif($tier === 2)
    <!-- Tier 2: Api Biru Elektrik (Cyan / Sapphire Blue) -->
    <svg class="flame-vector-svg flame-vector-tier-2" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 32 32" fill="none" aria-label="Api Biru">
        <defs>
            <linearGradient id="flameGradTier2" x1="16" y1="3" x2="16" y2="28" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#67e8f9"/>
                <stop offset="45%" stop-color="#06b6d4"/>
                <stop offset="100%" stop-color="#2563eb"/>
            </linearGradient>
            <linearGradient id="flameCoreTier2" x1="16" y1="13" x2="16" y2="26" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#e0f2fe"/>
                <stop offset="100%" stop-color="#38bdf8"/>
            </linearGradient>
        </defs>
        <path d="M16 2.5c-.8 2.5-4.5 6.5-5.5 11.5a7 7 0 0 0 13.5 3.5c.5-1.5.5-2.5.5-3.5-1.5 1.5-3.2 2-4.5 1.5-2.5-.8-3.5-3-2-5.5 1-1.7 1-3.5-.5-5.5-.3-.7-.5-1.5-.5-2z" fill="url(#flameGradTier2)"/>
        <path d="M16 13c-.7 1.8-2.3 3.5-2.8 5.8a3.5 3.5 0 0 0 6.2 2.2c-.7 0-1.5-.4-1.8-1.2-.4-1.3.4-2.2.4-3.5-.4-.7-1.3-2.2-2-3.3z" fill="url(#flameCoreTier2)"/>
        <!-- Electric Spikes -->
        <path d="M6 16l3-3-1 4 4-1-4 4 1-2.5L6 18.5z" fill="#38bdf8"/>
        <path d="M26 15l-3-3 1 4-4-1 4 4-1-2.5 3 1z" fill="#38bdf8"/>
    </svg>
@elseif($tier === 3)
    <!-- Tier 3: Api Plasma Ungu (Violet / Plasma Glow) -->
    <svg class="flame-vector-svg flame-vector-tier-3" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 32 32" fill="none" aria-label="Api Plasma">
        <defs>
            <linearGradient id="flameGradTier3" x1="16" y1="3" x2="16" y2="28" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#f472b6"/>
                <stop offset="45%" stop-color="#c084fc"/>
                <stop offset="100%" stop-color="#7c3aed"/>
            </linearGradient>
            <linearGradient id="flameCoreTier3" x1="16" y1="13" x2="16" y2="26" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#fae8ff"/>
                <stop offset="100%" stop-color="#e879f9"/>
            </linearGradient>
        </defs>
        <path d="M16 2.5c-.8 2.5-4.5 6.5-5.5 11.5a7 7 0 0 0 13.5 3.5c.5-1.5.5-2.5.5-3.5-1.5 1.5-3.2 2-4.5 1.5-2.5-.8-3.5-3-2-5.5 1-1.7 1-3.5-.5-5.5-.3-.7-.5-1.5-.5-2z" fill="url(#flameGradTier3)"/>
        <path d="M16 13c-.7 1.8-2.3 3.5-2.8 5.8a3.5 3.5 0 0 0 6.2 2.2c-.7 0-1.5-.4-1.8-1.2-.4-1.3.4-2.2.4-3.5-.4-.7-1.3-2.2-2-3.3z" fill="url(#flameCoreTier3)"/>
    </svg>
@else
    <!-- Tier 4: Api Emas Mahkota (100+ Hari Elite) -->
    <svg class="flame-vector-svg flame-vector-tier-4" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 32 32" fill="none" aria-label="Api Emas Mahkota">
        <defs>
            <linearGradient id="flameGradTier4" x1="16" y1="2" x2="16" y2="30" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#fef08a"/>
                <stop offset="35%" stop-color="#fbbf24"/>
                <stop offset="75%" stop-color="#f59e0b"/>
                <stop offset="100%" stop-color="#b45309"/>
            </linearGradient>
            <linearGradient id="flameCoreTier4" x1="16" y1="12" x2="16" y2="26" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#ffffff"/>
                <stop offset="100%" stop-color="#fef08a"/>
            </linearGradient>
        </defs>
        <!-- Golden Halo Glow -->
        <circle cx="16" cy="18" r="11" fill="rgba(251, 191, 36, 0.15)"/>
        <!-- Golden Flame Body -->
        <path d="M16 2.5c-.8 2.5-4.5 6.5-5.5 11.5a7 7 0 0 0 13.5 3.5c.5-1.5.5-2.5.5-3.5-1.5 1.5-3.2 2-4.5 1.5-2.5-.8-3.5-3-2-5.5 1-1.7 1-3.5-.5-5.5-.3-.7-.5-1.5-.5-2z" fill="url(#flameGradTier4)"/>
        <path d="M16 12c-.7 1.8-2.3 3.5-2.8 5.8a3.5 3.5 0 0 0 6.2 2.2c-.7 0-1.5-.4-1.8-1.2-.4-1.3.4-2.2.4-3.5-.4-.7-1.3-2.2-2-3.3z" fill="url(#flameCoreTier4)"/>
        <!-- Golden Crown Emblem -->
        <path d="M10 26.5l2.2-3 3.8 2.5 3.8-2.5 2.2 3H10z" fill="#fef08a" stroke="#b45309" stroke-width="0.8"/>
    </svg>
@endif
