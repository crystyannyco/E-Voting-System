<main class="p-6">     
    <!-- Administrator List Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Administrator List</h3>
            <div class="flex space-x-2">
                <?php if ($admin_role == 1): // Only Super Admin can add new administrators ?>
                <button id="addAdministratorBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
                    <span class="material-icons pr-2">person_add</span>
                    Add Administrator
                </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- DataTable Container -->
        <div class="p-4 overflow-x-auto">
            <!-- Alert Messages -->
            <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow" role="alert">
                <p><?= session()->getFlashdata('success') ?></p>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow" role="alert">
                <p><?= session()->getFlashdata('error') ?></p>
            </div>
            <?php endif; ?>
            <table id="administratorTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Administrator Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($administrators as $admin): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $admin['AdminID'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $admin['AdminName'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $admin['Email'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $admin['Username'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                                $roleLabels = [
                                    '1' => 'Super Admin',
                                    '2' => 'Admin',
                                    '3' => 'Moderator'
                                ];
                                echo isset($roleLabels[$admin['Role']]) ? $roleLabels[$admin['Role']] : 'Unknown Role';
                            ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex justify-center">
                                    <div class="inline-flex rounded-md border border-gray-200 overflow-hidden">
                                        <!-- View button - available to all roles -->
                                        <button 
                                            class="px-2 py-1 text-yellow-600 hover:text-yellow-900 hover:bg-gray-50 border-r border-gray-200 flex items-center" 
                                            onclick="viewAdministrator(<?= $admin['AdminID'] ?>)">
                                            <span class="material-icons">visibility</span>
                                        </button>
                                        
                                        <!-- Edit button - only for Super Admin and Admin -->
                                        <?php if ($admin_role <= 2): ?>
                                        <button 
                                            class="px-2 py-1 text-blue-600 hover:text-blue-900 hover:bg-gray-50 border-r border-gray-200 flex items-center" 
                                            onclick="editAdministrator(<?= $admin['AdminID'] ?>)">
                                            <span class="material-icons">edit_square</span>
                                        </button>
                                        <?php endif; ?>
                                        
                                        <!-- Delete button - only for Super Admin -->
                                        <?php if ($admin_role == 1): ?>
                                        <button 
                                            class="px-2 py-1 text-red-600 hover:text-red-900 hover:bg-gray-50 flex items-center" 
                                            onclick="confirmDeleteAdministrator(<?= $admin['AdminID'] ?>)">
                                            <span class="material-icons">delete</span>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit/View Administrator Modal -->
    <div id="administratorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add New Administrator</h3>
                <button onclick="closeModal('administratorModal')" class="text-gray-500 hover:text-gray-700">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <form id="administratorForm" action="<?= base_url('administrator/add') ?>" method="post">
                <input type="hidden" id="adminId" name="adminId" value="">
                
                <div class="mb-4">
                    <label for="adminName" class="block text-sm font-medium text-gray-700 mb-1">Administrator Name</label>
                    <input type="text" id="adminName" name="adminName" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="mb-4 md:w-1/2">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    <div class="mb-4 md:w-1/2">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                </div>
                
                <!-- New Birthdate and Sex Fields -->
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="mb-4 md:w-1/2">
                        <label for="birthdate" class="block text-sm font-medium text-gray-700 mb-1">Birthdate</label>
                        <input type="date" id="birthdate" name="birthdate" class="w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                    <div class="mb-4 md:w-1/2">
                        <label for="sex" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select id="sex" name="sex" class="w-full border border-gray-300 rounded-md shadow-sm p-2">
                            <option value="">Select Gender</option>
                            <option value="0">Male</option>
                            <option value="1">Female</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="mb-4 md:w-1/2">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" id="username" name="username" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    <div class="mb-4 md:w-1/2">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select id="role" name="role" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                            <option value="">Select Role</option>
                            <option value="1">Super Admin</option>
                            <option value="2">Admin</option>
                            <option value="3">Moderator</option>
                        </select>
                    </div>
                </div>
                
                <div class="password-fields-container flex flex-col md:flex-row md:space-x-4">
                    <div class="mb-4 md:w-1/2">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" class="w-full border border-gray-300 rounded-md shadow-sm p-2">
                        <p id="passwordNote" class="text-sm text-gray-500 mt-1 hidden">Leave empty to keep current password</p>
                    </div>
                    <div class="mb-4 md:w-1/2">
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="button" onclick="closeModal('administratorModal')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md mr-2">
                        <span id="cancelButtonText">Cancel</span>
                    </button>
                    <button type="submit" id="submitButton" class="bg-blue-600 text-white px-4 py-2 rounded-md">Add Administrator</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Administrator Modal -->
    <div id="viewAdministratorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Administrator Details</h3>
                <button id="closeViewModalBtn" class="text-gray-500 hover:text-gray-700">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div class="space-y-4">
                <div class="flex border-b pb-3">
                    <div class="w-1/3 font-medium text-gray-700">Administrator ID</div>
                    <div class="w-2/3" id="viewAdministratorID"></div>
                </div>
                <div class="flex border-b pb-3">
                    <div class="w-1/3 font-medium text-gray-700">Name</div>
                    <div class="w-2/3" id="viewAdministratorName"></div>
                </div>
                <div class="flex border-b pb-3">
                    <div class="w-1/3 font-medium text-gray-700">Email</div>
                    <div class="w-2/3" id="viewEmail"></div>
                </div>
                <div class="flex border-b pb-3">
                    <div class="w-1/3 font-medium text-gray-700">Phone Number</div>
                    <div class="w-2/3" id="viewPhone"></div>
                </div>
                <!-- New fields in view modal -->
                <div class="flex border-b pb-3">
                    <div class="w-1/3 font-medium text-gray-700">Birthdate</div>
                    <div class="w-2/3" id="viewBirthdate"></div>
                </div>
                <div class="flex border-b pb-3">
                    <div class="w-1/3 font-medium text-gray-700">Gender</div>
                    <div class="w-2/3" id="viewSex"></div>
                </div>
                <div class="flex border-b pb-3">
                    <div class="w-1/3 font-medium text-gray-700">Username</div>
                    <div class="w-2/3" id="viewUsername"></div>
                </div>
                <div class="flex border-b pb-3">
                    <div class="w-1/3 font-medium text-gray-700">Role</div>
                    <div class="w-2/3" id="viewRole"></div>
                </div>
                <div class="flex border-b pb-3">
                    <div class="w-1/3 font-medium text-gray-700">Created Date</div>
                    <div class="w-2/3" id="viewCreatedDate"></div>
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button id="closeViewModal" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md">Close</button>
            </div>
        </div>
    </div>
</main>

<script>
    // Store current admin role from PHP to use in JavaScript
    const currentAdminRole = <?= $admin_role ?? 3 ?>; // Default to lowest role (3) if not set

    $(document).ready(function() {
        // DataTable initialization
        $('#administratorTable').DataTable({
            scrollY: '350px',
            scrollX: true,
            scrollCollapse: true,
            initComplete: function() {
                $('.dataTables_length select, .dataTables_filter input').addClass('border border-gray-300 rounded px-3 py-1');
                $('.dataTables_paginate .paginate_button').addClass('px-3 py-1 border border-gray-300 rounded mx-1');
                $('.dataTables_paginate .paginate_button.current').addClass('bg-blue-600 text-white border-blue-600');
            }
        });

        // Search box
        $('#table-search').on('keyup', function() {
            $('#administratorTable').DataTable().search($(this).val()).draw();
        });

        // Add Administrator button click - only visible to Super Admin
        $("#addAdministratorBtn").on("click", function() {
            if (currentAdminRole === 1) {
                showAddModal();
            } else {
                alert("You don't have permission to add administrators.");
            }
        });
        
        // Form submission with validation
        $('#administratorForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            // First check if user has permission based on role
            const isUpdate = $('#submitButton').text().includes('Update');
            
            if (isUpdate && currentAdminRole > 2) {
                alert("You don't have permission to update administrators.");
                return false;
            }
            
            if (!isUpdate && currentAdminRole !== 1) {
                alert("You don't have permission to add administrators.");
                return false;
            }
            
            // Validate password match for new administrator or when password is being changed
            const password = $('#password').val();
            const confirmPassword = $('#confirmPassword').val();
            
            // For updates, only validate passwords if a new password is provided
            if (password || !isUpdate) {
                if (password !== confirmPassword) {
                    alert('Passwords do not match. Please try again.');
                    return false;
                }
            }
            
            // If updating, confirm with the user
            if (isUpdate) {
                if (!confirm('Are you sure you want to update this administrator?')) {
                    return false;
                }
            }
            
            // If all validations pass, submit the form
            this.submit();
        });

        // Close view modal buttons
        $("#closeViewModal, #closeViewModalBtn").on("click", function() {
            $("#viewAdministratorModal").addClass("hidden");
        });

        // Function to clear validation errors
        function clearValidationErrors() {
            $('.error-message').remove();
            $('.border-red-500').removeClass('border-red-500');
        }
    
        // Input event listeners to clear validation errors when user corrects input
        $('#administratorForm input, #administratorForm select').on('input change', function() {
            $(this).removeClass('border-red-500');
            $(this).parent().find('.error-message').remove();
        });
        
        // Function to display validation errors
        function displayValidationErrors() {
            // Check if we need to show Add Modal
            if (<?= json_encode(session()->getFlashdata('showAddModal') ?: false) ?>) {
                // Only Super Admin can add
                if (currentAdminRole !== 1) {
                    alert("You don't have permission to add administrators.");
                    return;
                }
                
                // Get form data from flashdata
                const formData = <?= json_encode(session()->getFlashdata('formData') ?: []) ?>;
                const errors = <?= json_encode(session()->getFlashdata('errors') ?: []) ?>;
                
                // Clear previous error messages
                clearValidationErrors();
                
                // Populate form with previous input
                if (formData) {
                    for (const key in formData) {
                        if (key !== 'password' && key !== 'confirmPassword') {
                            $('#' + key).val(formData[key] || '');
                        }
                    }
                }
                
                // Display validation errors
                if (errors) {
                    for (const field in errors) {
                        const inputField = $('#' + field.charAt(0).toLowerCase() + field.slice(1));
                        if (inputField.length) {
                            // Highlight the field
                            inputField.addClass('border-red-500');
                            
                            // Add error message
                            const errorElement = $('<p>', {
                                class: 'text-red-500 text-xs mt-1 error-message',
                                text: errors[field]
                            });
                            inputField.parent().append(errorElement);
                        }
                    }
                }
                
                // Show the modal
                showAddModal();
            }
            
            // Check if we need to show Edit Modal
            if (<?= json_encode(session()->getFlashdata('showEditModal') ?: false) ?>) {
                // Only Super Admin and Admin can edit
                if (currentAdminRole > 2) {
                    alert("You don't have permission to edit administrators.");
                    return;
                }
                
                const formData = <?= json_encode(session()->getFlashdata('formData') ?: []) ?>;
                const errors = <?= json_encode(session()->getFlashdata('errors') ?: []) ?>;
                const adminId = <?= json_encode(session()->getFlashdata('adminId') ?: '') ?>;
                
                // Clear previous error messages
                clearValidationErrors();
                
                if (adminId) {
                    // Fetch administrator data
                    fetchAdministratorData(adminId, function(data) {
                        // Populate form with fetched data first
                        populateAdministratorForm(data);
                        
                        // Then override with submitted form data if available
                        if (formData) {
                            for (const key in formData) {
                                if (key !== 'password' && key !== 'confirmPassword') {
                                    $('#' + key.charAt(0).toLowerCase() + key.slice(1)).val(formData[key] || '');
                                }
                            }
                        }
                        
                        // Set form action for editing
                        document.getElementById('administratorForm').action = "<?= base_url('administrator/update/') ?>" + adminId;
                        document.getElementById('adminId').value = adminId;
                        
                        // Password not required for updates
                        document.getElementById('password').required = false;
                        document.getElementById('confirmPassword').required = false;
                        document.getElementById('passwordNote').classList.remove('hidden');
                        
                        // Update modal title and button
                        document.getElementById('modalTitle').textContent = "Edit Administrator";
                        document.getElementById('submitButton').textContent = "Update Administrator";
                        
                        // Display validation errors
                        if (errors) {
                            for (const field in errors) {
                                const inputField = $('#' + field.charAt(0).toLowerCase() + field.slice(1));
                                if (inputField.length) {
                                    // Highlight the field
                                    inputField.addClass('border-red-500');
                                    
                                    // Add error message
                                    const errorElement = $('<p>', {
                                        class: 'text-red-500 text-xs mt-1 error-message',
                                        text: errors[field]
                                    });
                                    inputField.parent().append(errorElement);
                                }
                            }
                        }
                        
                        // Show the modal
                        document.getElementById('administratorModal').classList.remove("hidden");
                    });
                }
            }
        }
        
        // Call to display validation errors when page loads
        displayValidationErrors();

        if (<?= json_encode(session()->getFlashdata('showEditModal') ?: false) ?>) {
            document.querySelector('.password-fields-container').classList.add('hidden');
        }
    });


    // Function to show Add Administrator modal
    function showAddModal() {
        // Check permission - only Super Admin can add
        if (currentAdminRole !== 1) {
            alert("You don't have permission to add administrators.");
            return;
        }
        
        // Reset form
        document.getElementById('administratorForm').reset();
        
        // Update form action for adding
        document.getElementById('administratorForm').action = "<?= base_url('administrator/add') ?>";
        
        // Show password fields for new administrators
        document.querySelector('.password-fields-container').classList.remove('hidden');
        document.getElementById('password').required = true;
        document.getElementById('confirmPassword').required = true;
        document.getElementById('passwordNote').classList.add('hidden');
        
        // Update modal title and button
        document.getElementById('modalTitle').textContent = "Add New Administrator";
        document.getElementById('submitButton').textContent = "Add Administrator";
        
        // Enable all form fields
        enableFormFields(true);
        
        // Show modal
        document.getElementById('administratorModal').classList.remove("hidden");
    }

    // Function to edit administrator
    function editAdministrator(adminId) {
        // Check permission - only Super Admin and Admin can edit
        if (currentAdminRole > 2) {
            alert("You don't have permission to edit administrators.");
            return;
        }
        
        // Fetch administrator data and populate form
        fetchAdministratorData(adminId, function(data) {
            populateAdministratorForm(data);
            
            // Set form action for editing
            document.getElementById('administratorForm').action = "<?= base_url('administrator/update/') ?>" + adminId;
            document.getElementById('adminId').value = adminId;
            
            // Hide password fields when editing
            document.querySelector('.password-fields-container').classList.add('hidden');
            document.getElementById('password').required = false;
            document.getElementById('confirmPassword').required = false;
            
            // Update modal title and button
            document.getElementById('modalTitle').textContent = "Edit Administrator";
            document.getElementById('submitButton').textContent = "Update Administrator";
            document.getElementById('submitButton').classList.remove("hidden");
            
            // Enable all form fields
            enableFormFields(true);
            
            // Show modal
            document.getElementById('administratorModal').classList.remove("hidden");
        });
    }

    // Function to view administrator - available to all roles
    function viewAdministrator(adminId) {
        // Fetch administrator data
        fetchAdministratorData(adminId, function(data) {
            // Populate view modal
            document.getElementById('viewAdministratorID').textContent = data.AdminID || '';
            document.getElementById('viewAdministratorName').textContent = data.AdminName || '';
            document.getElementById('viewEmail').textContent = data.Email || '';
            document.getElementById('viewPhone').textContent = data.PhoneNumber || '';
            document.getElementById('viewUsername').textContent = data.Username || '';
            
            // Format birthdate for display
            const birthdate = data.Birthdate ? formatDate(data.Birthdate, true) : 'Not specified';
            document.getElementById('viewBirthdate').textContent = birthdate;
            
            // Format sex/gender for display with mapping from numeric to text
            let gender = 'Not specified';
            if (data.Sex !== null && data.Sex !== '') {
                gender = data.Sex == '0' ? 'Male' : 'Female';
            }
            document.getElementById('viewSex').textContent = gender;
                        
            // Map role ID to readable name
            const roles = {
                '1': 'Super Admin',
                '2': 'Admin',
                '3': 'Moderator'
            };
            document.getElementById('viewRole').textContent = roles[data.Role] || data.Role || '';
            
            // Format date for display
            document.getElementById('viewCreatedDate').textContent = formatDate(data.CreatedAt) || 'N/A';
            
            // Show the view modal
            document.getElementById('viewAdministratorModal').classList.remove('hidden');
        });
    }

    function enableFormFields(enable) {
        const formFields = document.getElementById('administratorForm').querySelectorAll('input, select');
        formFields.forEach(field => {
            field.disabled = !enable;
        });
        
        // Show/hide submit button
        document.getElementById('submitButton').classList.toggle("hidden", !enable);
        
        // For view mode, change "Cancel" button text to "Close"
        document.getElementById('cancelButtonText').textContent = enable ? "Cancel" : "Close";
    }

    function fetchAdministratorData(adminId, callback) {
        $.ajax({
            url: "<?= base_url('administrator/get/') ?>" + adminId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                callback(data);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching administrator data:', error);
                alert('Failed to load administrator data. Please try again.');
            }
        });
    }

    function populateAdministratorForm(data) {
        document.getElementById('adminName').value = data.AdminName || '';
        document.getElementById('email').value = data.Email || '';
        document.getElementById('phone').value = data.PhoneNumber || '';
        document.getElementById('username').value = data.Username || '';
        document.getElementById('password').value = ''; // Don't populate password for security
        document.getElementById('confirmPassword').value = ''; // Clear confirm password field too
        document.getElementById('role').value = data.Role || '';
        document.getElementById('birthdate').value = data.Birthdate || ''; // Set birthdate
        document.getElementById('sex').value = data.Sex || ''; // Set sex/gender
    }

    function formatDate(dateString, dateOnly = false) {
        if (!dateString) return '';
        
        // Convert date format from DB format to readable format
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString; // Return as is if invalid
        
        if (dateOnly) {
            // Format date only: Month DD, YYYY
            return date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        } else {
            // Format date: Month DD, YYYY HH:MM AM/PM
            return date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add("hidden");
        if (modalId === 'administratorModal') {
            document.getElementById('submitButton').classList.remove("hidden"); // Ensure button is visible for next time
        }
    }

    function confirmDeleteAdministrator(adminId) {
        // Check permission - only Super Admin can delete
        if (currentAdminRole !== 1) {
            alert("You don't have permission to delete administrators.");
            return;
        }
        
        if(confirm('Are you sure you want to delete this administrator?')) {
            window.location.href = "<?= base_url('administrator/delete/') ?>" + adminId;
        }
    }
</script>