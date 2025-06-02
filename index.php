<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="oaHub">
    <meta property="og:title" content="oaHub">
    <meta property="og:description" content="oaHub">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://oahub.com.tr">
    <title>oaHub</title>
    <style>
        @font-face {
            font-family: 'Arciform';
            src: url('Arciform.ttf') format('truetype');
        }

        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: white;
            text-align: center;
            font-family: 'Arciform', sans-serif;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
        }

        .logo {
            font-family: 'Arciform', sans-serif;
            font-size: 4rem;
            font-weight: bold;
            letter-spacing: .5rem;
            margin-bottom: 2rem;
        }

        .oasrv {
            color: #00a8cc;
        }

        .com {
            color: #dbe9ee;
        }

        h3 {
            font-family: 'Arciform', sans-serif;
            font-size: 1.2rem;
            margin-top: 1rem;
            color: #dbe9ee;
            margin-bottom: 2rem;
        }

        .toggle-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #00a8cc;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Arciform', sans-serif;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .toggle-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .endpoints {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 10px;
            max-width: 600px;
            width: 100%;
            text-align: left;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .endpoints.show {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .endpoint-group {
            margin-bottom: 1.5rem;
        }

        .endpoint-group h4 {
            color: #00a8cc;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
            font-family: 'Arciform', sans-serif;
        }

        .endpoint {
            color: #dbe9ee;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.9rem;
            margin: 0.3rem 0;
            padding: 0.3rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .endpoint:hover {
            color: #00a8cc;
            padding-left: 0.5rem;
        }

        .endpoint:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <span class="oasrv">oa</span><span class="com">Hub</span>
        </div>
        <h3>Kodun Olduğu Her Yerdeyim</h3>
        
        <button class="toggle-btn" onclick="toggleEndpoints()">API Endpointleri</button>
        
        <div class="endpoints" id="endpoints">
            <div class="endpoint-group">
                <h4>Meal Endpointleri</h4>
                <div class="endpoint" onclick="openEndpoint('/oahub/v1/meal')">GET /oahub/v1/meal</div>
                <div class="endpoint" onclick="openEndpoint('/oahub/v1/meal/1')">GET /oahub/v1/meal/[ayetno]</div>
                <div class="endpoint" onclick="openEndpoint('/oahub/v1/search/meal/test')">GET /oahub/v1/search/meal/[keyword]</div>
            </div>

            <div class="endpoint-group">
                <h4>Kavram Endpointleri</h4>
                <div class="endpoint" onclick="openEndpoint('/oahub/v1/kavram')">GET /oahub/v1/kavram</div>
                <div class="endpoint" onclick="openEndpoint('/oahub/v1/kavram/1')">GET /oahub/v1/kavram/[kavramno]</div>
                <div class="endpoint" onclick="openEndpoint('/oahub/v1/search/kavram/test')">GET /oahub/v1/search/kavram/[keyword]</div>
            </div>

            <div class="endpoint-group">
                <h4>Sure Listesi</h4>
                <div class="endpoint" onclick="openEndpoint('/oahub/v1/surelist')">GET /oahub/v1/surelist</div>
            </div>

            <div class="endpoint-group">
                <h4>Diğer Endpointler</h4>
                <div class="endpoint" onclick="openEndpoint('/oahub/v1/version')">GET /oahub/v1/version</div>
                <div class="endpoint" onclick="openEndpoint('/oahub/v1/count/meal')">GET /oahub/v1/count/meal</div>
                <div class="endpoint" onclick="openEndpoint('/oahub/v1/count/kavram')">GET /oahub/v1/count/kavram</div>
            </div>
            <div class="endpoint-group">
                <h4>Cron Trigger</h4>
                <div class="endpoint" onclick="openEndpoint('/cronjob/jsonCleaner.php')">Trigger</div>

            </div>
        </div>
    </div>

    <script>
        function toggleEndpoints() {
            const endpoints = document.getElementById('endpoints');
            endpoints.classList.toggle('show');
        }

        function openEndpoint(path) {
            const currentDomain = window.location.origin;
            window.open(currentDomain + path, '_blank');
        }
    </script>
</body>

</html>