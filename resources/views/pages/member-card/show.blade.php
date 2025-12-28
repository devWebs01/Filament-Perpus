<?php

use App\Models\{Setting};
use function Livewire\Volt\{state};

state([
    'user' => fn($user) => $user,
    'userDetail' => fn($userDetail) => $userDetail,
    'setting' => fn() => Setting::first(),
]);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota Perpustakaan - {{ $user->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Manrope', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 20px;
        }

        .print-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            max-width: 100%;
        }

        .print-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #D97706;
        }

        .print-header h1 {
            color: #1F2937;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .print-header p {
            color: #6B7280;
            font-size: 12px;
        }

        .id-card-container {
            display: flex;
            gap: 48px;
            justify-content: center;
            align-items: flex-start;
            background: white;
            padding: 32px;
            margin: 0;
            flex-wrap: wrap;
        }

        .card {
            width: 400px;
            height: 640px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0.1);
            position: relative;
            page-break-inside: avoid;
            break-inside: avoid;
            flex-shrink: 0;
        }

        /* Front Card - Student ID Card */
        .front-card {
            background: #D97706;
            display: flex;
            flex-direction: column;
        }

        .profile-photo-section {
            width: 100%;
            height: 400px;
            position: relative;
            overflow: hidden;
            border-radius: 20px 0 0;
        }

        .profile-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-photo-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #B45309 0%, #92400E 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-initial {
            font-size: 80px;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .verification-badge {
            position: absolute;
            bottom: 20px;
            left: 20px;
            width: 40px;
            height: 40px;
            background: #F2C4C4;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .check-icon {
            width: 24px;
            height: 24px;
            color: #D9534F;
        }

        .student-info {
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
        }

        .student-name {
            font-size: 24px;
            font-weight: 800;
            color: #FFFFFF;
            line-height: 1.1;
            margin-bottom: 8px;
        }

        .student-class {
            font-size: 18px;
            font-weight: 800;
            color: #F4F4F4;
            line-height: 1.4;
            margin-bottom: 16px;
        }

        .additional-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-row {
            display: flex;
            align-items: center;
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            color: #FFFFFF;
            margin-right: 8px;
            min-width: 40px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 800;
            color: #F4F4F4;
        }

        .logo-section {
            position: absolute;
            bottom: 15px;
            right: 15px;
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 6px 12px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .logo-icon {
            width: 16px;
            height: 16px;
            color: #FFFFFF;
            margin-right: 6px;
        }

        .logo-text {
            font-size: 14px;
            font-weight: 700;
            color: #FFFFFF;
        }

        /* Back Card - QR Code */
        .back-card {
            background: #D97706;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
        }

        .nis-section {
            margin-bottom: 20px;
            text-align: center;
        }

        .nis-label {
            font-size: 14px;
            font-weight: 600;
            color: #F4F4F4;
            margin-bottom: 8px;
        }

        .nis-number {
            font-size: 28px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: 1px;
            line-height: 1.1;
        }

        .qr-code-section {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .qr-code-container {
            width: 200px;
            height: 200px;
            background: #FFFFFF;
            border-radius: 12px;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .qr-code-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .qr-code-placeholder {
            width: 180px;
            height: 180px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
        }

        .qr-code-text {
            text-align: center;
        }

        .qr-code-title {
            font-size: 16px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 4px;
        }

        .qr-code-subtitle {
            font-size: 12px;
            font-weight: 500;
            color: #6c757d;
        }

        .institution-section {
            margin-top: 20px;
            text-align: center;
        }

        .institution-name {
            font-size: 14px;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 4px;
        }

        .institution-address {
            font-size: 12px;
            font-weight: 400;
            color: #F4F4F4;
            line-height: 1.3;
        }

        .print-footer {
            margin-top: 30px;
            text-align: center;
        }

        .print-button {
            background: #D97706;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-right: 12px;
        }

        .print-button:hover {
            background: #B45309;
        }

        .back-button {
            background: #6B7280;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .back-button:hover {
            background: #4B5563;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .id-card-container {
                flex-direction: column;
                align-items: center;
                gap: 24px;
                padding: 20px;
            }

            .card {
                width: 90vw;
                max-width: 400px;
                height: auto;
                aspect-ratio: 5/8;
            }
        }

        @media (max-width: 768px) {
            .card {
                max-width: 350px;
            }

            .student-name {
                font-size: 20px;
            }

            .student-class {
                font-size: 14px;
            }

            .nis-number {
                font-size: 24px;
            }

            .qr-code-container {
                width: 160px;
                height: 160px;
            }

            .qr-code-placeholder {
                width: 140px;
                height: 140px;
            }

            .student-info {
                padding: 15px;
            }

            .verification-badge {
                width: 35px;
                height: 35px;
                bottom: 15px;
                left: 15px;
            }

            .logo-section {
                bottom: 15px;
                right: 15px;
            }

            .profile-photo-section {
                height: 350px;
            }
        }

        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .print-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                border: none;
                background: white;
            }

            .print-header,
            .print-footer {
                display: none !important;
            }

            .card {
                box-shadow: none;
                transform: none;
                margin: 0;
                width: 400px !important;
                height: 640px !important;
            }

            .id-card-container {
                gap: 24px;
                justify-content: center;
                align-items: flex-start;
                padding: 0;
            }

            @page {
                size: A4;
                margin: 10mm;
            }

            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Ensure cards don't exceed printable area */
            .card {
                max-width: calc(210mm - 20mm) / 2 - 10px !important;
                width: 380px !important;
                height: 608px !important;
            }
        }
    </style>
</head>

<body>

    @volt('member-card.show')
    <div>
        <div class="print-container">
            <div class="print-header">
                <h1>Kartu Anggota Perpustakaan</h1>
                <p>{{ $setting->name ?? 'Perpustakaan' }} - {{ now()->format('d F Y') }}</p>
            </div>

            <div class="id-card-container font-manrope">
                <!-- Front Card - Student Information -->
                <div class="card front-card">
                    <!-- Profile Photo Section -->
                    <div class="profile-photo-section">
                        @if ($userDetail?->profile_photo)
                            <img src="{{ Storage::url($userDetail->profile_photo) }}" alt="{{ $user->name }}"
                                class="profile-photo">
                        @else
                            <div class="profile-photo-placeholder">
                                <div class="profile-initial">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>
                        @endif

                        <!-- Verification Badge -->
                        <div class="verification-badge">
                            <svg class="check-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Student Information -->
                    <div class="student-info">
                        <div class="student-name">{{ $user->name }}</div>
                        <div class="student-class">
                            {{ $userDetail?->class ?? '-' }}
                        </div>

                        <!-- Additional Information -->
                        <div class="additional-info">
                            <div class="info-row mb-0">
                                <span class="info-value">
                                    {{ $userDetail?->nisn ?? '-' }}
                                </span>
                            </div>
                            <div class="info-row mb-0">
                                <span class="info-value">{{ $userDetail?->phone_number ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Logo Section -->
                    <div class="logo-section">
                        <svg class="logo-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2L14.09 8.26L20.18 9.27L16.09 13.14L17.09 19.27L12 16L6.91 19.27L7.91 13.14L3.82 9.27L9.91 8.26L12 2Z" />
                        </svg>
                        <div class="logo-text">Perpustakaan Digital</div>
                    </div>
                </div>

                <!-- Back Card - QR Code -->
                <div class="card back-card">
                    <!-- NIS Section -->
                    <div class="nis-section">
                        <div class="nis-label">Nomor Induk Siswa</div>
                        <div class="nis-number">{{ $userDetail->nis ?? '12345678' }}</div>
                    </div>

                    <!-- QR Code Section -->
                    <div class="qr-code-section">
                        <div class="qr-code-container">
                            @if ($userDetail?->qr_code)
                                @php
                                    $qrCodeExists = Storage::disk('public')->exists($userDetail->qr_code);
                                @endphp
                                @if ($qrCodeExists)
                                    <img src="{{ Storage::url($userDetail->qr_code) }}" alt="QR Code - {{ $user->name }}"
                                        class="qr-code-image">
                                @else
                                    <div class="qr-code-placeholder">
                                        <div class="qr-code-text">
                                            <div class="qr-code-title">QR Code</div>
                                            <div class="qr-code-subtitle">File tidak ditemukan</div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="qr-code-placeholder">
                                    <div class="qr-code-text">
                                        <div class="qr-code-title">QR Code</div>
                                        <div class="qr-code-subtitle">Belum dibuat</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Institution Information -->
                        <div class="institution-section">
                            <div class="institution-name">{{ $setting?->name ?? 'Perpustakaan Digital' }}</div>
                            <div class="institution-address">
                                {{ $setting?->address ?? 'Jl. Pendidikan No. 123, Jakarta' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="print-footer">
                <button onclick="window.print()" class="print-button">
                    <svg style="width: 16px; height: 16px; margin-right: 8px; display: inline-block; vertical-align: middle;"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 0-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Cetak Kartu
                </button>
                <button onclick="window.close()" class="back-button">
                    <svg style="width: 16px; height: 16px; margin-right: 8px; display: inline-block; vertical-align: middle;"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endvolt

    <script>
        // Auto print when page loads
        window.addEventListener('load', function () {
            setTimeout(() => {
                window.print();
            }, 1000);
        });

        // Close window after printing
        window.addEventListener('afterprint', function () {
            setTimeout(() => {
                window.close();
            }, 100);
        });
    </script>
</body>

</html>