@props(['module'])

@php
    $preferenceService = app(\App\Services\PreferenceService::class);
    $prefs = $preferenceService->getModulePreferences($module);
    $hasActivePref = !empty($prefs['default_filter']) || !empty($prefs['default_scope']);
@endphp

@if($hasActivePref)
    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
        <i class="fas fa-magic me-2"></i>
        <div>
            <strong>Smart View Applied:</strong> 
            Your personal preferences have automatically filtered this list. 
            @if(isset($prefs['natural_language_summary']))
                <span class="fst-italic">"{{ $prefs['natural_language_summary'] }}"</span>
            @endif
        </div>
        <div class="ms-auto">
            <a href="{{ route('preferences.index', ['box_slug' => request()->route('box_slug')]) }}" class="btn btn-sm btn-outline-info">Edit</a>
            <!-- Form to clear session override if we built that out -->
        </div>
    </div>
@endif
