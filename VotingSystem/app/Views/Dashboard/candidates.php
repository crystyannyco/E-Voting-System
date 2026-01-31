<main class="p-6">     
    <!-- Candidate List Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Candidate List</h3>
            <div class="flex space-x-2">
                <button id="addCandidateBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
                    <span class="material-icons pr-2">person_add</span>
                    Add Candidate
                </button>
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
            <table id="candidateTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Candidate Name</th>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Partylist</th>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Election</th>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($candidates as $candidate): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $candidate['CandidateID'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $candidate['FirstName'] . ' ' . $candidate['MiddleName'] . ' ' . $candidate['LastName'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $candidate['PositionName'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $candidate['PartylistName'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $candidate['ElectionName'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex justify-center">
                                <div class="inline-flex rounded-md border border-gray-200 overflow-hidden">
                                    <button class="px-2 py-1 text-yellow-600 hover:text-yellow-900 hover:bg-gray-50 border-r border-gray-200 flex items-center" 
                                           onclick="viewCandidate(<?= $candidate['CandidateID'] ?>)">
                                        <span class="material-icons">visibility</span>
                                    </button>
                                    <button class="px-2 py-1 text-blue-600 hover:text-blue-900 hover:bg-gray-50 border-r border-gray-200 flex items-center"
                                           onclick="editCandidate(<?= $candidate['CandidateID'] ?>)">
                                        <span class="material-icons">edit_square</span>
                                    </button>
                                    <button class="px-2 py-1 text-red-600 hover:text-red-900 hover:bg-gray-50 flex items-center"
                                           onclick="confirmDeleteCandidate(<?= $candidate['CandidateID'] ?>)">
                                        <span class="material-icons">delete</span>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- View Candidate Modal -->
    <div id="viewCandidateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Candidate Details</h3>
                <button onclick="closeModal('viewCandidateModal')" class="text-gray-500 hover:text-gray-700">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Profile Picture Section -->
                <div class="md:w-1/3 w-full mb-4 md:mb-0">
                    <div class="w-full h-full rounded-md overflow-hidden bg-gray-100 border">
                        <img id="viewProfilePic" src="" alt="Candidate Profile" class="h-full w-full object-cover">
                    </div>
                </div>
                
                <!-- Details Section -->
                <div class="md:w-2/3">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex">
                            <p class="text-sm text-gray-500 w-24">ID:</p>
                            <p id="viewCandidateID" class="font-medium"></p>
                        </div>
                        
                        <div class="flex">
                            <p class="text-sm text-gray-500 w-24">Full Name:</p>
                            <p id="viewCandidateName" class="font-medium"></p>
                        </div>
                        
                        <div class="flex">
                            <p class="text-sm text-gray-500 w-24">Position:</p>
                            <p id="viewPosition" class="font-medium"></p>
                        </div>
                        
                        <div class="flex">
                            <p class="text-sm text-gray-500 w-24">Partylist:</p>
                            <p id="viewPartylist" class="font-medium"></p>
                        </div>
                        
                        <div class="flex">
                            <p class="text-sm text-gray-500 w-24">Election:</p>
                            <p id="viewElection" class="font-medium"></p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Platform:</p>
                            <div id="viewPlatform" class="bg-gray-50 p-3 rounded-md mt-1 max-h-32 overflow-y-auto"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end mt-4">
                <button onclick="closeModal('viewCandidateModal')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md">Close</button>
            </div>
        </div>
    </div>

    <!-- Add/Edit Candidate Modal -->
    <div id="candidateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add New Candidate</h3>
                <button onclick="closeModal('candidateModal')" class="text-gray-500 hover:text-gray-700">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <form id="candidateForm" action="<?= base_url('candidate/add') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" id="candidateID" name="candidateID" value="">
                
                <!-- Name fields in one row -->
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="mb-4 md:w-1/3">
                        <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">Firstname</label>
                        <input type="text" id="firstName" name="firstName" placeholder="Firstname" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    <div class="mb-4 md:w-1/3">
                        <label for="middleName" class="block text-sm font-medium text-gray-700 mb-1">Middlename</label>
                        <input type="text" id="middleName" name="middleName" placeholder="Middlename" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    <div class="mb-4 md:w-1/3">
                        <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Lastname</label>
                        <input type="text" id="lastName" name="lastName" placeholder="Lastname" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required> 
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <!-- Left side (form fields) -->
                    <div class="md:w-2/3">
                        <div class="flex flex-col md:flex-row md:space-x-4">
                            <div class="mb-4 md:w-1/2">
                                <label for="partylistName" class="block text-sm font-medium text-gray-700 mb-1">Partylist</label>
                                <select id="partylistName" name="partylistName" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                                    <option value="">Select partylist</option>
                                    <?php foreach ($partylists as $partylist): ?>
                                    <option value="<?= $partylist['PartylistID'] ?>"><?= $partylist['Name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Profile upload -->
                            <div class="mb-4 md:w-1/2">
                                <label for="profileUpload" class="block text-sm font-medium text-gray-700 mb-1">Profile</label>
                                <input type="file" id="profileUpload" name="profileUpload" class="w-full border border-gray-300 rounded-md shadow-sm p-2 bg-white">
                            </div>
                        </div>
                        <!-- Selection fields -->
                        <div class="flex flex-col md:flex-row md:space-x-4">
                            <div class="mb-4 md:w-1/2">
                                <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                                <select id="position" name="position" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                                    <option value="">Select position</option>
                                    <?php foreach ($positions as $position): ?>
                                    <option value="<?= $position['PositionID'] ?>"><?= $position['PositionName'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4 md:w-1/2">
                                <label for="electionTitle" class="block text-sm font-medium text-gray-700 mb-1">Election</label>
                                <select id="electionTitle" name="electionTitle" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                                    <option value="">Select election</option>
                                    <?php foreach ($elections as $election): ?>
                                    <option value="<?= $election['ElectionID'] ?>"><?= $election['ElectionName'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Platform text area -->
                        <div class="mb-4">
                            <label for="platform" class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
                            <textarea id="platform" name="platform" rows="6" class="w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="Platform"></textarea>
                        </div>
                    </div>
                    
                    <!-- Right side (profile preview) -->
                    <div class="md:w-1/3">
                        <div id="profileDisplayArea" class="bg-gray-200 rounded-md w-full h-96 flex items-center justify-center overflow-hidden">
                            <div id="emptyProfileState" class="text-gray-400 text-center p-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <p class="mt-2">Profile image preview</p>
                            </div>
                            <img id="candidateProfilePreview" class="w-full h-full object-cover hidden" src="" alt="Profile Preview">
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="button" onclick="closeModal('candidateModal')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md mr-2">
                        <span id="cancelButtonText">Cancel</span>
                    </button>
                    <button type="submit" id="submitButton" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Add Candidate</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    $(document).ready(function() {
        $('#candidateTable').DataTable({
            scrollY: '350px',
            scrollX: true,
            scrollCollapse: true,
            initComplete: function() {
                $('.dataTables_length select, .dataTables_filter input').addClass('border border-gray-300 rounded px-3 py-1');
                $('.dataTables_paginate .paginate_button').addClass('px-3 py-1 border border-gray-300 rounded mx-1');
                $('.dataTables_paginate .paginate_button.current').addClass('bg-indigo-600 text-white border-indigo-600');
            }
        });
        
        // Connect search box to DataTable search
        $('#table-search').on('keyup', function() {
            $('#candidateTable').DataTable().search($(this).val()).draw();
        });
        
        // Set up form submission with validation
        $('#candidateForm').on('submit', function(e) {
            if ($('#submitButton').text().includes('Update')) {
                if (!confirm('Are you sure you want to update this candidate?')) {
                    e.preventDefault();
                }
            }
        });
    });

    // Function to show Add Candidate modal
    document.getElementById('addCandidateBtn').addEventListener('click', function() {
        showAddModal();
    });

    function showAddModal() {
        // Reset form
        document.getElementById('candidateForm').reset();
        
        // Clear any profile preview
        document.getElementById('candidateProfilePreview').classList.add('hidden');
        document.getElementById('emptyProfileState').classList.remove('hidden');
        
        // Update form action for adding
        document.getElementById('candidateForm').action = "<?= base_url('candidate/add') ?>";
        
        // Update modal title and button
        document.getElementById('modalTitle').textContent = "Add New Candidate";
        document.getElementById('submitButton').textContent = "Add Candidate";
        
        // Enable all form fields
        enableFormFields(true);
        
        // Show modal
        document.getElementById('candidateModal').classList.remove("hidden");
    }

    // Function to edit candidate
    function editCandidate(candidateId) {
        // Fetch candidate data and populate form
        fetchCandidateData(candidateId, function(data) {
            populateCandidateForm(data);
            
            // Set form action for editing
            document.getElementById('candidateForm').action = "<?= base_url('candidate/update/') ?>" + candidateId;
            
            // Update modal title and button
            document.getElementById('modalTitle').textContent = "Edit Candidate";
            document.getElementById('submitButton').textContent = "Update Candidate";
            document.getElementById('submitButton').classList.remove("hidden");
            
            // Enable all form fields
            enableFormFields(true);
            
            // Show modal
            document.getElementById('candidateModal').classList.remove("hidden");
        });
    }

    // Function to view candidate details
    function viewCandidate(candidateId) {
        // Fetch candidate data and populate view modal
        fetchCandidateData(candidateId, function(data) {
            // Populate the view modal
            document.getElementById('viewCandidateID').textContent = data.CandidateID;
            document.getElementById('viewCandidateName').textContent = data.FirstName + ' ' + data.MiddleName + ' ' + data.LastName;
            
            // Find position, partylist and election names from their IDs
            let positionName = '';
            const positionSelect = document.getElementById('position');
            for (let i = 0; i < positionSelect.options.length; i++) {
                if (positionSelect.options[i].value == data.Position) {
                    positionName = positionSelect.options[i].text;
                    break;
                }
            }
            document.getElementById('viewPosition').textContent = positionName;
            
            let partylistName = '';
            const partylistSelect = document.getElementById('partylistName');
            for (let i = 0; i < partylistSelect.options.length; i++) {
                if (partylistSelect.options[i].value == data.Partylist) {
                    partylistName = partylistSelect.options[i].text;
                    break;
                }
            }
            document.getElementById('viewPartylist').textContent = partylistName;
            
            let electionName = '';
            const electionSelect = document.getElementById('electionTitle');
            for (let i = 0; i < electionSelect.options.length; i++) {
                if (electionSelect.options[i].value == data.Election) {
                    electionName = electionSelect.options[i].text;
                    break;
                }
            }
            document.getElementById('viewElection').textContent = electionName;
            
            document.getElementById('viewPlatform').textContent = data.Platform || 'No platform provided';
            
            // Handle profile image
            const profilePic = document.getElementById('viewProfilePic');
            if (data.Profile) {
                profilePic.src = "<?= base_url('uploads/profiles/') ?>" + data.Profile;
                profilePic.classList.remove('hidden');
            } else {
                profilePic.src = "<?= base_url('assets/default-profile.png') ?>";
                profilePic.classList.remove('hidden');
            }
            
            // Show view modal
            document.getElementById('viewCandidateModal').classList.remove("hidden");
        });
    }

    // Function to enable/disable form fields
    function enableFormFields(enable) {
        const formFields = document.getElementById('candidateForm').querySelectorAll('input, select, textarea');
        formFields.forEach(field => {
            field.disabled = !enable;
        });
        
        // Show/hide submit button
        document.getElementById('submitButton').classList.toggle("hidden", !enable);
        
        // For view mode, change "Cancel" button text to "Close"
        document.getElementById('cancelButtonText').textContent = enable ? "Cancel" : "Close";
    }

    // Function to fetch candidate data by ID
    function fetchCandidateData(candidateId, callback) {
        // Make a proper AJAX request to fetch candidate data
        $.ajax({
            url: "<?= base_url('candidate/get/') ?>" + candidateId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                callback(data);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching candidate data:', error);
                alert('Failed to load candidate data. Please try again.');
            }
        });
    }

    // Function to populate form with candidate data
    function populateCandidateForm(data) {
        document.getElementById('firstName').value = data.FirstName || '';
        document.getElementById('middleName').value = data.MiddleName || '';
        document.getElementById('lastName').value = data.LastName || '';
        document.getElementById('partylistName').value = data.Partylist || '';
        document.getElementById('position').value = data.Position || '';
        document.getElementById('electionTitle').value = data.Election || '';
        document.getElementById('platform').value = data.Platform || '';
        // Handle profile image preview for candidate modal only
        const profilePreview = document.getElementById('candidateProfilePreview');
        const emptyProfileState = document.getElementById('emptyProfileState');
        if (data.Profile) {
            profilePreview.src = "<?= base_url('uploads/profiles/') ?>" + data.Profile;
            profilePreview.classList.remove('hidden');
            emptyProfileState.classList.add('hidden');
        } else {
            profilePreview.classList.add('hidden');
            emptyProfileState.classList.remove('hidden');
        }
    }

    // Function to close modals
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add("hidden");
        if (modalId === 'candidateModal') {
            document.getElementById('submitButton').classList.remove("hidden"); // Ensure button is visible for next time
        }
    }

    // Function to confirm candidate deletion
    function confirmDeleteCandidate(candidateId) {
        if(confirm('Are you sure you want to delete this candidate?')) {
            window.location.href = "<?= base_url('candidate/delete/') ?>" + candidateId;
        }
    }

    // Function to handle profile image upload preview
    document.getElementById('profileUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const profilePreview = document.getElementById('candidateProfilePreview');
        const emptyProfileState = document.getElementById('emptyProfileState');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePreview.src = e.target.result;
                profilePreview.classList.remove('hidden');
                if (emptyProfileState) emptyProfileState.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            profilePreview.classList.add('hidden');
            if (emptyProfileState) emptyProfileState.classList.remove('hidden');
        }
    });

    // Add this JavaScript code at the end of your script section in the candidates view file
    function displayCandidateValidationErrors() {
        // Check if we need to show Add Modal
        if (<?= json_encode(session()->getFlashdata('showAddModal') ?: false) ?>) {
            // Get form data from flashdata
            const formData = <?= json_encode(session()->getFlashdata('formData') ?: []) ?>;
            const errors = <?= json_encode(session()->getFlashdata('errors') ?: []) ?>;
            
            // Clear previous error messages
            clearValidationErrors();
            
            // Populate form with the previous input
            if (formData) {
                document.getElementById('firstName').value = formData.FirstName || '';
                document.getElementById('middleName').value = formData.MiddleName || '';
                document.getElementById('lastName').value = formData.LastName || '';
                document.getElementById('partylistName').value = formData.Partylist || '';
                document.getElementById('position').value = formData.Position || '';
                document.getElementById('electionTitle').value = formData.Election || '';
                document.getElementById('platform').value = formData.Platform || '';
            }
            
            // Display validation errors for each field
            displayFieldError('firstName', errors.FirstName);
            displayFieldError('middleName', errors.MiddleName);
            displayFieldError('lastName', errors.LastName);
            displayFieldError('partylistName', errors.Partylist);
            displayFieldError('position', errors.Position);
            displayFieldError('electionTitle', errors.Election);
            displayFieldError('platform', errors.Platform);
            
            // Update modal title and button
            document.getElementById('modalTitle').textContent = "Add New Candidate";
            document.getElementById('submitButton').textContent = "Add Candidate";
            
            // Show modal
            document.getElementById('candidateModal').classList.remove("hidden");
        }
        
        // Check if we need to show Edit Modal
        if (<?= json_encode(session()->getFlashdata('showEditModal') ?: false) ?>) {
            // Get form data and candidate ID from flashdata
            const formData = <?= json_encode(session()->getFlashdata('formData') ?: []) ?>;
            const errors = <?= json_encode(session()->getFlashdata('errors') ?: []) ?>;
            const candidateId = <?= json_encode(session()->getFlashdata('candidateId') ?: '') ?>;
            
            // Clear previous error messages
            clearValidationErrors();
            
            // Populate form with the previous input
            if (formData) {
                document.getElementById('firstName').value = formData.FirstName || '';
                document.getElementById('middleName').value = formData.MiddleName || '';
                document.getElementById('lastName').value = formData.LastName || '';
                document.getElementById('partylistName').value = formData.Partylist || '';
                document.getElementById('position').value = formData.Position || '';
                document.getElementById('electionTitle').value = formData.Election || '';
                document.getElementById('platform').value = formData.Platform || '';
            }
            
            // Set form action for editing
            if (candidateId) {
                document.getElementById('candidateForm').action = "<?= base_url('candidate/update/') ?>" + candidateId;
            }
            
            // Display validation errors for each field
            displayFieldError('firstName', errors.FirstName);
            displayFieldError('middleName', errors.MiddleName);
            displayFieldError('lastName', errors.LastName);
            displayFieldError('partylistName', errors.Partylist);
            displayFieldError('position', errors.Position);
            displayFieldError('electionTitle', errors.Election);
            // displayFieldError('platform', errors.Platform);
            
            // Update modal title and button
            document.getElementById('modalTitle').textContent = "Edit Candidate";
            document.getElementById('submitButton').textContent = "Update Candidate";
            
            // Show modal
            document.getElementById('candidateModal').classList.remove("hidden");
        }
    }

    // Function to display validation error for a field
    function displayFieldError(fieldId, errorMessage) {
        if (errorMessage) {
            const field = document.getElementById(fieldId);
            field.classList.add('border-red-500');
            
            // Create error message element
            const errorElement = document.createElement('p');
            errorElement.className = 'text-red-500 text-xs mt-1 error-message';
            errorElement.textContent = errorMessage;
            
            // Append error message after the field
            field.parentNode.appendChild(errorElement);
            
            // Add input event listener to clear error when user types
            field.addEventListener('input', function() {
                this.classList.remove('border-red-500');
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            });
        }
    }

    // Function to clear all validation errors
    function clearValidationErrors() {
        // Remove all error messages
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        
        // Remove red border from all fields
        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
        });
    }

    // Call to display validation errors when page loads
    document.addEventListener('DOMContentLoaded', function() {
        displayCandidateValidationErrors();
    });
</script>
