<!-- Sidebar - with mobile responsive toggle -->
<div id="sidebar" class="no-print w-0 md:w-64 bg-white border-r border-gray-200 flex flex-col absolute md:relative h-full z-10 transition-all duration-300 overflow-hidden">
    <div class="px-6 py-8 border-b border-gray-200">
        <a href="/dashboard">
            <h1 class="text-xl font-bold text-blue-800">CSPC E-Vote</h1>
        </a>
    </div>
    <div class="flex-1 overflow-y-auto py-4">
        <nav class="px-2 space-y-1">
            <a href="<?= base_url('dashboard') ?>" class="sidebar-link flex items-center space-x-3 <?= str_contains(current_url(), 'dashboard') ? 'text-blue-600 bg-blue-50' : 'text-gray-500' ?> py-2 px-4 rounded-md transition duration-200">
                <span class="material-icons">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a href="<?= base_url('votes') ?>" class="sidebar-link flex items-center space-x-3 <?= str_contains(current_url(), 'votes') ? 'text-blue-600 bg-blue-50' : 'text-gray-500' ?> py-2 px-4 rounded-md transition duration-200">
                <span class="material-icons">ballot</span>
                <span>Votes</span>
            </a>
            <a href="<?= base_url('student') ?>" class="sidebar-link flex items-center space-x-3 <?= str_contains(current_url(), 'student') ? 'text-blue-600 bg-blue-50' : 'text-gray-500' ?> py-2 px-4 rounded-md transition duration-200">
                <span class="material-icons">group</span>
                <span>Students</span>
            </a>
            <a href="<?= base_url('position') ?>" class="sidebar-link flex items-center space-x-3 <?= str_contains(current_url(), 'position') ? 'text-blue-600 bg-blue-50' : 'text-gray-500' ?> py-2 px-4 rounded-md transition duration-200">
                <span class="material-icons">work</span>
                <span>Positions</span>
            </a>
            <a href="<?= base_url('partylist') ?>" class="sidebar-link flex items-center space-x-3 <?= str_contains(current_url(), 'partylist') ? 'text-blue-600 bg-blue-50' : 'text-gray-500' ?> py-2 px-4 rounded-md transition duration-200">
                <span class="material-icons">format_list_bulleted</span>
                <span>Partylist</span>
            </a>
            <a href="<?= base_url('candidate') ?>" class="sidebar-link flex items-center space-x-3 <?= str_contains(current_url(), 'candidate') ? 'text-blue-600 bg-blue-50' : 'text-gray-500' ?> py-2 px-4 rounded-md transition duration-200">
                <span class="material-icons">person</span>
                <span>Candidates</span>
            </a>
            <a href="<?= base_url('election') ?>" class="sidebar-link flex items-center space-x-3 <?= str_contains(current_url(), 'election') ? 'text-blue-600 bg-blue-50' : 'text-gray-500' ?> py-2 px-4 rounded-md transition duration-200">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12 1.586l-4 4v12.828l4-4V1.586zM3.707 3.293A1 1 0 002 4v10a1 1 0 00.293.707L6 18.414V5.586L3.707 3.293zM17.707 5.293L14 1.586v12.828l2.293 2.293A1 1 0 0018 16V6a1 1 0 00-.293-.707z" clip-rule="evenodd"></path>
                </svg>
                <span>Election Title</span>
            </a>
            <a href="<?= base_url('administrator') ?>" class="sidebar-link flex items-center space-x-3 <?= str_contains(current_url(), 'administrator') ? 'text-blue-600 bg-blue-50' : 'text-gray-500' ?> py-2 px-4 rounded-md transition duration-200">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                </svg>
                <span>Administrators</span>
            </a>
        </nav>
    </div>
</div>