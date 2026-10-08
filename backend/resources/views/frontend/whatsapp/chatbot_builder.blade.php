@extends('layouts.main')
@section('title', 'ChaatBoat')

@push('css')
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<!-- CSS -->
<link rel="stylesheet" href="{{ asset('whatsapp-builder/chatbot.css') }}">
@endpush

@section('content')
<div class="page-wrapper">
    <div class="content" style="padding:0;">

<div class="app-container">

    <!-- SIDEBAR -->
    <aside class="chatbot-sidebar" style="padding:15px;">

        <div class="logo-area">
            <h2>Chatbot</h2>
            <span>Flow Builder</span>
        </div>

        <div class="block-title">Chatbot Blocks</div>

        <div class="node-list">

            <div class="drag-node" draggable="true" data-type="message">
                <i class="fa-solid fa-message"></i>
                <span>Message</span>
            </div>

            <div class="drag-node" draggable="true" data-type="question">
                <i class="fa-solid fa-circle-question"></i>
                <span>Question</span>
            </div>

            <div class="drag-node" draggable="true" data-type="button">
                <i class="fa-solid fa-square-check"></i>
                <span>Buttons</span>
            </div>

            <div class="drag-node" draggable="true" data-type="condition">
                <i class="fa-solid fa-code-branch"></i>
                <span>Condition</span>
            </div>

            <div class="drag-node" draggable="true" data-type="api">
                <i class="fa-solid fa-plug"></i>
                <span>API</span>
            </div>

        </div>

    </aside>

    <!-- PREVIEW SIDEBAR (Docked on Left) -->
    <aside id="previewPanel" style="display:none; width: 380px; background: #202c33; border-right: 1px solid #2a3942; padding: 20px; flex-direction: column;">
        <div style="width: 100%; padding: 0; overflow: hidden; background: #efeae2; border-radius: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 10px solid #111; height: 100%; display: flex; flex-direction: column;">
            <div class="preview-header" style="background: #008069; color: #fff; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display:flex; align-items:center; gap: 12px;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" width="30" height="30" style="filter: brightness(0) invert(1);">
                    <div style="display:flex; flex-direction:column;">
                        <h3 style="margin:0; font-size:16px; font-weight:600;">Chatbot Live</h3>
                        <span style="font-size:12px; opacity:0.9;">online</span>
                    </div>
                </div>
                <button id="closePreview" style="background:transparent; border:none; color:#fff; font-size:20px; cursor:pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="preview-body" style="flex: 1; display: flex; flex-direction: column; background: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-color:#e5ddd5; background-size:contain;">
                <div class="chat-window" id="preview-chat" style="flex:1; overflow-y:auto; padding:20px 15px; display:flex; flex-direction:column; gap:8px;">
                    <!-- Preview injected here -->
                </div>
                <div style="padding: 10px; background: #f0f0f0; display: flex; gap: 10px; align-items:center;">
                    <input type="text" id="preview-input-field" placeholder="Type a message" style="flex:1; border-radius: 20px; border:none; padding: 12px 15px; background: #fff; color:#333; outline:none; font-size:14px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);" onkeypress="if(event.key==='Enter') submitPreviewAnswer()">
                    <button onclick="submitPreviewAnswer()" style="background: #00a884; color: #fff; border:none; border-radius:50%; width:42px; height:42px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                        <i class="fa-solid fa-paper-plane" style="transform: translateX(-1px);"></i>
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content" style="display:flex; flex-direction:column; min-width: 0; overflow: hidden;">
    
        <!-- TOP BAR -->
        <header class="topbar" style="display:flex; justify-content:space-between; padding: 0 20px; align-items:center; flex-shrink: 0; border-bottom: 1px solid #e5e7eb; background: #fff; box-sizing: border-box;">
            <div class="topbar-title">
                <h3 style="margin:0;">Chatbot Flow Builder</h3>
            </div>
            <div class="topbar-actions" style="display:flex; gap:10px; z-index: 50;">
                <button class="btn secondary" onclick="document.getElementById('globalSettingsModal').style.display='flex'">
                    <i class="fa-solid fa-gear"></i>
                    Settings
                </button>
                <button class="btn secondary" id="preview-btn">
                    <i class="fa-solid fa-eye"></i>
                    Preview
                </button>
                <button class="btn primary" id="save-btn" onclick="saveChatbot()">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Save Flow
                </button>
            </div>
        </header>

        <!-- FLOW CANVAS -->
        <section class="flow-wrapper" style="flex:1; position:relative;">
            
            <!-- Zoom Controls -->
            <div class="zoom-controls" style="position: absolute; bottom: 30px; right: 30px; z-index: 100; display:flex; flex-direction:column; gap:10px; background: #fff; padding: 10px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <button onclick="zoomIn()" style="width: 40px; height: 40px; border:none; border-radius: 8px; background: #f3f4f6; color: #333; cursor:pointer; font-size:16px; display:flex; align-items:center; justify-content:center;" title="Zoom In">
                    <i class="fa-solid fa-plus"></i>
                </button>
                <button onclick="resetZoom()" style="width: 40px; height: 40px; border:none; border-radius: 8px; background: #f3f4f6; color: #333; cursor:pointer; font-size:16px; display:flex; align-items:center; justify-content:center;" title="Reset Zoom">
                    <i class="fa-solid fa-compress"></i>
                </button>
                <button onclick="zoomOut()" style="width: 40px; height: 40px; border:none; border-radius: 8px; background: #f3f4f6; color: #333; cursor:pointer; font-size:16px; display:flex; align-items:center; justify-content:center;" title="Zoom Out">
                    <i class="fa-solid fa-minus"></i>
                </button>
            </div>

            <div id="flowCanvas" style="transform-origin: 0 0; transition: transform 0.2s ease-out;">
                <!-- Start node will be rendered by JS if loading, or hardcoded if new -->
            </div>
        </section>

    </main>

    <!-- GLOBAL SETTINGS MODAL -->
    <div id="globalSettingsModal" class="eb-modal" style="display:none;">
        <div class="eb-modal-content">
            <div class="eb-modal-header">
                <h3>Chatbot Global Settings</h3>
                <button class="eb-close-btn" onclick="document.getElementById('globalSettingsModal').style.display='none'"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="eb-modal-body">
                <div class="setting-group">
                    <label>Assigned Project</label>
                    <select id="whatsapp-project-id" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 10px; outline: none;">
                        <option value="">No Project (Global)</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ ($chatbot->whatsapp_project_id == $project->id) ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="setting-group">
                    <label>Chatbot Name</label>
                    <input type="text" id="bot-name" value="{{ $chatbot->name ?? 'New Chatbot' }}" placeholder="Enter Chatbot Name...">
                </div>
                <div class="setting-group">
                    <label>Trigger Type</label>
                    <select id="trigger-type" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 10px; outline: none;">
                        <option value="keyword" {{ ($chatbot->trigger_type ?? '') == 'keyword' ? 'selected' : '' }}>Keyword Match</option>
                        <option value="exact_match" {{ ($chatbot->trigger_type ?? '') == 'exact_match' ? 'selected' : '' }}>Exact Match</option>
                        <option value="all_messages" {{ ($chatbot->trigger_type ?? '') == 'all_messages' ? 'selected' : '' }}>All Messages</option>
                    </select>
                </div>
                <div class="setting-group">
                    <label>Trigger Value (Comma separated)</label>
                    <input type="text" id="trigger-value" value="{{ $chatbot->trigger_value ?? '' }}" placeholder="e.g. help, support, menu">
                </div>
                <div class="setting-group" style="display:flex; align-items:center; gap: 8px;">
                    <input type="checkbox" id="set-is-active" {{ ($chatbot->is_active ?? true) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <label style="margin: 0;">Is Active</label>
                </div>
            </div>
            <div class="eb-modal-footer">
                <button class="btn primary full-btn" onclick="document.getElementById('globalSettingsModal').style.display='none'; saveChatbot();">Done</button>
            </div>
        </div>
    </div>

    <!-- NODE SETTINGS MODAL -->
    <div id="nodeSettingsModal" class="eb-modal" style="display:none;">
        <div class="eb-modal-content">
            <div class="eb-modal-header">
                <h3 id="settings-title">Node Settings</h3>
                <button class="eb-close-btn" onclick="document.getElementById('nodeSettingsModal').style.display='none'"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="eb-modal-body" id="node-settings-form">
                <!-- Settings rendered dynamically by JS -->
            </div>
            <div class="eb-modal-footer">
                <button class="btn primary full-btn" onclick="document.getElementById('nodeSettingsModal').style.display='none'">Save Config</button>
            </div>
        </div>
    </div>



</div>
    </div>
</div>

@push('scripts')
<!-- jsPlumb -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsPlumb/2.15.6/js/jsplumb.min.js"></script>

<!-- Panzoom for advanced scroll and zoom -->
<script src="https://unpkg.com/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>

<script>
    window.LaravelData = {
        csrfToken: '{{ csrf_token() }}',
        chatbotId: '{{ $chatbot->id ?? '' }}',
        isEdit: {{ isset($chatbot->id) ? 'true' : 'false' }},
        saveUrl: '{{ isset($chatbot->id) ? box_route('whatsapp.chatbots.update', $chatbot->id) : box_route('whatsapp.chatbots.store') }}',
        redirectUrl: '{{ box_route('whatsapp.chatbots.index') }}',
        flowData: {!! json_encode($chatbot->flow_data ?? null) !!},
        whatsappProjectId: '{{ $chatbot->whatsapp_project_id ?? '' }}',
        chatbotName: '{{ $chatbot->name ?? '' }}',
        triggerType: '{{ $chatbot->trigger_type ?? '' }}',
        triggerValue: '{{ $chatbot->trigger_value ?? '' }}',
        isActive: {{ ($chatbot->is_active ?? true) ? 'true' : 'false' }}
    };
</script>

<!-- JS -->
<script src="{{ asset('whatsapp-builder/chatbot.js') }}"></script>
@endpush

@endsection
