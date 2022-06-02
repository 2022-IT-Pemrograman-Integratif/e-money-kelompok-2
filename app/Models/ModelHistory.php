<?php

namespace App\Models;

use CodeIgniter\Model;
use Codeigniter\API\ResponseTrait;
use Exception;

class ModelHistory extends Model
{
    use ResponseTrait;
    protected $table = "history";
    protected $primaryKey = "history_id";
    protected $allowedFields = [
        'sender_id',
        'reciever_id',
        'history_type',
        'history_amount',
        'history_description'
    ];

    protected $validationRules = [
        'sender_id'  => 'required',
        'reciever_id'  => 'required'
    ];

    protected $validationMessages = [
        'sender_id'  => [
            'required'  => 'sender id is needed'
        ],
        'reciever_id'  => [
            'required'  => 'reciever id is needed'
        ]
    ];

    

    function getDataWhere($where, $whering)
    {
        $builder = $this->table('history');
        $builder->where($where, $whering);
        $data = $builder->first();
        if (empty($data)) {
            return "data tidak ada";
        }
        return $data;
    }
}
