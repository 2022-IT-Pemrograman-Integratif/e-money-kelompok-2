<?php

namespace App\Controllers\Buskimarket;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelBuy_m;
use App\Models\ModelAccount_m;
use App\Models\item_m;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class SeeHistory extends BaseController
{
    use ResponseTrait;
    function __construct()
    {
        $this->ModelAccount_m = new ModelAccount_m();
        $this->ModelBuy_m = new ModelBuy_m();
        $this->item_m = new item_m();
        helper('jwt');
    }

    public function buy()
    {
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = $this->ModelBuy_m->see_history('id_buyer', $data_jwt['data']->id_user);
        $data_send = array();
        foreach ($data as $d) {
            $data_each = [
                "id pembelian" => $d->id_buy,
                "nama item" => $d->itemname,
                "nama seller" => $d->username,
                "nohp seller" => $d->phone,
                "jumlah" => $d->amount,
                "status"  => "pembelian sukses",
                "tanggal"   => $d->date_created
            ];
    
            array_push($data_send, $data_each);
        }
        if(!$data_send){
            $response =[
                'status'    => 200,
                'message'   => 'belum ada item yang dibeli'
            ];
            return $this->respond($response, 200);
        }
        return $this->respond($data_send, 200);
    }

    public function sell()
    {
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = $this->ModelBuy_m->see_history('id_seller', $data_jwt['data']->id_user);
        $data_send = array();
        foreach ($data as $d) {
            $data_each = [
                "id pembelian" => $d->id_buy,
                "nama item" => $d->itemname,
                "nama buyer" => $d->username,
                "nohp buyer" => $d->phone,
                "jumlah" => $d->amount,
                "status"  => "penjualan sukses",
                "tanggal"   => $d->date_created
            ];
    
            array_push($data_send, $data_each);
        }

        if(!$data_send){
            $response =[
                'status'    => 200,
                'message'   => 'belum ada item yang terjual'
            ];
            return $this->respond($response, 200);
        }
        return $this->respond($data_send, 200);
    }
}
