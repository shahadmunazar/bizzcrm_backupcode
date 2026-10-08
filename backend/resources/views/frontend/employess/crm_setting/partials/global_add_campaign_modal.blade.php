@php
    if (!isset($templates) || !isset($box)) {
        $userId = \Illuminate\Support\Facades\Auth::id();
        $boxUser = \App\Models\BoxUser::where('user_id', $userId)->first();
        if ($boxUser) {
            $templates = \App\Models\WhatsAppTemplate::where('box_id', $boxUser->box_id)->where('whats_app_status', 'APPROVED')->get();
            $chatbots = \App\Models\WhatsAppChatbot::where('box_id', $boxUser->box_id)->where('is_active', 1)->get();
            $box = \App\Models\Box::find($boxUser->box_id);
        } else {
            $templates = collect();
            $chatbots = collect();
            $box = null;
        }
    }
@endphp

@if($box)
    @include('whatsapp.partials.upload_campaign_modal')
    @push('scripts')
    <script>
    $(document).ready(function() {
        @include('whatsapp.partials.upload_campaign_js')
    });
    </script>
    @endpush
@endif
