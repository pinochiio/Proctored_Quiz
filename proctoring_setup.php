<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// You can now use $_SESSION['user_id'] to identify the logged-in user.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proctoring Setup</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/proctoring_setup.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand nav-logo" href="#">
                Quiz<span>Eye</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link help-support" href="#">Help/Support</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link home" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="setup-container">
        <h1>Proctoring Setup</h1>
        <p id="head-id">Complete the following steps to ensure your device is ready for the proctored exam.</p>

        <!-- Step 1: Camera Setup Card -->
        <div id="camera-card" class="setup-card">
            <h2>1. Enable Camera</h2>
            <p>Click the button to enable your camera and ensure your face is visible.</p>
            <button id="camera-btn" onclick="enableCamera()">Enable Camera</button>
            <div id="camera-status" class="status-text"></div>
        </div>

        <!-- Step 2: Microphone Setup Card -->
        <div id="mic-card" class="setup-card disabled">
            <h2>2. Enable Microphone</h2>
            <p>Enable your microphone to check if it's working properly.</p>
            <button id="mic-btn" onclick="enableMicrophone()">Enable Microphone</button>
            <div id="mic-status" class="status-text"></div>
        </div>

        <!-- Step 3: Full Screen Sharing Setup Card -->
        <div id="screen-card" class="setup-card disabled">
            <h2>3. Share Full Screen</h2>
            <p>Click the button to share your full screen for proctoring purposes.</p>
            <button id="screen-btn" onclick="shareScreen()">Share Full Screen</button>
            <div id="screen-status" class="status-text"></div>
        </div>

        <!-- Ready to Start the Test -->
        <div id="ready-card" class="setup-card disabled">
            <h2>You're ready to start the test!</h2>
            <button class="start-test" onclick="startTest()">Start the Test</button>
        </div>
    </section>

    <footer>
        <p class="footer-text">&copy; 2024 Your Company. All rights reserved.</p>
    </footer>

    <script>
        // Function to handle camera setup and permission
        function enableCamera() {
            const cameraCard = document.getElementById('camera-card');
            const cameraStatus = document.getElementById('camera-status');
            const micCard = document.getElementById('mic-card');

            // Request camera permission
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
                    cameraStatus.innerHTML = 'Camera enabled successfully!';
                    cameraCard.classList.add('orange-border'); // Add orange border
                    micCard.classList.remove('disabled'); // Unlock microphone step
                }).catch(function(err) {
                    cameraStatus.innerHTML = 'Camera access denied.';
                });
            } else {
                cameraStatus.innerHTML = 'Camera not supported on this browser.';
            }
        }

        // Function to enable microphone and unlock next step
        function enableMicrophone() {
            const micCard = document.getElementById('mic-card');
            const micStatus = document.getElementById('mic-status');
            const screenCard = document.getElementById('screen-card');

            // Request microphone permission
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ audio: true }).then(function(stream) {
                    micStatus.innerHTML = 'Microphone enabled successfully!';
                    micCard.classList.add('orange-border'); // Add orange border
                    screenCard.classList.remove('disabled'); // Unlock screen sharing step
                }).catch(function(err) {
                    micStatus.innerHTML = 'Microphone access denied.';
                });
            } else {
                micStatus.innerHTML = 'Microphone not supported on this browser.';
            }
        }

        // Function to enable screen sharing and unlock the test start button
        function shareScreen() {
            const screenCard = document.getElementById('screen-card');
            const screenStatus = document.getElementById('screen-status');
            const readyCard = document.getElementById('ready-card');

            // Request screen sharing
            if (navigator.mediaDevices.getDisplayMedia) {
                navigator.mediaDevices.getDisplayMedia({ video: true }).then(function(stream) {
                    screenStatus.innerHTML = 'Screen sharing enabled successfully!';
                    screenCard.classList.add('orange-border'); // Add orange border
                    readyCard.classList.remove('disabled'); // Unlock test start step
                }).catch(function(err) {
                    screenStatus.innerHTML = 'Screen sharing access denied.';
                });
            } else {
                screenStatus.innerHTML = 'Screen sharing not supported on this browser.';
            }
        }

        // Start the test by redirecting to the test page
        function startTest() {
            window.location.href = '/start-test'; // Redirect to actual test page
        }
    </script>

    <!-- Bootstrap JS (for navbar toggling) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
