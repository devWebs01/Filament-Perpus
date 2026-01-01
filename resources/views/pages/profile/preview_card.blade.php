{{-- Custom styles for card profile --}}
@push('styles')
    <style>
        .font-manrope {
            font-family: 'Manrope', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .id-card-container {
            display: flex;
            gap: 48px;
            justify-content: center;
            align-items: flex-start;
            padding: 32px;
            min-height: 100vh;
            background: #f5f5f5;
        }

        .card {
            width: 400px;
            height: 640px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
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
            border-radius: 20px 20px 0 0;
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

        @media (max-width: 480px) {
            .card {
                width: 95vw;
                max-width: 300px;
            }

            .profile-photo-section {
                height: 300px;
            }

            .student-name {
                font-size: 18px;
            }

            .student-class {
                font-size: 12px;
            }

            .nis-number {
                font-size: 20px;
            }

            .qr-code-container {
                width: 140px;
                height: 140px;
            }

            .qr-code-placeholder {
                width: 120px;
                height: 120px;
            }

            .qr-code-title {
                font-size: 14px;
            }

            .qr-code-subtitle {
                font-size: 10px;
            }

            .institution-name {
                font-size: 12px;
            }

            .institution-address {
                font-size: 9px;
            }
        }
    </style>
@endpush

<div class="id-card-container font-manrope">
    {{-- Front Card - Student Information --}}
    <div class="card front-card">
        {{-- Profile Photo Section --}}
        <div class="profile-photo-section">
            @if ($userDetail?->profile_photo)
                <img src="{{ Storage::url($userDetail->profile_photo) }}" alt="{{ $user->name }}" class="profile-photo">
            @else
                <div class="profile-photo-placeholder">
                    <div class="profile-initial">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
            @endif

            {{-- Verification Badge --}}
            <div class="verification-badge">
                <svg class="check-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                </svg>
            </div>
        </div>

        {{-- Student Information --}}
        <div class="student-info">
            <div class="student-name">{{ $user->name }}</div>
            <div class="student-class">
                {{ $userDetail?->class ?? '-' }}
            </div>

            {{-- Additional Information --}}
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

        {{-- Logo Section --}}
        <div class="logo-section">
            <svg class="logo-icon" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M12 2L14.09 8.26L20.18 9.27L16.09 13.14L17.09 19.27L12 16L6.91 19.27L7.91 13.14L3.82 9.27L9.91 8.26L12 2Z" />
            </svg>
            <div class="logo-text">Perpustakaan Digital</div>
        </div>
    </div>

    {{-- Back Card - QR Code --}}
    <div class="card back-card">
        {{-- NIS Section --}}
        <div class="nis-section">
            <div class="nis-label">Nomor Induk Siswa</div>
            <div class="nis-number">{{ $userDetail?->nis ?? '12345678' }}</div>
        </div>

        {{-- QR Code Section --}}
        <div class="qr-code-section">
            <div class="qr-code-container">
                @if ($userDetail?->barcode)
                    @php
                        $qrCodeExists = Storage::disk('public')->exists($userDetail->barcode);
                    @endphp
                    @if ($qrCodeExists)
                        <img src="{{ Storage::url($userDetail->barcode) }}" alt="QR Code - {{ $user->name }}"
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

            {{-- Institution Information --}}
            <div class="institution-section">
                <div class="institution-name">
                    {{ $setting?->name ?? 'Perpustakaan Digital' }}</div>
                <div class="institution-address">{{ $setting?->address ?? 'Jl. Pendidikan No. 123, Jakarta' }}</div>
            </div>
        </div>
    </div>
</div>
