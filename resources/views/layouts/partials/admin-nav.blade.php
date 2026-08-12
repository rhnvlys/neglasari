@php
    $navs = [
        [
            'route' => 'admin.dashboard',
            'label' => 'Dashboard Admin',
            'icon' => '<svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
            'roles' => ['Admin', 'Super Admin', 'Admin Desa', 'Kepala Desa']
        ],
        [
            'route' => 'admin.employees.index',
            'label' => 'Data Anggota & Perangkat',
            'icon' => '<svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
            'roles' => ['Admin', 'Super Admin', 'Admin Desa']
        ],
        [
            'route' => 'admin.positions.index',
            'label' => 'Jabatan',
            'icon' => '<svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>',
            'roles' => ['Admin', 'Super Admin', 'Admin Desa']
        ],
        [
            'route' => 'admin.schedules.index',
            'label' => 'Jadwal Kerja',
            'icon' => '<svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'roles' => ['Admin', 'Super Admin', 'Admin Desa']
        ],
        [
            'route' => 'admin.locations.index',
            'label' => 'Lokasi Kantor',
            'icon' => '<svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
            'roles' => ['Admin', 'Super Admin', 'Admin Desa']
        ],
        [
            'route' => 'admin.attendances.index',
            'label' => 'Log Absensi',
            'icon' => '<svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
            'roles' => ['Admin', 'Super Admin', 'Admin Desa', 'Kepala Desa']
        ],
        [
            'route' => 'admin.leave-requests.index',
            'label' => 'Pengajuan Izin/Cuti',
            'icon' => '<svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
            'roles' => ['Admin', 'Super Admin', 'Admin Desa', 'Kepala Desa']
        ],
        [
            'route' => 'admin.holidays.index',
            'label' => 'Hari Libur',
            'icon' => '<svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
            'roles' => ['Admin', 'Super Admin', 'Admin Desa']
        ],
        [
            'route' => 'admin.settings.index',
            'label' => 'Pengaturan',
            'icon' => '<svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
            'roles' => ['Admin', 'Super Admin', 'Admin Desa']
        ],
        [
            'route' => 'admin.audit-logs.index',
            'label' => 'Log Aktivitas',
            'icon' => '<svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
            'roles' => ['Admin', 'Super Admin']
        ],
    ];
@endphp

@foreach($navs as $nav)
    @if(Auth::user()->hasAnyRole($nav['roles']))
        @php
            $isActive = false;
            try {
                $currentRouteName = Route::currentRouteName();
                $baseRoute = explode('.', $nav['route'])[0] . '.' . explode('.', $nav['route'])[1];
                $isActive = str_starts_with($currentRouteName, $baseRoute);
            } catch (\Exception $e) {}
        @endphp
        
        <a href="{{ Route::has($nav['route']) ? route($nav['route']) : '#' }}" 
           class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition duration-150 ease-in-out 
                  {{ $isActive ? 'bg-neglasari-accent text-white shadow-md' : 'text-gray-300 hover:bg-neglasari-main hover:text-white' }}">
            {!! $nav['icon'] !!}
            {{ $nav['label'] }}
        </a>
    @endif
@endforeach
