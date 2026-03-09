<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Sensor Viewer</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        .container {
            text-align: center;
            background: #1e293b;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            font-size: 1.5rem;
            color: #94a3b8;
            margin-bottom: 20px;
            margin-top: 0;
        }
        .display-box {
            background: #000;
            border: 2px solid #334155;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            position: relative;
        }
        .distance {
            font-family: 'Courier New', Courier, monospace;
            font-size: 5rem;
            font-weight: bold;
            color: #10b981; /* Default Green */
            text-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
            line-height: 1;
        }
        .unit {
            font-size: 1.5rem;
            color: #64748b;
            vertical-align: super;
        }
        .error-msg {
            color: #ef4444;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 15px;
            min-height: 24px;
        }
        .status-dot {
            height: 12px;
            width: 12px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            box-shadow: 0 0 8px #10b981;
            transition: background-color 0.3s;
        }
        .footer {
            margin-top: 25px;
            font-size: 0.85rem;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Live Sensor Distance</h1>
    
    <div class="display-box">
        <div id="distanceDisplay" class="distance">-- <span class="unit">cm</span></div>
    </div>
    
    <div id="errorMessage" class="error-msg"></div>

    <div class="footer">
        <span id="statusDot" class="status-dot"></span>
        <span id="statusText">Waiting for data...</span>
    </div>
</div>

<script>
    const distanceDisplay = document.getElementById('distanceDisplay');
    const errorMessage = document.getElementById('errorMessage');
    const statusDot = document.getElementById('statusDot');
    const statusText = document.getElementById('statusText');

    function updateSensor() {
        fetch('sensor.php')
            .then(response => response.text())
            .then(data => {
                data = data.trim();
                
                if (data.startsWith("ERROR:")) {
                    // Handle Error state
                    distanceDisplay.innerHTML = '-- <span class="unit">cm</span>';
                    distanceDisplay.style.color = '#ef4444'; // Red
                    distanceDisplay.style.textShadow = '0 0 10px rgba(239, 68, 68, 0.4)';
                    
                    errorMessage.innerText = data;
                    
                    statusDot.style.backgroundColor = '#ef4444';
                    statusDot.style.boxShadow = '0 0 8px #ef4444';
                    statusText.innerText = 'Connection Lost';
                } else if (data !== "" && !isNaN(data)) {
                    // Handle Success state
                    distanceDisplay.innerHTML = `${data} <span class="unit">cm</span>`;
                    distanceDisplay.style.color = '#10b981'; // Green
                    distanceDisplay.style.textShadow = '0 0 10px rgba(16, 185, 129, 0.4)';
                    
                    errorMessage.innerText = '';
                    
                    statusDot.style.backgroundColor = '#10b981';
                    statusDot.style.boxShadow = '0 0 8px #10b981';
                    statusText.innerText = 'Connected & Polling (300ms)';
                }
            })
            .catch(error => {
                console.error("Fetch error:", error);
                errorMessage.innerText = "Fetch Event Failed. Is the local PHP server running?";
                statusDot.style.backgroundColor = '#f59e0b'; // Orange
                statusDot.style.boxShadow = '0 0 8px #f59e0b';
                statusText.innerText = 'Server Offline';
            });
    }

    // Call it immediately once, then every 300ms
    updateSensor();
    setInterval(updateSensor, 300);
</script>

</body>
</html>
