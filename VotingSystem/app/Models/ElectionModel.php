<?php

namespace App\Models;

use CodeIgniter\Model;

class ElectionModel extends Model
{
    protected $table      = 'election';
    protected $primaryKey = 'ElectionID';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
   

    protected $allowedFields = ['ElectionName', 'Start', 'End', 'Department'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

}