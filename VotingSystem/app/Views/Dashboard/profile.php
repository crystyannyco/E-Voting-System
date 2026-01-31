<!-- Profile.php view with improved image upload handling -->
<div class="p-6">
    <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="border-b border-gray-200 mb-6">
            <div class="flex">
                <a href="#" class="px-6 py-3 text-blue-600 border-b-2 border-blue-600 font-medium">Admin Profile</a>
            </div>
        </div>
        
        <!-- Display flash messages -->
        <?php if(session()->has('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?= session('success') ?>
            </div>
        <?php endif; ?>
        
        <?php if(session()->has('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?= session('error') ?>
            </div>
        <?php endif; ?>
        
        <div class="">
            <div class="mt-6 flex flex-col md:flex-row">
                <!-- Admin Photo Column -->
                <div class="md:w-1/4 flex flex-col items-center mb-6 md:mb-0">
                    <div class="relative mb-4">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-md">
                            <?php if(!empty($admin_profile_image)): ?>
                                <img id="profilePreview" src="<?= base_url('uploads/admin_profiles/' . $admin_profile_image) ?>" alt="Admin Profile" class="w-full h-full object-cover" />
                            <?php else: ?>
                                <img id="profilePreview" src="<?= base_url('assets/profile.png') ?>" alt="Admin Profile" class="w-full h-full object-cover" />
                            <?php endif; ?>
                        </div>
                        <div class="mt-3 text-center photo-buttons hidden">
                            <label for="profileImage" class="px-3 py-1 bg-blue-600 text-white text-sm rounded cursor-pointer hover:bg-blue-700 transition duration-200">
                                Choose Photo
                            </label>
                            <button type="button" id="removePhotoBtn" class="px-3 py-1 bg-red-600 text-white text-sm rounded cursor-pointer hover:bg-red-700 transition duration-200 ml-2 <?= empty($admin_profile_image) ? 'hidden' : '' ?>">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Admin Form Fields -->
                <div class="md:w-3/4 md:pl-6">
                    <form id="profileForm" action="<?= base_url('profile/update') ?>" method="post" enctype="multipart/form-data">
                        <!-- Hidden file input -->
                        <input type="file" id="profileImage" name="profileImage" class="hidden" accept="image/*">
                        <input type="hidden" id="removeProfilePic" name="removeProfilePic" value="0">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Admin Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Name</label>
                                <input type="text" name="adminName" value="<?= $admin_name ?>" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" disabled>
                            </div>
                            
                            <!-- Username -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                <input type="text" name="username" value="<?= $admin_username ?>" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" disabled>
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="<?= $admin_email ?>" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" disabled>
                            </div>
                            
                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" name="phone" value="<?= $admin_phone ?>" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" disabled>
                            </div>
                            
                            <!-- Date of Birth -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                <div class="relative">
                                    <input type="date" name="birthdate" value="<?= $admin_birthdate ?>"
                                        class="w-full p-3 border border-gray-300 rounded-md text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-400 appearance-none" disabled>
                                </div>
                            </div>

                            <!-- Gender -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                <div class="relative">
                                    <select name="sex" class="w-full p-3 pr-10 border border-gray-300 rounded-md text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-400 appearance-none" disabled>
                                        <option value="0" <?= ($admin_sex == '0') ? 'selected' : '' ?>>Male</option>
                                        <option value="1" <?= ($admin_sex == '1') ? 'selected' : '' ?>>Female</option>
                                    </select>
                                    <!-- Dropdown Icon -->
                                    <span class="material-icons absolute inset-y-0 right-3 flex items-center text-gray-500 pointer-events-none">
                                        arrow_drop_down
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Current Password - only show in edit mode -->
                            <div class="password-field hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password <span class="text-red-500">*</span></label>
                                <input type="password" name="currentPassword" placeholder="Enter your current password" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <p class="text-xs text-gray-500 mt-1">Required to change password</p>
                            </div>
                            
                            <!-- New Password fields will only show in edit mode -->
                            <div class="password-field hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <input type="password" name="password" placeholder="Leave blank to keep current password" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            
                            <!-- Confirm Password -->
                            <div class="password-field hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                <input type="password" name="confirmPassword" placeholder="Confirm new password" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>
                        
                        <!-- Buttons -->
                        <div class="mt-8 flex justify-end">
                            <button type="button" id="editButton" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition duration-200">Edit Profile</button>
                            <button type="submit" id="saveButton" class="px-6 py-3 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition duration-200 ml-3 hidden">Save Changes</button>
                            <button type="button" id="cancelButton" class="px-6 py-3 bg-gray-500 text-white font-medium rounded-md hover:bg-gray-600 transition duration-200 ml-3 hidden">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript to handle the edit button functionality with improved image preview
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM loaded - initializing profile edit functionality");
        
        const editButton = document.getElementById('editButton');
        const saveButton = document.getElementById('saveButton');
        const cancelButton = document.getElementById('cancelButton');
        const passwordFields = document.querySelectorAll('.password-field');
        const formInputs = document.querySelectorAll('#profileForm input:not([type="file"]):not([name="currentPassword"]):not([type="hidden"]), #profileForm select');
        const profileImageInput = document.getElementById('profileImage');
        const profilePreview = document.getElementById('profilePreview');
        const removePhotoBtn = document.getElementById('removePhotoBtn');
        const removeProfilePicInput = document.getElementById('removeProfilePic');
        
        // Store original values to revert on cancel
        const originalValues = {};
        formInputs.forEach(input => {
            if (input.name && input.name !== 'password' && input.name !== 'confirmPassword') {
                originalValues[input.name] = input.value;
                console.log(`Stored original value for ${input.name}: ${input.value}`);
            }
        });
        
        // Store original image src
        const originalImgSrc = profilePreview.src;
        let hasImageChanged = false;
        
        // Handle file selection to show preview
        profileImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Check file type
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    alert('Only JPG, PNG and GIF files are allowed');
                    this.value = ''; // Clear the file input
                    return false;
                }
                
                // Check file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    this.value = ''; // Clear the file input
                    return false;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                    removePhotoBtn.classList.remove('hidden');
                    hasImageChanged = true;
                    removeProfilePicInput.value = '0'; // Reset remove flag since we're uploading new image
                    console.log("Image preview updated");
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Handle remove photo button
        removePhotoBtn.addEventListener('click', function() {
            profilePreview.src = '<?= base_url('assets/profile.png') ?>';
            profileImageInput.value = ''; // Clear file input
            removePhotoBtn.classList.add('hidden');
            removeProfilePicInput.value = '1'; // Set flag to remove profile picture
            hasImageChanged = true;
            console.log("Profile picture marked for removal");
        });
        
        // Enable editing
        editButton.addEventListener('click', function() {
            console.log("Edit button clicked");
            
            // Enable all form fields
            formInputs.forEach(input => {
                if (input.name !== 'password' && input.name !== 'confirmPassword') {
                    input.disabled = false;
                    console.log(`Enabled input: ${input.name}`);
                }
            });
            
            // Show password fields
            passwordFields.forEach(field => {
                field.classList.remove('hidden');
            });
            
            // Show the photo buttons
            document.querySelector('.photo-buttons').classList.remove('hidden');
            
            // Show save and cancel buttons, hide edit button
            saveButton.classList.remove('hidden');
            cancelButton.classList.remove('hidden');
            editButton.classList.add('hidden');
        });
        
        // Cancel editing
        cancelButton.addEventListener('click', function() {
            console.log("Cancel button clicked");
            
            // Revert to original values
            for (const [name, value] of Object.entries(originalValues)) {
                const input = document.querySelector(`[name="${name}"]`);
                if (input) {
                    input.value = value;
                    console.log(`Reverted ${name} to ${value}`);
                }
            }
            
            // Revert profile image
            profilePreview.src = originalImgSrc;
            
            // Reset "remove profile pic" flag
            removeProfilePicInput.value = '0';
            
            // Hide the photo buttons
            document.querySelector('.photo-buttons').classList.add('hidden');
            
            // Show/hide remove button based on original image for when edit is clicked again
            if (originalImgSrc.includes('profile.png')) {
                removePhotoBtn.classList.add('hidden');
            } else {
                removePhotoBtn.classList.remove('hidden');
            }
            
            // Disable all form fields
            formInputs.forEach(input => {
                input.disabled = true;
            });
            
            // Reset file input
            profileImageInput.value = '';
            hasImageChanged = false;
            
            // Reset password fields
            document.querySelector('[name="currentPassword"]').value = '';
            document.querySelector('[name="password"]').value = '';
            document.querySelector('[name="confirmPassword"]').value = '';
            
            // Hide password fields
            passwordFields.forEach(field => {
                field.classList.add('hidden');
            });
            
            // Hide save and cancel buttons, show edit button
            saveButton.classList.add('hidden');
            cancelButton.classList.add('hidden');
            editButton.classList.remove('hidden');
        });
        
        // Form validation before submission
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            console.log("Form submitted");
            
            // Check if password is being changed
            const newPassword = document.querySelector('[name="password"]').value;
            const confirmPassword = document.querySelector('[name="confirmPassword"]').value;
            const currentPassword = document.querySelector('[name="currentPassword"]').value;
            
            if (newPassword && !currentPassword) {
                e.preventDefault();
                alert('Current password is required to change password');
                return false;
            }
            
            if (newPassword && newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match');
                return false;
            }
            
            // Additional validation for minimum password length
            if (newPassword && newPassword.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return false;
            }
            
            // Log form submission (except passwords)
            const formData = new FormData(this);
            for (const [key, value] of formData.entries()) {
                if (key !== 'currentPassword' && key !== 'password' && key !== 'confirmPassword') {
                    console.log(`${key}: ${value instanceof File ? value.name : value}`);
                } else {
                    console.log(`${key}: [password field]`);
                }
            }
        });
    });
</script>