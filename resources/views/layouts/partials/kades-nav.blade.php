@php
    $navs = [
        [
            'route' => 'kades.dashboard',
            'label' => 'Dashboard',
            'icon' => '<svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
        ],
        [
            'route' => 'kades.attendances.index',
            'label' => 'Log Absensi',
            'icon' => '<svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
        ],
        [
            'route' => 'kades.leave-requests.index',
            'label' => 'Pengajuan Izin/Cuti',
            'icon' => '<svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
        ],
    ];
@endphp

@foreach($navs as $nav)
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
@endforeach
