<?php

namespace App\Controllers;

use App\Models\StudentModel;

class StudentController extends BaseController
{
    protected $studentModel;
    
    public function __construct()
    {
        $this->studentModel = new StudentModel();
    }    
    
    /**
     * Get all students
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function index()
    {
        $students = $this->studentModel->findAll();
        
        $respData = [
            'meta' => [
                'code' => 200,
                'message' => 'Students fetched successfully',
            ],
            'data' => [
                'students' => $students
            ],
        ];
        return $this->response->setJSON($respData);
    }

    /**
     * Handle student login
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function studentLogin()
    {
        $json = $this->request->getJSON();

        // Validate required fields
        if (!isset($json->email) || !isset($json->password)) {
            return $this->response->setJSON([
                'meta' => [
                    'code' => 400,
                    'message' => 'Email and Password are required!',
                ],
                'data' => null,
            ]);
        }

        $email = $json->email;
        $password = $json->password;
        
        // Debug: Log the incoming credentials
        log_message('debug', 'Login attempt with email: ' . $email);

        // Get student by email (case-insensitive)
        $student = $this->studentModel->where('LOWER(Email)', strtolower($email))->first();

        if (!$student) {
            return $this->response->setJSON([
                'meta' => [
                    'code' => 404,
                    'message' => 'No account found with this email',
                ],
                'data' => null,
            ]);
        }
        
        // Debug: Log the retrieved hash
        log_message('debug', 'Retrieved password hash from DB: ' . substr($student['Password'], 0, 10) . '...');

        // Verify password hash
        if (!password_verify($password, $student['Password'])) {
            // Debug: Log verification failure
            log_message('debug', 'Password verification failed for user: ' . $email);
            
            return $this->response->setJSON([
                'meta' => [
                    'code' => 401,
                    'message' => 'Incorrect password. Please try again.',
                    'error_type' => 'wrong_password',
                ],
                'data' => null,
            ]);
        }

        // Debug: Log successful verification
        log_message('debug', 'Password verified successfully for user: ' . $email);

        // Remove password from response
        unset($student['Password']);

        return $this->response->setJSON([
            'meta' => [
                'code' => 200,
                'message' => 'Login successful',
            ],
            'data' => $student,
        ]);
    }

    /**
     * Get a specific student by ID
     *
     * @param int $id Student ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function show($id)
    {
        $student = $this->studentModel->find($id);
        
        if (!$student) {
            $respData = [
                'meta' => [
                    'code' => 404,
                    'message' => 'Student not found',
                ],
                'data' => null,
            ];
            return $this->response->setJSON($respData);
        }
        
        // Remove password hash from response - Fix: use consistent field name
        unset($student['Password']);
        
        // Add department name
        $departmentNames = [
            1 => 'College of Computer Studies',
            2 => 'College of Engineering and Architecture',
            3 => 'College of Health Sciences',
            4 => 'College of Tourism, Hospitality and Business Management',
            5 => 'College of Technological and Developmental Education',
            6 => 'College of Arts and Sciences'
        ];
        $programsByDepartment = [
            1 => [
                1 => ['name' => 'Bachelor of Science in Information Technology', 'acronym' => 'BSIT'],
                2 => ['name' => 'Bachelor of Science in Computer Science', 'acronym' => 'BSCS'],
                3 => ['name' => 'Bachelor of Science in Information Systems', 'acronym' => 'BSIS'],
                4 => ['name' => 'Bachelor of Library Information Science', 'acronym' => 'BLIS'],
            ],
            2 => [
                1 => ['name' => 'Bachelor of Science in Electrical Engineering', 'acronym' => 'BSEE'],
                2 => ['name' => 'Bachelor of Science in Computer Engineering', 'acronym' => 'BSCpE'],
                3 => ['name' => 'Bachelor of Science in Civil Engineering', 'acronym' => 'BSCE'],
                4 => ['name' => 'Bachelor of Science in Electronics Engineering', 'acronym' => 'BSECE'],
                5 => ['name' => 'Bachelor of Science in Mechanical Engineering', 'acronym' => 'BSME'],
                6 => ['name' => 'Bachelor of Science in Architecture', 'acronym' => 'BSA'],
            ],
            3 => [
                1 => ['name' => 'Bachelor of Science in Nursing', 'acronym' => 'BSN'],
                2 => ['name' => 'Bachelor of Science in Midwifery', 'acronym' => 'BSM'],
            ],
            4 => [
                1 => ['name' => 'Bachelor of Science in Tourism Management', 'acronym' => 'BSTM'],
                2 => ['name' => 'Bachelor of Science in Hospitality Management', 'acronym' => 'BSHM'],
                3 => ['name' => 'Bachelor of Science in Office Administration', 'acronym' => 'BSOA'],
                4 => ['name' => 'Bachelor of Science in Entrepreneurship', 'acronym' => 'BSE'],
                5 => ['name' => 'Bachelor of Science in Business Administration major in Financial Management', 'acronym' => 'BSBA-FM'],
            ],
            5 => [
                1 => ['name' => 'Bachelor of Secondary Education', 'acronym' => 'BSEd'],
                2 => ['name' => 'Bachelor of Elementary Education', 'acronym' => 'BEEd'],
                3 => ['name' => 'Bachelor of Technical-Vocational Teacher Education', 'acronym' => 'BTVTE'],
                4 => ['name' => 'Bachelor of Special Needs Education', 'acronym' => 'BSNE'],
                5 => ['name' => 'Bachelor of Physical Education', 'acronym' => 'BPE'],
                6 => ['name' => 'Bachelor of Culture and Arts Education', 'acronym' => 'BCAE'],
            ],
            6 => [
                1 => ['name' => 'Bachelor of Arts in English Language Studies', 'acronym' => 'BAELS'],
                2 => ['name' => 'Bachelor in Human Services', 'acronym' => 'BHS'],
                3 => ['name' => 'Bachelor of Science in Development Communication', 'acronym' => 'BSDC'],
                4 => ['name' => 'Bachelor of Public Administration', 'acronym' => 'BPA'],
                5 => ['name' => 'Bachelor of Science in Mathematics', 'acronym' => 'BSM'],
                6 => ['name' => 'Bachelor of Science in Applied Mathematics', 'acronym' => 'BSAM'],
            ],
        ];
        $sectionNames = [
            '1' => 'A',
            '2' => 'B',
            '3' => 'C',
            '4' => 'D',
            '5' => 'E',
            '6' => 'F',
            '7' => 'G',
            '8' => 'H',
        ];
        $departmentId = isset($student['Department']) ? (int)$student['Department'] : null;
        $courseId = isset($student['Course']) ? (int)$student['Course'] : null;
        $departmentName = $departmentId && isset($departmentNames[$departmentId]) ? $departmentNames[$departmentId] : null;
        $programAcronym = null;
        if ($departmentId && $courseId && isset($programsByDepartment[$departmentId][$courseId])) {
            $programAcronym = $programsByDepartment[$departmentId][$courseId]['acronym'];
        } else {
            $programAcronym = '';
        }
        $gender = isset($student['Gender']) ? ($student['Gender'] == 1 ? 'Male' : 'Female') : null;
        $sectionKey = isset($student['Section']) ? (string)$student['Section'] : null;
        $sectionName = $sectionKey && isset($sectionNames[$sectionKey]) ? $sectionNames[$sectionKey] : $student['Section'];
        $student['sectionName'] = $sectionName;
        $student['departmentName'] = $departmentName;
        $student['programAcronym'] = $programAcronym;
        $student['genderText'] = $gender;
        
        $respData = [
            'meta' => [
                'code' => 200,
                'message' => 'Student fetched successfully',
            ],
            'data' => $student,
        ];
        
        return $this->response->setJSON($respData);
    }

    /**
     * Get all elections with info (REST API)
     * @return \CodeIgniter\HTTP\Response
     */
    public function getAllElectionsInfo()
    {
        $electionModel = new \App\Models\ElectionModel();
        $voteModel = new \App\Models\VoteModel();
        $studentModel = new \App\Models\StudentModel();
        $candidateModel = new \App\Models\CandidateModel();
        $departmentNames = [
            1 => 'College of Computer Studies',
            2 => 'College of Engineering and Architecture',
            3 => 'College of Health Sciences',
            4 => 'College of Tourism, Hospitality and Business Management',
            5 => 'College of Technological and Developmental Education',
            6 => 'College of Arts and Sciences'
        ];
        $elections = $electionModel->findAll();
        $result = [];
        foreach ($elections as $election) {
            $departmentId = $election['Department'];
            $departmentName = $departmentId == 0 ? 'All Departments' : ($departmentNames[$departmentId] ?? 'Unknown');
            // Eligible students: all or by department
            $eligibleStudents = ($departmentId == 0)
                ? $studentModel->countAllResults()
                : $studentModel->where('Department', $departmentId)->countAllResults();
            // Voted students: unique voters in this election
            $votedStudents = $voteModel->getTotalVoters($election['ElectionID']);
            $notVotedStudents = $eligibleStudents - $votedStudents;
            $votedPercentage = ($eligibleStudents > 0) ? round(($votedStudents / $eligibleStudents) * 100) : 0;
            $notVotedPercentage = 100 - $votedPercentage;
            // Get all candidates for this election
            $candidates = $candidateModel->where('Election', $election['ElectionID'])->findAll();
            // Get vote counts for each candidate
            $votes = $voteModel->getVotesByCandidate($election['ElectionID']);
            $votesByCandidate = [];
            foreach ($votes as $vote) {
                $votesByCandidate[$vote['CandidateID']] = $vote['voteCount'];
            }
            foreach ($candidates as &$candidate) {
                $candidate['voteCount'] = $votesByCandidate[$candidate['CandidateID']] ?? 0;
            }
            $result[] = [
                'election' => $election,
                'departmentName' => $departmentName,
                'turnout' => [
                    'eligibleStudents' => $eligibleStudents,
                    'votedStudents' => $votedStudents,
                    'notVotedStudents' => $notVotedStudents,
                    'votedPercentage' => $votedPercentage,
                    'notVotedPercentage' => $notVotedPercentage,
                ],
                'candidates' => $candidates,
            ];
        }
        return $this->response->setJSON([
            'meta' => [
                'code' => 200,
                'message' => 'All elections info fetched successfully',
            ],
            'data' => $result,
        ]);
    }

    /**
     * Get a specific election info (REST API)
     * @param int $id Election ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function getElectionInfo($id)
    {
        $electionModel = new \App\Models\ElectionModel();
        $voteModel = new \App\Models\VoteModel();
        $studentModel = new \App\Models\StudentModel();
        $candidateModel = new \App\Models\CandidateModel();
        $departmentNames = [
            1 => 'College of Computer Studies',
            2 => 'College of Engineering and Architecture',
            3 => 'College of Health Sciences',
            4 => 'College of Tourism, Hospitality and Business Management',
            5 => 'College of Technological and Developmental Education',
            6 => 'College of Arts and Sciences'
        ];
        $election = $electionModel->find($id);
        if (!$election) {
            return $this->response->setJSON([
                'meta' => [
                    'code' => 404,
                    'message' => 'Election not found',
                ],
                'data' => null,
            ]);
        }
        $departmentId = $election['Department'];
        $departmentName = $departmentId == 0 ? 'All Departments' : ($departmentNames[$departmentId] ?? 'Unknown');
        $eligibleStudents = ($departmentId == 0)
            ? $studentModel->countAllResults()
            : $studentModel->where('Department', $departmentId)->countAllResults();
        $votedStudents = $voteModel->getTotalVoters($election['ElectionID']);
        $notVotedStudents = $eligibleStudents - $votedStudents;
        $votedPercentage = ($eligibleStudents > 0) ? round(($votedStudents / $eligibleStudents) * 100) : 0;
        $notVotedPercentage = 100 - $votedPercentage;
        $candidates = $candidateModel->where('Election', $election['ElectionID'])->findAll();

        // Add partylist name and position name for each candidate
        $partylistModel = new \App\Models\PartylistModel();
        $positionModel = new \App\Models\PositionModel();
        foreach ($candidates as &$candidate) {
            // Partylist Name
            $partylistName = null;
            if (!empty($candidate['Partylist'])) {
                $partylist = $partylistModel->find($candidate['Partylist']);
                $partylistName = ($partylist && isset($partylist['Name'])) ? $partylist['Name'] : null;
            }
            $candidate['PartylistName'] = $partylistName;
            // Position Name
            $positionName = null;
            if (!empty($candidate['Position'])) {
                $position = $positionModel->find($candidate['Position']);
                $positionName = ($position && isset($position['PositionName'])) ? $position['PositionName'] : null;
            }
            $candidate['PositionName'] = $positionName;
            // Build full profile URL if Profile is set
            $profileUrl = null;
            if (!empty($candidate['Profile'])) {
                $baseUrl = isset($_SERVER['REQUEST_SCHEME']) && isset($_SERVER['HTTP_HOST'])
                    ? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
                    : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
                $profileUrl = $baseUrl . '/uploads/profiles/' . $candidate['Profile'];
            }
            $candidate['ProfileUrl'] = $profileUrl;
        }

        $votes = $voteModel->getVotesByCandidate($election['ElectionID']);
        $votesByCandidate = [];
        foreach ($votes as $vote) {
            $votesByCandidate[$vote['CandidateID']] = $vote['voteCount'];
        }
        foreach ($candidates as &$candidate) {
            $candidate['voteCount'] = $votesByCandidate[$candidate['CandidateID']] ?? 0;
        }
        $result = [
            'election' => $election,
            'departmentName' => $departmentName,
            'turnout' => [
                'eligibleStudents' => $eligibleStudents,
                'votedStudents' => $votedStudents,
                'notVotedStudents' => $notVotedStudents,
                'votedPercentage' => $votedPercentage,
                'notVotedPercentage' => $notVotedPercentage,
            ],
            'candidates' => $candidates,
        ];
        return $this->response->setJSON([
            'meta' => [
                'code' => 200,
                'message' => 'Election info fetched successfully',
            ],
            'data' => $result,
        ]);
    }

    /**
     * Get all candidates with their election info, partylist name, position name, and election name (REST API)
     * Returns: CandidateID, FirstName, MiddleName, LastName, Position, PositionName, Partylist, PartylistName, Election, ElectionName, Profile, Platform, and election details
     * @return \CodeIgniter\HTTP\Response
     */
    public function getAllCandidatesWithElectionInfo()
    {
        $candidateModel = new \App\Models\CandidateModel();
        $electionModel = new \App\Models\ElectionModel();
        $partylistModel = new \App\Models\PartylistModel();
        $positionModel = new \App\Models\PositionModel();
        $candidates = $candidateModel->findAll();
        $result = [];
        foreach ($candidates as $candidate) {
            $election = $electionModel->find($candidate['Election']);
            $electionName = $election && isset($election['ElectionName']) ? $election['ElectionName'] : null;
            $partylistName = null;
            if (!empty($candidate['Partylist'])) {
                $partylist = $partylistModel->find($candidate['Partylist']);
                $partylistName = ($partylist && isset($partylist['Name'])) ? $partylist['Name'] : null;
            }
            $positionName = null;
            if (!empty($candidate['Position'])) {
                $position = $positionModel->find($candidate['Position']);
                $positionName = ($position && isset($position['PositionName'])) ? $position['PositionName'] : null;
            }
            // Build full profile URL if Profile is set
            $profileUrl = null;
            if (!empty($candidate['Profile'])) {
                $baseUrl = isset($_SERVER['REQUEST_SCHEME']) && isset($_SERVER['HTTP_HOST'])
                    ? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
                    : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
                $profileUrl = $baseUrl . '/uploads/profiles/' . $candidate['Profile'];
            }
            $result[] = [
                'CandidateID' => $candidate['CandidateID'],
                'FirstName' => $candidate['FirstName'],
                'MiddleName' => $candidate['MiddleName'],
                'LastName' => $candidate['LastName'],
                'Position' => $candidate['Position'],
                'PositionName' => $positionName,
                'Partylist' => $candidate['Partylist'],
                'PartylistName' => $partylistName,
                'Election' => $candidate['Election'],
                'ElectionName' => $electionName,
                'Profile' => $candidate['Profile'],
                'ProfileUrl' => $profileUrl,
                'Platform' => $candidate['Platform'],
                'election' => $election,
            ];
        }
        return $this->response->setJSON([
            'meta' => [
                'code' => 200,
                'message' => 'All candidates with election info fetched successfully',
            ],
            'data' => $result,
        ]);
    }

    /**
     * Get a specific candidate with all details (REST API)
     * @param int $id Candidate ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function getCandidateWithElectionInfo($id)
    {
        $candidateModel = new \App\Models\CandidateModel();
        $electionModel = new \App\Models\ElectionModel();
        $partylistModel = new \App\Models\PartylistModel();
        $positionModel = new \App\Models\PositionModel();
        $candidate = $candidateModel->find($id);
        if (!$candidate) {
            return $this->response->setJSON([
                'meta' => [
                    'code' => 404,
                    'message' => 'Candidate not found',
                ],
                'data' => null,
            ]);
        }
        $election = $electionModel->find($candidate['Election']);
        $electionName = $election && isset($election['ElectionName']) ? $election['ElectionName'] : null;
        $partylistName = null;
        if (!empty($candidate['Partylist'])) {
            $partylist = $partylistModel->find($candidate['Partylist']);
            $partylistName = ($partylist && isset($partylist['Name'])) ? $partylist['Name'] : null;
        }
        $positionName = null;
        if (!empty($candidate['Position'])) {
            $position = $positionModel->find($candidate['Position']);
            $positionName = ($position && isset($position['PositionName'])) ? $position['PositionName'] : null;
        }
        // Build full profile URL if Profile is set
        $profileUrl = null;
        if (!empty($candidate['Profile'])) {
            $baseUrl = isset($_SERVER['REQUEST_SCHEME']) && isset($_SERVER['HTTP_HOST'])
                ? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
                : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
            $profileUrl = $baseUrl . '/uploads/profiles/' . $candidate['Profile'];
        }
        $result = [
            'CandidateID' => $candidate['CandidateID'],
            'FirstName' => $candidate['FirstName'],
            'MiddleName' => $candidate['MiddleName'],
            'LastName' => $candidate['LastName'],
            'Position' => $candidate['Position'],
            'PositionName' => $positionName,
            'Partylist' => $candidate['Partylist'],
            'PartylistName' => $partylistName,
            'Election' => $candidate['Election'],
            'ElectionName' => $electionName,
            'Profile' => $candidate['Profile'],
            'ProfileUrl' => $profileUrl,
            'Platform' => $candidate['Platform'],
            'election' => $election,
        ];
        return $this->response->setJSON([
            'meta' => [
                'code' => 200,
                'message' => 'Candidate info fetched successfully',
            ],
            'data' => $result,
        ]);
    }

    /**
     * Cast a vote (REST API)
     * Expects: StudentID, ElectionID, votes: [{ CandidateID, PositionID }]
     * @return \CodeIgniter\HTTP\Response
     */
    public function castVote()
    {
        $voteModel = new \App\Models\VoteModel();
        $studentModel = new \App\Models\StudentModel();
        $electionModel = new \App\Models\ElectionModel();
        $candidateModel = new \App\Models\CandidateModel();

        $json = $this->request->getJSON();
        if (!$json || !isset($json->StudentID) || !isset($json->ElectionID) || !isset($json->votes) || !is_array($json->votes)) {
            return $this->response->setJSON([
                'meta' => [ 'code' => 400, 'message' => 'Missing required fields.' ],
                'data' => null,
            ]);
        }
        
        // Convert to integers to ensure proper type matching with database
        $studentId = (int)$json->StudentID;
        $electionId = (int)$json->ElectionID;
        $votes = $json->votes;

        // Log incoming data for debugging
        log_message('debug', 'Received vote request - Student ID: ' . $studentId . ', Election ID: ' . $electionId);
        log_message('debug', 'Votes payload: ' . json_encode($votes));

        // Check if student exists
        $student = $studentModel->find($studentId);
        if (!$student) {
            log_message('error', 'Student not found: ' . $studentId);
            return $this->response->setJSON([
                'meta' => [ 'code' => 404, 'message' => 'Student not found.' ],
                'data' => null,
            ]);
        }
        
        // Check if election exists
        $election = $electionModel->find($electionId);
        if (!$election) {
            log_message('error', 'Election not found: ' . $electionId);
            return $this->response->setJSON([
                'meta' => [ 'code' => 404, 'message' => 'Election not found.' ],
                'data' => null,
            ]);
        }
        
        // Prevent double voting - use a transaction to ensure data integrity
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // First check if the student has already voted in this election
            $existingVotes = $voteModel->where([
                'StudentID' => $studentId,
                'ElectionID' => $electionId
            ])->countAllResults();
            
            if ($existingVotes > 0) {
                $db->transRollback();
                log_message('notice', 'Double voting attempt - Student: ' . $studentId . ', Election: ' . $electionId);
                return $this->response->setJSON([
                    'meta' => [ 'code' => 409, 'message' => 'You have already voted in this election.' ],
                    'data' => null,
                ]);
            }
            
            // Save votes
            $success = true;
            foreach ($votes as $vote) {
                // Require PositionID in each vote
                if (!isset($vote->PositionID)) {
                    log_message('error', 'Invalid vote data - missing PositionID');
                    $success = false;
                    break;
                }
                
                $voteData = [
                    'StudentID'   => $studentId,
                    'CandidateID' => $vote->CandidateID,
                    'PositionID'  => $vote->PositionID,
                    'ElectionID'  => $electionId,
                    'IsAbstain'   => ($vote->CandidateID == 0) ? 1 : 0,
                    'TimeVoted'   => date('Y-m-d H:i:s'),
                ];
                
                if (!$voteModel->insert($voteData)) {
                    $success = false;
                    break;
                }
            }
            
            if (!$success) {
                $db->transRollback();
                return $this->response->setJSON([
                    'meta' => [ 'code' => 400, 'message' => 'Invalid vote data.' ],
                    'data' => null,
                ]);
            }
            
            // Commit the transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                log_message('error', 'Transaction failed when saving votes');
                return $this->response->setJSON([
                    'meta' => [ 'code' => 500, 'message' => 'Failed to save votes due to database error.' ],
                    'data' => null,
                ]);
            }
            
            log_message('info', 'Vote cast successfully - Student: ' . $studentId . ', Election: ' . $electionId);
            return $this->response->setJSON([
                'meta' => [ 'code' => 200, 'message' => 'Vote cast successfully.' ],
                'data' => null,
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Exception when casting vote: ' . $e->getMessage());
            return $this->response->setJSON([
                'meta' => [ 'code' => 500, 'message' => 'An error occurred while processing your vote.' ],
                'data' => null,
            ]);
        }
    }

    /**
     * Check if a student has already voted in a specific election
     * @param int $studentId Student ID
     * @param int $electionId Election ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function checkVotingStatus($studentId, $electionId)
    {
        $voteModel = new \App\Models\VoteModel();
        $studentModel = new \App\Models\StudentModel();
        $electionModel = new \App\Models\ElectionModel();

        // Check if student exists
        $student = $studentModel->find($studentId);
        if (!$student) {
            return $this->response->setJSON([
                'meta' => [
                    'code' => 404,
                    'message' => 'Student not found',
                ],
                'data' => null,
            ]);
        }

        // Check if election exists
        $election = $electionModel->find($electionId);
        if (!$election) {
            return $this->response->setJSON([
                'meta' => [
                    'code' => 404,
                    'message' => 'Election not found',
                ],
                'data' => null,
            ]);
        }

        // Check if student has already voted
        $hasVoted = $voteModel->where([
            'StudentID' => $studentId,
            'ElectionID' => $electionId
        ])->countAllResults() > 0;

        return $this->response->setJSON([
            'meta' => [
                'code' => 200,
                'message' => 'Voting status checked successfully',
            ],
            'data' => [
                'hasVoted' => $hasVoted
            ],
        ]);
    }

    /**
     * Update student profile (REST API)
     * Expects: StudentID, FirstName, MiddleName, LastName, Email, PhoneNumber, current_password (optional), new_password (optional), profileImage (optional)
     * @return \CodeIgniter\HTTP\Response
     */
    public function updateProfile()
    {
        $studentModel = new \App\Models\StudentModel();
        
        // Get JSON data from request
        $json = $this->request->getJSON();
        
        // Validate required fields
        if (!isset($json->StudentID)) {
            return $this->response->setJSON([
                'meta' => [
                    'code' => 400,
                    'message' => 'Student ID is required',
                ],
                'data' => null,
            ]);
        }

        $studentId = (int)$json->StudentID;
        
        // Check if student exists
        $student = $studentModel->find($studentId);
        if (!$student) {
            return $this->response->setJSON([
                'meta' => [
                    'code' => 404,
                    'message' => 'Student not found',
                ],
                'data' => null,
            ]);
        }

        // Prepare update data
        $updateData = [];
        
        // Only update fields that are provided
        $allowedFields = ['FirstName', 'MiddleName', 'LastName', 'Email', 'PhoneNumber'];
        foreach ($allowedFields as $field) {
            if (isset($json->$field)) {
                $updateData[$field] = $json->$field;
            }
        }

        // Handle password update if provided
        if (isset($json->current_password) && isset($json->new_password)) {
            // Verify current password
            if (!password_verify($json->current_password, $student['Password'])) {
                return $this->response->setJSON([
                    'meta' => [
                        'code' => 401,
                        'message' => 'Current password is incorrect',
                    ],
                    'data' => null,
                ]);
            }
            
            // Validate new password
            if (strlen($json->new_password) < 6) {
                return $this->response->setJSON([
                    'meta' => [
                        'code' => 400,
                        'message' => 'New password must be at least 6 characters long',
                    ],
                    'data' => null,
                ]);
            }
            
            $updateData['Password'] = password_hash($json->new_password, PASSWORD_DEFAULT);
        }

        // Handle profile image upload if provided
        if ($this->request->getFile('profileImage')) {
            $file = $this->request->getFile('profileImage');
            
            // Validate file
            if (!$file->isValid() || $file->hasMoved()) {
                return $this->response->setJSON([
                    'meta' => [
                        'code' => 400,
                        'message' => 'Invalid file upload',
                    ],
                    'data' => null,
                ]);
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return $this->response->setJSON([
                    'meta' => [
                        'code' => 400,
                        'message' => 'Only JPG, PNG and GIF files are allowed',
                    ],
                    'data' => null,
                ]);
            }

            // Validate file size (max 2MB)
            if ($file->getSize() > 2 * 1024 * 1024) {
                return $this->response->setJSON([
                    'meta' => [
                        'code' => 400,
                        'message' => 'File size must be less than 2MB',
                    ],
                    'data' => null,
                ]);
            }

            // Generate unique filename
            $newName = $file->getRandomName();
            
            // Move file to uploads directory
            $file->move(WRITEPATH . 'uploads/profiles', $newName);
            
            // Delete old profile image if exists
            if (!empty($student['Profile'])) {
                $oldFile = WRITEPATH . 'uploads/profiles/' . $student['Profile'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            
            $updateData['Profile'] = $newName;
        }

        // If no fields to update
        if (empty($updateData)) {
            return $this->response->setJSON([
                'meta' => [
                    'code' => 400,
                    'message' => 'No fields to update',
                ],
                'data' => null,
            ]);
        }

        // Update student profile
        if ($studentModel->update($studentId, $updateData) === false) {
            return $this->response->setJSON([
                'meta' => [
                    'code' => 400,
                    'message' => 'Failed to update profile: ' . implode(', ', $studentModel->errors()),
                ],
                'data' => null,
            ]);
        }

        // Get updated student data
        $updatedStudent = $studentModel->find($studentId);
        
        // Remove password from response
        unset($updatedStudent['Password']);
        
        // Add profile URL if profile image exists
        if (!empty($updatedStudent['Profile'])) {
            $baseUrl = isset($_SERVER['REQUEST_SCHEME']) && isset($_SERVER['HTTP_HOST'])
                ? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
                : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
            $updatedStudent['ProfileUrl'] = $baseUrl . '/uploads/profiles/' . $updatedStudent['Profile'];
        }

        return $this->response->setJSON([
            'meta' => [
                'code' => 200,
                'message' => 'Profile updated successfully',
            ],
            'data' => $updatedStudent,
        ]);
    }

}