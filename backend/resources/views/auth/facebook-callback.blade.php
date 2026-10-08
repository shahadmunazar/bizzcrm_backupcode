<!DOCTYPE html>
<html>
<head>
    <title>Facebook Authentication</title>
    <meta charset="utf-8">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 400px;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #1877f2;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .message {
            color: #1c1e21;
            font-size: 16px;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <p class="message">Completing connection...</p>
    </div>
    <script>
        @if(isset($success) && $success && isset($code))
            // Send success message to parent window with authorization code
            if (window.opener && !window.opener.closed) {
                try {
                    window.opener.postMessage({
                        type: 'facebook-auth-success',
                        code: '{{ $code }}',
                        waba_id: '{{ $waba_id ?? "" }}',
                        phone_id: '{{ $phone_id ?? "" }}'
                    }, '*');
                    
                    // Small delay to ensure message is sent before closing
                    setTimeout(function() {
                        window.close();
                    }, 200);
                } catch (e) {
                    console.error('Failed to post message to opener:', e);
                    window.close();
                }
            } else {
                // Intelligent Fallback based on connection type
                const lastAuthUrl = localStorage.getItem('last_auth_url');
                const lastTenant = localStorage.getItem('last_tenant_slug') || 'default';
                const connectionType = '{{ $type ?? "api" }}';
                
                let defaultPath = '/' + lastTenant + '/settings/integrations';
                if (connectionType === 'whatsapp') {
                    defaultPath = '/' + lastTenant + '/whatsapp/dashboard';
                }
                
                const fallbackUrl = lastAuthUrl || (defaultPath + '?success=1');
                window.location.href = fallbackUrl;
            }
        @else
            if (window.opener && !window.opener.closed) {
                window.opener.postMessage({
                    type: 'facebook-auth-error',
                    error: '{{ $error ?? "Unknown error" }}'
                }, '*');
                setTimeout(function() {
                    window.close();
                }, 200);
            } else {
                const lastAuthUrl = localStorage.getItem('last_auth_url');
                const lastTenant = localStorage.getItem('last_tenant_slug') || 'default';
                const connectionType = '{{ $type ?? "api" }}';
                
                let defaultPath = '/' + lastTenant + '/settings/integrations';
                if (connectionType === 'whatsapp') {
                    defaultPath = '/' + lastTenant + '/whatsapp/dashboard';
                }
                
                const fallbackUrl = lastAuthUrl || (defaultPath + '?error=auth_failed');
                window.location.href = fallbackUrl;
            }
        @endif

        // Ultimate Fallback: Close after 1.5 seconds if still open
        setTimeout(function() {
            if (window.opener) {
                window.close();
            }
        }, 1500);
    </script>
</body>
</html>

