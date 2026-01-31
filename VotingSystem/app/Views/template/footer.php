
    <!-- JavaScript for sidebar toggle functionality -->
        <!-- JavaScript for sidebar toggle and profile dropdown functionality -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle functionality
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            sidebarToggle.addEventListener('click', function() {
                if (sidebar.classList.contains('w-0')) {
                    sidebar.classList.remove('w-0');
                    sidebar.classList.add('w-64');
                } else {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-0');
                }
            });
            
            // Profile dropdown functionality
            const profileDropdown = document.getElementById('profileDropdown');
            const profileMenu = document.getElementById('profileMenu');
            
            profileDropdown.addEventListener('click', function() {
                profileMenu.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!profileDropdown.contains(event.target) && !profileMenu.contains(event.target)) {
                    profileMenu.classList.add('hidden');
                }
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 768 && 
                    !sidebar.contains(event.target) && 
                    !sidebarToggle.contains(event.target) && 
                    !sidebar.classList.contains('w-0')) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-0');
                }
            });
            
            // Adjust sidebar on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('w-0');
                    sidebar.classList.add('w-64');
                } else {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-0');
                }
            });
        });
    </script>
</body>
</html>