<main class="p-6">     
    <!-- Election List Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Election Title List</h3>
            <div class="flex space-x-2">
                <button id="addElectionBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
                    <span class="material-icons pr-2">person_add</span>
                    Add Election Title
                </button>
            </div>
        </div>
        
        <!-- DataTable Container -->
        <div class="p-4 overflow-x-auto">
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
            <table id="electionTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Election Title</th>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Election Start</th>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Election End</th>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($elections as $election): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $election['ElectionID'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $election['ElectionName'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                            <?= date('F j, Y \a\t g:i A', strtotime($election['Start'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                            <?= date('F j, Y \a\t g:i A', strtotime($election['End'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                            <?php
                            $departments = [
                                '0' => 'All Departments',
                                '1' => 'CSS',
                                '2' => 'CEA',
                                '3' => 'CHS',
                                '4' => 'CTHBM',
                                '5' => 'CTDE',
                                '6' => 'CAS'
                            ];
                            echo $departments[$election['Department']] ?? 'Unknown';
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                            <div class="flex justify-center">
                                <div class="inline-flex rounded-md border border-gray-200 overflow-hidden">
                                    <button class="px-2 py-1 text-yellow-600 hover:text-yellow-900 hover:bg-gray-50 border-r border-gray-200 flex items-center viewElectionBtn" data-id="<?= $election['ElectionID'] ?>">
                                            <span class="material-icons">visibility</span>
                                        </button>
                                    <button class="px-2 py-1 text-blue-600 hover:text-blue-900 hover:bg-gray-50 border-r border-gray-200 flex items-center editElectionBtn" data-id="<?= $election['ElectionID'] ?>">
                                        <span class="material-icons">edit_square</span>
                                    </button>
                                    <button 
                                        class="px-2 py-1 text-red-600 hover:text-red-900 hover:bg-gray-50 flex items-center deleteElectionBtn"
                                        data-id="<?= $election['ElectionID'] ?>" data-url="<?= base_url('election/delete/' . $election['ElectionID']) ?>">
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

    <!-- Add Election Modal -->
    <div id="addElectionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-xl w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add New Election</h3>
                <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addElectionForm" action="<?= base_url(isset($electionInfo) ? '/election/update' : '/election/add') ?>" method="post">
                <div class="mb-4">
                    <label for="titleName" class="block text-sm font-medium text-gray-700 mb-1">Election Name</label>
                    <input type="text" id="titleName" name="titleName" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    <!-- Error message will be inserted here by JavaScript -->
                </div>
                <div class="mb-4 flex flex-col md:flex-row md:space-x-4">
                    <div class="md:w-1/2 mb-4 md:mb-0">
                        <label for="startDateTime" class="block text-sm font-medium text-gray-700 mb-1">Starting Period</label>
                        <input type="datetime-local" id="startDateTime" name="startDateTime" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                        <!-- Error message will be inserted here by JavaScript -->
                    </div>
                    <div class="md:w-1/2">
                        <label for="endDateTime" class="block text-sm font-medium text-gray-700 mb-1">End Period</label>
                        <input type="datetime-local" id="endDateTime" name="endDateTime" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                        <!-- Error message will be inserted here by JavaScript -->
                    </div>
                </div>
                <div class="mb-4">
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select id="department" name="department" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                        <option value="">Select Department</option>
                        <option value="0">All Departments</option>
                        <option value="1">CSS</option>
                        <option value="2">CEA</option>
                        <option value="3">CHS</option>
                        <option value="4">CTHBM</option>
                        <option value="5">CTDE</option>
                        <option value="6">CAS</option>
                    </select>
                    <!-- Error message will be inserted here by JavaScript -->
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancelAddBtn" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md mr-2">Cancel</button>
                    <?php if (isset($electionInfo)): ?>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Edit election</button>
                    <?php else: ?>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Add election</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    
</main>

<script>
    $(document).ready(function () {
        // Define baseUrl
        const baseUrl = window.location.origin;

        // Initialize DataTable
        $('#electionTable').DataTable({
            scrollY: '350px',
            scrollX: true,
            scrollCollapse: true,
        });

        // Modal elements
        const modal = $('#addElectionModal');
        const form = $('#addElectionForm');
        const modalTitle = modal.find('h3');
        const submitBtn = form.find("button[type='submit']");
        const cancelBtn = $('#cancelAddBtn');

        // Show modal for add
        $('#addElectionBtn').on('click', function () {
            form.trigger('reset');
            form.attr('action', baseUrl + '/election/add');
            modalTitle.text('Add New Election');
            submitBtn.text('Add Election').show();
            enableFields(true);
            modal.show().css('display', 'flex');
        });

        // Edit button click
        $('.editElectionBtn').on('click', function () {
            const id = $(this).data('id');

            $.get(baseUrl + '/election/get/' + id, function (res) {
                if (res.success) {
                    fillForm(res.data);
                    if (!$('#electionId').length) {
                        form.append(`<input type="hidden" id="electionId" name="electionId" value="${id}">`);
                    } else {
                        $('#electionId').val(id);
                    }
                    form.attr('action', baseUrl + '/election/update/' + id);
                    modalTitle.text('Edit Election');
                    submitBtn.text('Update Election').show();
                    enableFields(true);
                    modal.show().css('display', 'flex');
                } else {
                    alert('Could not fetch data.');
                }
            });
        });

        // View button click
        $('.viewElectionBtn').on('click', function () {
            const id = $(this).data('id');

            $.get(baseUrl + '/election/get/' + id, function (res) {
                if (res.success) {
                    fillForm(res.data);
                    modalTitle.text('View Election');
                    submitBtn.hide();
                    enableFields(false);
                    modal.show().css('display', 'flex');
                } else {
                    alert('Could not fetch data.');
                }
            });
        });

        // Close modal
        $('#closeModalBtn, #cancelAddBtn').on('click', function () {
            modal.hide();
            submitBtn.show();
        });

        // Confirm before submitting update
        form.on('submit', function (e) {
            const actionUrl = form.attr('action');
            if (actionUrl.includes('/update/')) {
                const confirmed = confirm("Are you sure you want to update this election?");
                if (!confirmed) {
                    e.preventDefault();
                }
            }
        });

        // Delete confirmation
        $('.deleteElectionBtn').on('click', function (e) {
            e.preventDefault();
            const url = $(this).data('url');
            if (confirm("Are you sure you want to delete this election?")) {
                window.location.href = url;
            }
        });

        // Enable/disable form fields
        function enableFields(enable) {
            form.find('input, select').prop('disabled', !enable);
            cancelBtn.text(enable ? 'Cancel' : 'Close');
        }

        // Fill form with data
        function fillForm(data) {
            $('#titleName').val(data.ElectionName);
            $('#startDateTime').val(formatDate(data.Start));
            $('#endDateTime').val(formatDate(data.End));
            $('#department').val(data.Department);
        }

        // Format datetime for input
        function formatDate(dateStr) {
            const d = new Date(dateStr);
            if (isNaN(d)) return '';
            const pad = (n) => n.toString().padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
        }
        // Add this to your existing $(document).ready(function() { ... }); block
        function clearValidationErrors() {
            $('.error-message').remove();
            $('.border-red-500').removeClass('border-red-500');
        }

        // Input event listeners to clear validation errors when user corrects input
        $('#titleName, #startDateTime, #endDateTime, #department').on('input change', function() {
            $(this).removeClass('border-red-500');
            $(this).parent().find('.error-message').remove();
        });

        // Function to display validation errors
        function displayElectionValidationErrors() {
            // Check if we need to show Add Election Modal
            if (<?= json_encode(session()->getFlashdata('showAddElectionModal') ?: false) ?>) {
                // Get form data from flashdata
                const formData = <?= json_encode(session()->getFlashdata('formData') ?: []) ?>;
                const errors = <?= json_encode(session()->getFlashdata('errors') ?: []) ?>;
                
                // Clear previous error messages
                clearValidationErrors();
                
                // Populate form with the previous input
                if (formData) {
                    $('#titleName').val(formData.ElectionName || '');
                    $('#startDateTime').val(formData.Start || '');
                    $('#endDateTime').val(formData.End || '');
                    $('#department').val(formData.Department || '');
                }
                
                // Display validation errors
                if (errors) {
                    Object.keys(errors).forEach(function(fieldName) {
                        const fieldMap = {
                            'ElectionName': '#titleName',
                            'Start': '#startDateTime',
                            'End': '#endDateTime',
                            'Department': '#department'
                        };
                        
                        const field = $(fieldMap[fieldName]);
                        if (field.length) {
                            // Highlight the field
                            field.addClass('border-red-500');
                            
                            // Add error message below the field
                            const errorElement = $('<p>', {
                                class: 'text-red-500 text-xs mt-1 error-message',
                                text: errors[fieldName]
                            });
                            field.parent().append(errorElement);
                        }
                    });
                }
                
                // Show the modal
                modal.show().css('display', 'flex');
            }
            
            // Check if we need to show Edit Election Modal
            if (<?= json_encode(session()->getFlashdata('showEditElectionModal') ?: false) ?>) {
                // Get form data and election ID from flashdata
                const formData = <?= json_encode(session()->getFlashdata('formData') ?: []) ?>;
                const errors = <?= json_encode(session()->getFlashdata('errors') ?: []) ?>;
                const electionId = <?= json_encode(session()->getFlashdata('electionId') ?: '') ?>;
                
                // Clear previous error messages
                clearValidationErrors();
                
                // Populate form with the previous input
                if (formData) {
                    $('#titleName').val(formData.ElectionName || '');
                    $('#startDateTime').val(formData.Start || '');
                    $('#endDateTime').val(formData.End || '');
                    $('#department').val(formData.Department || '');
                }
                
                // Set the election ID for the form action
                if (electionId) {
                    if (!$('#electionId').length) {
                        form.append(`<input type="hidden" id="electionId" name="electionId" value="${electionId}">`);
                    } else {
                        $('#electionId').val(electionId);
                    }
                    form.attr('action', baseUrl + '/election/update/' + electionId);
                }
                
                // Display validation errors
                if (errors) {
                    Object.keys(errors).forEach(function(fieldName) {
                        const fieldMap = {
                            'ElectionName': '#titleName',
                            'Start': '#startDateTime',
                            'End': '#endDateTime',
                            'Department': '#department'
                        };
                        
                        const field = $(fieldMap[fieldName]);
                        if (field.length) {
                            // Highlight the field
                            field.addClass('border-red-500');
                            
                            // Add error message below the field
                            const errorElement = $('<p>', {
                                class: 'text-red-500 text-xs mt-1 error-message',
                                text: errors[fieldName]
                            });
                            field.parent().append(errorElement);
                        }
                    });
                }
                
                // Set the button text to indicate update
                submitBtn.text('Update Election');
                modalTitle.text('Edit Election');
                
                // Show the modal
                modal.show().css('display', 'flex');
            }
        }

        // Call to display validation errors when page loads
        displayElectionValidationErrors();
    });
</script>

