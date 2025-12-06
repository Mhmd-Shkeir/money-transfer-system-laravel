<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - Jaybtak Pro')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style> 
        :root {
            --primary-color: #0077B6;
            --primary-hover: #005a8d;
            --accent-color: #00B4D8;
            --success-color: #06D6A0;
            --danger-color: #EF476F;
            --text-primary: #1a1a1a;
            --text-secondary: #6c757d;
            --border-radius: 10px;
            --spacing: 24px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: var(--text-primary);
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }

        .card {
            border-radius: var(--border-radius);
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--border-radius);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .icon-box {
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius);
        }

        .icon-box-sm {
            width: 40px;
            height: 40px;
        }

        .bg-primary-light {
            background-color: rgba(0, 119, 182, 0.1);
        }

        .bg-accent-light {
            background-color: rgba(0, 180, 216, 0.1);
        }

        .bg-success-light {
            background-color: rgba(6, 214, 160, 0.1);
        }

        .bg-danger-light {
            background-color: rgba(239, 71, 111, 0.1);
        }

        .text-primary-custom {
            color: var(--primary-color) !important;
        }

        .text-success-custom {
            color: var(--success-color) !important;
        }

        .main-content {
            padding: 2rem;
        }

        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="d-flex flex-grow-1" style="flex: 1;">
        <!-- Sidebar -->
        @include('partials.dashboard-sidebar')

        <!-- Main Content -->
        <div class="flex-grow-1 d-flex flex-column" style="flex: 1; display: flex; flex-direction: column;">
            <!-- Top Bar -->
            <div class="bg-white border-bottom p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
                        <p class="text-muted mb-0 small">@yield('page-subtitle', 'Welcome back')</p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Notifications Bell -->
                        @include('partials.notifications-bell')

                        <!-- User Menu -->
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm" type="button" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-2"></i>
                                {{ auth()->user()->first_name ?? 'User' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <main class="main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: var(--border-radius);">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: var(--border-radius);">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>

    @stack('scripts')
</body>
</html>
