<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Webhook Tester</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; margin: 0; padding: 2rem; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        h1 { margin-top: 0; color: #111827; }
        p { color: #4b5563; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
        input[type="text"] { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-size: 1rem; }
        button { background: #25D366; color: white; border: none; padding: 0.75rem 1.5rem; font-size: 1rem; border-radius: 6px; cursor: pointer; font-weight: 600; width: 100%; }
        button:hover { background: #1da851; }
        .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-weight: 500; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .ngrok-info { background: #eff6ff; padding: 1rem; border-radius: 6px; border: 1px solid #bfdbfe; margin-top: 2rem; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>💬 WhatsApp Local Tester</h1>
        <p>Instantly simulate an incoming customer message to your local database.</p>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('whatsapp.tester.simulate', ['box_slug' => request()->route('box_slug')]) }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="waba_id">WhatsApp Business Account ID (WABA ID)</label>
                <input type="text" id="waba_id" name="waba_id" value="319518821236508" required placeholder="e.g. 319518821236508">
            </div>

            <div class="form-group">
                <label for="phone">Customer Mobile Number</label>
                <input type="text" id="phone" name="phone" placeholder="Enter 10-digit mobile number" required>
            </div>

            <div class="form-group">
                <label for="message">Test Message</label>
                <input type="text" id="message" name="message" value="Hello! This is a test from the local simulator." required>
            </div>

            <button type="submit">Send Test Message</button>
        </form>

        <div class="ngrok-info">
            <strong>🚀 How to connect Meta directly to Localhost:</strong>
            <ol style="margin-bottom: 0; padding-left: 1.2rem;">
                <li>Run <code>ngrok http 80</code> (or your XAMPP port) in terminal.</li>
                <li>Copy the <code>https://....ngrok-free.app</code> URL.</li>
                <li>Go to the Meta Dashboard -> WhatsApp -> Configuration.</li>
                <li>Set Webhook URL to: <code>YOUR_NGROK_URL/webhook/whatsapp</code></li>
                <li>Verify Token: <code>BizzWhatsAppWebhook2026</code></li>
            </ol>
        </div>
    </div>
</body>
</html>
