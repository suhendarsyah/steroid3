<x-filament-panels::page>

<!-- filter dashboard -->

@if (method_exists($this, 'getFiltersForm'))
    <div class="mb-6">
        {{ $this->getFiltersForm() }}
    </div>
@endif


    @php
        $user = auth()->user();

        $roles = [
            'super_admin' => [
                'title' => 'Super Admin',
                'message' => 'Kelola pengguna, data master, dan pengaturan sistem.',
            ],
            'kepala_dinas' => [
                'title' => 'Kepala Dinas',
                'message' => 'Pantau kinerja strategis lintas bidang peternakan dan perikanan.',
            ],
            'kepala_bidang' => [
                'title' => 'Kepala Bidang',
                'message' => 'Monitor aktivitas UPT dan capaian teknis pada bidang Anda.',
            ],
            'upt' => [
                'title' => 'UPT',
                'message' => 'Lakukan input dan pemutakhiran data teknis secara berkala.',
            ],
            'perencanaan' => [
                'title' => 'Perencanaan',
                'message' => 'Gunakan data sebagai dasar analisis dan perencanaan program.',
            ],
        ];

        $activeRole = collect(array_keys($roles))
            ->first(fn ($role) => $user?->hasRole($role));
    @endphp

    <div class="space-y-4">
        <h1 class="text-2xl font-bold">
            Selamat Datang
        </h1>

        @if ($activeRole)
            <p>
                Anda login sebagai <strong>{{ $roles[$activeRole]['title'] }}</strong>.
            </p>
            <p class="text-gray-600">
                {{ $roles[$activeRole]['message'] }}
            </p>
        @endif
    </div>

    @if ($activeRole === 'kepala_dinas' && !empty($this->kepalaDinasSummary))
        <div class="mt-6 border rounded-lg p-4 bg-gray-50">
            <h2 class="font-semibold mb-2">Fokus</h2>
            <ul class="list-disc list-inside text-gray-700">
                <li>Monitoring kondisi umum peternakan dan perikanan</li>
                <li>Pengawasan kinerja bidang secara makro</li>
                <li>Identifikasi area yang memerlukan perhatian</li>
            </ul>
        </div>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border rounded-lg p-4 bg-white">
                <p class="text-sm text-gray-500">Total Pengguna Sistem</p>
                <p class="text-2xl font-bold">
                    {{ $this->kepalaDinasSummary['total_user'] ?? '-' }}
                </p>
            </div>

            <div class="border rounded-lg p-4 bg-white">
                <p class="text-sm text-gray-500">Terakhir Diperbarui</p>
                <p class="text-sm">
                    {{ $this->kepalaDinasSummary['updated_at'] ?? '-' }}
                </p>
            </div>
        </div>
    @endif
</x-filament-panels::page>
