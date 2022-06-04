<?php

namespace App\Models;

use CodeIgniter\Model;
use Codeigniter\API\ResponseTrait;
use Exception;

class ModelBuy_m extends Model
{
    use ResponseTrait;
    protected $table = "buy_m";
    protected $primaryKey = "id";
    protected $allowedFields = [
        'id_seller',
        'id_buyer',
        'id_item',
        'amount',
        'emoney',
        'status'
    ];

    protected $validationRules = [
        'id_seller'  => 'required',
        'id_buyer'  => 'required',
        'id_item'  => 'required',
        'amount'  => 'required',
        'emoney'  => 'required',
        'status'  => 'required',
    ];

    

    // function getDataWhere($where, $whering)
    // {
    //     $builder = $this->table('item_m');
    //     $builder->where($where, $whering);
    //     $data = $builder->first();
    //     if (empty($data)) {
    //         return "data tidak ada";
    //     }
    //     return $data;
    // }
    // function updateData($where, $whering, $set, $seting)
    // {
    //     $builder = $this->table('item_m');
    //     $builder->set($set, $seting);
    //     $builder->where($where, $whering);
    //     $builder->update();
    // }
}
