<?php
namespace App\Models;

use CodeIgniter\Model;

class CandidateModel extends Model
{
    protected $table = 'candidate';
    protected $primaryKey = 'CandidateID';
    protected $allowedFields = ['FirstName', 'MiddleName', 'LastName', 'Partylist', 'Position', 'Election', 'Platform', 'Profile'];
    
    // Get candidate with details from related tables
    public function getCandidatesWithDetails()
    {
        $builder = $this->db->table('candidate');
        $builder->select('candidate.*, partylist.Name as PartylistName, position.PositionName, election.ElectionName');
        $builder->join('partylist', 'partylist.PartylistID = candidate.Partylist', 'left');
        $builder->join('position', 'position.PositionID = candidate.Position', 'left');
        $builder->join('election', 'election.ElectionID = candidate.Election', 'left'); // assuming foreign key
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get a specific candidate with all details (for API)
     * @param int $id
     * @return array|null
     */
    public function getCandidateWithDetails($id)
    {
        $builder = $this->db->table('candidate');
        $builder->select('candidate.*, partylist.Name as PartylistName, position.PositionName, election.ElectionName');
        $builder->join('partylist', 'partylist.PartylistID = candidate.Partylist', 'left');
        $builder->join('position', 'position.PositionID = candidate.Position', 'left');
        $builder->join('election', 'election.ElectionID = candidate.Election', 'left');
        $builder->where('candidate.CandidateID', $id);
        return $builder->get()->getRowArray();
    }
}