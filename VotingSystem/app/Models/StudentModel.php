<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table      = 'student';
    protected $primaryKey = 'StudentID';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $allowedFields = ['FirstName','LastName','MiddleName','Birthdate','Gender','PhoneNumber','Email', 'Department','Course', 'Year', 'Section', 'Password', 'Profile'];
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    
    // Define validation rules as a property
    protected $validationRules = [
        'FirstName'   => 'required|min_length[2]|max_length[50]|string',
        'MiddleName'  => 'permit_empty|min_length[1]|max_length[50]|string',
        'LastName'    => 'required|min_length[2]|max_length[50]|string',
        'Birthdate'   => 'required|valid_date',
        'Gender'      => 'required|in_list[0,1]',
        'PhoneNumber' => 'required|regex_match[/^09[0-9]{9}$/]',
        'Email'       => 'required|valid_email|max_length[100]',
        'Department'  => 'required',
        'Course'      => 'required',
        'Year'        => 'required|in_list[1,2,3,4]',
        'Section'     => 'required|in_list[1,2,3,4,5,6,7,8]',
        'Password'    => 'permit_empty|min_length[6]',
    ];
    
    // Define validation messages
    protected $validationMessages = [
        'FirstName' => [
            'required' => 'First name is required.',
            'min_length' => 'First name must be at least 2 characters long.',
            'max_length' => 'First name cannot exceed 50 characters.'
        ],
        'LastName' => [
            'required' => 'Last name is required.',
            'min_length' => 'Last name must be at least 2 characters long.',
            'max_length' => 'Last name cannot exceed 50 characters.'
        ],
        'Birthdate' => [
            'required' => 'Birthdate is required.',
            'valid_date' => 'Please enter a valid date.'
        ],
        'Gender' => [
            'required' => 'Gender is required.',
            'in_list' => 'Please select a valid gender option.'
        ],
        'PhoneNumber' => [
            'required' => 'Phone number is required.',
            'regex_match' => 'Phone number must be in format 09XXXXXXXXX (11 digits starting with 09).'
        ],
        'Email' => [
            'required' => 'Email is required.',
            'valid_email' => 'Please enter a valid email address.',
            'max_length' => 'Email cannot exceed 100 characters.'
        ],
        'Department' => [
            'required' => 'Please select a department.'
        ],
        'Course' => [
            'required' => 'Please select a program.'
        ],
        'Year' => [
            'required' => 'Please select a year level.',
            'in_list' => 'Please select a valid year level.'
        ],
        'Section' => [
            'required' => 'Please select a section.',
            'in_list' => 'Please select a valid section.'
        ],
        'Password' => [
            'min_length' => 'Password must be at least 6 characters long.'
        ]
    ];
    
    // Enable validation
    protected $skipValidation = false;
    
    /**
     * Generate default password for student
     * Format: First letter of first name + full last name + birthdate (MMDDYY)
     * 
     * @param string $firstName First name of student
     * @param string $lastName Last name of student
     * @param string $birthdate Birthdate in YYYY-MM-DD format
     * @return string Generated password
     */
    public function generateDefaultPassword($firstName, $lastName, $birthdate)
    {
        // Get first letter of first name
        $firstLetter = substr($firstName, 0, 1);
        
        // Format birthdate (assuming input is in YYYY-MM-DD format)
        $date = new \DateTime($birthdate);
        $formattedDate = $date->format('mdy');
        
        // Create password: First letter + Last name + birthdate in MMDDYY format
        $password = $firstLetter . $lastName . $formattedDate;
        
        // Log the generated password for debugging
        log_message('debug', 'Generated default password: ' . $password);
        
        return $password;
    }
    
    /**
     * Override insert method to add password before saving
     * 
     * @param array $data Data to be inserted
     * @return mixed Result of the insert operation
     */
    public function insert($data = null, bool $returnID = true)
    {
        // Generate default password if not provided
        if (!isset($data['Password']) || empty($data['Password'])) {
            $data['Password'] = $this->generateDefaultPassword(
                $data['FirstName'], 
                $data['LastName'], 
                $data['Birthdate']
            );
            
            // Log the pre-hashed password
            log_message('debug', 'Pre-hash password: ' . $data['Password']);
            
            // Hash the password for security
            $data['Password'] = password_hash($data['Password'], PASSWORD_DEFAULT);
            
            // Log the hashed password (just first few chars for security)
            log_message('debug', 'Hashed password prefix: ' . substr($data['Password'], 0, 10) . '...');
        }
        
        return parent::insert($data, $returnID);
    }
    
    /**
     * Check if the password is already hashed
     * 
     * @param string $password The password to check
     * @return bool True if already hashed, false otherwise
     */
    private function isHashed($password)
    {
        // Passwords hashed with password_hash() will start with $2y$
        return (strpos($password, '$2y$') === 0);
    }
    
    /**
     * Override update method to ensure passwords are always hashed
     * 
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id = null, $data = null): bool
    {
        // If we're updating a password, make sure it's hashed
        if (isset($data['Password']) && !empty($data['Password']) && !$this->isHashed($data['Password'])) {
            // Hash the password for security
            $data['Password'] = password_hash($data['Password'], PASSWORD_DEFAULT);
            log_message('debug', 'Hashed password for update operation');
        }
        
        return parent::update($id, $data);
    }
    /**
     * Count students by department
     * 
     * @return array Department IDs as keys and student counts as values
     */
    public function countStudentsByDepartment()
    {
        $result = [];
        
        // Get all departments first
        $departments = $this->db->table('student')
                            ->select('Department, COUNT(*) as count')
                            ->groupBy('Department')
                            ->get()
                            ->getResultArray();
        
        foreach ($departments as $row) {
            $result[$row['Department']] = $row['count'];
        }
        
        // Add total count
        $result['total'] = $this->countAll();
        
        return $result;
    }
    
    /**
     * Check if a student is eligible to vote in a specific election
     * 
     * @param int $studentId Student ID
     * @param int $electionDepartment Department ID from the election
     * @return bool True if eligible, false if not
     */
    public function isEligibleForElection($studentId, $electionDepartment)
    {
        $student = $this->find($studentId);
        
        if (!$student) {
            return false;
        }
        
        // If election is for all departments (0) or student belongs to the specific department
        return ($electionDepartment == 0 || $student['Department'] == $electionDepartment);
    }
}