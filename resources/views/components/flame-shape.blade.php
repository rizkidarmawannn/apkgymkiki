@props([
    'tier' => 0,
    'status' => 'extinguished',
    'size' => 36,
])

@php
    $tier = (int)$tier;
    $width = is_numeric($size) ? $size : 36;
    $height = is_numeric($size) ? $size : 36;
    $uid = substr(md5(uniqid(mt_rand(), true)), 0, 6);
@endphp

@if($status === 'rest')
    <!-- Rest Recovery Shield Shape (Kuning / Amber) -->
    <svg class="flame-vector-svg flame-vector-rest" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 32 32" fill="none" aria-label="Hari Istirahat">
        <path d="M16 3l10 4.5v9c0 6-4.5 11-10 13.5C10.5 27.5 6 22.5 6 16.5v-9L16 3z" fill="rgba(245, 158, 11, 0.2)" stroke="#f59e0b" stroke-width="2" stroke-linejoin="round"/>
        <path d="M11 16h10M16 11v10" stroke="#f59e0b" stroke-width="2.2" stroke-linecap="round"/>
    </svg>
@elseif($tier === 0 || $status === 'extinguished')
    <!-- Extinguished Flame Shape (Api Padam) -->
    <svg class="flame-vector-svg flame-vector-dead" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 32 32" fill="none" aria-label="Api Padam">
        <!-- Smoke Wisps drifting upward -->
        <path d="M13.5 5c-.5 1.5.5 2.5 0 4s-1.2 1.5-.7 3" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" opacity="0.6"/>
        <path d="M18.5 3c-.5 1.5.5 2.5 0 4s-1.2 1.5-.7 3" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" opacity="0.4"/>
        <!-- Extinguished Flame Contour -->
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
            <linearGradient id="flameGradTier1_{{ $uid }}" x1="16" y1="2" x2="16" y2="30.5" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFDD00"/>
                <stop offset="28%" stop-color="#FF7A00"/>
                <stop offset="68%" stop-color="#FF3300"/>
                <stop offset="100%" stop-color="#CC1100"/>
            </linearGradient>
            <linearGradient id="flameCoreTier1_{{ $uid }}" x1="16" y1="15" x2="16" y2="29" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFFFFF"/>
                <stop offset="45%" stop-color="#FFF9C4"/>
                <stop offset="100%" stop-color="#FFB300"/>
            </linearGradient>
            <filter id="flameGlow1_{{ $uid }}" x="-20%" y="-20%" width="140%" height="140%">
                <feDropShadow dx="0" dy="1" stdDeviation="1.5" flood-color="#FF5500" flood-opacity="0.35"/>
            </filter>
        </defs>
        <!-- Outer Flame Body -->
        <path d="M16 2.5C15 6 12 9 10 11.5C7 15 5 19 5 23C5 28 9.5 30.5 16 30.5C22.5 30.5 27 28 27 23C27 18 24 14.5 21.5 11.5C22 13 21 15 19.5 16C20.5 11 18 6 16 2.5Z" fill="url(#flameGradTier1_{{ $uid }})" filter="url(#flameGlow1_{{ $uid }})"/>
        <!-- Inner Core Luminous Ember -->
        <path d="M16 15C14 17.5 12 20.5 12 24C12 27.5 13.8 29 16 29C18.2 29 20 27.5 20 24C20 20.5 18 17.5 16 15Z" fill="url(#flameCoreTier1_{{ $uid }})"/>
    </svg>
@elseif($tier === 2)
    <!-- Tier 2: Api Biru Elektrik (Cyan / Sapphire Blue) -->
    <svg class="flame-vector-svg flame-vector-tier-2" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 32 32" fill="none" aria-label="Api Biru">
        <defs>
            <linearGradient id="flameGradTier2_{{ $uid }}" x1="16" y1="2" x2="16" y2="30.5" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#A5F3FC"/>
                <stop offset="30%" stop-color="#38BDF8"/>
                <stop offset="70%" stop-color="#0284C7"/>
                <stop offset="100%" stop-color="#1D4ED8"/>
            </linearGradient>
            <linearGradient id="flameCoreTier2_{{ $uid }}" x1="16" y1="15" x2="16" y2="29" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFFFFF"/>
                <stop offset="45%" stop-color="#E0F2FE"/>
                <stop offset="100%" stop-color="#7DD3FC"/>
            </linearGradient>
            <filter id="flameGlow2_{{ $uid }}" x="-20%" y="-20%" width="140%" height="140%">
                <feDropShadow dx="0" dy="1" stdDeviation="1.5" flood-color="#0284C7" flood-opacity="0.4"/>
            </filter>
        </defs>
        <!-- Outer Flame Body -->
        <path d="M16 2.5C15 6 12 9 10 11.5C7 15 5 19 5 23C5 28 9.5 30.5 16 30.5C22.5 30.5 27 28 27 23C27 18 24 14.5 21.5 11.5C22 13 21 15 19.5 16C20.5 11 18 6 16 2.5Z" fill="url(#flameGradTier2_{{ $uid }})" filter="url(#flameGlow2_{{ $uid }})"/>
        <!-- Inner Core Luminous Ember -->
        <path d="M16 15C14 17.5 12 20.5 12 24C12 27.5 13.8 29 16 29C18.2 29 20 27.5 20 24C20 20.5 18 17.5 16 15Z" fill="url(#flameCoreTier2_{{ $uid }})"/>
        <!-- Electric Sparks -->
        <path d="M5 16l3-2-1 4 4-1-3 3 1-2z" fill="#7DD3FC"/>
        <path d="M27 15l-3-2 1 4-4-1 3 3-1-2z" fill="#7DD3FC"/>
    </svg>
@elseif($tier === 3)
    <!-- Tier 3: Api Plasma Ungu (Violet / Plasma Glow) -->
    <svg class="flame-vector-svg flame-vector-tier-3" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 32 32" fill="none" aria-label="Api Plasma">
        <defs>
            <linearGradient id="flameGradTier3_{{ $uid }}" x1="16" y1="2" x2="16" y2="30.5" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#F472B6"/>
                <stop offset="30%" stop-color="#C084FC"/>
                <stop offset="70%" stop-color="#9333EA"/>
                <stop offset="100%" stop-color="#6B21A8"/>
            </linearGradient>
            <linearGradient id="flameCoreTier3_{{ $uid }}" x1="16" y1="15" x2="16" y2="29" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFFFFF"/>
                <stop offset="45%" stop-color="#FDF4FF"/>
                <stop offset="100%" stop-color="#F0ABFC"/>
            </linearGradient>
            <filter id="flameGlow3_{{ $uid }}" x="-20%" y="-20%" width="140%" height="140%">
                <feDropShadow dx="0" dy="1" stdDeviation="1.5" flood-color="#9333EA" flood-opacity="0.4"/>
            </filter>
        </defs>
        <!-- Outer Flame Body -->
        <path d="M16 2.5C15 6 12 9 10 11.5C7 15 5 19 5 23C5 28 9.5 30.5 16 30.5C22.5 30.5 27 28 27 23C27 18 24 14.5 21.5 11.5C22 13 21 15 19.5 16C20.5 11 18 6 16 2.5Z" fill="url(#flameGradTier3_{{ $uid }})" filter="url(#flameGlow3_{{ $uid }})"/>
        <!-- Inner Core Luminous Ember -->
        <path d="M16 15C14 17.5 12 20.5 12 24C12 27.5 13.8 29 16 29C18.2 29 20 27.5 20 24C20 20.5 18 17.5 16 15Z" fill="url(#flameCoreTier3_{{ $uid }})"/>
    </svg>
@else
    <!-- Tier 4: Api Emas Mahkota (100+ Hari Elite) -->
    <svg class="flame-vector-svg flame-vector-tier-4" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 32 32" fill="none" aria-label="Api Emas Mahkota">
        <defs>
            <linearGradient id="flameGradTier4_{{ $uid }}" x1="16" y1="2" x2="16" y2="30.5" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFFBEB"/>
                <stop offset="25%" stop-color="#FDE047"/>
                <stop offset="65%" stop-color="#F59E0B"/>
                <stop offset="100%" stop-color="#B45309"/>
            </linearGradient>
            <linearGradient id="flameCoreTier4_{{ $uid }}" x1="16" y1="15" x2="16" y2="29" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFFFFF"/>
                <stop offset="50%" stop-color="#FEF08A"/>
                <stop offset="100%" stop-color="#FBBF24"/>
            </linearGradient>
            <filter id="flameGlow4_{{ $uid }}" x="-20%" y="-20%" width="140%" height="140%">
                <feDropShadow dx="0" dy="1" stdDeviation="1.5" flood-color="#F59E0B" flood-opacity="0.45"/>
            </filter>
        </defs>
        <!-- Golden Halo Glow -->
        <circle cx="16" cy="18" r="11" fill="rgba(251, 191, 36, 0.18)"/>
        <!-- Outer Flame Body -->
        <path d="M16 2.5C15 6 12 9 10 11.5C7 15 5 19 5 23C5 28 9.5 30.5 16 30.5C22.5 30.5 27 28 27 23C27 18 24 14.5 21.5 11.5C22 13 21 15 19.5 16C20.5 11 18 6 16 2.5Z" fill="url(#flameGradTier4_{{ $uid }})" filter="url(#flameGlow4_{{ $uid }})"/>
        <!-- Inner Core Luminous Ember -->
        <path d="M16 15C14 17.5 12 20.5 12 24C12 27.5 13.8 29 16 29C18.2 29 20 27.5 20 24C20 20.5 18 17.5 16 15Z" fill="url(#flameCoreTier4_{{ $uid }})"/>
        <!-- Golden Crown Emblem -->
        <path d="M10 27l2.2-3 3.8 2.5 3.8-2.5 2.2 3H10z" fill="#FEF08A" stroke="#B45309" stroke-width="0.8"/>
    </svg>
@endif
