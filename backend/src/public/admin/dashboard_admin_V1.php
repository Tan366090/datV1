<?php include 'headers.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="no-referrer">
    <!-- <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://code.jquery.com; img-src 'self' data: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com;"> -->
    <title>Admin Dashboard</title>
    <!-- <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://code.jquery.com; img-src 'self' data: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data: https://use.fontawesome.com;"> -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://kit.fontawesome.com/your-kit-code.js" crossorigin="anonymous"></script>
    <!-- Custom Admin Dashboard CSS -->
    <link href="dashboard_admin.css" rel="stylesheet">
    <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta name="theme-color" content="#ffffff" />
        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
        <!-- Content Security Policy -->
        <meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline' 'unsafe-eval' data: blob:; style-src * 'unsafe-inline' https://cdn.jsdelivr.net; script-src * 'unsafe-inline' 'unsafe-eval' https://code.jquery.com; connect-src * 'unsafe-inline'; img-src * data: blob: 'unsafe-inline'; frame-src *; font-src * data: 'unsafe-inline';" />

        <title>Admin Dashboard - Quản trị hệ thống</title>
        
        <link rel="stylesheet" href="css/libs/bootstrap.min.css">
        <link rel="stylesheet" href="css/libs/bootstrap-icons.min.css">
        <link rel="stylesheet" href="css/libs/font-awesome.min.css">
        <link rel="stylesheet" href="css/libs/roboto.css">
        <link rel="stylesheet" href="dashboard_admin.css">
       
        <script src="js/libs/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="js/libs/jquery-3.7.1.min.js"></script>
        
        <!-- Configuration -->
        <script src="js/config.js"></script>
        <script type="module">
            import { AIAnalysis } from './js/modules/ai-analysis.js';
            
            document.addEventListener('DOMContentLoaded', function() {
                // Khởi tạo và tải dữ liệu
                AIAnalysis.init();
                
                // Thêm event listener cho nút cập nhật
                const updateBtn = document.getElementById('updateAnalysis');
                if (updateBtn) {
                    updateBtn.addEventListener('click', () => {
                        AIAnalysis.loadAnalysis();
                    });
                }
            });
        </script>

        <!-- Main Dashboard Scripts -->
        <script type="module">
            import { WidgetManager } from './js/modules/widget-manager.js';
            import { CommonUtils, AuthUtils, PermissionUtils, NotificationUtils, UIUtils } from './js/modules/utils.js';
            import { APIUtils } from './js/modules/api.js';
            import { Dashboard } from './js/modules/dashboard.js';
            import { APITest } from './js/modules/test.js';
            import { ChartManager } from './js/modules/chart-manager.js';
            import { WidgetManager } from './js/modules/widget-manager.js';
            import { ThemeManager } from './js/modules/theme-manager.js';
            import { ErrorHandler } from './js/modules/error-handler.js';
            import { API } from './js/modules/api.js';
            import { GlobalSearch } from './js/modules/global-search.js';
            import { MenuSearch } from './js/modules/menu-search.js';
            import { RecentMenu } from './js/modules/recent-menu.js';
            import { UserProfile } from './js/modules/user-profile.js';
            import { ExportData } from './js/modules/export-data.js';
            import { AIAnalysis } from './js/modules/ai-analysis.js';
            import { Gamification } from './js/modules/gamification.js';
            import { MobileStats } from './js/modules/mobile-stats.js';
            import { ActivityFilter } from './js/modules/activity-filter.js';
            import { NotificationHandler } from './js/modules/notification-handler.js';
            import { LoadingOverlay } from './js/modules/loading-overlay.js';
            import { DarkMode } from './js/modules/dark-mode.js';
                    

            // Khởi tạo các module
            
            const widgetManager = new WidgetManager();
            await widgetManager.initialize();
            const dashboard = new Dashboard();
            const globalSearch = new GlobalSearch();
            const menuSearch = new MenuSearch();
            const recentMenu = new RecentMenu();
            const userProfile = new UserProfile();
            const exportData = new ExportData();
            const aiAnalysis = new AIAnalysis();
            // const gamification = new Gamification();
            const mobileStats = new MobileStats();
            const activityFilter = new ActivityFilter();
            const notificationHandler = new NotificationHandler();
            const loadingOverlay = new LoadingOverlay();
            const darkMode = new DarkMode();

            // Khởi tạo theo dõi phiên đăng nhập
            AuthUtils.initSessionMonitoring();

            // Chạy kiểm tra hệ thống
            APITest.runAllTests();

            // Xử lý sự kiện khi trang được tải
            document.addEventListener('DOMContentLoaded', async () => {
                // Kiểm tra xác thực
                if (!AuthUtils.isAuthenticated()) {
                    window.location.href = '/login_new.html';
                    return;
                }

                // Khởi tạo các module
                await initializeModules();
                
                // Thêm các event listeners
                addEventListeners();
            });

            // Khởi tạo các module
            async function initializeModules() {
                try {
                    loadingOverlay.show();
                    
                    // Khởi tạo dashboard
                    await dashboard.loadData();
                    
                    // Khởi tạo tìm kiếm
                    globalSearch.initialize();
                    menuSearch.initialize();
                    
                    // Khởi tạo menu gần đây
                    recentMenu.initialize();
                    
                    // Khởi tạo thông tin người dùng
                    await userProfile.loadProfile();
                    
                    // Khởi tạo phân tích AI
                    await aiAnalysis.initialize();
                    
                    // Khởi tạo gamification
                    // await gamification.initialize();
                    
                    // Khởi tạo thống kê mobile
                    await mobileStats.initialize();
                    
                    // Khởi tạo bộ lọc hoạt động
                    activityFilter.initialize();
                    
                    // Khởi tạo thông báo
                    await notificationHandler.initialize();
                    
                    // Khởi tạo chế độ tối
                    darkMode.initialize();
                    
                    loadingOverlay.hide();
                } catch (error) {
                    console.error('Lỗi khởi tạo:', error);
                    loadingOverlay.hide();
                }
            }

            // Thêm các event listeners
            function addEventListeners() {
                // Xử lý tìm kiếm toàn cục
                document.getElementById('globalSearch').addEventListener('input', (e) => {
                    globalSearch.search(e.target.value);
                });

                // Xử lý tìm kiếm menu
                document.querySelector('.menu-search input').addEventListener('input', (e) => {
                    menuSearch.search(e.target.value);
                });

                // Xử lý chuyển đổi chế độ tối
                document.getElementById('themeToggle').addEventListener('click', () => {
                    darkMode.toggle();
                });

                // Xử lý làm mới hoạt động
                document.getElementById('refreshActivities').addEventListener('click', async () => {
                    await activityFilter.refresh();
                });

                // Xử lý làm mới thông báo
                document.getElementById('refreshNotifications').addEventListener('click', async () => {
                    await notificationHandler.refresh();
                });

                // Xử lý xuất dữ liệu
                document.querySelectorAll('.export-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        exportData.export(btn.dataset.type);
                    });
                });

                // Xử lý cập nhật phân tích AI
                document.getElementById('updateAnalysis').addEventListener('click', async () => {
                    await aiAnalysis.update();
                });

                // Xử lý cập nhật gamification
                // document.getElementById('updateGamification').addEventListener('click', async () => {
                //     await gamification.update();
                // });

                // Xử lý cập nhật thống kê mobile
                document.getElementById('updateMobileStats').addEventListener('click', async () => {
                    await mobileStats.update();
                });

                // Xử lý đăng xuất
                document.getElementById('logoutBtn').addEventListener('click', () => {
                    AuthUtils.logout();
                });

                // Xử lý các nút quick action
                document.querySelectorAll('.quick-action-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const action = btn.dataset.action;
                        handleQuickAction(action);
                    });
                });

                // Xử lý các selector
                document.querySelectorAll('select').forEach(select => {
                    select.addEventListener('change', (e) => {
                        const type = select.id;
                        handleSelectorChange(type, e.target.value);
                    });
                });
            }

            // Xử lý quick action
            function handleQuickAction(action) {
                switch (action) {
                    case 'add-employee':
                        window.location.href = 'employees/add.html';
                        break;
                    case 'check-attendance':
                        window.location.href = 'attendance/check.html';
                        break;
                    case 'register-leave':
                        window.location.href = 'leave/register.html';
                        break;
                    case 'calculate-salary':
                        window.location.href = 'salary/calculate.html';
                        break;
                }
            }

            // Xử lý thay đổi selector
            function handleSelectorChange(type, value) {
                switch (type) {
                    case 'performanceTimeRange':
                        dashboard.updatePerformanceChart(value);
                        break;
                    case 'attendancePeriod':
                        dashboard.updateAttendanceChart(value);
                        break;
                    case 'activityType':
                        activityFilter.filterByType(value);
                        break;
                }
            }

            // Xử lý sự kiện khi trang bị đóng
            window.addEventListener('beforeunload', () => {
                dashboard.cleanup();
                globalSearch.cleanup();
                menuSearch.cleanup();
                recentMenu.cleanup();
                userProfile.cleanup();
                aiAnalysis.cleanup();
                // gamification.cleanup();
                mobileStats.cleanup();
                activityFilter.cleanup();
                notificationHandler.cleanup();
                loadingOverlay.cleanup();
                darkMode.cleanup();
            });
        </script>
    <style>
        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #eee;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .header-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Search Styles */
        .search-container {
           margin-top: 15px;
            position: relative;
            width: 300px;
        }

        .search-input {
            width: 100%;
            height: 38px;
            padding: 8px 35px 8px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #2196F3;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
            outline: none;
        }

        .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 14px;
        }

        /* Theme Toggle Button */
        .theme-toggle {
            background: none;
            border: 1px solid #e0e0e0;
            width: 38px;
            height: 38px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #666;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            background: #f5f5f5;
            color: #333;
        }

        .theme-toggle i {
            font-size: 16px;
        }

        .theme-toggle .fa-moon {
            display: none;
        }

        [data-theme="dark"] .theme-toggle .fa-sun {
            display: none;
        }

        [data-theme="dark"] .theme-toggle .fa-moon {
            display: block;
        }

        /* User Menu */
        .user-menu {
            position: relative;
        }

        .user-menu-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: 1px solid #e0e0e0;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-menu-btn:hover {
            background: #f5f5f5;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-name {
            font-weight: 500;
            color: #333;
        }

        /* Dropdown Menu */
        .dropdown-menu {
            position: absolute;
            top: calc(100% + 5px);
            right: 0;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            min-width: 180px;
            padding: 8px 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: #f5f5f5;
        }

        .dropdown-item i {
            font-size: 14px;
            width: 16px;
            text-align: center;
        }

        .dropdown-divider {
            height: 1px;
            background: #e0e0e0;
            margin: 8px 0;
        }

        /* Dark Mode Styles */
        [data-theme="dark"] .header {
            background: #1a1a1a;
            border-bottom-color: #2d2d2d;
        }

        [data-theme="dark"] .header-title {
            color: #fff;
        }

        [data-theme="dark"] .search-input {
            background: #2d2d2d;
            border-color: #3d3d3d;
            color: #fff;
        }

        [data-theme="dark"] .search-input:focus {
            border-color: #2196F3;
        }

        [data-theme="dark"] .search-icon {
            color: #888;
        }

        [data-theme="dark"] .theme-toggle {
            border-color: #3d3d3d;
            color: #888;
        }

        [data-theme="dark"] .theme-toggle:hover {
            background: #2d2d2d;
            color: #fff;
        }

        [data-theme="dark"] .user-menu-btn {
            border-color: #3d3d3d;
            color: #fff;
        }

        [data-theme="dark"] .user-menu-btn:hover {
            background: #2d2d2d;
        }

        [data-theme="dark"] .user-name {
            color: #fff;
        }

        [data-theme="dark"] .dropdown-menu {
            background: #1a1a1a;
            border-color: #3d3d3d;
        }

        [data-theme="dark"] .dropdown-item {
            color: #fff;
        }

        [data-theme="dark"] .dropdown-item:hover {
            background: #2d2d2d;
        }

        [data-theme="dark"] .dropdown-divider {
            background: #3d3d3d;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .header {
                left: 0;
            }

            .search-container {
                width: 200px;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 0 15px;
            }

            .header-title {
                font-size: 1.2rem;
            }

            .search-container {
                display: none;
            }

            .user-name {
                display: none;
            }

            .user-menu-btn {
                padding: 6px;
            }
        }

        @media (max-width: 576px) {
            .header {
                height: 50px;
            }

            .theme-toggle,
            .user-menu-btn {
                width: 32px;
                height: 32px;
                padding: 4px;
            }

            .user-avatar {
                width: 24px;
                height: 24px;
            }
        }

        /* Icon styles */
        .nav-icon,
        .nav-link i:not(.fa-chevron-right),
        .submenu .nav-link i {
            display: none !important; /* Hide all menu icons */
        }

        /* Show only Dashboard icon */
        .nav-item[data-menu-id="dashboard"] .nav-icon,
        .nav-item[data-menu-id="dashboard"] .nav-link i {
            display: inline-flex !important;
        }

        /* Show only the chevron-right icon for submenus */
        .nav-item.has-submenu .fa-chevron-right {
            display: inline-flex !important;
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        /* Rotate chevron when submenu is open */
        .nav-item.has-submenu.open .fa-chevron-right {
            transform: rotate(90deg);
        }

        /* Menu styles */
        .nav-list {
            list-style: none; /* Remove bullet points from menu */
            padding-left: 0; /* Remove default padding */
        }

        .submenu {
            list-style: none; /* Remove bullet points from submenu */
            padding-left: 0; /* Remove default padding */
        }

        /* Remove underline on menu hover */
        .nav-link {
            text-decoration: none;
        }

        .nav-link:hover {
            text-decoration: none;
        }

        /* Statistics Cards Styles */
        .statistics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .stat-content {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            gap: 1rem;
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-card:nth-child(1) .stat-icon-wrapper {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .stat-card:nth-child(2) .stat-icon-wrapper {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        }

        .stat-card:nth-child(3) .stat-icon-wrapper {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        }

        .stat-card:nth-child(4) .stat-icon-wrapper {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        }

        .stat-info {
            flex: 1;
        }

        .stat-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #5a5c69;
            margin: 0;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2e3a59;
            margin: 0;
        }

        /* Dark Mode Support */
        [data-theme="dark"] .stat-card {
            background: #2d2d2d;
        }

        [data-theme="dark"] .stat-title {
            color: #adb5bd;
        }

        [data-theme="dark"] .stat-value {
            color: #fff;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .statistics-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-content {
                padding: 1rem;
            }
            
            .stat-icon-wrapper {
                width: 40px;
                height: 40px;
                font-size: 1.25rem;
            }
            
            .stat-title {
                font-size: 0.8rem;
            }
            
            .stat-value {
                font-size: 1.25rem;
            }
        }

        /* Sidebar Header Styles */
        .sidebar-header {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-details {
            flex: 1;
        }

        .user-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #212529;
            margin: 0 0 5px 0;
        }

        .user-role {
            font-size: 0.85rem;
            color: #6c757d;
            display: block;
        }

        /* Dark mode support */
        [data-theme="dark"] .sidebar-header {
            background: #2d2d2d;
            border-bottom-color: #3d3d3d;
        }

        [data-theme="dark"] .user-name {
            color: #fff;
        }

        [data-theme="dark"] .user-role {
            color: #adb5bd;
        }

        [data-theme="dark"] .user-avatar {
            border-color: #3d3d3d;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .sidebar-header {
                padding: 15px;
            }

            .user-avatar {
                width: 40px;
                height: 40px;
            }

            .user-name {
                font-size: 1rem;
            }

            .user-role {
                font-size: 0.8rem;
            }
        }

        /* Show only the chevron-right icon for submenus */
        .submenu-toggle {
            display: inline-flex !important;
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        /* Rotate chevron when submenu is open */
        .nav-item.has-submenu.open .submenu-toggle {
            transform: rotate(90deg);
        }

        /* Menu styles */
        .nav-list {
            list-style: none; /* Remove bullet points from menu */
            padding-left: 0; /* Remove default padding */
        }

        .submenu {
            list-style: none; /* Remove bullet points from submenu */
            padding-left: 0; /* Remove default padding */
        }

        /* Icon styles */
        .nav-icon {
            display: none; /* Hide all menu icons */
        }

        /* Show only Dashboard icon */
        .nav-item[data-menu-id="dashboard"] .nav-icon {
            display: inline-flex !important;
        }

        /* Show only the chevron-right icon for submenus */
        .submenu-toggle {
            display: inline-flex !important;
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        /* Rotate chevron when submenu is open */
        .nav-item.has-submenu.open .submenu-toggle {
            transform: rotate(90deg);
        }

        /* Menu styles */
        .nav-list {
            list-style: none; /* Remove bullet points from menu */
            padding-left: 0; /* Remove default padding */
        }

        .submenu {
            list-style: none; /* Remove bullet points from submenu */
            padding-left: 0; /* Remove default padding */
        }

        /* Remove underline on menu hover */
        .nav-link {
            text-decoration: none;
        }

        .nav-link:hover {
            text-decoration: none;
        }
    </style>
</head>
<body class="theme-transition">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" role="complementary">
            <div class="sidebar-header">
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="male.png" alt="User Avatar" class="rounded-circle" width="40" height="40">
                    </div>
                    <div class="user-details">
                        <h2 class="user-name">VNPT</h2>
                        <span class="user-role">Administrator</span>
                    </div>
                </div>
            </div>

            <!-- Menu Search -->
            <div class="menu-search">
                <input type="text" placeholder="Tìm kiếm menu..." aria-label="Search Menu" class="search-input" />
            </div>

            <nav role="navigation">
                <ul class="nav-menu">
                    <li class="nav-item" data-menu-id="dashboard">
                        <a href="dashboard.html" class="nav-link active" aria-current="page">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item has-submenu" data-menu-id="employees">
                        <a href="#" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>Nhân viên</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="employees/list.html" class="nav-link">
                                    <span>Danh sách nhân viên</span>
                                </a>
                            </li>
                            <li>
                                <a href="employees/add.html" class="nav-link">
                                    <span>Thêm nhân viên</span>
                                </a>
                            </li>
                            <li>
                                <a href="employees/contracts.html" class="nav-link">
                                    <span>Hợp đồng</span>
                                </a>
                            </li>
                            <li>
                                <a href="employees/attendance.html" class="nav-link">
                                    <span>Chấm công</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Lương</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="salary/list.html" class="nav-link">
                                    <span>Danh sách lương</span>
                                </a>
                            </li>
                            <li>
                                <a href="salary/adjust.html" class="nav-link">
                                    <span>Điều chỉnh lương</span>
                                </a>
                            </li>
                            <li>
                                <a href="salary/payslip.html" class="nav-link">
                                    <span>Phiếu lương</span>
                                </a>
                            </li>
                            <li>
                                <a href="salary/history.html" class="nav-link">
                                    <span>Lịch sử điều chỉnh</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Nghỉ phép</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="leave/register.html" class="nav-link">
                                    <span>Đăng ký nghỉ phép</span>
                                </a>
                            </li>
                            <li>
                                <a href="leave/list.html" class="nav-link">
                                    <span>Danh sách nghỉ phép</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Hiệu suất</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="performance/report.html" class="nav-link">
                                    <span>Báo cáo hiệu suất</span>
                                </a>
                            </li>
                            <li>
                                <a href="performance/evaluation.html" class="nav-link">
                                    <span>Đánh giá nhân viên</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-building"></i>
                            <span>Phòng ban</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="departments/list.html" class="nav-link">
                                    <span>Danh sách phòng ban</span>
                                </a>
                            </li>
                            <li>
                                <a href="positions/list.html" class="nav-link">
                                    <span>Vị trí công việc</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-file-alt"></i>
                            <span>Tài liệu</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="documents/list.html" class="nav-link">
                                    <span>Danh sách tài liệu</span>
                                </a>
                            </li>
                            <li>
                                <a href="documents/upload.html" class="nav-link">
                                    <span>Upload tài liệu</span>
                                </a>
                            </li>
                            <li>
                                <a href="documents/manage.html" class="nav-link">
                                    <span>Quản lý tài liệu</span>
                                </a>
                            </li>
                            <li>
                                <a href="documents/view_document.html" class="nav-link">
                                    <span>Xem tài liệu</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-laptop"></i>
                            <span>Thiết bị</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="equipment/list.html" class="nav-link">
                                    <span>Danh sách thiết bị</span>
                                </a>
                            </li>
                            <li>
                                <a href="equipment/assign.html" class="nav-link">
                                    <span>Cấp phát thiết bị</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>Báo cáo</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="reports/employee.html" class="nav-link">
                                    <span>Đánh giá hiệu suất</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-user-plus"></i>
                            <span>Tuyển dụng</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="recruitment/positions.html" class="nav-link">
                                    <span>Vị trí tuyển dụng</span>
                                </a>
                            </li>
                            <li>
                                <a href="recruitment/candidates.html" class="nav-link">
                                    <span>Quản lý ứng viên</span>
                                </a>
                            </li>
                            <li>
                                <a href="recruitment/interviews.html" class="nav-link">
                                    <span>Lịch phỏng vấn</span>
                                </a>
                            </li>
                            <li>
                                <a href="recruitment/onboarding.html" class="nav-link">
                                    <span>Onboarding</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-gift"></i>
                            <span>Phúc lợi</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="benefits/insurance.html" class="nav-link">
                                    <span>Bảo hiểm</span>
                                </a>
                            </li>
                            <li>
                                <a href="benefits/policies.html" class="nav-link">
                                    <span>Chính sách phúc lợi</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-project-diagram"></i>
                            <span>Dự án</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="projects/list.html" class="nav-link">
                                    <span>Danh sách dự án</span>
                                </a>
                            </li>
                            <li>
                                <a href="projects/tasks.html" class="nav-link">
                                    <span>Quản lý công việc</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Cài đặt</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="settings/security.html" class="nav-link">
                                    <span>Bảo mật</span>
                                </a>
                            </li>
                            <li>
                                <a href="settings/integrations.html" class="nav-link">
                                    <span>Tích hợp</span>
                                </a>
                            </li>
                            <li>
                                <a href="settings/backup.html" class="nav-link">
                                    <span>Sao lưu</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Đào tạo</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="training/courses.html" class="nav-link">
                                    <span>Quản lý khóa đào tạo</span>
                                </a>
                            </li>
                            <li>
                                <a href="training/register.html" class="nav-link">
                                    <span>Đăng ký đào tạo</span>
                                </a>
                            </li>
                            <li>
                                <a href="training/list.html" class="nav-link">
                                    <span>Danh sách đăng ký</span>
                                </a>
                            </li>
                            <li>
                                <a href="training/evaluation.html" class="nav-link">
                                    <span>Đánh giá kết quả</span>
                                </a>
                            </li>
                            <li>
                                <a href="training/reports.html" class="nav-link">
                                    <span>Báo cáo đào tạo</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" id="logoutBtn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content" role="main">
            <header class="header">
                <div class="header-left">
                    <h1 class="header-title">Admin Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="header-controls">
                        <div class="search-container">
                            <input type="text" class="search-input" placeholder="Search..." aria-label="Global Search">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                        
                        <button class="theme-toggle" id="themeToggle" aria-label="Toggle Theme">
                            <i class="fas fa-sun"></i>
                            <i class="fas fa-moon"></i>
                        </button>

                        <div class="user-menu">
                            <button class="user-menu-btn" type="button" id="userMenuBtn">
                                <div class="user-avatar">
                                    <img src="male.png" alt="Admin Avatar">
                                </div>
                                <span class="user-name">Admin</span>
                            </button>
                            <div class="dropdown-menu">
                                <a href="#" class="dropdown-item">
                                    <i class="fas fa-user"></i>
                                    <span>Profile</span>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item text-danger" id="logoutBtn">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <!-- Statistics Cards -->
                <div class="statistics-grid d-grid gap-3">
                    <div class="stat-card card shadow-sm hover-shadow">
                        <div class="stat-content">
                            <div class="stat-icon-wrapper">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3 class="stat-title">Tổng số nhân viên</h3>
                                <p class="stat-value" id="totalEmployees">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card card shadow-sm hover-shadow">
                        <div class="stat-content">
                            <div class="stat-icon-wrapper">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-info">
                                <h3 class="stat-title">Đi làm đúng giờ</h3>
                                <p class="stat-value" id="onTimePercentage">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card card shadow-sm hover-shadow">
                        <div class="stat-content">
                            <div class="stat-icon-wrapper">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-info">
                                <h3 class="stat-title">Có mặt hôm nay</h3>
                                <p class="stat-value" id="presentToday">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card card shadow-sm hover-shadow">
                        <div class="stat-content">
                            <div class="stat-icon-wrapper">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <div class="stat-info">
                                <h3 class="stat-title">Vắng mặt hôm nay</h3>
                                <p class="stat-value" id="absentToday">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="charts-grid d-grid gap-4">
                    <!-- Attendance Chart -->
                    <div class="chart-card card shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h3 class="chart-title text-primary mb-0">Tổng quan chấm công</h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="attendanceChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Department Distribution Chart -->
                    <div class="chart-card card shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h3 class="chart-title text-primary mb-0">Phân bố phòng ban</h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="departmentChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Chart -->
                    <div class="chart-card card shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h3 class="chart-title text-primary mb-0">Hiệu suất nhân viên</h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="performanceChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Chart -->
                    <div class="chart-card card shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h3 class="chart-title text-primary mb-0">Chi phí lương</h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="salaryChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Chart -->
                    <div class="chart-card card shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h3 class="chart-title text-primary mb-0">Thống kê nghỉ phép</h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="leaveChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Recruitment Chart -->
                    <div class="chart-card card shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h3 class="chart-title text-primary mb-0">Tuyển dụng</h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="recruitmentChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Training Chart -->
                    <div class="chart-card card shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h3 class="chart-title text-primary mb-0">Đào tạo</h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="trainingChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Assets Chart -->
                    <div class="chart-card card shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h3 class="chart-title text-primary mb-0">Quản lý tài sản</h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="assetsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="activity-section mt-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h3 class="section-title text-primary mb-0">Hoạt động gần đây</h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="activity-timeline">
                                <div class="activity-item">
                                    <div class="activity-icon bg-primary">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text">Thêm nhân viên mới: Nguyễn Văn A</p>
                                        <span class="activity-time">2 giờ trước</span>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon bg-success">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text">Cập nhật bảng chấm công</p>
                                        <span class="activity-time">4 giờ trước</span>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon bg-info">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text">Tải lên tài liệu mới</p>
                                        <span class="activity-time">6 giờ trước</span>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon bg-warning">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text">Đăng ký nghỉ phép: Trần Thị B</p>
                                        <span class="activity-time">8 giờ trước</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions mt-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h3 class="section-title text-primary mb-0">Thao tác nhanh</h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="quick-actions-grid">
                                <button class="quick-action-btn" onclick="showAddEmployeeModal()">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Thêm nhân viên</span>
                                </button>
                                <button class="quick-action-btn" onclick="showAttendanceModal()">
                                    <i class="fas fa-clock"></i>
                                    <span>Chấm công</span>
                                </button>
                                <button class="quick-action-btn" onclick="showLeaveModal()">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Đăng ký nghỉ phép</span>
                                </button>
                                <button class="quick-action-btn" onclick="showSalaryModal()">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>Tính lương</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <p>&copy; 2023 VNPT. All rights reserved.</p>
            </footer>
        </main>
    </div>

    <!-- Form thêm nhân viên -->
    <div id="addEmployeeModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Thêm nhân viên mới</h3>
                <button style="color: brown;" type="button" class="modal-close-btn" onclick="closeAddEmployeeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addEmployeeForm" class="employee-form" enctype="multipart/form-data">
                    <div class="form-grid">
                        <!-- Thông tin cá nhân -->
                        <div class="form-group">
                            <label class="form-label required">Họ</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Tên</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Số điện thoại</label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Phòng ban</label>
                            <select class="form-select" name="department_id" required>
                                <option value="">Chọn phòng ban</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Vị trí</label>
                            <select class="form-select" name="position_id" required>
                                <option value="">Chọn vị trí</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Ngày bắt đầu</label>
                            <input type="date" class="form-control" name="hire_date" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Mức lương</label>
                            <input type="number" class="form-control" name="salary" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="active">Đang làm việc</option>
                                <option value="inactive">Nghỉ việc</option>
                                <option value="probation">Thử việc</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Địa chỉ</label>
                            <textarea class="form-control" name="address" rows="3"></textarea>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Ảnh đại diện</label>
                            <div class="avatar-upload" onclick="document.getElementById('avatar').click()">
                                <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none">
                                <div id="avatarPreview" class="avatar-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span>Nhấn để tải ảnh lên</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeAddEmployeeModal()">Hủy</button>
                        <button type="submit" class="btn-submit">Thêm nhân viên</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal chấm công -->
    <div id="attendanceModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-clock"></i>
                    Chấm công nhân viên
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeAttendanceModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="attendanceForm" class="attendance-form">
                    <!-- Phần tìm kiếm nhân viên -->
                    <div class="form-section employee-search-section">
                        <h4 class="section-title">Tìm kiếm nhân viên</h4>
                        <div class="search-container">
                            <div class="search-input-group">
                                <input type="text" 
                                       id="employeeSearch" 
                                       name="employeeSearch"
                                       class="form-control search-input" 
                                       placeholder="Nhập tên, mã nhân viên hoặc email..."
                                       autocomplete="off"
                                       style="z-index: 1;">
                                <button type="button" class="search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div id="searchResults" class="search-results" style="z-index: 2;"></div>
                        </div>
                    </div>

                    <style>
                    .search-container {
                        position: relative;
                        width: 100%;
                        margin-bottom: 15px;
                    }

                    .search-input-group {
                        display: flex;
                        gap: 10px;
                        width: 100%;
                        position: relative;
                    }

                    .search-input {
                        flex: 1;
                        height: 38px;
                        padding: 8px 12px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        font-size: 14px;
                        background: #fff;
                        width: 100%;
                        pointer-events: auto;
                    }

                    .search-input:focus {
                        border-color: #007bff;
                        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
                        outline: none;
                    }

                    .search-btn {
                        padding: 8px 15px;
                        background: #007bff;
                        color: white;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                        transition: background 0.3s;
                    }

                    .search-btn:hover {
                        background: #0056b3;
                    }

                    .search-results {
                        position: absolute;
                        top: 100%;
                        left: 0;
                        right: 0;
                        background: white;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        max-height: 200px;
                        overflow-y: auto;
                        margin-top: 5px;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                        display: none;
                    }

                    .search-result-item {
                        padding: 10px 15px;
                        cursor: pointer;
                        border-bottom: 1px solid #eee;
                    }

                    .search-result-item:hover {
                        background-color: #f8f9fa;
                    }

                    .search-result-item:last-child {
                        border-bottom: none;
                    }
                    </style>

                    <!-- Thông tin nhân viên -->
                    <div class="form-section employee-info-section">
                        <h4 class="section-title">Thông tin nhân viên</h4>
                        <div class="employee-info-grid">
                            <div class="info-item">
                                <label>Mã nhân viên</label>
                                <input type="text" id="employeeId" class="form-control" readonly>
                            </div>
                            <div class="info-item">
                                <label>Tên nhân viên</label>
                    <input type="text" id="employeeName" class="form-control" readonly>
                            </div>
                            <div class="info-item">
                                <label>Phòng ban</label>
                                <input type="text" id="employeeDepartment" class="form-control" readonly>
                            </div>
                            <div class="info-item">
                                <label>Vị trí</label>
                                <input type="text" id="employeePosition" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin chấm công -->
                    <div class="form-section attendance-info-section">
                        <h4 class="section-title">Thông tin chấm công</h4>
                        <div class="attendance-info-grid">
                            <div class="info-item">
                                <label>Ngày chấm công</label>
                    <input type="date" id="attendanceDate" class="form-control" required>
                </div>
                            <div class="info-item">
                                <label>Thời gian</label>
                    <input type="time" id="recordedTime" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <!-- Ký hiệu chấm công -->
                    <div class="form-section attendance-symbols-section">
                <!-- Ký hiệu chấm công -->
                <div class="form-section attendance-symbols-section">
                    <h4 class="section-title">Ký hiệu chấm công</h4>
                    <div class="symbols-grid">
                        <button type="button" class="symbol-btn" data-symbol="P" data-color="success">
                            <i class="fas fa-check-circle"></i>
                            <span>P - Có mặt</span>
                        </button>
                        <button type="button" class="symbol-btn" data-symbol="Ô" data-color="warning">
                            <i class="fas fa-procedures"></i>
                            <span>Ô - Nghỉ ốm</span>
                        </button>
                        <button type="button" class="symbol-btn" data-symbol="Cô" data-color="info">
                            <i class="fas fa-baby"></i>
                            <span>Cô - Chăm sóc con ốm</span>
                        </button>
                        <button type="button" class="symbol-btn" data-symbol="TS" data-color="primary">
                            <i class="fas fa-female"></i>
                            <span>TS - Nghỉ thai sản</span>
                        </button>
                        <button type="button" class="symbol-btn" data-symbol="T" data-color="danger">
                            <i class="fas fa-ambulance"></i>
                            <span>T - Tai nạn lao động</span>
                        </button>
                        <button type="button" class="symbol-btn" data-symbol="CN" data-color="secondary">
                            <i class="fas fa-calendar-week"></i>
                            <span>CN - Chủ nhật</span>
                        </button>
                        <button type="button" class="symbol-btn" data-symbol="NL" data-color="secondary">
                            <i class="fas fa-calendar-day"></i>
                            <span>NL - Nghỉ lễ</span>
                        </button>
                        <button type="button" class="symbol-btn" data-symbol="NB" data-color="info">
                            <i class="fas fa-exchange-alt"></i>
                            <span>NB - Nghỉ bù</span>
                        </button>
                        <button type="button" class="symbol-btn" data-symbol="1/2K" data-color="warning">
                            <i class="fas fa-clock"></i>
                            <span>1/2K - Nghỉ nửa ngày không lương</span>
                        </button>
                        <button type="button" class="symbol-btn" data-symbol="K" data-color="danger">
                            <i class="fas fa-calendar-times"></i>
                            <span>K - Nghỉ nguyên ngày không lương</span>
                        </button>
                        <button type="button" class="symbol-btn" data-symbol="N" data-color="danger">
                            <i class="fas fa-ban"></i>
                            <span>N - Ngừng làm việc</span>
                        </button>
                        <button type="button" class="symbol-btn" data-symbol="P" data-color="success">
                            <i class="fas fa-calendar-check"></i>
                            <span>P - Nghỉ phép</span>
                        </button>
                        <button type="button" class="symbol-btn" data-symbol="1/2P" data-color="success">
                            <i class="fas fa-clock"></i>
                            <span>1/2P - Nghỉ nửa ngày phép</span>
                        </button>
                        <button type="button" class="symbol-btn" data-symbol="NN" data-color="info">
                            <i class="fas fa-clock"></i>
                            <span>NN - Làm nửa ngày</span>
                        </button>
                    </div>
                    <input type="hidden" id="attendanceSymbol" required>
                </div>

                <!-- Ghi chú -->
                <div class="form-section notes-section">
                    <h4 class="section-title">Ghi chú</h4>
                    <textarea id="notes" class="form-control" rows="3" 
                              placeholder="Nhập ghi chú (nếu có)..."></textarea>
                </div>

                <!-- Nút thao tác -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeAttendanceModal()">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu chấm công
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Style cho modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    backdrop-filter: blur(5px);
    }

    .modal-overlay.active {
        display: flex;
        justify-content: center;
        align-items: center;
    animation: fadeIn 0.3s ease;
    }

    .modal-content {
        background: white;
    border-radius: 12px;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    animation: slideIn 0.3s ease;
    }

    .modal-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
    }

    .modal-title {
        margin: 0;
        font-size: 1.5rem;
        color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
    }

    .modal-close-btn {
        background: none;
        border: none;
    font-size: 1.2rem;
        cursor: pointer;
    padding: 5px;
    color: #666;
    transition: color 0.3s;
}

.modal-close-btn:hover {
    color: #333;
    }

    .modal-body {
        padding: 20px;
    }

/* Style cho form sections */
.form-section {
    margin-bottom: 25px;
    padding: 15px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.section-title {
    font-size: 1.1rem;
    color: #333;
        margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

/* Style cho tìm kiếm nhân viên */
.search-container {
    position: relative;
    width: 100%;
}

.search-input-group {
    display: flex;
    gap: 10px;
        width: 100%;
}

.search-input-group input {
    flex: 1;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    outline: none;
    transition: border-color 0.3s;
    }

.search-input-group input:focus {
        border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.search-btn {
    padding: 8px 15px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s;
}

.search-btn:hover {
    background: #0056b3;
    }

    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .search-result-item {
    padding: 10px 15px;
        cursor: pointer;
    transition: background 0.2s;
    }

    .search-result-item:hover {
    background: #f8f9fa;
}

/* Style cho thông tin nhân viên */
.employee-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-item label {
    font-size: 0.9rem;
    color: #666;
    }

    /* Style cho ký hiệu chấm công */
.symbols-grid {
        display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
    }

    .symbol-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
        border: 1px solid #ddd;
    border-radius: 6px;
    background: white;
        cursor: pointer;
    transition: all 0.3s;
        text-align: left;
    }

    .symbol-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .symbol-btn.active {
        background: #007bff;
    color: white;
        border-color: #007bff;
    }

.symbol-btn[data-color="success"]:hover {
    background: #28a745;
    color: white;
    border-color: #28a745;
}

.symbol-btn[data-color="warning"]:hover {
    background: #ffc107;
    color: white;
    border-color: #ffc107;
}

.symbol-btn[data-color="danger"]:hover {
    background: #dc3545;
        color: white;
    border-color: #dc3545;
}

.symbol-btn[data-color="info"]:hover {
    background: #17a2b8;
    color: white;
    border-color: #17a2b8;
}

.symbol-btn[data-color="primary"]:hover {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.symbol-btn[data-color="secondary"]:hover {
    background: #6c757d;
    color: white;
    border-color: #6c757d;
}

/* Style cho nút thao tác */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* Responsive */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 10px;
    }
    
    .employee-info-grid,
    .symbols-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Initialize theme
document.addEventListener('DOMContentLoaded', function() {
    // Check for saved theme preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.body.setAttribute('data-theme', savedTheme);
    }

    // Initialize charts
    initCharts();
});

// Theme toggle functionality
function toggleTheme() {
    const currentTheme = document.body.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    document.body.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
}

// Initialize all charts
function initCharts() {
    // Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(attendanceCtx, {
        type: 'line',
        data: {
            labels: ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'CN'],
            datasets: [{
                label: 'Tỷ lệ đi làm',
                data: [85, 90, 88, 92, 95, 80, 75],
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Department Chart
    const departmentCtx = document.getElementById('departmentChart').getContext('2d');
    new Chart(departmentCtx, {
        type: 'doughnut',
        data: {
            labels: ['IT', 'HR', 'Tài chính', 'Marketing', 'Vận hành'],
            datasets: [{
                data: [30, 20, 15, 25, 10],
                backgroundColor: [
                    '#4CAF50',
                    '#2196F3',
                    '#FFC107',
                    '#9C27B0',
                    '#F44336'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });

    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'bar',
        data: {
            labels: ['Q1', 'Q2', 'Q3', 'Q4'],
            datasets: [{
                label: 'Hiệu suất trung bình',
                data: [75, 82, 78, 85],
                backgroundColor: '#2196F3',
                borderColor: '#1976D2',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Salary Chart
    const salaryCtx = document.getElementById('salaryChart').getContext('2d');
    new Chart(salaryCtx, {
        type: 'line',
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'],
            datasets: [{
                label: 'Tổng chi phí lương',
                data: [50000000, 52000000, 51000000, 53000000, 54000000, 55000000],
                borderColor: '#FFC107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + 'đ';
                        }
                    }
                }
            }
        }
    });

    // Leave Chart
    const leaveCtx = document.getElementById('leaveChart').getContext('2d');
    new Chart(leaveCtx, {
        type: 'bar',
        data: {
            labels: ['Nghỉ phép', 'Nghỉ ốm', 'Nghỉ không lương'],
            datasets: [{
                data: [120, 45, 30],
                backgroundColor: [
                    '#4CAF50',
                    '#2196F3',
                    '#FFC107'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Recruitment Chart
    const recruitmentCtx = document.getElementById('recruitmentChart').getContext('2d');
    new Chart(recruitmentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Đã tuyển', 'Đang phỏng vấn', 'Đã từ chối', 'Đang chờ'],
            datasets: [{
                data: [15, 8, 12, 5],
                backgroundColor: [
                    '#4CAF50',
                    '#2196F3',
                    '#F44336',
                    '#FFC107'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Training Chart
    const trainingCtx = document.getElementById('trainingChart').getContext('2d');
    new Chart(trainingCtx, {
        type: 'bar',
        data: {
            labels: ['Kỹ năng mềm', 'Kỹ thuật', 'Quản lý', 'An toàn'],
            datasets: [{
                label: 'Số người tham gia',
                data: [45, 30, 25, 40],
                backgroundColor: '#9C27B0'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Assets Chart
    const assetsCtx = document.getElementById('assetsChart').getContext('2d');
    new Chart(assetsCtx, {
        type: 'pie',
        data: {
            labels: ['Đang sử dụng', 'Đang bảo trì', 'Đã thanh lý', 'Chưa cấp phát'],
            datasets: [{
                data: [60, 15, 10, 15],
                backgroundColor: [
                    '#4CAF50',
                    '#2196F3',
                    '#F44336',
                    '#FFC107'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

// Search functionality
function handleSearch(event) {
    event.preventDefault();
    const searchTerm = document.querySelector('.search-input').value;
    // Implement search logic here
    console.log('Searching for:', searchTerm);
}

// User menu toggle
function toggleUserMenu() {
    const userMenu = document.querySelector('.user-menu');
    userMenu.classList.toggle('show');
}

// Close user menu when clicking outside
document.addEventListener('click', function(event) {
    const userMenu = document.querySelector('.user-menu');
    const userButton = document.querySelector('.user-button');
    if (userMenu && userButton) {
        if (!userButton.contains(event.target) && !userMenu.contains(event.target)) {
            userMenu.classList.remove('show');
        }
    }
});

// Hàm hiển thị modal thêm nhân viên
function showAddEmployeeModal() {
    const modal = document.getElementById('addEmployeeModal');
    if (!modal) {
        console.error('Modal element not found');
            return;
        }
        
    modal.classList.add('active');
    
    // Reset form
    const form = document.getElementById('addEmployeeForm');
    if (form) {
        form.reset();
    }
    
    // Reset avatar preview
    const avatarPreview = document.getElementById('avatarPreview');
    if (avatarPreview) {
        avatarPreview.innerHTML = '<i class="fas fa-user"></i>';
    }
}

// Hàm đóng modal thêm nhân viên
    function closeAddEmployeeModal() {
    const modal = document.getElementById('addEmployeeModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Hàm hiển thị modal chấm công
function showAttendanceModal() {
        const modal = document.getElementById('attendanceModal');
        if (!modal) {
            console.error('Modal element not found');
            return;
        }

        modal.classList.add('active');
        
        // Set default date to today
        const today = new Date();
        const dateInput = document.getElementById('attendanceDate');
        const timeInput = document.getElementById('recordedTime');
        
        if (dateInput && timeInput) {
            dateInput.valueAsDate = today;
            timeInput.value = today.toTimeString().slice(0,5);
        }
        
        // Reset form
        const form = document.getElementById('attendanceForm');
        if (form) {
            form.reset();
        }
        
        // Reset employee fields
    const employeeFields = ['employeeId', 'employeeName', 'employeeDepartment', 'employeePosition'];
    employeeFields.forEach(field => {
        const element = document.getElementById(field);
        if (element) element.value = '';
    });
        
        // Reset attendance symbol
        const symbolInput = document.getElementById('attendanceSymbol');
        if (symbolInput) {
            symbolInput.value = '';
        }
        
        // Remove active class from all symbol buttons
        document.querySelectorAll('.symbol-btn').forEach(btn => {
            btn.classList.remove('active');
        });
    }

// Hàm đóng modal chấm công
    function closeAttendanceModal() {
        const modal = document.getElementById('attendanceModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Xử lý sự kiện khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý modal thêm nhân viên
    const addEmployeeBtn = document.querySelector('.add-employee-btn');
    if (addEmployeeBtn) {
        addEmployeeBtn.addEventListener('click', showAddEmployeeModal);
    }

    // Xử lý sự kiện khi click ra ngoài modal thêm nhân viên
    const employeeModal = document.getElementById('addEmployeeModal');
    if (employeeModal) {
        employeeModal.addEventListener('click', function(e) {
            if (e.target === employeeModal) {
                closeAddEmployeeModal();
            }
        });
    }

    // Xử lý sự kiện khi click ra ngoài modal chấm công
    const attendanceModal = document.getElementById('attendanceModal');
    if (attendanceModal) {
        attendanceModal.addEventListener('click', function(e) {
            if (e.target === attendanceModal) {
        closeAttendanceModal();
    }
});
    }

    // Xử lý sự kiện khi tải ảnh đại diện
    const avatarInput = document.getElementById('avatar');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarPreview = document.getElementById('avatarPreview');
                    if (avatarPreview) {
                        avatarPreview.innerHTML = `<img src="${e.target.result}" alt="Avatar">`;
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Xử lý form thêm nhân viên
    const employeeForm = document.getElementById('addEmployeeForm');
    if (employeeForm) {
        employeeForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
            const submitButton = employeeForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            
            try {
                const formData = new FormData(employeeForm);
                
                const response = await fetch('/api/employees/add.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    showNotification('success', 'Thành công', 'Thêm nhân viên thành công');
                    closeAddEmployeeModal();
                    // Reload danh sách nhân viên nếu cần
                } else {
                    throw new Error(result.message || 'Có lỗi xảy ra khi thêm nhân viên');
                }
            } catch (error) {
                showNotification('error', 'Lỗi', error.message);
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Thêm nhân viên';
            }
        });
    }

    // Xử lý form chấm công
    const attendanceForm = document.getElementById('attendanceForm');
    if (attendanceForm) {
        attendanceForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = attendanceForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        
        try {
                const formData = new FormData(attendanceForm);

            const response = await fetch('/api/attendance/save.php', {
                method: 'POST',
                    body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                showNotification('success', 'Thành công', 'Chấm công thành công');
                closeAttendanceModal();
            } else {
                    throw new Error(result.message || 'Có lỗi xảy ra khi chấm công');
            }
        } catch (error) {
            showNotification('error', 'Lỗi', error.message);
        } finally {
            submitButton.disabled = false;
                submitButton.innerHTML = 'Lưu chấm công';
        }
    });
    }

    // Xử lý tìm kiếm nhân viên trong modal chấm công
    let searchTimeout;
    const employeeSearch = document.getElementById('employeeSearch');
    const searchResults = document.getElementById('searchResults');
    
    if (employeeSearch) {
        // Xử lý khi click ra ngoài để đóng kết quả tìm kiếm
        document.addEventListener('click', (e) => {
            if (!employeeSearch.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });

        // Xử lý khi nhấn ESC để đóng kết quả tìm kiếm
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                searchResults.style.display = 'none';
            }
        });

        employeeSearch.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const searchTerm = e.target.value.trim();
        
        if (searchTerm.length < 2) {
                searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`/api/employees/search.php?q=${encodeURIComponent(searchTerm)}`);
                const data = await response.json();
                
                    searchResults.innerHTML = '';
                
                if (data.length > 0) {
                    data.forEach(employee => {
                        const div = document.createElement('div');
                        div.className = 'search-result-item';
                            div.innerHTML = `
                                <div class="employee-id">${employee.employee_id}</div>
                                <div class="employee-name">${employee.full_name}</div>
                                <div class="employee-department">${employee.department_name}</div>
                            `;
                            div.onclick = () => {
                                selectEmployee(employee);
                                searchResults.style.display = 'none';
                            };
                            searchResults.appendChild(div);
                        });
                        searchResults.style.display = 'block';
                } else {
                        searchResults.style.display = 'none';
                }
            } catch (error) {
                console.error('Error:', error);
                    searchResults.style.display = 'none';
            }
        }, 300);
    });
    }

    // Xử lý chọn ký hiệu chấm công
    document.querySelectorAll('.symbol-btn').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.symbol-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.style.backgroundColor = '';
                btn.style.color = '';
                btn.style.borderColor = '';
            });
            
            button.classList.add('active');
            const color = button.dataset.color;
            button.style.backgroundColor = `var(--${color})`;
            button.style.color = 'white';
            button.style.borderColor = `var(--${color})`;
            
            document.getElementById('attendanceSymbol').value = button.dataset.symbol;
        });
    });
});

// Hàm chọn nhân viên từ kết quả tìm kiếm
function selectEmployee(employee) {
    document.getElementById('employeeId').value = employee.employee_id;
    document.getElementById('employeeName').value = employee.full_name;
    document.getElementById('employeeDepartment').value = employee.department_name;
    document.getElementById('employeePosition').value = employee.position_name;
    
    document.getElementById('employeeSearch').value = '';
    document.getElementById('searchResults').style.display = 'none';
}

// Hàm hiển thị modal nghỉ phép
function showLeaveModal() {
    // TODO: Thêm modal nghỉ phép
    console.log('Show leave modal');
}

// Hàm hiển thị modal tính lương
function showSalaryModal() {
    // TODO: Thêm modal tính lương
    console.log('Show salary modal');
}

// Function to update dashboard statistics
async function updateDashboardStats() {
    try {
        const response = await fetch('../api/dashboard_stats.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        
        if (data.success) {
            // Update total employees
            const totalEmployees = document.getElementById('totalEmployees');
            if (totalEmployees) {
                totalEmployees.textContent = data.data.totalEmployees.toLocaleString();
            }

            // Update on-time percentage
            const onTimePercentage = document.getElementById('onTimePercentage');
            if (onTimePercentage) {
                onTimePercentage.textContent = data.data.onTimePercentage + '%';
            }

            // Update present today
            const presentToday = document.getElementById('presentToday');
            if (presentToday) {
                presentToday.textContent = data.data.presentToday.toLocaleString();
            }

            // Update absent today
            const absentToday = document.getElementById('absentToday');
            if (absentToday) {
                absentToday.textContent = data.data.absentToday.toLocaleString();
            }
        } else {
            console.error('Error fetching dashboard stats:', data.message);
            // Show error message to user
            showNotification('error', 'Lỗi', 'Không thể tải dữ liệu thống kê: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        // Show error message to user
        showNotification('error', 'Lỗi', 'Có lỗi xảy ra khi tải dữ liệu thống kê');
    }
}

// Function to show notification
function showNotification(type, title, message) {
    // You can implement your own notification system here
    alert(`${title}: ${message}`);
}

// Update stats when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initial update
    updateDashboardStats();
    
    // Update stats every 5 minutes
    setInterval(updateDashboardStats, 300000);
});

// Function to fetch and update dashboard statistics
function updateDashboardStats() {
    fetch('../api/dashboard_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update total employees
                document.getElementById('totalEmployees').textContent = data.data.totalEmployees;
                
                // Update present today
                document.getElementById('presentToday').textContent = data.data.presentToday;
                
                // Update absent today
                document.getElementById('absentToday').textContent = data.data.absentToday;
                
                // Update on-time percentage with % symbol
                document.getElementById('onTimePercentage').textContent = data.data.onTimePercentage + '%';
            } else {
                console.error('Failed to fetch dashboard stats:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching dashboard stats:', error);
        });
}

// Update stats when page loads
document.addEventListener('DOMContentLoaded', updateDashboardStats);

// Update stats every 5 minutes
setInterval(updateDashboardStats, 300000);
</script>

    </div>

    <!-- JavaScript Files -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>