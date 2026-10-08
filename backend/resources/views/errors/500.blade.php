<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Server Error | 500 Error</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #ffffff;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            overflow-x: hidden;
        }
        
        .error-container {
            text-align: center;
            max-width: 600px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 0.8s ease-out;
        }
        
        .illustration-container {
            position: relative;
            width: 100%;
            max-width: 480px;
            margin-bottom: 24px;
        }
        
        .illustration-img {
            width: 100%;
            height: auto;
            max-height: 380px;
            object-fit: contain;
        }
        
        .error-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 12px;
            line-height: 1.2;
        }
        
        .error-desc {
            font-size: 1.05rem;
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.6;
            max-width: 500px;
        }
        
        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #E22812; /* Premium brand red color */
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid #E22812;
            box-shadow: 0 4px 6px -1px rgba(226, 40, 18, 0.15), 0 2px 4px -1px rgba(226, 40, 18, 0.1);
        }
        
        .btn-back:hover {
            background-color: #ffffff;
            color: #E22812;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(226, 40, 18, 0.2), 0 4px 6px -2px rgba(226, 40, 18, 0.1);
        }
        
        .btn-back:active {
            transform: translateY(0);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 576px) {
            .error-title {
                font-size: 1.75rem;
            }
            .error-desc {
                font-size: 0.95rem;
            }
            .btn-back {
                width: 100%;
                padding: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <!-- Gorgeous Vector Illustration -->
        <div class="illustration-container">
            <img src="{{ asset('assets/img/error-500.png') }}" alt="500 Error" class="illustration-img">
        </div>
        
        <!-- Error Content -->
        <h1 class="error-title">Oops, something went wrong</h1>
        <p class="error-desc">Error 500 Page not found. Sorry the page you looking for doesn't exist or has been moved</p>
        
        <!-- Back Button -->
        <a href="{{ url('/') }}" class="btn-back">
            &larr; Back to Dashboard
        </a>
    </div>
</body>
</html>
