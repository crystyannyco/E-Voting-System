<!-- Main Content Area -->


<div class="flex-1 overflow-auto ">
    <!-- Overview Header with Toggle Button and Profile Pic -->
    <div class="no-print">
        <div class="bg-white px-6 py-6 border-b border-gray-200 shadow-md flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800" id="overviewText">
                <?= $title ?? 'Dashboard' ?>
            </h2>
            <div class="flex items-center no-print">
                <!-- Profile Pic Button -->
                <div class="relative mr-4">
                    <button class="flex items-center focus:outline-none" id="profileDropdown">
                        <!-- Profile avatar circle with icon -->
                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center border-2 border-blue-300 overflow-hidden">
                            <?php if(!empty($admin_profile_image)): ?>
                                <img id="profilePreview" src="<?= base_url('uploads/admin_profiles/' . $admin_profile_image) ?>" alt="Admin Profile" class="w-full h-full object-cover" />
                            <?php else: ?>
                                <img id="profilePreview" src="<?= base_url('assets/profile.png') ?>" alt="Admin Profile" class="w-full h-full object-cover" />
                            <?php endif; ?>
                        </div>

                        <!-- Username and Role Text -->
                        <div class="ml-2 hidden md:block text-left">
                            <p class="text-sm font-medium text-gray-700">
                                <?= htmlspecialchars($admin_username ?? 'Admin') ?>
                            </p>
                            <p class="text-xs text-gray-500">
                                <?php 
                                if (isset($admin_role)) {
                                    switch ($admin_role) {
                                        case 1: echo 'Super Admin'; break;
                                        case 2: echo 'Administrator'; break;
                                        case 3: echo 'Moderator'; break;
                                        default: echo 'User'; break;
                                    }
                                } else {
                                    echo 'Administrator';
                                }
                                ?>
                            </p>
                        </div>

                        <!-- Dropdown arrow -->
                        <svg class="w-4 h-4 ml-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    
                    <!-- Dropdown Menu (Hidden by default) -->
                    <div id="profileMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20 hidden">
                        <a href="<?= base_url('profile') ?>" data-title="Admin Profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Your Profile</a>
                        <!-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Settings</a> -->
                        <div class="border-t border-gray-100"></div>
                        <a href="/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Sign out</a>
                    </div>
                </div>
                
                <!-- Sidebar Toggle Button -->
                <button id="sidebarToggle" class="md:hidden p-2 rounded-md bg-blue-50 text-blue-600">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Set up click handlers for all links
                document.querySelectorAll('a[href]').forEach(link => {
                    link.addEventListener('click', function() {
                        // Skip links that are just page anchors
                        if (this.getAttribute('href').startsWith('#')) return;
                        
                        // Get the link text as the title
                        let title = '';
                        
                        // First try to get from data-title attribute if present
                        if (this.hasAttribute('data-title')) {
                            title = this.getAttribute('data-title');
                        } 
                        // Next try to get from span element
                        else if (this.querySelector('span:last-child')) {
                            title = this.querySelector('span:last-child').textContent.trim();
                        }
                        // Otherwise use the full link text
                        else {
                            title = this.textContent.trim();
                        }
                        
                        // Store in session storage
                        sessionStorage.setItem('pageTitle', title);
                    });
                });
                
                // Update the title on page load
                const storedTitle = sessionStorage.getItem('pageTitle');
                if (storedTitle && document.getElementById('overviewText')) {
                    document.getElementById('overviewText').textContent = storedTitle;
                }
            });

            // Add this to your existing script in the topbar section
            document.addEventListener('DOMContentLoaded', function() {
                // Check for refresh parameter in URL
                const urlParams = new URLSearchParams(window.location.search);
                if(urlParams.get('refresh') === '1') {
                    // Clear the parameter but don't trigger another page load
                    window.history.replaceState({}, document.title, window.location.pathname);
                    
                    // If you have an admin data table, refresh it
                    if($.fn.DataTable.isDataTable('#administratorTable')) {
                        $('#administratorTable').DataTable().ajax.reload(null, false);
                    }
                }
            });

            function updateRoleDisplay() {
                // Get the current role from session
                const adminRole = <?= session()->get('admin_role') ?>;
                
                // Update the role text in the topbar
                const roleElement = document.querySelector('.text-xs.text-gray-500');
                if (roleElement) {
                    let roleText = 'User';
                    switch (adminRole) {
                        case 1: roleText = 'Super Admin'; break;
                        case 2: roleText = 'Administrator'; break;
                        case 3: roleText = 'Moderator'; break;
                    }
                    roleElement.textContent = roleText;
                }
            }
        </script>