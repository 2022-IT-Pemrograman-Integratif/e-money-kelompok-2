<?php

namespace App\Controllers\Buskimarket;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelBuy_m;
use App\Models\item_m;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class SeeBuytoConfirm extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $this->ModelBuy_m = new ModelBuy_m();
        helper('jwt');

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        //var_dump($data_jwt['data']->id_user);
        $data = $this->ModelBuy_m->see_buy($data_jwt['data']->id_user);
        $data_send = array();
        foreach ($data as $d) {
            $data_each = [
                "id pembelian" => $d->id_buy,
                "nama item" => $d->itemname,
                "nama seller" => $d->username,
                "nohp seller" => $d->phone,
                "jumlah" => $d->amount,
                "status"  => "menunggu konfirmasi dari buyer",
                "tanggal"   => $d->date_created
            ];
    
            array_push($data_send, $data_each);
        }
        if(!$data_send){
            $response =[
                'status'    => 200,
                'message'   => 'belum ada item yang datang'
            ];
            return $this->respond($response, 200);
        }
        return $this->respond($data_send, 200);
    }
}
