<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon | BizzCRM</title>
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
        
        .container {
            text-align: center;
            max-width: 650px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 0.8s ease-out;
        }
        
        .illustration-container {
            position: relative;
            width: 100%;
            max-width: 440px;
            margin-bottom: 20px;
        }
        
        .illustration-img {
            width: 100%;
            height: auto;
            max-height: 340px;
            object-fit: contain;
        }
        
        .title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        
        .subtitle {
            font-size: 1.05rem;
            color: #6b7280;
            margin-bottom: 35px;
            line-height: 1.6;
            max-width: 500px;
        }
        
        /* Countdown Timer Styles */
        .countdown {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
            justify-content: center;
            width: 100%;
        }
        
        .countdown-item {
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 12px;
            padding: 16px;
            min-width: 90px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        
        .countdown-item:hover {
            transform: translateY(-3px);
        }
        
        .countdown-number {
            font-size: 2rem;
            font-weight: 700;
            color: #E22812;
            line-height: 1;
            margin-bottom: 4px;
        }
        
        .countdown-label {
            font-size: 0.8rem;
            color: #9ca3af;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        /* Subscribe Form Styles */
        .subscribe-form {
            display: flex;
            width: 100%;
            max-width: 480px;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .subscribe-input {
            flex: 1;
            padding: 14px 20px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 0.95rem;
            font-family: inherit;
            outline: none;
            transition: all 0.3s ease;
            background-color: #ffffff;
        }
        
        .subscribe-input:focus {
            border-color: #E22812;
            box-shadow: 0 0 0 3px rgba(226, 40, 18, 0.1);
        }
        
        .subscribe-btn {
            background-color: #E22812;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            padding: 14px 28px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(226, 40, 18, 0.15);
        }
        
        .subscribe-btn:hover {
            background-color: #bd1f0d;
            transform: translateY(-1px);
        }
        
        .back-link {
            font-size: 0.95rem;
            color: #6b7280;
            text-decoration: none;
            margin-top: 10px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: #E22812;
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
            .title {
                font-size: 2rem;
            }
            .subtitle {
                font-size: 0.95rem;
                margin-bottom: 25px;
            }
            .countdown {
                gap: 10px;
            }
            .countdown-item {
                min-width: 70px;
                padding: 12px 8px;
            }
            .countdown-number {
                font-size: 1.5rem;
            }
            .countdown-label {
                font-size: 0.7rem;
            }
            .subscribe-form {
                flex-direction: column;
                width: 100%;
            }
            .subscribe-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Gorgeous Vector Illustration -->
        <div class="illustration-container">
            <img src="{{ asset('assets/img/coming-soon.png') }}" alt="Coming Soon" class="illustration-img">
        </div>
        
        <!-- Content -->
        <h1 class="title">Coming Soon</h1>
        <p class="subtitle">Please check back later, We are working hard to get everything just right.</p>
        
        <!-- Live Countdown Timer -->
        <div class="countdown">
            <div class="countdown-item">
                <div class="countdown-number" id="days">54</div>
                <div class="countdown-label">Days</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-number" id="hours">10</div>
                <div class="countdown-label">Hrs</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-number" id="minutes">47</div>
                <div class="countdown-label">Min</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-number" id="seconds">00</div>
                <div class="countdown-label">Sec</div>
            </div>
        </div>
        
        <!-- Subscribe Box -->
        <form class="subscribe-form" onsubmit="event.preventDefault(); alert('Thank you for subscribing!'); this.reset();">
            <input type="email" placeholder="Enter your email address..." class="subscribe-input" required>
            <button type="submit" class="subscribe-btn">Subscribe</button>
        </form>
        
        <a href="{{ url('/') }}" class="back-link">&larr; Back to Dashboard</a>
    </div>

    <!-- Countdown Timer Script -->
    <script>
        // Set dynamic launch target: 54 days, 10 hours, 47 minutes from now
        const launchDate = new Date();
        launchDate.setDate(launchDate.getDate() + 54);
        launchDate.setHours(launchDate.getHours() + 10);
        launchDate.setMinutes(launchDate.getMinutes() + 47);

        function updateCountdown() {
            const now = new Date().getTime();
            const difference = launchDate.getTime() - now;

            if (difference <= 0) {
                document.getElementById('days').innerText = "00";
                document.getElementById('hours').innerText = "00";
                document.getElementById('minutes').innerText = "00";
                document.getElementById('seconds').innerText = "00";
                return;
            }

            const d = Math.floor(difference / (1000 * 60 * 60 * 24));
            const h = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((difference % (1000 * 60)) / 1000);

            document.getElementById('days').innerText = String(d).padStart(2, '0');
            document.getElementById('hours').innerText = String(h).padStart(2, '0');
            document.getElementById('minutes').innerText = String(m).padStart(2, '0');
            document.getElementById('seconds').innerText = String(s).padStart(2, '0');
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
</body>
</html>
