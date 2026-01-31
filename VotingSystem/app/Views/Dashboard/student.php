

<main class="p-6">     
    <!-- Student List Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Student List</h3>
            <div class="flex space-x-2">
                <button 
                    onclick="document.getElementById('importStudentModal').classList.remove('hidden');" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md flex items-center">
                    <span class="material-icons pr-2">add</span>
                    Import Students
                </button>
                <button 
                    onclick="showAddModal();" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md flex items-center">
                    <span class="material-icons pr-2">person_add</span>
                    Add Student
                </button>
            </div>
        </div>
        
        <!-- Session Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-50 border border-green-200 text-green-800 p-4 mx-4 mt-2 rounded-md flex items-center">
                <span class="material-icons text-green-600 mr-2">check_circle</span>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 p-4 mb-4 mx-4 mt-2 rounded-md flex items-center">
                <span class="material-icons text-red-600 mr-2">error</span>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        
        <!-- DataTable Container -->
        <div class="p-4 overflow-x-auto">
            <table id="studentTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">M.I.</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td class="px-6 py-4"><?= $student['StudentID'] ?></td>
                            <td class="px-6 py-4"><?= $student['FirstName'] ?></td>
                            <td class="px-6 py-4"><?= !empty($student['MiddleName']) ? strtoupper($student['MiddleName'][0]) . '.' : '' ?></td>
                            <td class="px-6 py-4"><?= $student['LastName'] ?></td>
                            <td class="px-6 py-4"><?= $student['Email'] ?></td>
                            <td class="px-6 py-4">
                                <?php
                                $departments = [
                                    '1' => 'CSS',
                                    '2' => 'CEA',
                                    '3' => 'CHS',
                                    '4' => 'CTHBM',
                                    '5' => 'CTDE',
                                    '6' => 'CAS'
                                ];
                                echo isset($departments[$student['Department']]) ? $departments[$student['Department']] : 'Unknown';
                                ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $programs = [
                                    1 => [
                                        1 => "BSIT", 
                                        2 => "BSCS", 
                                        3 => "BSIS", 
                                        4 => "BLIS"
                                    ],
                                    2 => [
                                        1 => "BSEE", 
                                        2 => "BSCpE", 
                                        3 => "BSCE", 
                                        4 => "BSECE", 
                                        5 => "BSME", 
                                        6 => "BSA"
                                    ],
                                    3 => [
                                        1 => "BSN", 
                                        2 => "BSM"
                                    ],
                                    4 => [
                                        1 => "BSTM", 
                                        2 => "BSHM", 
                                        3 => "BSOA", 
                                        4 => "BSE", 
                                        5 => "BSBA-FM"
                                    ],
                                    5 => [
                                        1 => "BSEd", 
                                        2 => "BEEd", 
                                        3 => "BTVTE", 
                                        4 => "BSNE", 
                                        5 => "BPE", 
                                        6 => "BCAE"
                                    ],
                                    6 => [
                                        1 => "BAELS", 
                                        2 => "BHS", 
                                        3 => "BSDC", 
                                        4 => "BPA", 
                                        5 => "BSM", 
                                        6 => "BSAM"
                                    ]
                                ];                                
                                
                                if (isset($programs[$student['Department']]) && isset($programs[$student['Department']][$student['Course']])) {
                                    echo $programs[$student['Department']][$student['Course']];
                                } else {
                                    echo 'Unknown';
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $years = ['1' => '1st', '2' => '2nd', '3' => '3rd', '4' => '4th'];
                                echo isset($years[$student['Year']]) ? $years[$student['Year']] : 'Unknown';
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex justify-center">
                                    <div class="inline-flex rounded-md border border-gray-200 overflow-hidden">
                                        <button 
                                            class="px-2 py-1 text-yellow-600 hover:text-yellow-900 hover:bg-gray-50 border-r border-gray-200 flex items-center" 
                                            onclick="viewStudent(<?= $student['StudentID'] ?>)">
                                            <span class="material-icons">visibility</span>
                                        </button>
                                        <button 
                                            class="px-2 py-1 text-blue-600 hover:text-blue-900 hover:bg-gray-50 border-r border-gray-200 flex items-center" 
                                            onclick="editStudent(<?= $student['StudentID'] ?>)">
                                            <span class="material-icons">edit_square</span>
                                        </button>
                                        <button 
                                            class="px-2 py-1 text-red-600 hover:text-red-900 hover:bg-gray-50 flex items-center" 
                                            onclick="confirmDeleteStudent(<?= $student['StudentID'] ?>)">
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

    <!-- Add/Edit/View Student Modal -->
    <div id="studentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add New Student</h3>
                <button onclick="closeModal('studentModal')" class="text-gray-500 hover:text-gray-700">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <form id="studentForm" action="<?= base_url('student/add') ?>" method="post">
                <input type="hidden" id="studentId" name="studentId" value="">
                
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="mb-4 md:w-1/2">
                        <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" id="firstName" name="firstName" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    <div class="mb-4 md:w-1/2">
                        <label for="middleName" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                        <input type="text" id="middleName" name="middleName" class="w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                    <div class="mb-4 md:w-1/2">
                        <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" id="lastName" name="lastName" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="mb-4 md:w-1/2">
                        <label for="birthdate" class="block text-sm font-medium text-gray-700 mb-1">Birthdate</label>
                        <input type="date" id="birthdate" name="birthdate" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    <div class="mb-4 md:w-1/2">
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select id="gender" name="gender" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                            <option value="">Select Gender</option>
                            <option value="0">Female</option>
                            <option value="1">Male</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="mb-4 md:w-1/2">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    <div class="mb-4 md:w-1/2">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="mb-4 md:w-1/2">
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select id="department" name="department" class="w-full border border-gray-300 rounded-md shadow-sm p-2" onchange="updateProgramOptions(this.value)" required>
                            <option value="">Select Department</option>
                            <option value="1">College of Computer Studies</option>
                            <option value="2">College of Engineering and Architecture</option>
                            <option value="3">College of Health Sciences</option>
                            <option value="4">College of Tourism, Hospitality and Business Management</option>
                            <option value="5">College of Technological and Developmental Education</option>
                            <option value="6">College of Arts and Sciences</option>
                        </select>
                    </div>
                    <div class="mb-4 md:w-1/2">
                        <label for="course" class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                        <select id="course" name="course" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                            <option value="">Select Program</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="mb-4 md:w-1/2">
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                        <select id="year" name="year" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                            <option value="">Select Year</option>
                            <option value="1">1st</option>
                            <option value="2">2nd</option>
                            <option value="3">3rd</option>
                            <option value="4">4th</option>
                        </select>
                    </div>
                    <div class="mb-4 md:w-1/2">
                        <label for="section" class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                        <select id="section" name="section" class="w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                            <option value="">Select Section</option>
                            <option value="1">A</option>
                            <option value="2">B</option>
                            <option value="3">C</option>
                            <option value="4">D</option>
                            <option value="5">E</option>
                            <option value="6">F</option>
                            <option value="7">G</option>
                            <option value="8">H</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeModal('studentModal')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md mr-2">
                        <span id="cancelButtonText">Cancel</span>
                    </button>
                    <button type="submit" id="submitButton" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Add Student</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Students Modal -->
    <div id="importStudentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Import Students</h3>
                <button onclick="closeModal('importStudentModal')" class="text-gray-500 hover:text-gray-700">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <form id="importStudentForm" action="<?= base_url('student/import') ?>" method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload CSV File</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Upload a file</span>
                                    <input id="file-upload" name="csvFile" type="file" class="sr-only" accept=".csv">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">CSV up to 10MB</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 bg-yellow-50 p-3 rounded-md">
                    <p class="text-xs text-yellow-700">
                        <i class="fas fa-info-circle mr-1"></i> The CSV file should have columns for Student ID, Name, Email, Year & Program, and Status.
                    </p>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="button" onclick="closeModal('importStudentModal')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md mr-2">Cancel</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Import</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
// Program options mapped to department
const programsByDepartment = {
    '1': [
        {id: 1, name: "Bachelor of Science in Information Technology", acronym: "BSIT"},
        {id: 2, name: "Bachelor of Science in Computer Science", acronym: "BSCS"},
        {id: 3, name: "Bachelor of Science in Information Systems", acronym: "BSIS"},
        {id: 4, name: "Bachelor of Library Information Science", acronym: "BLIS"}
    ],
    '2': [
        {id: 1, name: "Bachelor of Science in Electrical Engineering", acronym: "BSEE"},
        {id: 2, name: "Bachelor of Science in Computer Engineering", acronym: "BSCpE"},
        {id: 3, name: "Bachelor of Science in Civil Engineering", acronym: "BSCE"},
        {id: 4, name: "Bachelor of Science in Electronics Engineering", acronym: "BSECE"},
        {id: 5, name: "Bachelor of Science in Mechanical Engineering", acronym: "BSME"},
        {id: 6, name: "Bachelor of Science in Architecture", acronym: "BSA"}
    ],
    '3': [
        {id: 1, name: "Bachelor of Science in Nursing", acronym: "BSN"},
        {id: 2, name: "Bachelor of Science in Midwifery", acronym: "BSM"}
    ],
    '4': [
        {id: 1, name: "Bachelor of Science in Tourism Management", acronym: "BSTM"},
        {id: 2, name: "Bachelor of Science in Hospitality Management", acronym: "BSHM"},
        {id: 3, name: "Bachelor of Science in Office Administration", acronym: "BSOA"},
        {id: 4, name: "Bachelor of Science in Entrepreneurship", acronym: "BSE"},
        {id: 5, name: "Bachelor of Science in Business Administration major in Financial Management", acronym: "BSBA-FM"}
    ],
    '5': [
        {id: 1, name: "Bachelor of Secondary Education", acronym: "BSEd"},
        {id: 2, name: "Bachelor of Elementary Education", acronym: "BEEd"},
        {id: 3, name: "Bachelor of Technical-Vocational Teacher Education", acronym: "BTVTE"},
        {id: 4, name: "Bachelor of Special Needs Education", acronym: "BSNE"},
        {id: 5, name: "Bachelor of Physical Education", acronym: "BPE"},
        {id: 6, name: "Bachelor of Culture and Arts Education", acronym: "BCAE"}
    ],
    '6': [
        {id: 1, name: "Bachelor of Arts in English Language Studies", acronym: "BAELS"},
        {id: 2, name: "Bachelor in Human Services", acronym: "BHS"},
        {id: 3, name: "Bachelor of Science in Development Communication", acronym: "BSDC"},
        {id: 4, name: "Bachelor of Public Administration", acronym: "BPA"},
        {id: 5, name: "Bachelor of Science in Mathematics", acronym: "BSM"},
        {id: 6, name: "Bachelor of Science in Applied Mathematics", acronym: "BSAM"}
    ]
};

// Initialize DataTable when document is ready
$(document).ready(function() {
    $('#studentTable').DataTable({
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
        $('#studentTable').DataTable().search($(this).val()).draw();
    });
    
    // Set up form submission with validation
    $('#studentForm').on('submit', function(e) {
        if ($('#submitButton').text().includes('Update')) {
            if (!confirm('Are you sure you want to update this student?')) {
                e.preventDefault();
            }
        }
    });
});

// Function to show Add Student modal
function showAddModal() {
    // Reset form
    document.getElementById('studentForm').reset();
    
    // Update form action for adding
    document.getElementById('studentForm').action = "<?= base_url('student/add') ?>";
    
    // Update modal title and button
    document.getElementById('modalTitle').textContent = "Add New Student";
    document.getElementById('submitButton').textContent = "Add Student";
    
    // Enable all form fields
    enableFormFields(true);
    
    // Show modal
    document.getElementById('studentModal').classList.remove("hidden");
}

    // Function to edit student
    function editStudent(studentId) {
        // Fetch student data and populate form
        fetchStudentData(studentId, function(data) {
            populateStudentForm(data);
            
            // Set form action for editing
            document.getElementById('studentForm').action = "<?= base_url('student/update/') ?>" + studentId;
            document.getElementById('studentId').value = studentId;
            
            // Update modal title and button
            document.getElementById('modalTitle').textContent = "Edit Student";
            document.getElementById('submitButton').textContent = "Update Student";
            document.getElementById('submitButton').classList.remove("hidden");
            
            // Enable all form fields
            enableFormFields(true);
            
            // Show modal
            document.getElementById('studentModal').classList.remove("hidden");
        });
    }

    // Function to view student details
    function viewStudent(studentId) {
        // Fetch student data and populate form
        fetchStudentData(studentId, function(data) {
            populateStudentForm(data);
            
            // Update modal title and hide submit button
            document.getElementById('modalTitle').textContent = "Student Details";
            document.getElementById('submitButton').classList.add("hidden");
            
            // Disable all form fields for view-only mode
            enableFormFields(false);
            
            // Show modal
            document.getElementById('studentModal').classList.remove("hidden");
        });
    }

    // Function to enable/disable form fields
    function enableFormFields(enable) {
        const formFields = document.getElementById('studentForm').querySelectorAll('input, select');
        formFields.forEach(field => {
            field.disabled = !enable;
        });
        
        // Show/hide submit button
        document.getElementById('submitButton').classList.toggle("hidden", !enable);
        
        // For view mode, change "Cancel" button text to "Close"
        document.getElementById('cancelButtonText').textContent = enable ? "Cancel" : "Close";
    }

// Function to fetch student data by ID
function fetchStudentData(studentId, callback) {
    // Make a proper AJAX request to fetch student data
    $.ajax({
        url: "<?= base_url('student/get/') ?>" + studentId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            callback(data);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching student data:', error);
            alert('Failed to load student data. Please try again.');
        }
    });
}

// Function to populate form with student data
function populateStudentForm(data) {
    document.getElementById('firstName').value = data.FirstName || '';
    document.getElementById('middleName').value = data.MiddleName || '';
    document.getElementById('lastName').value = data.LastName || '';
    document.getElementById('birthdate').value = formatDate(data.Birthdate) || '';
    document.getElementById('gender').value = data.Gender || '';
    document.getElementById('phone').value = data.PhoneNumber || '';
    document.getElementById('email').value = data.Email || '';
    document.getElementById('department').value = data.Department || '';
    
    // Update programs based on department
    updateProgramOptions(data.Department);
    setTimeout(() => {
        document.getElementById('course').value = data.Course || '';
    }, 100); // Small delay to ensure programs are populated
    
    document.getElementById('year').value = data.Year || '';
    document.getElementById('section').value = data.Section || '';
}

// Function to update program options based on department
function updateProgramOptions(departmentId, selectedProgram = '') {
    const programSelect = document.getElementById('course');
    programSelect.innerHTML = '<option value="">Select Program</option>';
    
    if (programsByDepartment[departmentId]) {
        programsByDepartment[departmentId].forEach(program => {
            const option = document.createElement('option');
            option.value = program.id;
            option.textContent = program.name;
            if (program.id == selectedProgram) {
                option.selected = true;
            }
            programSelect.appendChild(option);
        });
    }
}

// Format date function
function formatDate(dateString) {
    if (!dateString) return '';
    
    // Convert date format from DB format to input field format
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString; // Return as is if invalid
    
    // Format to YYYY-MM-DD for the date input
    return date.toISOString().split('T')[0];
}

// Function to close modals
function closeModal(modalId) {
    document.getElementById(modalId).classList.add("hidden");
    if (modalId === 'studentModal') {
        document.getElementById('submitButton').classList.remove("hidden"); // Ensure button is visible for next time
    }
}

// Function to confirm student deletion
function confirmDeleteStudent(studentId) {
    if(confirm('Are you sure you want to delete this student?')) {
        window.location.href = "<?= base_url('student/delete/') ?>" + studentId;
    }
}

// Add this at the end of your existing JavaScript code
// Display validation errors in the modal
function displayValidationErrors() {
    <?php if (session()->getFlashdata('showModal')): ?>
        // Get form data from flashdata
        const formData = <?= json_encode(session()->getFlashdata('formData') ?: []) ?>;
        const errors = <?= json_encode(session()->getFlashdata('errors') ?: []) ?>;
        const editingId = '<?= session()->getFlashdata('editingStudentId') ?>';
        
        // Clear previous error messages
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
        
        // Populate form with the previous input
        if (formData) {
            document.getElementById('firstName').value = formData.FirstName || '';
            document.getElementById('middleName').value = formData.MiddleName || '';
            document.getElementById('lastName').value = formData.LastName || '';
            document.getElementById('birthdate').value = formatDate(formData.Birthdate) || '';
            document.getElementById('gender').value = formData.Gender || '';
            document.getElementById('phone').value = formData.PhoneNumber || '';
            document.getElementById('email').value = formData.Email || '';
            document.getElementById('department').value = formData.Department || '';
            
            // Update course options based on department
            if (formData.Department) {
                updateProgramOptions(formData.Department);
                setTimeout(() => {
                    document.getElementById('course').value = formData.Course || '';
                }, 100);
            }
            
            document.getElementById('year').value = formData.Year || '';
            document.getElementById('section').value = formData.Section || '';
        }
        
        // Display validation errors
        if (errors) {
            const fieldMappings = {
                'FirstName': 'firstName',
                'MiddleName': 'middleName',
                'LastName': 'lastName',
                'Birthdate': 'birthdate',
                'Gender': 'gender',
                'PhoneNumber': 'phone',
                'Email': 'email',
                'Department': 'department',
                'Course': 'course',
                'Year': 'year',
                'Section': 'section'
            };
            
            Object.keys(errors).forEach(field => {
                const mappedField = fieldMappings[field];
                
                if (mappedField) {
                    const inputField = document.getElementById(mappedField);
                    if (inputField) {
                        // Highlight the field
                        inputField.classList.add('border-red-500');
                        
                        // Add error message below the field
                        const errorElement = document.createElement('p');
                        errorElement.className = 'text-red-500 text-xs mt-1 error-message';
                        errorElement.textContent = errors[field];
                        inputField.parentNode.appendChild(errorElement);
                    }
                }
            });
        }
        
        // Show the modal and setup correct form action
        if (editingId) {
            // For edit operation
            document.getElementById('studentForm').action = "<?= base_url('student/update/') ?>" + editingId;
            document.getElementById('modalTitle').textContent = "Edit Student";
            document.getElementById('submitButton').textContent = "Update Student";
        } else {
            // For add operation
            document.getElementById('studentForm').action = "<?= base_url('student/add') ?>";
            document.getElementById('modalTitle').textContent = "Add New Student";
            document.getElementById('submitButton').textContent = "Add Student";
        }
        
        // Show the modal
        document.getElementById('studentModal').classList.remove("hidden");
    <?php endif; ?>
}

// Set up input event listeners to clear validation errors when user corrects input
function setupValidationListeners() {
    const formInputs = document.getElementById('studentForm').querySelectorAll('input, select');
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('border-red-500');
            
            // Remove error message if exists
            const errorMessage = this.parentNode.querySelector('.error-message');
            if (errorMessage) {
                errorMessage.remove();
            }
        });
    });
}

// Call this function on page load
$(document).ready(function() {
    displayValidationErrors();
    setupValidationListeners();
});
</script>