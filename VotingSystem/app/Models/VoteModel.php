<?php

namespace App\Models;

use CodeIgniter\Model;

class VoteModel extends Model
{
    protected $table      = 'votes';
    protected $primaryKey = 'VoteID';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'StudentID',
        'CandidateID',
        'PositionID',
        'ElectionID',
        'IsAbstain',
        'TimeVoted'
    ];
    
    // Dates
    protected $useTimestamps = false;
    
    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    /**
     * Get total unique students who voted in the election (any position)
     */
    public function getTotalVoters($electionId)
    {
        $builder = $this->db->table('votes v')
            ->select('COUNT(DISTINCT v.StudentID) as total')
            ->where('v.ElectionID', $electionId);
        
        $result = $builder->get()->getRow();
        return $result ? $result->total : 0;
    }
    
    public function getVotesByCandidate($electionId, $includePercentages = false)
    {
        // First, get the vote counts for regular candidates
        $builder = $this->db->table('votes v')
            ->select('v.CandidateID, v.PositionID, COUNT(DISTINCT v.StudentID) as voteCount')
            ->where('v.ElectionID', $electionId)
            ->where('v.IsAbstain', 0)
            ->groupBy('v.CandidateID, v.PositionID');
        
        $result = $builder->get()->getResultArray();
        
        // Get abstain votes separately
        $abstainBuilder = $this->db->table('votes v')
            ->select('v.PositionID, COUNT(DISTINCT v.StudentID) as voteCount')
            ->where('v.ElectionID', $electionId)
            ->where('v.IsAbstain', 1)
            ->groupBy('v.PositionID');
            
        $abstainVotes = $abstainBuilder->get()->getResultArray();
        
        // Convert abstain votes to the same format as regular votes
        foreach ($abstainVotes as $abstain) {
            $result[] = [
                'CandidateID' => null,
                'PositionID' => $abstain['PositionID'],
                'voteCount' => $abstain['voteCount'],
                'isAbstain' => true
            ];
        }
        
        // If percentages are requested, calculate them based on position totals
        if ($includePercentages) {
            // Get total votes per position (including abstains)
            $positionTotals = $this->getPositionVoteCounts($electionId);
            
            foreach ($result as &$row) {
                $positionId = $row['PositionID'];
                $totalPositionVotes = $positionTotals[$positionId] ?? 0;
                $row['percentage'] = ($totalPositionVotes > 0) ? 
                    round(($row['voteCount'] / $totalPositionVotes) * 100) : 0;
            }
        }
        
        return $result;
    }

    /**
     * Get total votes per position for an election
     */
    private function getPositionVoteCounts($electionId)
    {
        $builder = $this->db->table('votes v')
            ->select('v.PositionID, COUNT(DISTINCT v.StudentID) as total')
            ->where('v.ElectionID', $electionId)
            ->groupBy('v.PositionID');
            
        $result = $builder->get()->getResultArray();
        
        $totals = [];
        foreach ($result as $row) {
            $totals[$row['PositionID']] = $row['total'];
        }
        
        return $totals;
    }

    public function getAbstainVotesByPosition($electionId)
    {
        $builder = $this->db->table('votes v')
            ->select('v.PositionID, COUNT(DISTINCT v.StudentID) as voteCount')
            ->where('v.ElectionID', $electionId)
            ->where('v.IsAbstain', 1)
            ->groupBy('v.PositionID');
        
        return $builder->get()->getResultArray();
    }
    /**
     * Get eligible voter counts by position
     * 
     * @param int $electionId The ID of the election
     * @param int $departmentId The department ID (0 for all departments)
     * @return array Array of position IDs with their eligible voter counts
     */
    public function getEligibleVotersByPosition($electionId, $departmentId)
    {
        $StudentModel = new \App\Models\StudentModel();
        $PositionModel = new \App\Models\PositionModel();
        
        // Get all positions for this election
        // The issue is here - there's no 'ElectionID' column in the position table
        // or the alias 'pe' is incorrect
        $positions = $PositionModel->findAll(); // Get all positions instead
        
        $result = [];
        
        foreach ($positions as $position) {
            $positionId = $position['PositionID'];
            
            // If department is 0, count all students, otherwise count students in that department
            if ($departmentId == 0) {
                $eligibleCount = $StudentModel->countAllResults();
            } else {
                $eligibleCount = $StudentModel->where('Department', $departmentId)->countAllResults();
            }
            
            $result[$positionId] = $eligibleCount;
        }
        
        return $result;
    }

    /**
     * Verify that a student can only vote in their department's elections or all-department elections
     * 
     * @param int $studentId The student ID
     * @param int $electionId The election ID
     * @return bool True if verification passes, false if not
     */
    public function verifyStudentVoteByDepartment($studentId, $electionId)
    {
        $studentModel = new \App\Models\StudentModel();
        $electionModel = new \App\Models\ElectionModel();
        
        $student = $studentModel->find($studentId);
        $election = $electionModel->find($electionId);
        
        if (!$student || !$election) {
            return false;
        }
        
        // Check if student has already voted in this election
        $existingVote = $this->db->table('votes v')
            ->select('v.VoteID')
            ->join('candidate c', 'v.CandidateID = c.CandidateID')
            ->where('c.Election', $electionId)
            ->where('v.StudentID', $studentId)
            ->limit(1)
            ->get()
            ->getRow();
            
        if ($existingVote) {
            return false; // Student has already voted
        }
        
        // Verify department eligibility
        return $studentModel->isEligibleForElection($studentId, $election['Department']);
    }
    
    /**
     * Get vote statistics by election and department
     * 
     * @param int $electionId The election ID
     * @return array Statistics including department breakdown
     */
    public function getVoteStatisticsByDepartment($electionId)
    {
        $studentModel = new \App\Models\StudentModel();
        $electionModel = new \App\Models\ElectionModel();
        
        $election = $electionModel->find($electionId);
        
        if (!$election) {
            return [
                'success' => false,
                'message' => 'Election not found'
            ];
        }
        
        $studentsByDept = $studentModel->countStudentsByDepartment();
        $stats = [
            'electionInfo' => $election,
            'totalStudents' => $studentsByDept['total'],
            'departmentBreakdown' => [],
            'eligibleStudents' => 0,
            'votedStudents' => 0,
            'votedPercentage' => 0
        ];
        
        // If election is for specific department, count only that department
        if ($election['Department'] > 0) {
            $stats['eligibleStudents'] = $studentsByDept[$election['Department']] ?? 0;
        } else {
            // All departments are eligible
            $stats['eligibleStudents'] = $studentsByDept['total'];
        }
        
        // Get total unique voters for this election
        $votedStudents = $this->getTotalVoters($electionId);
        $stats['votedStudents'] = $votedStudents;
        
        // Calculate percentage
        if ($stats['eligibleStudents'] > 0) {
            $stats['votedPercentage'] = round(($votedStudents / $stats['eligibleStudents']) * 100);
        }
        
        // If this is an all-department election, get detailed breakdown
        if ($election['Department'] == 0) {
            // Get all departments
            $departments = $this->db->table('student')
                                ->distinct()
                                ->select('Department')
                                ->get()
                                ->getResultArray();
            
            foreach ($departments as $dept) {
                $deptId = $dept['Department'];
                
                // Skip if department ID is missing
                if ($deptId === null || $deptId === '') continue;
                
                // Count students in this department
                $totalInDept = $studentsByDept[$deptId] ?? 0;
                
                // Count students from this department who voted in this election
                $votedFromDept = $this->db->table('votes v')
                    ->select('COUNT(DISTINCT v.StudentID) as voted')
                    ->join('candidate c', 'v.CandidateID = c.CandidateID')
                    ->join('student s', 'v.StudentID = s.StudentID')
                    ->where('c.Election', $electionId)
                    ->where('s.Department', $deptId)
                    ->get()
                    ->getRow();
                    
                $votedCount = $votedFromDept ? $votedFromDept->voted : 0;
                $votePercentage = ($totalInDept > 0) ? round(($votedCount / $totalInDept) * 100) : 0;
                
                $stats['departmentBreakdown'][$deptId] = [
                    'totalStudents' => $totalInDept,
                    'votedStudents' => $votedCount,
                    'votedPercentage' => $votePercentage
                ];
            }
        }
        
        return $stats;
    }

}