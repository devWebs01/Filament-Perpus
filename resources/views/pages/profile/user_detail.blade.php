<div class="px-6 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Kolom Kiri -->
        <div class="space-y-6">

            <!-- Nama -->
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">Nama
                    Lengkap:</p>
                <p class="text-gray-900 dark:text-white">{{ $user->name }}</p>
            </div>

            <!-- Email -->
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">Email:
                </p>
                <p class="text-gray-900 dark:text-white">{{ $user->email }}
                </p>
            </div>

            <!-- Role -->
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">Role:
                </p>
                <p class="text-gray-900 dark:text-white">
                    {{ $user->role ? ucfirst($user->role) : '-' }}
                </p>
            </div>

            <!-- NIK -->
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">NIK:
                </p>
                <p class="text-gray-900 dark:text-white">
                    {{ $userDetail?->nik ?: '-' }}
                </p>
            </div>

            <!-- Telepon -->
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">No.
                    Telepon:</p>
                <p class="text-gray-900 dark:text-white">
                    {{ $userDetail?->phone_number ?: '-' }}
                </p>
            </div>

            <!-- Tanggal Lahir -->
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">
                    Tanggal Lahir:</p>
                <p class="text-gray-900 dark:text-white">
                    {{ $userDetail?->birth_date ? \Carbon\Carbon::parse($userDetail->birth_date)->format('d M Y') : '-' }}
                </p>
            </div>

            <!-- Tempat Lahir -->
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">Tempat
                    Lahir:</p>
                <p class="text-gray-900 dark:text-white">
                    {{ $userDetail?->birth_place ?: '-' }}
                </p>
            </div>

            <!-- Jenis Kelamin -->
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">Jenis
                    Kelamin:</p>
                <p class="text-gray-900 dark:text-white">
                    {{ $userDetail?->gender ? ($userDetail->gender == 'male' ? 'Laki-laki' : 'Perempuan') : '-' }}
                </p>
            </div>

            <!-- Agama -->
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">Agama:
                </p>
                <p class="text-gray-900 dark:text-white">
                    {{ $userDetail?->religion ? __('religion.' . $userDetail->religion) : '-' }}
                </p>
            </div>

            <!-- Alamat -->
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">
                    Alamat:</p>
                <p class="text-gray-900 dark:text-white">
                    {{ $userDetail?->address ?: '-' }}
                </p>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="space-y-6">

            <!-- NIS -->
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">NIS:
                </p>
                <p class="text-gray-900 dark:text-white">
                    {{ $userDetail?->nis ?: '-' }}
                </p>
            </div>

            <!-- NISN -->
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">NISN:
                </p>
                <p class="text-gray-900 dark:text-white">
                    {{ $userDetail?->nisn ?: '-' }}
                </p>
            </div>

            <!-- Kelas -->
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-sm">Kelas:
                </p>
                <p class="text-gray-900 dark:text-white">
                    {{ $userDetail?->class ?: '-' }}
                </p>
            </div>

        </div>
    </div>
</div>