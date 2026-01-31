<main class="p-6">     
    <!-- Partylist List Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Partylist List</h3>
            <div class="flex space-x-2">
                <button id="addPartylistBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
                    <span class="material-icons pr-2">add</span>
                    Add Partylist
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
            <table id="partylistTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-leftfont-medium text-gray-500 uppercase tracking-wider">Partylist Name</th>
                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($partylists as $partylist): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $partylist['PartylistID'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?= $partylist['Name'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                            <div class="inline-flex rounded-md border border-gray-200 overflow-hidden">
                                <!-- Edit Button -->
                                <button class="px-2 py-1 text-blue-600 hover:text-blue-900 hover:bg-gray-50 border-r border-gray-200 flex items-center editPartylistBtn" data-id="<?= $partylist['PartylistID'] ?>" data-name="<?= $partylist['Name'] ?>">
                                    <span class="material-icons">edit_square</span>
                                </button>

                                <!-- Delete Button -->
                                <button 
                                    class="px-2 py-1 text-red-600 hover:text-red-900 hover:bg-gray-50 flex items-center deleteElectionBtn"
                                    data-id="<?= $partylist['PartylistID'] ?>" 
                                    data-url="<?= base_url('partylist/delete/' . $partylist['PartylistID']) ?>">
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

    <!-- Add Partylist Modal -->
    <div id="addPartylistModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add New Partylist</h3>
                <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <form id="addPartylistForm" action="<?= base_url('partylist/add') ?>" method="post">
                <div class="mb-4">
                    <label for="partylistName" class="block text-sm font-medium text-gray-700 mb-1">Partylist Name</label>
                    <input type="text" id="partylistName" name="partylistName" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    <!-- Error message will be inserted here by JavaScript -->
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancelAddBtn" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md mr-2">Cancel</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Add Partylist</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    $(document).ready(function () {
        const addPartylistModal = $("#addPartylistModal");
        const addPartylistForm = $("#addPartylistForm");
        const partylistNameInput = $("#partylistName");
        const submitBtn = addPartylistForm.find("button[type='submit']");
        const baseUrl = "<?= base_url() ?>";

        // Initialize DataTable
        $('#partylistTable').DataTable({
            scrollY: '350px',
            scrollX: true,
            scrollCollapse: true,
            initComplete: function () {
                $('.dataTables_length select, .dataTables_filter input').addClass('border border-gray-300 rounded px-3 py-1');
                $('.dataTables_paginate .paginate_button').addClass('px-3 py-1 border border-gray-300 rounded mx-1');
                $('.dataTables_paginate .paginate_button.current').addClass('bg-indigo-600 text-white border-indigo-600');
            }
        });

        // Open modal for adding new partylist
        $("#addPartylistBtn").on("click", function () {
            addPartylistForm[0].reset();
            clearValidationErrors();
            submitBtn.text("Add Partylist");
            addPartylistForm.attr("action", baseUrl + "/partylist/add");
            addPartylistModal.removeClass("hidden");
        });

        // Open modal for editing a partylist
        $(document).on("click", ".editPartylistBtn", function () {
            const id = $(this).data("id");
            const name = $(this).data("name");

            clearValidationErrors();
            partylistNameInput.val(name);
            submitBtn.text("Update Partylist");
            addPartylistForm.attr("action", baseUrl + "/partylist/update/" + id);
            addPartylistModal.removeClass("hidden");
        });

        // Add confirmation before submitting edits
        addPartylistForm.on("submit", function (e) {
            if (submitBtn.text().includes("Update")) {
                const confirmEdit = confirm("Are you sure you want to update this partylist?");
                if (!confirmEdit) {
                    e.preventDefault();
                }
            }
        });

        // Confirm before deleting
        $(document).on("click", ".deleteElectionBtn", function (e) {
            e.preventDefault();
            const deleteUrl = $(this).data("url");

            if (confirm("Are you sure you want to delete this partylist?")) {
                window.location.href = deleteUrl;
            }
        });

        // Close modal
        $("#closeModalBtn, #cancelAddBtn").on("click", function () {
            addPartylistModal.addClass("hidden");
        });

        // Function to clear validation errors
        function clearValidationErrors() {
            $('.error-message').remove();
            $('.border-red-500').removeClass('border-red-500');
        }

        // Input event listener to clear validation errors when user corrects input
        partylistNameInput.on('input', function() {
            $(this).removeClass('border-red-500');
            $(this).parent().find('.error-message').remove();
        });

        // Display validation errors if any
        function displayPartylistValidationErrors() {
            // Check if we need to show Add/Edit Modal
            if (<?= json_encode(session()->getFlashdata('showAddModal') ?: false) ?>) {
                // Get form data from flashdata
                const formData = <?= json_encode(session()->getFlashdata('formData') ?: []) ?>;
                const errors = <?= json_encode(session()->getFlashdata('errors') ?: []) ?>;
                
                // Clear previous error messages
                clearValidationErrors();
                
                // Populate form with the previous input
                if (formData) {
                    partylistNameInput.val(formData.Name || '');
                }
                
                // Display validation errors
                if (errors && errors.Name) {
                    // Highlight the field
                    partylistNameInput.addClass('border-red-500');
                    
                    // Add error message below the field
                    const errorElement = $('<p>', {
                        class: 'text-red-500 text-xs mt-1 error-message',
                        text: errors.Name
                    });
                    partylistNameInput.parent().append(errorElement);
                }
                
                // Show the modal
                addPartylistModal.removeClass("hidden");
            }
            
            // Check if we need to show Edit Modal
            if (<?= json_encode(session()->getFlashdata('showEditModal') ?: false) ?>) {
                // Get form data and partylist ID from flashdata
                const formData = <?= json_encode(session()->getFlashdata('formData') ?: []) ?>;
                const errors = <?= json_encode(session()->getFlashdata('errors') ?: []) ?>;
                const partylistId = <?= json_encode(session()->getFlashdata('partylistId') ?: '') ?>;
                
                // Clear previous error messages
                clearValidationErrors();
                
                // Populate form with the previous input
                if (formData) {
                    partylistNameInput.val(formData.Name || '');
                }
                
                // Set the partylist ID for the form action
                if (partylistId) {
                    addPartylistForm.attr("action", baseUrl + "/partylist/update/" + partylistId);
                }
                
                // Display validation errors
                if (errors && errors.Name) {
                    // Highlight the field
                    partylistNameInput.addClass('border-red-500');
                    
                    // Add error message below the field
                    const errorElement = $('<p>', {
                        class: 'text-red-500 text-xs mt-1 error-message',
                        text: errors.Name
                    });
                    partylistNameInput.parent().append(errorElement);
                }
                
                // Set the button text to indicate update
                submitBtn.text("Update Partylist");
                
                // Show the modal
                addPartylistModal.removeClass("hidden");
            }
        }

        // Call to display validation errors when page loads
        displayPartylistValidationErrors();
    });
</script>


