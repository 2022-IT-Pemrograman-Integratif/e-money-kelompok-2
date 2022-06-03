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
        'id_seller',
        'itemname',
        'stock',
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

    public function see_all()
    {
        $builder = $this->table('item_m');
        $builder->select('*');
        $builder->join('account_m', 'item_m.id_seller = account_m.id_user');
        $query = $builder->get();
        return $query->getResult();
    }
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
    // function updateData($where, $whering, $set, $seting)
    // {
    //     $builder = $this->table('item_m');
    //     $builder->set($set, $seting);
    //     $builder->where($where, $whering);
    //     $builder->update();
    // }
}
