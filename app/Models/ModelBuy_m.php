<?php

namespace App\Models;

use CodeIgniter\Model;
use Codeigniter\API\ResponseTrait;
use Exception;

class ModelBuy_m extends Model
{
    use ResponseTrait;
    protected $table = "buy_m";
    protected $primaryKey = "id_buy";
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

    public function see_order($id_seller)
    {
        $builder = $this->table('buy_m');
        $builder->select('*');
        $builder->join('account_m', 'account_m.id_user = buy_m.id_buyer', 'LEFT');
        $builder->join('item_m', 'item_m.id_item = buy_m.id_item', 'LEFT');
        $builder->where('buy_m.id_seller', $id_seller);
        $builder->where('buy_m.status', '1');
        $query = $builder->get();
        return $query->getResult();
    }
    
    public function see_buy($id_buyer)
    {
        $builder = $this->table('buy_m');
        $builder->select('*');
        $builder->join('account_m', 'account_m.id_user = buy_m.id_seller', 'LEFT');
        $builder->join('item_m', 'item_m.id_item = buy_m.id_item', 'LEFT');
        $builder->where('buy_m.id_buyer', $id_buyer);
        $builder->where('buy_m.status', '2');
        $query = $builder->get();
        return $query->getResult();
    }

    public function cek_buy($id_buy, $id_seller)
    {
        $builder = $this->table('buy_m');
        $builder->where("id_buy", $id_buy);
        $builder->where("id_seller", $id_seller);
        $builder->where("status", 1);
        $data = $builder->first();
        if (empty($data)) {
            return "data tidak ada";
        }
        return $data;
    }

    public function cek_confirm($id_buy, $id_buyer)
    {
        $builder = $this->table('buy_m');
        $builder->where("id_buy", $id_buy);
        $builder->where("id_buyer", $id_buyer);
        $builder->where("status", 2);
        $data = $builder->first();
        if (empty($data)) {
            return "data tidak ada";
        }
        return $data;
    }
    public function see_history($as_what, $id_as_what)
    {
        $builder = $this->table('buy_m');
        $builder->where($as_what, $id_as_what);
        $builder->where("status", 3);
        if($as_what == 'id_buyer'){
            $builder->select('*');
            $builder->join('account_m', 'account_m.id_user = buy_m.id_seller', 'LEFT');
            $builder->join('item_m', 'item_m.id_item = buy_m.id_item', 'LEFT');
        }else{
            $builder->select('*');
            $builder->join('account_m', 'account_m.id_user = buy_m.id_buyer', 'LEFT');
            $builder->join('item_m', 'item_m.id_item = buy_m.id_item', 'LEFT');
        }
        $query = $builder->get();
        return $query->getResult();
    }


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
    function updateData($where, $whering, $set, $seting)
    {
        $builder = $this->table('buy_m');
        $builder->set($set, $seting);
        $builder->where($where, $whering);
        $builder->update();
    }
}
