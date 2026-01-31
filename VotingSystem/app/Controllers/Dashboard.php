<?php

namespace App\Controllers;

use App\Models\StudentModel;
use App\Models\PositionModel;
use App\Models\CandidateModel;
use App\Models\ElectionModel;
use App\Models\PartylistModel;
use App\Models\VoteModel;
use App\Models\AdministratorModel;

class Dashboard extends BaseController
{

    public function login(): string
    {
        return 
        view('login');
    }

    public function processLogin()
    {
        // Load the administrator model
        $AdministratorModel = new AdministratorModel();
        
        // Get the login credentials from the form submission
        $username = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        
        // Validate the credentials against the database
        $admin = $AdministratorModel->validateLogin($username, $password);
        
        if ($admin) {
            // Login successful - create session
            $session = session();
            $session->set([
                'admin_id' => $admin['AdminID'],
                'admin_name' => $admin['AdminName'],
                'admin_email' => $admin['Email'],
                'admin_username' => $admin['Username'],
                'admin_role' => $admin['Role'],
                'is_logged_in' => true
            ]);
            
            // Check if there was a redirect URL stored in the session
            $redirectURL = $session->getFlashdata('redirect_url');
            if ($redirectURL) {
                return redirect()->to($redirectURL);
            }
            
            // If no redirect URL, go to dashboard
            return redirect()->to(base_url('dashboard'));
        } else {
            // Login failed - set error message and redirect back
            session()->setFlashdata('error', 'Invalid username or password');
            return redirect()->to(base_url('/'));
        }
    }

    private function getViewData($customData = [])
    {
        $session = session();
        $baseData = [
            'admin_id' => $session->get('admin_id'),
            'admin_name' => $session->get('admin_name'),
            'admin_email' => $session->get('admin_email'),
            'admin_username' => $session->get('admin_username'),
            'admin_role' => $session->get('admin_role')
        ];
        
        return array_merge($baseData, $customData);
    }

    public function logout()
    {
        // Destroy the session
        session()->destroy();
        
        // Redirect to login page
        return redirect()->to(base_url('/'));
    }

    public function maindashboard(): string
    {
        $StudentModel = new StudentModel();
        $CandidateModel = new CandidateModel();
        $PartylistModel = new PartylistModel();
        $PositionModel = new PositionModel();
        $ElectionModel = new ElectionModel();
        $VoteModel = new VoteModel();

        // Department names mapping
        $departments = [
            '1' => 'CSS',
            '2' => 'CEA',
            '3' => 'CHS',
            '4' => 'CTHBM',
            '5' => 'CTDE',
            '6' => 'CAS',
        ];
        
        // Get candidates with their details
        $candidates = $CandidateModel->getCandidatesWithDetails();
        
        // Group candidates by position
        $candidatesByPosition = [];
        foreach ($candidates as $candidate) {
            $positionId = $candidate['Position'];
            if (!isset($candidatesByPosition[$positionId])) {
                $candidatesByPosition[$positionId] = [];
            }
            $candidatesByPosition[$positionId][] = $candidate;
        }
        
        // Get position names
        $positions = [];
        foreach ($PositionModel->findAll() as $position) {
            $positions[$position['PositionID']] = $position['PositionName'];
        }
        
        // Get all elections with vote statistics
        $elections = $ElectionModel->findAll();
        $electionStats = [];
        
        foreach ($elections as $election) {
            $electionStats[$election['ElectionID']] = $VoteModel->getVoteStatisticsByDepartment($election['ElectionID']);
        }
        
        $data = [
            'totalStudents' => count($StudentModel->findAll()),
            'totalCandidates' => count($CandidateModel->findAll()),
            'totalPartylists' => count($PartylistModel->findAll()),
            'totalElections' => count($ElectionModel->findAll()),
            'candidatesByPosition' => $candidatesByPosition,
            'positions' => $positions,
            'elections' => $elections,
            'electionStats' => $electionStats,
            'departments' => $departments
        ];

        $viewData = $this->getViewData($data);
        
        return 
            view('template/header') . 
            view('template/sidebar') . 
            view('template/topbar', $viewData) . 
            view('Dashboard/maindashboard', $data) . 
            view('template/footer');
    }

    public function election(): string
    {
        $ElectionModel = new ElectionModel();
        $elections = $ElectionModel -> findAll();
        $data = [
            'page_title' => 'Election',
            'elections' => $elections,
        ];

        $viewData = $this->getViewData($data);

        return 
        view('template/header') . 
        view('template/sidebar') . 
        view('template/topbar', $viewData) . 
        view('Dashboard/election') . 
        view('template/footer');
    }

    public function votes(): string
    {
        $ElectionModel = new ElectionModel();
        $elections = $ElectionModel->findAll();
        
        // Default to empty data - will be populated via AJAX when election is selected
        $data = [
            'title' => 'Votes',
            'elections' => $elections,
            'selectedElection' => null,
        ];

        $viewData = $this->getViewData($data);

        return 
        view('template/header') . 
        view('template/sidebar') . 
        view('template/topbar', $viewData) . 
        view('Dashboard/votes', $data) . 
        view('template/footer');
    }

    public function getElectionVotes($id)
    {
        $ElectionModel = new ElectionModel();
        $CandidateModel = new CandidateModel();
        $PositionModel = new PositionModel();
        $VoteModel = new VoteModel();
        $StudentModel = new StudentModel();
        
        // Set PHP timezone to Philippines
        date_default_timezone_set('Asia/Manila');
        
        // Get election details
        $election = $ElectionModel->find($id);
        
        if (!$election) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Election not found'
            ]);
        }
        
        // Get positions for this election
        $positions = $PositionModel->findAll();
        $positionMap = [];
        foreach ($positions as $position) {
            $positionMap[$position['PositionID']] = $position['PositionName'];
        }
        
        // Get candidates for this election
        $candidates = $CandidateModel->where('Election', $id)->findAll();
        
        // Get eligible students count based on department
        $departmentId = $election['Department'];
        $totalStudents = ($departmentId == 0) ? 
            $StudentModel->countAllResults() : 
            $StudentModel->where('Department', $departmentId)->countAllResults();
        
        // Get total unique voters for this election
        $votedStudents = $VoteModel->getTotalVoters($id);
        $votedPercentage = ($totalStudents > 0) ? round(($votedStudents / $totalStudents) * 100) : 0;
        $notVotedPercentage = 100 - $votedPercentage;
        
        // Get votes by candidate
        $candidateVotes = $VoteModel->getVotesByCandidate($id);
        $votesByCandidateId = [];
        foreach ($candidateVotes as $vote) {
            $votesByCandidateId[$vote['CandidateID']] = $vote['voteCount'];
        }
        
        // Get abstain votes by position
        $abstainVotes = $VoteModel->getAbstainVotesByPosition($id);
        $abstainByPositionId = [];
        foreach ($abstainVotes as $vote) {
            $abstainByPositionId[$vote['PositionID']] = $vote['voteCount'];
        }
        
        // Get eligible voters by position
        $eligibleVotersByPosition = $VoteModel->getEligibleVotersByPosition($id, $departmentId);
        
        // Group candidates by position and calculate stats
        $candidateStats = [];
        foreach ($candidates as $candidate) {
            $positionId = $candidate['Position'];
            
            if (!isset($candidateStats[$positionId])) {
                $candidateStats[$positionId] = [
                    'totalVotes' => 0,
                    'eligibleVoters' => $eligibleVotersByPosition[$positionId] ?? $totalStudents,
                    'candidates' => []
                ];
            }
            
            // Get votes for this candidate
            $votes = isset($votesByCandidateId[$candidate['CandidateID']]) ? 
                    $votesByCandidateId[$candidate['CandidateID']] : 0;
            
            // Add to total votes for this position
            $candidateStats[$positionId]['totalVotes'] += $votes;
            
            // Store candidate with vote info
            $candidate['votes'] = $votes;
            $candidateStats[$positionId]['candidates'][] = $candidate;
        }
        
        // Add abstain votes and calculate percentages
        foreach ($candidateStats as $positionId => &$positionData) {
            // Add abstain votes to total
            $abstainCount = isset($abstainByPositionId[$positionId]) ? $abstainByPositionId[$positionId] : 0;
            $positionData['totalVotes'] += $abstainCount;
            
            // Add abstain as a candidate
            $positionData['candidates'][] = [
                'FirstName' => 'Abstain',
                'MiddleName' => '',
                'LastName' => '',
                'votes' => $abstainCount,
                'isAbstain' => true
            ];
            
            // Calculate percentages based on total votes cast for this position
            foreach ($positionData['candidates'] as &$candidate) {
                $candidate['percentage'] = ($positionData['totalVotes'] > 0) ? 
                    round(($candidate['votes'] / $positionData['totalVotes']) * 100) : 0;
            }
        }
        
        // Calculate time info
        $now = new \DateTime('now', new \DateTimeZone('Asia/Manila'));
        $end = new \DateTime($election['End'], new \DateTimeZone('Asia/Manila'));
        
        if ($now < $end) {
            $interval = $now->diff($end);
            $timeRemaining = $interval->format('%a Days %h Hours %i Minutes');
            $status = 'Ongoing';
            $statusColor = 'green';
        } else {
            $timeRemaining = 'Finished';
            $status = 'Closed';
            $statusColor = 'red';
        }
        
        // Format dates for certificate
        $startDate = new \DateTime($election['Start'], new \DateTimeZone('Asia/Manila'));
        $endDate = new \DateTime($election['End'], new \DateTimeZone('Asia/Manila'));
        
        return $this->response->setJSON([
            'success' => true,
            'election' => $election,
            'positions' => $positionMap,
            'candidateStats' => $candidateStats,
            'votingStats' => [
                'totalStudents' => $totalStudents,
                'votedStudents' => $votedStudents,
                'votedPercentage' => $votedPercentage,
                'notVotedPercentage' => $notVotedPercentage
            ],
            'timeInfo' => [
                'timeRemaining' => $timeRemaining,
                'status' => $status,
                'statusColor' => $statusColor
            ],
            'dateInfo' => [
                'startDate' => $startDate->format('F j, Y'),
                'endDate' => $endDate->format('F j, Y')
            ]
        ]);
    }
    public function student()
    {
        $StudentModel = new StudentModel();
        $students = $StudentModel->findAll();
        
        $data = [
            'page_title' => 'Student',
            'students' => $students,
            'validation' => \Config\Services::validation(),
        ];
    
        $viewData = $this->getViewData($data);
        
        // If there are validation errors, set them as flashdata
        if (session()->getFlashdata('errors')) {
            $data['validation'] = session()->getFlashdata('errors');
        }
        
        return 
            view('template/header') . 
            view('template/sidebar') . 
            view('template/topbar', $viewData) . 
            view('Dashboard/student', $data) . 
            view('template/footer');
    }
    
    // Get student by ID for AJAX
    public function getStudent($id)
    {
        $StudentModel = new StudentModel();
        $student = $StudentModel->find($id);
        
        if (!$student) {
            return $this->response->setJSON(['error' => 'Student not found'])->setStatusCode(404);
        }
        
        // Return JSON response
        return $this->response->setJSON($student);
    }
    
    //Add Student
    public function addStudent()
    {
        $StudentModel = new StudentModel();
    
        // Collect data from form submission
        $data = [
            'FirstName' => $this->request->getPost('firstName'),
            'MiddleName' => $this->request->getPost('middleName'),
            'LastName' => $this->request->getPost('lastName'),
            'Birthdate' => $this->request->getPost('birthdate'),
            'Gender' => $this->request->getPost('gender'),
            'PhoneNumber' => $this->request->getPost('phone'),
            'Email' => $this->request->getPost('email'),
            'Department' => $this->request->getPost('department'),
            'Course' => $this->request->getPost('course'),
            'Year' => $this->request->getPost('year'),
            'Section' => $this->request->getPost('section'),
            // Password will be automatically generated in the model
        ];
        
        // Generate the default password to show to admin
        $defaultPassword = $StudentModel->generateDefaultPassword(
            $data['FirstName'],
            $data['LastName'],
            $data['Birthdate']
        );
    
        // Insert will automatically validate using model rules
        if ($StudentModel->insert($data) === false) {
            // Store the form data and validation errors in session
            session()->setFlashdata('formData', $data);
            session()->setFlashdata('errors', $StudentModel->errors());
            session()->setFlashdata('error', 'Please check the form for errors.');
            session()->setFlashdata('showModal', true);
            return redirect()->to(base_url('student'));
        }
        
        session()->setFlashdata('success', 'Student added successfully with default password: ' . $defaultPassword);
        return redirect()->to(base_url('student'));
    }
    
    public function updateStudent($id)
    {
        $StudentModel = new StudentModel();
        
        $data = [
            'FirstName' => $this->request->getPost('firstName'),
            'MiddleName' => $this->request->getPost('middleName'),
            'LastName' => $this->request->getPost('lastName'),
            'Birthdate' => $this->request->getPost('birthdate'),
            'Gender' => $this->request->getPost('gender'),
            'PhoneNumber' => $this->request->getPost('phone'),
            'Email' => $this->request->getPost('email'),
            'Department' => $this->request->getPost('department'),
            'Course' => $this->request->getPost('course'),
            'Year' => $this->request->getPost('year'),
            'Section' => $this->request->getPost('section'),
        ];
        
        // For updates, we don't regenerate password automatically
        // If you want to reset password, you can add a dedicated method
    
        // Update will automatically validate using model rules
        if ($StudentModel->update($id, $data) === false) {
            // Store the form data and validation errors in session
            session()->setFlashdata('formData', $data);
            session()->setFlashdata('errors', $StudentModel->errors());
            session()->setFlashdata('error', 'Please check the form for errors.');
            session()->setFlashdata('showModal', true);
            
            // For update, add the ID to flashdata
            session()->setFlashdata('editingStudentId', $id);
            
            return redirect()->to(base_url('student'));
        }
    
        session()->setFlashdata('success', 'Student updated successfully.');
        return redirect()->to(base_url('student'));
    }
    
    public function deleteStudent($id)
    {
        $StudentModel = new StudentModel();
        
        if ($StudentModel->delete($id)) {
            session()->setFlashdata('success', 'Student deleted successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to delete student. Please try again.');
        }
        
        return redirect()->to(base_url('student'));
    }
    
    //Reset student password
    public function resetPassword($id)
    {
        $StudentModel = new StudentModel();
        $student = $StudentModel->find($id);
        
        if (!$student) {
            session()->setFlashdata('error', 'Student not found.');
            return redirect()->to(base_url('student'));
        }
        
        // Generate new default password
        $newPassword = $StudentModel->generateDefaultPassword(
            $student['FirstName'],
            $student['LastName'],
            $student['Birthdate']
        );
        
        // Store the plain text password temporarily to display to admin
        session()->setFlashdata('plain_password', $newPassword);
        
        // Hash the password before storing
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        if ($StudentModel->update($id, ['Password' => $hashedPassword])) {
            session()->setFlashdata('success', 'Password reset successfully to: ' . $newPassword);
        } else {
            session()->setFlashdata('error', 'Failed to reset password. Please try again.');
        }
        
        return redirect()->to(base_url('student'));
    }
    //end of Student CRUD

    // Controller methods for Position
    public function position(): string
    {
        $PositionModel = new PositionModel();
        $positions = $PositionModel->findAll();
        
        $data = [
            'page_title' => 'Position',
            'positions' => $positions,
            'validation' => \Config\Services::validation(),
        ];

        $viewData = $this->getViewData($data);

        return 
        view('template/header') . 
        view('template/sidebar') . 
        view('template/topbar', $viewData) . 
        view('Dashboard/positions') . 
        view('template/footer');
    }

    public function addPosition()
    {
        $PositionModel = new PositionModel();
        
        // Collect data from form submission
        $data = [
            'PositionName' => $this->request->getPost('positionName'),
        ];
        
        // Validate the data
        if (!$this->validateData($data, [
            'PositionName' => [
                'rules' => 'required|min_length[2]|max_length[50]|string',
                'errors' => [
                    'required' => 'Position name is required.',
                    'min_length' => 'Position name must be at least 2 characters.',
                    'max_length' => 'Position name cannot exceed 50 characters.',
                    'string' => 'Position name must be text.'
                ]
            ]
        ])) {
            // Store the form data and validation errors in session
            session()->setFlashdata('formData', $data);
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('error', 'Please check the form for errors.');
            session()->setFlashdata('showAddModal', true);
            return redirect()->to(base_url('position'));
        }

        // Insert the validated data
        $PositionModel->insert($data);
        
        session()->setFlashdata('success', 'Position added successfully.');
        return redirect()->to(base_url('position'));
    }

    public function updatePosition($id)
    {
        $PositionModel = new PositionModel();
        
        // Collect data from form submission
        $data = [
            'PositionName' => $this->request->getPost('positionName'),
        ];
        
        // Validate the data
        if (!$this->validateData($data, [
            'PositionName' => [
                'rules' => 'required|min_length[2]|max_length[50]|string',
                'errors' => [
                    'required' => 'Position name is required.',
                    'min_length' => 'Position name must be at least 2 characters.',
                    'max_length' => 'Position name cannot exceed 50 characters.',
                    'string' => 'Position name must be text.'
                ]
            ]
        ])) {
            // Store the form data, position ID and validation errors in session
            session()->setFlashdata('formData', $data);
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('error', 'Please check the form for errors.');
            session()->setFlashdata('showEditModal', true);
            session()->setFlashdata('positionId', $id);
            return redirect()->to(base_url('position'));
        }

        // Update the position record
        $PositionModel->update($id, $data);
        
        session()->setFlashdata('success', 'Position updated successfully.');
        return redirect()->to(base_url('position'));
    }

    public function deletePosition($id)
    {
        $PositionModel = new PositionModel();
        $PositionModel->delete($id);

        session()->setFlashdata('success', 'Position deleted successfully.');
        return redirect()->to(base_url('position'));
    }

    public function partylist(): string
    {
        $PartylistModel = new PartylistModel();
        $partylists = $PartylistModel->findAll();
        
        $data = [
            'page_title' => 'Partylist',
            'partylists' => $partylists,
            'validation' => \Config\Services::validation(),
        ];

        $viewData = $this->getViewData($data);

        return 
        view('template/header') . 
        view('template/sidebar') . 
        view('template/topbar', $viewData) . 
        view('Dashboard/partylist', $data) . 
        view('template/footer');
    }

    public function addPartylist()
    {
        $PartylistModel = new PartylistModel();
        
        // Collect data from form submission
        $data = [
            'Name' => $this->request->getPost('partylistName'),
        ];
        
        // Validate the data
        if (!$this->validateData($data, [
            'Name' => [
                'rules' => 'required|min_length[2]|max_length[50]|string',
                'errors' => [
                    'required' => 'Partylist name is required.',
                    'min_length' => 'Partylist name must be at least 2 characters.',
                    'max_length' => 'Partylist name cannot exceed 50 characters.',
                    'string' => 'Partylist name must be text.'
                ]
            ]
        ])) {
            // Store the form data and validation errors in session
            session()->setFlashdata('formData', $data);
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('error', 'Please check the form for errors.');
            session()->setFlashdata('showAddModal', true);
            return redirect()->to(base_url('partylist'));
        }

        // Insert the validated data
        $PartylistModel->insert($data);
        
        session()->setFlashdata('success', 'Partylist added successfully.');
        return redirect()->to(base_url('partylist'));
    }

    public function updatePartylist($id)
    {
        $PartylistModel = new PartylistModel();
        
        // Collect data from form submission
        $data = [
            'Name' => $this->request->getPost('partylistName'),
        ];
        
        // Validate the data
        if (!$this->validateData($data, [
            'Name' => [
                'rules' => 'required|min_length[2]|max_length[50]|string',
                'errors' => [
                    'required' => 'Partylist name is required.',
                    'min_length' => 'Partylist name must be at least 2 characters.',
                    'max_length' => 'Partylist name cannot exceed 50 characters.',
                    'string' => 'Partylist name must be text.'
                ]
            ]
        ])) {
            // Store the form data, partylist ID and validation errors in session
            session()->setFlashdata('formData', $data);
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('error', 'Please check the form for errors.');
            session()->setFlashdata('showEditModal', true);
            session()->setFlashdata('partylistId', $id);
            return redirect()->to(base_url('partylist'));
        }

        // Update the partylist record
        $PartylistModel->update($id, $data);
        
        session()->setFlashdata('success', 'Partylist updated successfully.');
        return redirect()->to(base_url('partylist'));
    }

    public function deletePartylist($id)
    {
        $PartylistModel = new PartylistModel();
        $PartylistModel->delete($id);
        
        session()->setFlashdata('success', 'Partylist deleted successfully.');
        return redirect()->to(base_url('partylist'));
    }
    //end of Partylist CRUD

    public function candidate(): string
    {
        $CandidateModel = new CandidateModel();
        $PositionModel = new PositionModel();
        $PartylistModel = new PartylistModel();
        $ElectionModel = new ElectionModel();

        // Use getCandidatesWithDetails instead of findAll()
        $candidates = $CandidateModel->getCandidatesWithDetails();
        $positions = $PositionModel->findAll();
        $partylists = $PartylistModel->findAll();
        $elections = $ElectionModel->findAll();

        $data = [
            'page_title' => 'Candidates',
            'candidates' => $candidates,
            'positions' => $positions,
            'partylists' => $partylists,
            'elections' => $elections,
        ];

        $viewData = $this->getViewData($data);

        return 
        view('template/header') . 
        view('template/sidebar') . 
        view('template/topbar', $viewData) . 
        view('Dashboard/candidates') . 
        view('template/footer');
    }
    
    // Get Candidate Data (for AJAX requests)
    public function getCandidate($id)
    {
        $CandidateModel = new CandidateModel();
        $candidate = $CandidateModel->find($id);
        
        if ($candidate) {
            // If the candidate has a profile image, add the full URL
            if (!empty($candidate['Profile'])) {
                $candidate['ProfileUrl'] = base_url('uploads/profiles/' . $candidate['Profile']);
            }
        }
        
        // Return JSON response
        return $this->response->setJSON($candidate);
    }

    // Add Candidate with improved validation
    public function addCandidate()
    {
        $CandidateModel = new CandidateModel();
        
        // Handle file upload if a profile image was submitted
        $profileImage = null;
        $file = $this->request->getFile('profileUpload');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Generate a unique name
            $newName = $file->getRandomName();
            // Move the file to the uploads directory
            $file->move(ROOTPATH . 'public/uploads/profiles/', $newName);
            $profileImage = $newName;
        }
        
        // Collect data from form submission with corrected field mappings
        $data = [
            'FirstName' => $this->request->getPost('firstName'),
            'MiddleName' => $this->request->getPost('middleName'),
            'LastName' => $this->request->getPost('lastName'),
            'Partylist' => $this->request->getPost('partylistName'),
            'Position' => $this->request->getPost('position'),
            'Election' => $this->request->getPost('electionTitle'),
            'Platform' => $this->request->getPost('platform'),
        ];
        
        // Add profile image if uploaded
        if ($profileImage) {
            $data['Profile'] = $profileImage;
        }

        // Validate the data with appropriate rules and custom error messages
        if (!$this->validateData($data, [
            'FirstName' => [
                'rules' => 'required|min_length[2]|max_length[50]|alpha_space',
                'errors' => [
                    'required' => 'First name is required.',
                    'min_length' => 'First name must be at least 2 characters.',
                    'max_length' => 'First name cannot exceed 50 characters.',
                    'alpha_space' => 'First name can only contain alphabets and spaces.'
                ]
            ],
            'MiddleName' => [
                'rules' => 'permit_empty|min_length[2]|max_length[50]|alpha_space',
                'errors' => [
                    'min_length' => 'Middle name must be at least 2 character.',
                    'max_length' => 'Middle name cannot exceed 50 characters.',
                    'alpha_space' => 'Middle name can only contain alphabets and spaces.'
                ]
            ],
            'LastName' => [
                'rules' => 'required|min_length[2]|max_length[50]|alpha_space',
                'errors' => [
                    'required' => 'Last name is required.',
                    'min_length' => 'Last name must be at least 2 characters.',
                    'max_length' => 'Last name cannot exceed 50 characters.',
                    'alpha_space' => 'Last name can only contain alphabets and spaces.'
                ]
            ],
            'Partylist' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Partylist is required.',
                    'numeric' => 'Please select a valid partylist.'
                ]
            ],
            'Position' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Position is required.',
                    'numeric' => 'Please select a valid position.'
                ]
            ],
            'Election' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Election is required.',
                    'numeric' => 'Please select a valid election.'
                ]
            ],
            // 'Platform' => [
            //     'rules' => 'required',
            //     'errors' => [
            //         'required' => 'Platform is required.'
            //     ]
            // ],
        ])) {
            // If validation fails, store form data and errors in session
            session()->setFlashdata('formData', $data);
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('error', 'Please check the form for errors.');
            session()->setFlashdata('showAddModal', true);
            return redirect()->to(base_url('candidate'));
        }

        // Insert the validated data
        $CandidateModel->insert($data);
        
        session()->setFlashdata('success', 'Candidate added successfully.');
        return redirect()->to(base_url('candidate'));
    }

    // Update Candidate with improved validation
    public function updateCandidate($id)
    {
        $CandidateModel = new CandidateModel();
        
        // First get the current candidate data to handle profile image properly
        $currentCandidate = $CandidateModel->find($id);
        
        if (!$currentCandidate) {
            session()->setFlashdata('error', 'Candidate not found.');
            return redirect()->to(base_url('candidate'));
        }
        
        // Handle file upload if a new profile image was submitted
        $profileImage = $currentCandidate['Profile'] ?? null; // Keep existing image by default
        $file = $this->request->getFile('profileUpload');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Generate a unique name
            $newName = $file->getRandomName();
            // Move the file to the uploads directory
            $file->move(ROOTPATH . 'public/uploads/profiles/', $newName);
            
            // Delete the old profile image if it exists
            if (!empty($currentCandidate['Profile'])) {
                $oldFilePath = ROOTPATH . 'public/uploads/profiles/' . $currentCandidate['Profile'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
            
            $profileImage = $newName;
        }
        
        // Collect data from form submission with corrected field mappings
        $data = [
            'FirstName' => $this->request->getPost('firstName'),
            'MiddleName' => $this->request->getPost('middleName'),
            'LastName' => $this->request->getPost('lastName'),
            'Partylist' => $this->request->getPost('partylistName'),
            'Position' => $this->request->getPost('position'),
            'Election' => $this->request->getPost('electionTitle'),
            'Platform' => $this->request->getPost('platform'),
        ];
        
        // Add profile image if updated
        if ($profileImage) {
            $data['Profile'] = $profileImage;
        }

        // Validate the data with appropriate rules and custom error messages
        if (!$this->validateData($data, [
            'FirstName' => [
                'rules' => 'required|min_length[2]|max_length[50]|alpha_space',
                'errors' => [
                    'required' => 'First name is required.',
                    'min_length' => 'First name must be at least 2 characters.',
                    'max_length' => 'First name cannot exceed 50 characters.',
                    'alpha_space' => 'First name can only contain alphabets and spaces.'
                ]
            ],
            'MiddleName' => [
                'rules' => 'permit_empty|min_length[2]|max_length[50]|alpha_space',
                'errors' => [
                    'min_length' => 'Middle name must be at least 2 character.',
                    'max_length' => 'Middle name cannot exceed 50 characters.',
                    'alpha_space' => 'Middle name can only contain alphabets and spaces.'
                ]
            ],
            'LastName' => [
                'rules' => 'required|min_length[2]|max_length[50]|alpha_space',
                'errors' => [
                    'required' => 'Last name is required.',
                    'min_length' => 'Last name must be at least 2 characters.',
                    'max_length' => 'Last name cannot exceed 50 characters.',
                    'alpha_space' => 'Last name can only contain alphabets and spaces.'
                ]
            ],
            'Partylist' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Partylist is required.',
                    'numeric' => 'Please select a valid partylist.'
                ]
            ],
            'Position' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Position is required.',
                    'numeric' => 'Please select a valid position.'
                ]
            ],
            'Election' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Election is required.',
                    'numeric' => 'Please select a valid election.'
                ]
            ],
            // 'Platform' => [
            //     'rules' => 'required',
            //     'errors' => [
            //         'required' => 'Platform is required.'
            //     ]
            // ],
        ])) {
            // If validation fails, store form data, candidate ID and errors in session
            session()->setFlashdata('formData', $data);
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('error', 'Please check the form for errors.');
            session()->setFlashdata('showEditModal', true);
            session()->setFlashdata('candidateId', $id);
            return redirect()->to(base_url('candidate'));
        }

        // Update the candidate record
        $CandidateModel->update($id, $data);
        
        session()->setFlashdata('success', 'Candidate updated successfully.');
        return redirect()->to(base_url('candidate'));
    }

    // Delete Candidate
    public function deleteCandidate($id)
    {
        $CandidateModel = new CandidateModel();
        
        // Get the candidate to delete their profile image if exists
        $candidate = $CandidateModel->find($id);
        
        if ($candidate) {
            // Delete the profile image if it exists
            if (!empty($candidate['Profile'])) {
                $filePath = ROOTPATH . 'public/uploads/profiles/' . $candidate['Profile'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            // Delete the candidate record
            $CandidateModel->delete($id);
        }
        
        session()->setFlashdata('success', 'Candidate deleted successfully.');
        return redirect()->to(base_url('candidate'));
    }
    //end of Candidate CRUD

    // Add Election
    public function addElection()
    {
        $ElectionModel = new ElectionModel();  
        $data = [
            'ElectionName' => $this->request->getPost('titleName'),
            'Start' => $this->request->getPost('startDateTime'),
            'End' => $this->request->getPost('endDateTime'),
            'Department' => $this->request->getPost('department'),  
        ];

        // Enhanced validation with custom error messages
        if (!$this->validateData($data, [
            'ElectionName' => [
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'Election name is required.',
                    'min_length' => 'Election name must be at least 2 characters.',
                    'max_length' => 'Election name cannot exceed 100 characters.'
                ]
            ],
            'Start' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Start date is required.',
                    'valid_date' => 'Start date must be a valid date and time.'
                ]
            ],
            'End' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'End date is required.',
                    'valid_date' => 'End date must be a valid date and time.'
                ]
            ],
            'Department' => [
                'rules' => 'required|in_list[0,1,2,3,4,5,6]',
                'errors' => [
                    'required' => 'Department is required.',
                    'in_list' => 'Please select a valid department.'
                ]
            ],
        ])) {
            // Store the form data and validation errors in session
            session()->setFlashdata('formData', $data);
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('error', 'Please check the form for errors.');
            session()->setFlashdata('showAddElectionModal', true);
            return redirect()->to(base_url('election'));
        }

        $ElectionModel->insert($data);
        session()->setFlashdata('success', 'Election added successfully.');
        return redirect()->to('election'); 
    }

    public function updateElection($id)
    {
        $ElectionModel = new ElectionModel();
        
        $data = [
            'ElectionName' => $this->request->getPost('titleName'),
            'Start' => $this->request->getPost('startDateTime'),
            'End' => $this->request->getPost('endDateTime'),
            'Department' => $this->request->getPost('department'),
        ];

        // Enhanced validation with custom error messages
        if (!$this->validateData($data, [
            'ElectionName' => [
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'Election name is required.',
                    'min_length' => 'Election name must be at least 2 characters.',
                    'max_length' => 'Election name cannot exceed 100 characters.'
                ]
            ],
            'Start' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Start date is required.',
                    'valid_date' => 'Start date must be a valid date and time.'
                ]
            ],
            'End' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'End date is required.',
                    'valid_date' => 'End date must be a valid date and time.'
                ]
            ],
            'Department' => [
                'rules' => 'required|in_list[0,1,2,3,4,5,6]',
                'errors' => [
                    'required' => 'Department is required.',
                    'in_list' => 'Please select a valid department.'
                ]
            ],
        ])) {
            // Store the form data, election ID and validation errors in session
            session()->setFlashdata('formData', $data);
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('error', 'Please check the form for errors.');
            session()->setFlashdata('showEditElectionModal', true);
            session()->setFlashdata('electionId', $id);
            return redirect()->to(base_url('election'));
        }

        $ElectionModel->update($id, $data);
        session()->setFlashdata('success', 'Election updated successfully.');
        return redirect()->to('election');
    }

    public function deleteElection($id)
    {
        $ElectionModel = new ElectionModel();
        $ElectionModel->delete($id);
        
        session()->setFlashdata('success', 'Election deleted successfully.');
        return redirect()->to('election');
    }

    public function getElection($id)
    {
        $ElectionModel = new ElectionModel();
        $election = $ElectionModel->find($id);
        
        if ($election) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $election
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Election not found'
            ]);
        }
    }
    
    public function getAdministrator($id)
    {
        $AdministratorModel = new AdministratorModel();
        $administrator = $AdministratorModel->find($id);
        
        // Remove password from response for security
        if (isset($administrator['Password'])) {
            unset($administrator['Password']);
        }
        
        // Return JSON response
        return $this->response->setJSON($administrator);
    }
    
    public function admin(): string
    {
        $AdministratorModel = new AdministratorModel();
        $data = [
            'title' => 'Admin',
            'administrators' => $AdministratorModel->findAll(),
            'validation' => \Config\Services::validation(),
        ];
        
        $viewData = $this->getViewData($data);  
        
        return 
        view('template/header') . 
        view('template/sidebar') . 
        view('template/topbar', $viewData) .  
        view('Dashboard/administrator', $data) . 
        view('template/footer');
    }

    public function addAdministrator()
    {
        $AdministratorModel = new AdministratorModel();

        // Check if passwords match
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirmPassword');
        
        if ($password !== $confirmPassword) {
            session()->setFlashdata('error', 'Passwords do not match');
            session()->setFlashdata('formData', $this->request->getPost());
            session()->setFlashdata('showAddModal', true);
            return redirect()->to(base_url('administrator'));
        }
        
        // Collect data from form submission
        $data = [
            'AdminName' => $this->request->getPost('adminName'),
            'Email' => $this->request->getPost('email'),
            'PhoneNumber' => $this->request->getPost('phone'),
            'Username' => $this->request->getPost('username'),
            'Password' => $password,
            'Role' => $this->request->getPost('role'),
            'Birthdate' => $this->request->getPost('birthdate'),
            'Sex' => $this->request->getPost('sex')  // Will now receive '0' or '1'
        ];
        
        // Validate and insert
        if ($AdministratorModel->insert($data) === false) {
            // If validation fails
            session()->setFlashdata('errors', $AdministratorModel->errors());
            session()->setFlashdata('formData', $data);
            session()->setFlashdata('error', 'Please check the form for errors');
            session()->setFlashdata('showAddModal', true);
            return redirect()->to(base_url('administrator'));
        }
        
        // Redirect with success message
        session()->setFlashdata('success', 'Administrator added successfully');
        return redirect()->to(base_url('administrator'));
    }

    public function updateAdministrator($id)
    {
        $AdministratorModel = new AdministratorModel();
        
        // Get the current administrator data
        $currentAdmin = $AdministratorModel->find($id);
        if (!$currentAdmin) {
            session()->setFlashdata('error', 'Administrator not found');
            return redirect()->to(base_url('administrator'));
        }
        
        // Collect data from form submission - NO password handling
        $data = [
            'AdminName' => $this->request->getPost('adminName'),
            'Email' => $this->request->getPost('email'),
            'PhoneNumber' => $this->request->getPost('phone'),
            'Username' => $this->request->getPost('username'),
            'Role' => $this->request->getPost('role'),
            'Birthdate' => $this->request->getPost('birthdate'),
            'Sex' => $this->request->getPost('sex')  // Will now receive '0' or '1'
        ];
        
        // Create a validation instance
        $validation = \Config\Services::validation();
        
        // Define validation rules for updating (without password)
        $validationRules = [
            'AdminName'   => 'required|min_length[3]|max_length[100]',
            'Email'       => 'required|valid_email|max_length[100]|is_unique[administrator.Email,AdminID,'.$id.']',
            'PhoneNumber' => 'permit_empty|numeric|min_length[10]|max_length[15]',
            'Username'    => 'required|min_length[5]|max_length[50]|is_unique[administrator.Username,AdminID,'.$id.']',
            'Role'        => 'required|in_list[1,2,3]',
            'Birthdate'   => 'permit_empty|valid_date[Y-m-d]',
            'Sex'         => 'permit_empty|in_list[0,1]'  // Changed to accept 0 or 1
        ];
        
        // Set validation rules
        $validation->setRules($validationRules, [
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
        ]);
        
        // Run validation
        if (!$validation->run($data)) {
            // If validation fails
            session()->setFlashdata('errors', $validation->getErrors());
            session()->setFlashdata('formData', $data);
            session()->setFlashdata('error', 'Please check the form for errors');
            session()->setFlashdata('showEditModal', true);
            session()->setFlashdata('adminId', $id);
            return redirect()->to(base_url('administrator'));
        }
        
        // Update the record - without validation since we've already done it
        try {
            $AdministratorModel->skipValidation(true);
            if ($AdministratorModel->update($id, $data) === false) {
                session()->setFlashdata('error', 'Failed to update administrator');
                return redirect()->to(base_url('administrator'));
            }
            
            // Redirect with success message
            session()->setFlashdata('success', 'Administrator updated successfully');
            return redirect()->to(base_url('administrator'));
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Error updating administrator: ' . $e->getMessage());
            return redirect()->to(base_url('administrator'));
        }
    }

    public function deleteAdministrator($id)
    {
        $AdministratorModel = new AdministratorModel();
        
        // Check if administrator exists
        $administrator = $AdministratorModel->find($id);
        if (!$administrator) {
            session()->setFlashdata('error', 'Administrator not found');
            return redirect()->to(base_url('administrator'));
        }
        
        // Check if this is the last super admin (prevent deleting all super admins)
        if ($administrator['Role'] == 1) {
            $superAdminCount = $AdministratorModel->where('Role', 1)->countAllResults();
            if ($superAdminCount <= 1) {
                session()->setFlashdata('error', 'Cannot delete the last Super Admin account');
                return redirect()->to(base_url('administrator'));
            }
        }
        
        // Delete the administrator
        $AdministratorModel->delete($id);
        
        // Redirect with success message
        session()->setFlashdata('success', 'Administrator deleted successfully');
        return redirect()->to(base_url('administrator'));
    }
    
    public function profile(): string
    {
        // Get current admin data from database to ensure it's up-to-date
        $AdministratorModel = new AdministratorModel();
        $adminData = $AdministratorModel->find(session()->get('admin_id'));
        
        $data = [
            'title' => 'Profile',
            'admin_id' => $adminData['AdminID'],
            'admin_name' => $adminData['AdminName'],
            'admin_email' => $adminData['Email'],
            'admin_username' => $adminData['Username'],
            'admin_role' => $adminData['Role'],
            'admin_phone' => $adminData['PhoneNumber'] ?? '',
            'admin_birthdate' => $adminData['Birthdate'] ?? '', 
            'admin_sex' => $adminData['Sex'] ?? '',
            'admin_profile_image' => $adminData['Profile'] ?? '', // Changed to match database column name (uppercase P)
        ];
    
        $viewData = $this->getViewData($data);
    
        return 
        view('template/header') . 
        view('template/sidebar') . 
        view('template/topbar', $viewData) . 
        view('template/footer') . 
        view('Dashboard/profile', $data); // Moved profile view to the end
    }
    
    public function updateProfile()
    {
        $AdministratorModel = new AdministratorModel();
        $adminId = session()->get('admin_id');
        
        // Get current admin data
        $currentAdmin = $AdministratorModel->find($adminId);
        if (!$currentAdmin) {
            session()->setFlashdata('error', 'Admin profile not found');
            return redirect()->to(base_url('profile'));
        }
        
        // Debug: Display received form data
        log_message('debug', 'Received form data: ' . print_r($this->request->getPost(), true));
        log_message('debug', 'Current admin data: ' . print_r($currentAdmin, true));
        
        // Check current password if provided (required for password changes or sensitive info updates)
        $currentPassword = $this->request->getPost('currentPassword');
        $newPassword = $this->request->getPost('password');
        
        // Check if password change is requested
        if (!empty($newPassword)) {
            // Verify current password first
            if (empty($currentPassword)) {
                session()->setFlashdata('error', 'Current password is required to change password');
                return redirect()->to(base_url('profile'));
            }
            
            // Verify current password is correct using password_verify function
            if (!password_verify($currentPassword, $currentAdmin['Password'])) {
                session()->setFlashdata('error', 'Current password is incorrect');
                return redirect()->to(base_url('profile'));
            }
            
            // Validate new password match
            $confirmPassword = $this->request->getPost('confirmPassword');
            if ($newPassword !== $confirmPassword) {
                session()->setFlashdata('error', 'New passwords do not match');
                return redirect()->to(base_url('profile'));
            }
            
            // Hash the new password for security
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        
        // Handle profile image logic
        $profileImage = $currentAdmin['Profile'] ?? null; // Changed to uppercase P to match DB field
        
        // Check if user wants to remove the profile image
        $removeProfilePic = $this->request->getPost('removeProfilePic');
        if ($removeProfilePic == '1') {
            // Delete the existing profile image if it exists
            if (!empty($currentAdmin['Profile'])) { // Changed to uppercase P
                $oldFilePath = ROOTPATH . 'public/uploads/admin_profiles/' . $currentAdmin['Profile']; // Changed to uppercase P
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
            $profileImage = null; // Set to null to remove from database
        } 
        // Check if a new profile image was uploaded
        else {
            $file = $this->request->getFile('profileImage');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Check file type and size
                if (!in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'])) {
                    session()->setFlashdata('error', 'Only JPG, PNG and GIF files are allowed');
                    return redirect()->to(base_url('profile'));
                }
                
                if ($file->getSizeByUnit('mb') > 2) {
                    session()->setFlashdata('error', 'File size must be less than 2MB');
                    return redirect()->to(base_url('profile'));
                }
                
                // Generate a unique name
                $newName = $file->getRandomName();
                
                try {
                    // Move the file to the uploads directory
                    $file->move(ROOTPATH . 'public/uploads/admin_profiles/', $newName);
                    
                    // Delete the old profile image if it exists
                    if (!empty($currentAdmin['Profile'])) { // Changed to uppercase P
                        $oldFilePath = ROOTPATH . 'public/uploads/admin_profiles/' . $currentAdmin['Profile']; // Changed to uppercase P
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                    
                    $profileImage = $newName;
                } catch (\Exception $e) {
                    log_message('error', 'Failed to upload profile image: ' . $e->getMessage());
                    session()->setFlashdata('error', 'Failed to upload profile image');
                    return redirect()->to(base_url('profile'));
                }
            }
        }
        
        // Explicitly cast Sex to ensure it's stored as integer
        $sex = $this->request->getPost('sex');
        $sex = ($sex === '') ? null : (int)$sex;
        
        // Collect data from form submission
        $data = [
            'AdminName' => $this->request->getPost('adminName'),
            'Email' => $this->request->getPost('email'),
            'PhoneNumber' => $this->request->getPost('phone'),
            'Username' => $this->request->getPost('username'),
            'Birthdate' => $this->request->getPost('birthdate') ?: null,
            'Sex' => $sex // Explicitly cast to integer
        ];
        
        // Add profile image if updated or removed
        if ($profileImage !== null) {
            $data['Profile'] = $profileImage; // Changed to uppercase P to match DB field
        } else if ($removeProfilePic == '1') {
            $data['Profile'] = null; // Changed to uppercase P to match DB field
        }
        
        // Add hashed password to data if being changed
        if (!empty($newPassword)) {
            $data['Password'] = $hashedPassword; // Use the hashed password we created earlier
        }
    
        // Debug: Display data to be updated
        log_message('debug', 'Data to update: ' . print_r($data, true));
    
        // Update the admin record - Use try-catch to catch any database errors
        try {
            // Update without validation - we already validated what we need
            $AdministratorModel->skipValidation(true);
            
            $result = $AdministratorModel->update($adminId, $data);
            
            // Debug: Log the result of the update operation
            log_message('debug', 'Update result: ' . var_export($result, true));
            log_message('debug', 'Any errors: ' . print_r($AdministratorModel->errors(), true));
            
            // Re-enable validation
            $AdministratorModel->skipValidation(false);
            
            // Update session data
            session()->set([
                'admin_name' => $data['AdminName'],
                'admin_email' => $data['Email'],
                'admin_username' => $data['Username'],
            ]);
            
            session()->setFlashdata('success', 'Profile updated successfully');
            return redirect()->to(base_url('profile'));
        } catch (\Exception $e) {
            // Log the exception message for debugging
            log_message('error', 'Exception updating profile: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update profile: ' . $e->getMessage());
            return redirect()->to(base_url('profile'));
        }
    }
}
