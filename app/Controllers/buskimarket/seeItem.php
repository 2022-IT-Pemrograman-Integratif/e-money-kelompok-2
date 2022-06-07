<?php

namespace App\Controllers\Buskimarket;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\item_m;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class SeeItem extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $this->item_m = new item_m();
        $data = $this->item_m->see_all();

        $data_send = array();
        foreach ($data as $d) {
            $data_each = [
                "id item" => $d->id_item,
                "nama seller" => $d->username,
                "phone" => $d->phone,
                "itemname" => $d->itemname,
                "price" => $d->price,
                "stock" => $d->stock,
            ];
            array_push($data_send, $data_each);
        }

        return $this->respond($data_send, 200);
    }
}
