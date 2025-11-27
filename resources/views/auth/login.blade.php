<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Coffe - Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary-color: #3b6b0d;
            --primary-dark: #335e0c;
            --accent-color: #d4a574;
            --text-light: #6b7280;
            --text-dark: #1f2937;
        }
        
        body {
            font-family: 'Inter', 'Arial', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            overflow: hidden;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 107, 13, 0.3);
        }
        
        .btn-secondary {
            background-color: white;
            border: 1px solid #e5e7eb;
            color: var(--text-dark);
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .input-field {
            transition: all 0.3s ease;
            border: 1px solid #d1d5db;
        }
        
        .input-field:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 107, 13, 0.1);
        }
        
        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        
        .logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        .login-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            width: 100%;
            max-width: 380px;
            position: relative;
            overflow: hidden;
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--accent-color) 100%);
        }
        
        .title {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .subtitle {
            margin: 0;
            padding: 0;
            font-size: 14px;
            color: #555;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 1rem 0;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #e5e7eb;
        }
        
        .divider-text {
            padding: 0 1rem;
            color: var(--text-light);
            font-size: 0.875rem;
        }
        
        .footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 14px;
            color: #555;
            margin-top: 1rem;
        }
        
        .footer-logo {
            height: 80px;
            width: auto;
            vertical-align: middle;
        }
        
        /* Notification styles */
        .notification {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            transform: translateX(150%);
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 1000;
            max-width: 350px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.success {
            background-color: var(--primary-color);
            color: white;
        }
        
        .notification.error {
            background-color: rgba(239, 68, 68, 0.9);
            color: white;
        }
        
        .notification-icon {
            margin-right: 0.75rem;
            flex-shrink: 0;
        }
        
        .notification-close {
            margin-left: 1rem;
            cursor: pointer;
            flex-shrink: 0;
            opacity: 0.7;
            transition: opacity 0.2s;
        }
        
        .notification-close:hover {
            opacity: 1;
        }

        /* Face ID Modal Styles */
        .faceid-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .faceid-modal.active {
            opacity: 1;
            visibility: visible;
        }

        .faceid-content {
            background-color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            width: 90%;
            max-width: 450px;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .faceid-camera-container {
            width: 100%;
            height: 250px;
            background-color: #f3f4f6;
            border-radius: 0.5rem;
            margin: 1rem 0;
            overflow: hidden;
            position: relative;
        }

        .faceid-camera {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .faceid-scanning {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                to bottom,
                rgba(59, 107, 13, 0.2) 0%,
                transparent 50%,
                rgba(59, 107, 13, 0.2) 100%
            );
            animation: scanning 2s infinite linear;
        }

        @keyframes scanning {
            0% {
                transform: translateY(-100%);
            }
            100% {
                transform: translateY(100%);
            }
        }

        .faceid-status {
            margin-top: 0.75rem;
            font-weight: 500;
        }

        .faceid-success {
            color: var(--primary-color);
        }

        .faceid-error {
            color: #ef4444;
        }

        .faceid-loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(59, 107, 13, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive adjustments */
        @media (max-width: 480px) {
            .login-card {
                padding: 1.25rem;
            }
            
            .logo {
                width: 100px;
                height: 100px;
            }
            
            .footer-logo {
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <!-- Notification container -->
    <div id="notification-container"></div>

    <!-- Face ID Modal -->
    <div class="faceid-modal" id="faceidModal">
        <div class="faceid-content">
            <h3 class="text-xl font-semibold text-gray-800">Login dengan Face ID</h3>
            <p class="text-gray-600 mt-2">Posisikan wajah Anda di depan kamera</p>
            
            <div class="faceid-camera-container">
                <video id="faceidVideo" class="faceid-camera" autoplay playsinline></video>
                <div class="faceid-scanning" id="faceidScanning"></div>
                <canvas id="faceidCanvas" style="display: none;"></canvas>
            </div>
            
            <div class="faceid-status" id="faceidStatus">
                <div class="faceid-loading"></div>
                <span class="ml-2">Menginisialisasi kamera...</span>
            </div>
            
            <div class="mt-4 flex justify-center space-x-3">
                <button 
                    type="button" 
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition"
                    id="faceidCancel"
                >
                    Batalkan
                </button>
                <button 
                    type="button" 
                    class="px-4 py-2 btn-primary text-white rounded-lg hover:shadow-md transition"
                    id="faceidRetry"
                    style="display: none;"
                >
                    Coba Lagi
                </button>
            </div>
        </div>
    </div>

    <!-- Logo and Title -->
    <div class="logo-container">
        <img src="/images/m-coffe.png" alt="M-Coffee Logo" class="logo" />
        <p class="subtitle">Sistem Manajemen Bisnis Terintegrasi</p>
    </div>

    <!-- Login Card -->
    <div class="login-card">
        <div class="mb-4">
            <h2 class="title text-xl text-center">Masuk ke Akun Anda</h2>
            <p class="subtitle text-center">Gunakan kredensial Anda untuk mengakses sistem</p>
        </div>

        <form id="loginForm">
            @csrf
            <div class="mb-3">
                <label class="block text-gray-700 text-sm font-medium mb-2" for="email">Email</label>
                <div class="relative">
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Masukkan email"
                        class="w-full px-4 py-3 pl-10 input-field rounded-lg focus:outline-none transition"
                        required
                    >
                    <i data-lucide="user" class="absolute left-3 top-3 text-gray-400 w-5 h-5"></i>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2" for="password">Password</label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Masukkan password"
                        class="w-full px-4 py-3 pl-10 input-field rounded-lg focus:outline-none transition"
                        required
                    >
                    <i data-lucide="lock" class="absolute left-3 top-3 text-gray-400 w-5 h-5"></i>
                    <i data-lucide="eye" class="absolute right-3 top-3 text-gray-400 w-5 h-5 cursor-pointer" id="togglePassword"></i>
                </div>
            </div>

            <div class="mb-4 flex items-center">
                <input 
                    type="checkbox" 
                    id="remember" 
                    name="remember"
                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                >
                <label for="remember" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
            </div>

            <button 
                type="submit" 
                class="w-full btn-primary font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition mb-3"
            >
                Masuk
            </button>

            <div class="divider">
                <span class="divider-text">Atau</span>
            </div>

            <!-- Face ID Login Button -->
            <button 
                type="button" 
                id="faceidLoginBtn"
                class="w-full btn-secondary font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition flex items-center justify-center"
            >
                <i data-lucide="scan-face" class="w-5 h-5 mr-2"></i>
                Login dengan Face ID
            </button>
        </form>
    </div>

    <div class="footer">
        © Copyright 
        <span class="font-medium">
            <img src="/images/doa-ibuk.png" alt="IT Solution Logo" class="footer-logo" />
        </span>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Toggle between eye and eye-off icons
            const icon = this;
            if (icon.getAttribute('data-lucide') === 'eye') {
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        });

        // Notification system
        function showNotification(type, message) {
            const container = document.getElementById('notification-container');
            const notification = document.createElement('div');
            const iconName = type === 'success' ? 'check-circle' : 'alert-circle';
            
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <div class="notification-icon">
                    <i data-lucide="${iconName}" class="w-5 h-5"></i>
                </div>
                <div class="notification-message flex-1 text-sm font-medium">${message}</div>
                <div class="notification-close">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </div>
            `;
            
            container.appendChild(notification);
            lucide.createIcons();
            
            // Show notification
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            // Auto-remove after 5 seconds
            const autoRemove = setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    container.removeChild(notification);
                }, 300);
            }, 5000);
            
            // Manual close
            notification.querySelector('.notification-close').addEventListener('click', () => {
                clearTimeout(autoRemove);
                notification.classList.remove('show');
                setTimeout(() => {
                    container.removeChild(notification);
                }, 300);
            });
        }

        // Example notification functions
        function showError(message) {
            showNotification('error', message);
        }

        function showSuccess(message) {
            showNotification('success', message);
        }

        // Face ID Login Implementation
        const faceidModal = document.getElementById('faceidModal');
        const faceidVideo = document.getElementById('faceidVideo');
        const faceidCanvas = document.getElementById('faceidCanvas');
        const faceidStatus = document.getElementById('faceidStatus');
        const faceidScanning = document.getElementById('faceidScanning');
        const faceidCancel = document.getElementById('faceidCancel');
        const faceidRetry = document.getElementById('faceidRetry');
        const faceidLoginBtn = document.getElementById('faceidLoginBtn');

        let stream = null;
        let isScanning = false;

        // Open Face ID modal
        faceidLoginBtn.addEventListener('click', () => {
            faceidModal.classList.add('active');
            startFaceRecognition();
        });

        // Close Face ID modal
        faceidCancel.addEventListener('click', () => {
            stopFaceRecognition();
            faceidModal.classList.remove('active');
        });

        // Retry Face ID
        faceidRetry.addEventListener('click', () => {
            faceidRetry.style.display = 'none';
            faceidScanning.style.display = 'block';
            faceidStatus.innerHTML = '<div class="faceid-loading"></div><span class="ml-2">Memindai wajah...</span>';
            startFaceRecognition();
        });

        // Start face recognition
        async function startFaceRecognition() {
            try {
                faceidStatus.innerHTML = '<div class="faceid-loading"></div><span class="ml-2">Mengakses kamera...</span>';
                
                // Access camera
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: 640, 
                        height: 480,
                        facingMode: 'user' 
                    } 
                });
                
                faceidVideo.srcObject = stream;
                faceidStatus.innerHTML = '<div class="faceid-loading"></div><span class="ml-2">Memindai wajah...</span>';
                isScanning = true;
                
                // Start face detection
                detectFace();
            } catch (error) {
                console.error('Error accessing camera:', error);
                faceidStatus.innerHTML = `<span class="faceid-error">Tidak dapat mengakses kamera. Pastikan Anda memberikan izin.</span>`;
                faceidRetry.style.display = 'inline-block';
                faceidScanning.style.display = 'none';
            }
        }

        // Stop face recognition
        function stopFaceRecognition() {
            isScanning = false;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        // Face detection function
        function detectFace() {
            if (!isScanning) return;
            
            const context = faceidCanvas.getContext('2d');
            faceidCanvas.width = faceidVideo.videoWidth;
            faceidCanvas.height = faceidVideo.videoHeight;
            
            context.drawImage(faceidVideo, 0, 0, faceidCanvas.width, faceidCanvas.height);
            
            // In a real implementation, you would send this image to your backend
            // for face recognition processing
            // For this example, we'll simulate the process
            
            // Simulate face detection with a random success rate
            setTimeout(() => {
                if (!isScanning) return;
                
                // 70% success rate for demo purposes
                const isFaceDetected = Math.random() > 0.3;
                
                if (isFaceDetected) {
                    faceidStatus.innerHTML = '<span class="faceid-success">Wajah dikenali! Mengautentikasi...</span>';
                    faceidScanning.style.display = 'none';
                    
                    // Simulate authentication process
                    setTimeout(() => {
                        if (!isScanning) return;
                        
                        // Simulate successful authentication
                        authenticateWithFaceID();
                    }, 1500);
                } else {
                    faceidStatus.innerHTML = '<span class="faceid-error">Wajah tidak dikenali. Coba lagi.</span>';
                    faceidRetry.style.display = 'inline-block';
                    faceidScanning.style.display = 'none';
                }
            }, 2000);
        }

        // Authenticate with Face ID
        async function authenticateWithFaceID() {
            try {
                // In a real implementation, you would send the captured image to your backend
                // For this demo, we'll simulate the API call
                
                // Capture the current frame
                const context = faceidCanvas.getContext('2d');
                context.drawImage(faceidVideo, 0, 0, faceidCanvas.width, faceidCanvas.height);
                
                // Convert to blob for sending to server
                faceidCanvas.toBlob(async (blob) => {
                    try {
                        const formData = new FormData();
                        formData.append('face_image', blob);
                        
                        // Send to your backend for face recognition
                        const response = await fetch('/api/face-login', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok && data.success) {
                            // Login successful
                            handleSuccessfulLogin(data);
                        } else {
                            // Login failed
                            faceidStatus.innerHTML = `<span class="faceid-error">${data.message || 'Autentikasi gagal'}</span>`;
                            faceidRetry.style.display = 'inline-block';
                        }
                    } catch (error) {
                        console.error('Face ID authentication error:', error);
                        faceidStatus.innerHTML = '<span class="faceid-error">Terjadi kesalahan saat autentikasi.</span>';
                        faceidRetry.style.display = 'inline-block';
                    }
                }, 'image/jpeg', 0.8);
                
            } catch (error) {
                console.error('Face ID authentication error:', error);
                faceidStatus.innerHTML = '<span class="faceid-error">Terjadi kesalahan saat autentikasi.</span>';
                faceidRetry.style.display = 'inline-block';
            }
        }

        // Handle successful login
        function handleSuccessfulLogin(data) {
            // Simpan token dan data user
            localStorage.setItem('token', data.data.token);
            localStorage.setItem('role', data.data.user.role);
            localStorage.setItem('user_id', data.data.user.id);
            localStorage.setItem('name', data.data.user.name);

            const userRole = data.data.user.role;

            // Hanya simpan outlet_name dan outlet_id jika bukan admin
            if (userRole !== 'admin') {
                localStorage.setItem('outlet_name', data.data.user.outlet.name);
                localStorage.setItem('outlet_id', data.data.user.outlet.id);
            }

            // Jika ada data shift, simpan juga
            if (data.data.user.last_shift) {
                localStorage.setItem('shift_id', data.data.user.last_shift.id);
                localStorage.setItem('shift_data', JSON.stringify(data.data.user.last_shift));
            }

            // Tampilkan notifikasi sukses
            showSuccess('Login dengan Face ID berhasil!');
            
            // Stop camera
            stopFaceRecognition();
            
            // Tutup modal
            faceidModal.classList.remove('active');
            
            // Redirect berdasarkan role
            setTimeout(() => {
                if (userRole === 'kasir') {
                    window.location.href = '/pos';
                } else {
                    window.location.href = '/dashboard';
                }
            }, 1500);
        }

        // Regular login form submission
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email, password }),
                });

                const data = await response.json();

                if (!response.ok) {
                    return showError(data.message || 'Login gagal.');
                }

                // Simpan token dan data user
                localStorage.setItem('token', data.data.token);
                localStorage.setItem('role', data.data.user.role);
                localStorage.setItem('user_id', data.data.user.id);
                localStorage.setItem('name', data.data.user.name);

                const userRole = data.data.user.role;

                // Hanya simpan outlet_name dan outlet_id jika bukan admin
                if (userRole !== 'admin') {
                    localStorage.setItem('outlet_name', data.data.user.outlet.name);
                    localStorage.setItem('outlet_id', data.data.user.outlet.id);
                }

                // Jika ada data shift, simpan juga
                if (data.data.user.last_shift) {
                    localStorage.setItem('shift_id', data.data.user.last_shift.id);
                    localStorage.setItem('shift_data', JSON.stringify(data.data.user.last_shift));
                }

                // Arahkan ke halaman sesuai role
                if (userRole === 'kasir') {
                    showSuccess('Login berhasil! Mengarahkan ke POS...');
                    setTimeout(() => window.location.href = '/pos', 1500);
                } else {
                    showSuccess('Login berhasil! Mengarahkan ke dashboard...');
                    setTimeout(() => window.location.href = '/dashboard', 1500);
                }

            } catch (error) {
                console.error('Login error:', error);
                showError('Terjadi kesalahan pada server.');
            }
        });

        // Demo notifications (can be removed in production)
        document.addEventListener('DOMContentLoaded', function() {
            // Initialization code if needed
        });
    </script>
</body>
{{-- login / --}}
</html>