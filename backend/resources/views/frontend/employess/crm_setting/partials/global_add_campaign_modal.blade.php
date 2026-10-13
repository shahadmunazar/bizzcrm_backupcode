@php
    $globalWaBox = $box ?? null;
    $globalWaUserId = \Illuminate\Support\Facades\Auth::id();
    $globalWaBoxUser = \App\Models\BoxUser::where('user_id', $globalWaUserId)->first();
    if ($globalWaBoxUser) {
        $globalWaTemplates = \App\Models\WhatsAppTemplate::where('box_id', $globalWaBoxUser->box_id)->where('whats_app_status', 'APPROVED')->get();
        $globalWaChatbots = \App\Models\WhatsAppChatbot::where('box_id', $globalWaBoxUser->box_id)->where('is_active', 1)->get();
        if (!$globalWaBox) {
            $globalWaBox = \App\Models\Box::find($globalWaBoxUser->box_id);
        }
    } else {
        $globalWaTemplates = collect();
        $globalWaChatbots = collect();
    }
@endphp

@if($globalWaBox)
    @include('whatsapp.partials.upload_campaign_modal', ['templates' => $globalWaTemplates, 'chatbots' => $globalWaChatbots, 'box' => $globalWaBox])
    @push('scripts')
    <script>
    $(document).ready(function() {
        @include('whatsapp.partials.upload_campaign_js', ['templates' => $globalWaTemplates, 'chatbots' => $globalWaChatbots, 'box' => $globalWaBox])
    });
    </script>
    @endpush
@endif
