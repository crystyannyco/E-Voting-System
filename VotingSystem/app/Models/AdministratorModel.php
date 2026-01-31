<?php

namespace App\Models;

use CodeIgniter\Model;

class AdministratorModel extends Model
{
    protected $table      = 'administrator';
    protected $primaryKey = 'AdminID';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $allowedFields = [
        'AdminName', 'Email', 'PhoneNumber', 'Username', 'Password', 
        'Role', 'CreatedAt', 'Birthdate', 'Sex', 'Profile'
    ];
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = ''; // No updated_at field
    
    // Validation
    protected $validationRules = [
        'AdminName'   => 'required|min_length[3]|max_length[100]',
        'Email'       => 'required|valid_email|max_length[100]|is_unique[administrator.Email,AdminID,{AdminID}]',
        'PhoneNumber' => 'permit_empty|numeric|min_length[10]|max_length[15]',
        'Username'    => 'required|min_length[5]|max_length[50]|is_unique[administrator.Username,AdminID,{AdminID}]',
        'Password'    => 'permit_empty|min_length[8]',
        'Role'        => 'required|in_list[1,2,3]',
        'Birthdate'   => 'permit_empty|valid_date[Y-m-d]',
        'Sex'         => 'permit_empty|in_list[0,1]'  // Changed to accept 0 or 1
    ];
    
    protected $validationMessages = [
        'AdminName' => [
            'required' => 'Administrator name is required',
            'min_length' => 'Administrator name must be at least 3 characters long'
        ],
        'Email' => [
            'required' => 'Email address is required',
            'valid_email' => 'Please provide a valid email address',
            'is_unique' => 'This email is already in use'
        ],
        'Username' => [
            'required' => 'Username is required',
            'min_length' => 'Username must be at least 5 characters long',
            'is_unique' => 'This username is already taken'
        ],
        'Password' => [
            'min_length' => 'Password must be at least 8 characters long'
        ],
        'Role' => [
            'required' => 'Please select a role',
            'in_list' => 'Invalid role selected'
        ],
        'Birthdate' => [
            'valid_date' => 'Please provide a valid date in YYYY-MM-DD format'
        ],
        'Sex' => [
            'in_list' => 'Invalid gender selected'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Hash the password before inserting
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['Password']) && $data['data']['Password']) {
            $data['data']['Password'] = password_hash($data['data']['Password'], PASSWORD_DEFAULT);
        }
        
        return $data;
    }
    
    /**
     * Before insert callback to hash password
     */
    protected function beforeInsert(array $data)
    {
        return $this->hashPassword($data);
    }
    
    /**
     * Before update callback to hash password if it's being updated
     */
    protected function beforeUpdate(array $data)
    {
        // Only hash password if it's provided in the update
        if (isset($data['data']['Password'])) {
            // If password field is empty, remove it from the data to prevent updating with empty string
            if (empty($data['data']['Password'])) {
                unset($data['data']['Password']);
            } else {
                return $this->hashPassword($data);
            }
        }
        
        return $data;
    }
    
    /**
     * Check if administrator credentials are valid
     * 
     * @param string $username Username or Email
     * @param string $password Plain text password
     * @return array|null Administrator data if valid, null if not
     */
    public function validateLogin($username, $password)
    {
        // Try to find admin by Username OR Email
        $admin = $this->where('Username', $username)
                    ->orWhere('Email', $username)
                    ->first();
        
        if (!$admin) {
            return null;
        }
        
        // Check if password matches (currently stored as plain text)
        // TEMPORARY SOLUTION until passwords are properly hashed
        if ($password === $admin['Password']) {
            // Remove password from the returned data for security
            unset($admin['Password']);
            return $admin;
        }
        
        // If above check fails, try with password_verify in case some passwords are hashed
        if (password_verify($password, $admin['Password'])) {
            unset($admin['Password']);
            return $admin;
        }
        
        return null;
    }
    
    /**
     * Get administrators by role
     * 
     * @param int $roleId Role ID
     * @return array List of administrators with the specified role
     */
    public function getByRole($roleId)
    {
        return $this->where('Role', $roleId)->findAll();
    }
}
