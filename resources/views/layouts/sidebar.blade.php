<div class="sidebar bg-white text-gray-800 flex flex-col fixed h-full z-50 transition-all duration-300 ease-in-out" id="sidebar">
    <!-- Logo -->
    <div class="p-4 flex items-center justify-between border-b">
        <div class="flex items-center">
            <img src="/images/m-coffe.png" alt="Kifa Bakery Logo" class="w-10 h-10 object-contain" />
            <span class="ml-2 font-bold text-xl whitespace-nowrap sidebar-logo-text">M-Coffee</span>
        </div>
        <button id="toggleSidebarBtn" class="text-gray-500 hover:text-black hidden md:block transition-all">
            <i data-lucide="chevrons-left" class="w-5 h-5 text-black" id="toggleIcon"></i>
        </button>
    </div>

    <!-- Outlet Dropdown -->
    <div class="px-4 py-3 border-b">
        <div class="relative">
            <button id="outletDropdownButton" class="w-full flex items-center justify-between px-3 py-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all">
                <div class="flex items-center">
                    <i data-lucide="store" class="w-5 h-5 text-black"></i>
                    <span class="ml-3 font-medium truncate sidebar-text">Loading outlets...</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 transition-transform text-black" id="outletDropdownArrow"></i>
            </button>
            
            <!-- Outlet Dropdown Menu -->
            <div id="outletDropdown" class="hidden absolute left-0 right-0 mt-1 bg-white rounded-lg shadow-lg z-50 border border-gray-200 max-h-60 overflow-y-auto">
                <!-- Search Box -->
                <div class="p-2 border-b">
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                        <input type="text" placeholder="Cari outlet..." class="w-full pl-9 pr-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-green-700 focus:border-green-700">
                    </div>
                </div>
                
                <!-- Outlet List -->
                <div class="p-2">
                    <ul id="outletListContainer" class="divide-y divide-gray-100"></ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto py-4">
        <div class="px-4 py-2 group rounded-lg transition-all menu-item">
           <a href="/dashboard" class="flex items-center py-2 hover:text-green-700 transition-all menu-subitem">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 text-black sidebar-icon"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </div>

        <!-- Product Dropdown -->
        @if(auth()->check() && auth()->user()->role !== 'supervisor')
        <div class="menu-item px-4 py-2 group rounded-lg transition-all" data-dropdown="productDropdown">
            <div class="flex items-center justify-between w-full cursor-pointer">
                <div class="flex items-center">
                    <i data-lucide="package" class="w-5 h-5 sidebar-icon"></i>
                    <span class="ml-3 sidebar-text">Produk</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform sidebar-arrow" id="productDropdownArrow"></i>
            </div>
            <div id="productDropdown" class="hidden pl-12 mt-2 sidebar-dropdown">
                <a href="/list-produk" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="list" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Daftar Produk</span>
                </a>
                <a href="/kategori" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="tag" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Kategori</span>
                </a>
            </div>
        </div>

        <!-- Outlet Management Dropdown -->
        <div class="menu-item px-4 py-2 group rounded-lg transition-all" data-dropdown="outletManagementDropdown">
            <div class="flex items-center justify-between w-full cursor-pointer">
                <div class="flex items-center">
                    <i data-lucide="building-2" class="w-5 h-5 sidebar-icon"></i>
                    <span class="ml-3 sidebar-text">Outlet</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform sidebar-arrow" id="outletManagementDropdownArrow"></i>
            </div>
            <div id="outletManagementDropdown" class="hidden pl-12 mt-2 sidebar-dropdown">
                <a href="/outlet" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="list" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Daftar Outlet</span>
                </a>
            </div>
        </div>
        @endif

        <!-- Stock Dropdown -->
        <div class="menu-item px-4 py-2 group rounded-lg transition-all" data-dropdown="stockDropdown">
            <div class="flex items-center justify-between w-full cursor-pointer">
                <div class="flex items-center">
                    <i data-lucide="package-open" class="w-5 h-5 sidebar-icon"></i>
                    <span class="ml-3 sidebar-text">Stok</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform sidebar-arrow" id="stockDropdownArrow"></i>
            </div>
            <div id="stockDropdown" class="hidden pl-12 mt-2 sidebar-dropdown">
                <a href="/bahan-baku" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="beaker" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Bahan Baku</span>
                </a>

                <a href="/riwayat-stok" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="history" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Riwayat Stok</span>
                </a>
                <a href="/stok-per-tanggal" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="calendar" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Stok Pertanggal</span>
                </a>
                @if(auth()->check() && auth()->user()->role !== 'supervisor')
                <a href="/penyesuaian-stok" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="edit" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Penyesuaian Stok</span>
                </a>
                {{-- <a href="/transfer-stok" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="truck" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Transfer Stok</span>
                </a>
                <a href="/approve-stok" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Approve Stok</span>
                </a> --}}
                @endif
            </div>
        </div>

        <!-- User Dropdown -->
        <div class="menu-item px-4 py-2 group rounded-lg transition-all" data-dropdown="userDropdown">
            <div class="flex items-center justify-between w-full cursor-pointer">
                <div class="flex items-center">
                    <i data-lucide="users" class="w-5 h-5 sidebar-icon"></i>
                    <span class="ml-3 sidebar-text">User</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform sidebar-arrow" id="userDropdownArrow"></i>
            </div>
            <div id="userDropdown" class="hidden pl-12 mt-2 sidebar-dropdown">
                <a href="/member" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="user" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Member</span>
                </a>
                @if(auth()->check() && auth()->user()->role !== 'supervisor')
                <a href="/staff" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="users" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Staff</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Closing Dropdown -->
        <div class="menu-item px-4 py-2 group rounded-lg transition-all" data-dropdown="closingDropdown">
            <div class="flex items-center justify-between w-full cursor-pointer">
                <div class="flex items-center">
                    <i data-lucide="clock" class="w-5 h-5 sidebar-icon"></i>
                    <span class="ml-3 sidebar-text">Closing</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform sidebar-arrow" id="closingDropdownArrow"></i>
            </div>
            <div id="closingDropdown" class="hidden pl-12 mt-2 sidebar-dropdown">
                <a href="/riwayat-kas" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="wallet" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Riwayat Kas</span>
                </a>
                <a href="/riwayat-transaksi" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="receipt" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Riwayat Transaksi</span>
                </a>
            </div>
        </div>

        <!-- Insight Sales Dropdown -->
        <div class="menu-item px-4 py-2 group rounded-lg transition-all" data-dropdown="insightSalesDropdown">
            <div class="flex items-center justify-between w-full cursor-pointer">
                <div class="flex items-center">
                    <i data-lucide="brain" class="w-5 h-5 sidebar-icon"></i>
                    <span class="ml-3 sidebar-text">Insight Penjualan</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform sidebar-arrow" id="insightSalesDropdownArrow"></i>
            </div>
            <div id="insightSalesDropdown" class="hidden pl-12 mt-2 sidebar-dropdown">
                <a href="/menu-favorit" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="trending-up" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Prediksi Menu Favorit</span>
                </a>
                <a href="/prediksi-penjualan" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Prediksi Penjualan</span>
                </a>
                <!-- Tambahan menu Analisis Pembelian -->
                <a href="/analisis-pembelian" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="shopping-cart" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Analisis Pembelian</span>
                </a>
            </div>
        </div>


        <!-- Report Dropdown -->
        <div class="menu-item px-4 py-2 group rounded-lg transition-all" data-dropdown="reportDropdown">
            <div class="flex items-center justify-between w-full cursor-pointer">
                <div class="flex items-center">
                    <i data-lucide="file-text" class="w-5 h-5 sidebar-icon"></i>
                    <span class="ml-3 sidebar-text">Laporan</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform sidebar-arrow" id="reportDropdownArrow"></i>
            </div>
            <div id="reportDropdown" class="hidden pl-12 mt-2 sidebar-dropdown">
                <a href="/perhari" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="calendar" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Perhari</span>
                </a>
                <a href="/per-item" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="box" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Per Item</span>
                </a>
                <a href="/per-kategori" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="tag" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Per Kategori</span>
                </a>
                <a href="/per-member" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="user" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Per Member</span>
                </a>
                {{-- <a href="/stok" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="package" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Stock</span>
                </a> --}}
                {{-- <a href="/laporan-riwayat-stok" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="history" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Riwayat Stok</span>
                </a>
                <a href="/laporan-approve" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Approve</span>
                </a> --}}
                 <a href="/laporan-laba" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Laba</span>
                </a>
                <a href="/laporan-bahan-baku" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="beaker" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">bahan baku</span>
                </a>
            </div>
        </div>

        @if(auth()->check() && auth()->user()->role !== 'supervisor')
        <!-- Settings Dropdown -->
        <div class="menu-item px-4 py-2 group rounded-lg transition-all" data-dropdown="settingsDropdown">
            <div class="flex items-center justify-between w-full cursor-pointer">
                <div class="flex items-center">
                    <i data-lucide="settings" class="w-5 h-5 sidebar-icon"></i>
                    <span class="ml-3 sidebar-text">Pengaturan</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform sidebar-arrow" id="settingsDropdownArrow"></i>
            </div>
            <div id="settingsDropdown" class="hidden pl-12 mt-2 sidebar-dropdown">
                <a href="/template-print" class="menu-subitem flex items-center py-2 transition-all w-full">
                    <i data-lucide="printer" class="w-4 h-4 mr-3 sidebar-icon"></i>
                    <span class="sidebar-text">Template Print</span>
                </a>
            </div>
        </div>
        @endif
    </nav>
    
    <!-- Collapse Button for Desktop -->
    <div class="p-4 border-t flex justify-center">
        <button id="toggleSidebar" class="text-gray-500 hover:text-black transition-all">
            <i data-lucide="chevrons-left" class="w-5 h-5 text-black" id="bottomToggleIcon"></i>
        </button>
    </div>
</div>

<!-- Mobile overlay for clicking outside to close -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

<!-- Mobile toggle button (hamburger) -->
<button id="mobileSidebarToggle" class="fixed bottom-4 right-4 md:hidden bg-green-700 text-white p-3 rounded-full shadow-lg z-30">
    <i data-lucide="menu" class="w-6 h-6"></i>
</button>

<style>
    /* Sidebar responsive styles */
    .sidebar {
        width: 280px;
        left: 0;
        top: 0;
        bottom: 0;
        transform: translateX(0);
        transition: transform 0.3s ease, width 0.3s ease;
    }
    
    .sidebar.collapsed {
        width: 80px;
    }
    
    .sidebar.collapsed .sidebar-text,
    .sidebar.collapsed .sidebar-logo-text,
    .sidebar.collapsed .sidebar-arrow {
        display: none;
    }
    
    .sidebar.collapsed .sidebar-icon {
        margin-right: 0;
    }
    
    .sidebar.collapsed .menu-item > div > div {
        justify-content: center;
    }
    
    .sidebar.collapsed .sidebar-dropdown {
        position: absolute;
        left: 80px;
        width: 200px;
        background: white;
        box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        border-radius: 0 8px 8px 0;
        padding-left: 0;
        margin-left: 0;
    }
    
    /* Mobile styles */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            width: 280px;
        }
        
        .sidebar.mobile-open {
            transform: translateX(0);
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }
        
        .sidebar.collapsed {
            width: 280px;
        }
        
        #mainContent {
            margin-left: 0 !important;
        }
    }
    
    /* Main content adjust with sidebar */
    #mainContent {
        margin-left: 280px;
        transition: margin-left 0.3s ease;
    }
    
    #mainContent.collapsed {
        margin-left: 80px;
    }
    
    /* Hover effect menu item and subitem */
    .sidebar .menu-item:hover > .flex.items-center,
    .sidebar .menu-subitem:hover {
        background-color: #f3f4f6;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }

    /* Active menu parent */
    .menu-item.active-parent > .flex.items-center.justify-between {
        background-color: #dcfce7; /* Changed from orange to green */
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }

    /* Active menu subitem */
    .menu-subitem.active {
        background-color: #dcfce7; /* Changed from orange to green */
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }

    /* Text color and font weight for active items */
    .menu-subitem.active i,
    .menu-subitem.active span,
    .menu-item.active-parent > .flex.items-center.justify-between i,
    .menu-item.active-parent > .flex.items-center.justify-between span {
        color: #3b6b0d; /* Changed from orange to green */
        font-weight: 500;
    }

    /* Dropdown styles */
    .sidebar-dropdown {
        transition: all 0.3s ease;
    }

    /* Icon rotation animation */
    .rotate-180 {
        transform: rotate(180deg);
        transition: transform 0.3s ease;
    }

    /* Cursor for clickable items */
    .cursor-pointer {
        cursor: pointer;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Lucide icons
        lucide.createIcons();

        // Get DOM elements
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
        const toggleSidebar = document.getElementById('toggleSidebar');
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const toggleIcon = document.getElementById('toggleIcon');
        const bottomToggleIcon = document.getElementById('bottomToggleIcon');

        // Function to toggle sidebar collapsed state
        function toggleCollapse() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
            
            // Rotate toggle icons
            if (toggleIcon) toggleIcon.classList.toggle('rotate-180');
            if (bottomToggleIcon) bottomToggleIcon.classList.toggle('rotate-180');
            
            // Store state in localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        // Function to toggle mobile sidebar
        function toggleMobileSidebar() {
            sidebar.classList.toggle('mobile-open');
            sidebarOverlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }

        // Initialize sidebar state from localStorage
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('collapsed');
            if (toggleIcon) toggleIcon.classList.add('rotate-180');
            if (bottomToggleIcon) bottomToggleIcon.classList.add('rotate-180');
        }

        // Event listeners for desktop toggle
        if (toggleSidebarBtn) {
            toggleSidebarBtn.addEventListener('click', toggleCollapse);
        }
        
        if (toggleSidebar) {
            toggleSidebar.addEventListener('click', toggleCollapse);
        }
        
        // Event listeners for mobile toggle
        if (mobileSidebarToggle) {
            mobileSidebarToggle.addEventListener('click', toggleMobileSidebar);
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', toggleMobileSidebar);
        }

        // Close mobile sidebar when clicking on a link
        document.querySelectorAll('.sidebar a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    toggleMobileSidebar();
                }
            });
        });

        // Handle window resize
        function handleResize() {
            if (window.innerWidth > 768) {
                // On desktop, ensure mobile sidebar is closed
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                
                // Apply collapsed state if it was saved
                if (localStorage.getItem('sidebarCollapsed') === 'true') {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('collapsed');
                } else {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('collapsed');
                }
            } else {
                // On mobile, ensure sidebar starts closed
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // Initial check
        handleResize();
        
        // Add resize listener with debounce
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(handleResize, 250);
        });

        // Outlet Dropdown
        const outletDropdownButton = document.getElementById('outletDropdownButton');
        const outletDropdown = document.getElementById('outletDropdown');
        const outletDropdownArrow = document.getElementById('outletDropdownArrow');

        if (outletDropdownButton && outletDropdown) {
            outletDropdownButton.addEventListener('click', function (e) {
                e.stopPropagation();
                outletDropdown.classList.toggle('hidden');
                outletDropdownArrow.classList.toggle('rotate-180');
                closeOtherDropdowns(outletDropdown);
            });

            document.addEventListener('click', function () {
                outletDropdown.classList.add('hidden');
                outletDropdownArrow.classList.remove('rotate-180');
            });

            outletDropdown.addEventListener('click', function (e) {
                e.stopPropagation();
            });

            const searchInput = outletDropdown.querySelector('input');
            const outletItems = outletDropdown.querySelectorAll('li');

            searchInput?.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                outletItems.forEach(item => {
                    const name = item.textContent.toLowerCase();
                    item.style.display = name.includes(searchTerm) ? 'block' : 'none';
                });
            });
        }

        // Menu Dropdowns
        const menuItems = document.querySelectorAll('.menu-item[data-dropdown]');

        menuItems.forEach(item => {
            const dropdownId = item.getAttribute('data-dropdown');
            const dropdown = document.getElementById(dropdownId);
            const arrow = item.querySelector('[data-lucide="chevron-down"]');
            const dropdownHeader = item.querySelector('.flex.items-center.justify-between');

            if (dropdownHeader && dropdown) {
                dropdownHeader.addEventListener('click', function (e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                    arrow?.classList.toggle('rotate-180');
                    closeOtherDropdowns(dropdown);
                });
            }

            const menuSubitems = item.querySelectorAll('.menu-subitem');
            menuSubitems.forEach(subitem => {
                subitem.addEventListener('click', function (e) {
                    document.querySelectorAll('.menu-subitem').forEach(si => si.classList.remove('active'));
                    subitem.classList.add('active');
                    document.querySelectorAll('.menu-item').forEach(mi => mi.classList.remove('active-parent'));
                    item.classList.add('active-parent');
                });
            });
        });

        // Close other dropdowns when opening a new one
        function closeOtherDropdowns(currentDropdown) {
            document.querySelectorAll('.sidebar-dropdown').forEach(dropdown => {
                if (dropdown !== currentDropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                    const arrow = dropdown.closest('.menu-item')?.querySelector('[data-lucide="chevron-down"]');
                    if (arrow) arrow.classList.remove('rotate-180');
                }
            });
        }

        // Set active menu based on current URL
        function setActiveMenu() {
            const currentPath = window.location.pathname;

            // Remove all active classes first
            document.querySelectorAll('.active-parent, .active').forEach(el => {
                el.classList.remove('active-parent', 'active');
            });

            // Find matching menu item
            const allLinks = [...document.querySelectorAll('.menu-link, .menu-subitem')];
            const activeLink = allLinks.find(link => {
                const href = link.getAttribute('href');
                return href && (currentPath === href || currentPath.startsWith(href));
            });

            if (activeLink) {
                activeLink.classList.add('active');
                const parentItem = activeLink.closest('.menu-item');
                if (parentItem && activeLink.classList.contains('menu-subitem')) {
                    parentItem.classList.add('active-parent');
                    const dropdownId = parentItem.getAttribute('data-dropdown');
                    const dropdown = document.getElementById(dropdownId);
                    if (dropdown) {
                        dropdown.classList.remove('hidden');
                        parentItem.querySelector('[data-lucide="chevron-down"]')?.classList.add('rotate-180');
                    }
                }
            }
        }

    // Load outlets function
    async function loadOutletsFromAPI() {
        const outletListContainer = document.getElementById('outletListContainer');
        const outletNameDisplay = document.querySelector('#outletDropdownButton span');
        const outletDropdownButton = document.getElementById('outletDropdownButton');
        const outletDropdown = document.getElementById('outletDropdown');
        const outletDropdownArrow = document.getElementById('outletDropdownArrow');

        try {
            const response = await fetch('/api/outlets', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!result.success) throw new Error('Gagal memuat outlet');

            // Clear existing list
            outletListContainer.innerHTML = '';

            // Cek apakah ada outlet yang aktif
            const hasActiveOutlet = result.data.some(outlet => outlet.is_active);
            const userRole = "{{ auth()->user()->role }}"; // Ambil role user dari Laravel

            if (!hasActiveOutlet) {
                outletListContainer.innerHTML = `
                    <li class="no-outlet-message">
                        Tidak ada outlet aktif. Silakan aktifkan outlet terlebih dahulu.
                    </li>
                `;
                if (outletNameDisplay) {
                    outletNameDisplay.textContent = 'Tidak Ada Outlet Aktif';
                }
                return;
            }

            // Jika role supervisor, langsung pilih outlet pertama yang aktif
            if (userRole === 'supervisor') {
                const activeOutlets = result.data.filter(o => o.is_active);
                if (activeOutlets.length > 0) {
                    const defaultOutlet = activeOutlets[0];
                    if (outletNameDisplay) {
                        outletNameDisplay.textContent = defaultOutlet.name;
                    }
                    localStorage.setItem('selectedOutletId', defaultOutlet.id);
                    
                    // Nonaktifkan dropdown untuk supervisor
                    if (outletDropdownButton) {
                        outletDropdownButton.style.pointerEvents = 'none';
                        outletDropdownButton.style.cursor = 'default';
                    }
                    if (outletDropdownArrow) {
                        outletDropdownArrow.style.display = 'none';
                    }
                }
                return; // Keluar dari fungsi setelah memilih outlet untuk supervisor
            }

            // Untuk role selain supervisor, tampilkan dropdown seperti biasa
            result.data.forEach(outlet => {
                // Hanya tampilkan outlet yang aktif
                if (outlet.is_active) {
                    const li = document.createElement('li');
                    li.className = 'px-4 py-2 hover:bg-orange-50 cursor-pointer text-sm flex items-center gap-2';
                    li.innerHTML = `<i data-lucide="store" class="w-4 h-4 text-orange-500"></i> <span>${outlet.name}</span>`;
                    
                    li.addEventListener('click', () => {
                        outletNameDisplay.textContent = outlet.name;
                        outletDropdown.classList.add('hidden');
                        outletDropdownArrow.classList.remove('rotate-180');
                        localStorage.setItem('selectedOutletId', outlet.id);
                    });

                    outletListContainer.appendChild(li);
                }
            });

            // Re-initialize Lucide icons
            lucide.createIcons();
            
            // Set outlet aktif sebagai default
            const savedOutletId = localStorage.getItem('selectedOutletId');
            const activeOutlets = result.data.filter(o => o.is_active);
            if (activeOutlets.length > 0) {
                const defaultOutlet = activeOutlets.find(o => o.id.toString() === savedOutletId) || activeOutlets[0];
                if (outletNameDisplay) {
                    outletNameDisplay.textContent = defaultOutlet.name;
                }
            }
            
        } catch (err) {
            console.error('Failed to load outlets:', err);
            outletListContainer.innerHTML = '<li class="px-4 py-2 text-sm text-red-500">Gagal memuat outlet</li>';
            if (outletNameDisplay) {
                outletNameDisplay.textContent = 'Pilih Outlet';
            }
        }
    }

        // Initialize active menu and load outlets
        setActiveMenu();
        loadOutletsFromAPI();
    });

    function updateSidebarVisibility() {
        const hasActiveOutlet = localStorage.getItem('hasActiveOutlet') === 'true';
        const sidebarDropdowns = [
            'productDropdown',
            'outletManagementDropdown',
            'stockDropdown',
            'userDropdown',
            'closingDropdown',
            'insightSalesDropdown',
            'reportDropdown',
            'settingsDropdown'
        ];

        sidebarDropdowns.forEach(dropdownId => {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                if (!hasActiveOutlet) {
                    dropdown.classList.add('hidden');
                    const arrow = document.getElementById(`${dropdownId}Arrow`);
                    if (arrow) arrow.classList.remove('rotate-180');
                }
            }
        });

        // Sembunyikan/munculkan menu utama berdasarkan status outlet
        const menuItems = document.querySelectorAll('.menu-item[data-dropdown]');
        menuItems.forEach(item => {
            if (!hasActiveOutlet) {
                item.style.display = 'none';
            } else {
                item.style.display = 'block';
            }
        });

        // Sembunyikan/munculkan dashboard
        const dashboardItem = document.querySelector('.menu-item:not([data-dropdown])');
        if (dashboardItem) {
            dashboardItem.style.display = 'block'; // Dashboard selalu ditampilkan
        }
    }
</script>