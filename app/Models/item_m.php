<?php

namespace App\Models;

use CodeIgniter\Model;
use Codeigniter\API\ResponseTrait;
use Exception;

class item_m extends Model
{
    use ResponseTrait;
    protected $table = "item_m";
    protected $primaryKey = "id";
    protected $allowedFields = [
        'itemname',
        'price',
    ];

    protected $validationRules = [
        'itemname'  => 'required',
        'price'  => 'required',
    ];

    protected $validationMessages = [
        'itemname'  => [
            'required'  => 'Silahkan masukkan nama barang',

        ],
        'price'  => [
            'required'  => 'Silahkan masukkan harga',
        ],

    ];

    function getDataWhere($where, $whering)
    {
        $builder = $this->table('item_m');
        $builder->where($where, $whering);
        $data = $builder->first();
        if (empty($data)) {
            return "data tidak ada";
        }
        return $data;
    }
    function updateData($where, $whering, $set, $seting)
    {
        $builder = $this->table('item_m');
        $builder->set($set, $seting);
        $builder->where($where, $whering);
        $builder->update();
    }
}
