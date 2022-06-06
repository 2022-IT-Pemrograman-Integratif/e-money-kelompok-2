<?php

namespace App\Controllers\Buskimarket;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelBuy_m;
use App\Models\item_m;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class SeeOrder extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $this->ModelBuy_m = new ModelBuy_m();
        helper('jwt');

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        //var_dump($data_jwt['data']->id_user);
        $data = $this->ModelBuy_m->see_order($data_jwt['data']->id_user);
        $data_send = array();
        foreach ($data as $d) {
            $data_each = [
                "id item" => $d->id,
                "nama item" => $d->itemname,
                "nama buyer" => $d->username,
                "nohp buyer" => $d->phone,
                "jumlah" => $d->amount,
                "status"  => "menunggu pengiriman dari seller",
                "tanggal"   => $d->date_created
            ];
    
            array_push($data_send, $data_each);
        }

        return $this->respond($data_send, 200);
    }

    
}
