<?php

namespace App\Models;

use CodeIgniter\Model;

class PartylistModel extends Model
{
    protected $table      = 'partylist';
    protected $primaryKey = 'PartylistID';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
   

    protected $allowedFields = ['Name'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

}