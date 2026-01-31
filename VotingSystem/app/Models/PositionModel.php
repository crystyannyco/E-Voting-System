<?php

namespace App\Models;

use CodeIgniter\Model;

class PositionModel extends Model
{
    protected $table      = 'position';
    protected $primaryKey = 'PositionID';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
   

    protected $allowedFields = ['PositionName'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

}