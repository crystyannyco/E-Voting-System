<main class="p-6">     
    <!-- Position List Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Position List</h3>
            <div class="flex space-x-2">
                <button id="addPositionBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
                    <span class="material-icons pr-2">add</span>
                    Add Position
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
            <table id="positionTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Position Name</th>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($positions as $position): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $position['PositionID'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $position['PositionName'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                            <div class="inline-flex rounded-md border border-gray-200 overflow-hidden">
                                <!-- Edit Button -->
                                <button class="px-2 py-1 text-blue-600 hover:text-blue-900 hover:bg-gray-50 border-r border-gray-200 flex items-center editPositionBtn" data-id="<?= $position['PositionID'] ?>" data-name="<?= $position['PositionName'] ?>">
                                    <span class="material-icons">edit_square</span>
                                </button>

                                <!-- Delete Button -->
                                <button 
                                    class="px-2 py-1 text-red-600 hover:text-red-900 hover:bg-gray-50 flex items-center deletePositionBtn"
                                    data-id="<?= $position['PositionID'] ?>" 
                                    data-url="<?= base_url('position/delete/' . $position['PositionID']) ?>">
                                    <span class="material-icons">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Position Modal -->
    <div id="addPositionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add New Position</h3>
                <button onclick="closeModal('modalId')" class="text-gray-500 hover:text-gray-700 modal-close-btn">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <form id="addPositionForm" action="<?= base_url('position/add') ?>" method="post">
                <div class="mb-4">
                    <label for="positionName" class="block text-sm font-medium text-gray-700 mb-1">Position Name</label>
                    <input type="text" id="positionName" name="positionName" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeModal('modalId')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md mr-2 modal-close-btn">Cancel</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Add Position</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Position Modal -->
    <div id="editPositionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Edit Position</h3>
                <button onclick="closeModal('modalId')" class="text-gray-500 hover:text-gray-700 modal-close-btn">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <form id="editPositionForm" action="" method="post">
                <input type="hidden" id="editPositionId" name="positionId" value="">
                <div class="mb-4">
                    <label for="editPositionName" class="block text-sm font-medium text-gray-700 mb-1">Position Name</label>
                    <input type="text" id="editPositionName" name="positionName" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeModal('modalId')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md mr-2 modal-close-btn">Cancel</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Update Position</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    $(document).ready(function () {
        const addPositionModal = $("#addPositionModal");
        const editPositionModal = $("#editPositionModal");
        const addPositionForm = $("#addPositionForm");
        const editPositionForm = $("#editPositionForm");
        const baseUrl = "<?= base_url() ?>";

        // Initialize DataTable
        $('#positionTable').DataTable({
            scrollY: '350px',
            scrollX: true,
            scrollCollapse: true,
            initComplete: function () {
                $('.dataTables_length select, .dataTables_filter input').addClass('border border-gray-300 rounded px-3 py-1');
                $('.dataTables_paginate .paginate_button').addClass('px-3 py-1 border border-gray-300 rounded mx-1');
                $('.dataTables_paginate .paginate_button.current').addClass('bg-indigo-600 text-white border-indigo-600');
            }
        });

        // Open modal for adding new position
        $("#addPositionBtn").on("click", function () {
            addPositionForm[0].reset();
            addPositionModal.removeClass("hidden");
        });

        // Open modal for editing a position
        $(document).on("click", ".editPositionBtn", function () {
            const id = $(this).data("id");
            const name = $(this).data("name");

            $("#editPositionName").val(name);
            editPositionForm.attr("action", baseUrl + "/position/update/" + id);
            editPositionModal.removeClass("hidden");
        });

        // Confirm before deleting
        $(document).on("click", ".deletePositionBtn", function (e) {
            e.preventDefault();
            const deleteUrl = $(this).data("url");

            if (confirm("Are you sure you want to delete this position?")) {
                window.location.href = deleteUrl;
            }
        });

        // Add global function for closing modals
        window.closeModal = function(modalId) {
            if (modalId === 'addPositionModal' || modalId === 'modalId') {
                addPositionModal.addClass("hidden");
            } else if (modalId === 'editPositionModal') {
                editPositionModal.addClass("hidden");
            }
        };

        // Set up modal close buttons
        $(".modal-close-btn").on("click", function() {
            const modal = $(this).closest(".fixed");
            modal.addClass("hidden");
        });
        
        // Show modals if validation error occurred
        <?php if (session()->getFlashdata('showAddModal')): ?>
            addPositionModal.removeClass("hidden");
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('showEditModal')): ?>
            const editId = "<?= session()->getFlashdata('positionId') ?>";
            editPositionForm.attr("action", baseUrl + "/position/update/" + editId);
            editPositionModal.removeClass("hidden");
        <?php endif; ?>
    });

    // Function to display validation errors for Position modal
    function displayPositionValidationErrors() {
        // Check if we need to show Add Modal
        if (<?= json_encode(session()->getFlashdata('showAddModal') ?: false) ?>) {
            // Get form data from flashdata
            const formData = <?= json_encode(session()->getFlashdata('formData') ?: []) ?>;
            const errors = <?= json_encode(session()->getFlashdata('errors') ?: []) ?>;
            
            // Clear previous error messages
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
            
            // Populate form with the previous input
            if (formData) {
                document.getElementById('positionName').value = formData.PositionName || '';
            }
            
            // Display validation errors
            if (errors && errors.PositionName) {
                const inputField = document.getElementById('positionName');
                if (inputField) {
                    // Highlight the field
                    inputField.classList.add('border-red-500');
                    
                    // Add error message below the field
                    const errorElement = document.createElement('p');
                    errorElement.className = 'text-red-500 text-xs mt-1 error-message';
                    errorElement.textContent = errors.PositionName;
                    inputField.parentNode.appendChild(errorElement);
                }
            }
            
            // Show the modal
            document.getElementById('addPositionModal').classList.remove("hidden");
        }
        
        // Check if we need to show Edit Modal
        if (<?= json_encode(session()->getFlashdata('showEditModal') ?: false) ?>) {
            // Get form data and position ID from flashdata
            const formData = <?= json_encode(session()->getFlashdata('formData') ?: []) ?>;
            const errors = <?= json_encode(session()->getFlashdata('errors') ?: []) ?>;
            const positionId = <?= json_encode(session()->getFlashdata('positionId') ?: '') ?>;
            
            // Clear previous error messages
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
            
            // Populate form with the previous input
            if (formData) {
                document.getElementById('editPositionName').value = formData.PositionName || '';
            }
            
            // Set the position ID for the form action
            if (positionId) {
                document.getElementById('editPositionForm').action = "<?= base_url('position/update/') ?>" + positionId;
                document.getElementById('editPositionId').value = positionId;
            }
            
            // Display validation errors
            if (errors && errors.PositionName) {
                const inputField = document.getElementById('editPositionName');
                if (inputField) {
                    // Highlight the field
                    inputField.classList.add('border-red-500');
                    
                    // Add error message below the field
                    const errorElement = document.createElement('p');
                    errorElement.className = 'text-red-500 text-xs mt-1 error-message';
                    errorElement.textContent = errors.PositionName;
                    inputField.parentNode.appendChild(errorElement);
                }
            }
            
            // Show the modal
            document.getElementById('editPositionModal').classList.remove("hidden");
        }
    }

    // Set up input event listeners to clear validation errors when user corrects input
    function setupPositionValidationListeners() {
        // For add position form
        const addPositionInput = document.getElementById('positionName');
        if (addPositionInput) {
            addPositionInput.addEventListener('input', function() {
                this.classList.remove('border-red-500');
                
                // Remove error message if exists
                const errorMessage = this.parentNode.querySelector('.error-message');
                if (errorMessage) {
                    errorMessage.remove();
                }
            });
        }
        
        // For edit position form
        const editPositionInput = document.getElementById('editPositionName');
        if (editPositionInput) {
            editPositionInput.addEventListener('input', function() {
                this.classList.remove('border-red-500');
                
                // Remove error message if exists
                const errorMessage = this.parentNode.querySelector('.error-message');
                if (errorMessage) {
                    errorMessage.remove();
                }
            });
        }
    }

    // Call these functions when document is ready
    $(document).ready(function() {
        displayPositionValidationErrors();
        setupPositionValidationListeners();
    });
</script>