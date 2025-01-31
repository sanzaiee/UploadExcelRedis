@if(session('success'))
    <div class="text-sm text-green-600 dark:text-green-400 space-y-1">{{ session('success') }}</div>
@endif

@if(session('danger'))
    <div class="text-sm text-red-600 dark:text-red-400 space-y-1">{{ session('danger') }}</div>
@endif

@if(session('warning'))
    <div class="text-sm text-yellow-600 dark:text-yellow-400 space-y-1">{{ session('warning') }}</div>
@endif
