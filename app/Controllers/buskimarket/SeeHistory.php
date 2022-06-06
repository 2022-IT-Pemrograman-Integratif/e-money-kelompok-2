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

        $data_tosee = $this->ModelBuy_m->see_history('id_buyer', $data_jwt['data']->id_user);
        return $this->respond($data_tosee);
    }

    public function sell()
    {
        return view('buskidi');
    }
}
