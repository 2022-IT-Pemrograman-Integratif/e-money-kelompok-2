<?php

namespace App\Controllers\Buskidicoin;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelAccount;

class admin extends BaseController
{
    use ResponseTrait;
    
    function __construct()
    {
        $this->modelAccount = new ModelAccount();
    }

    public function index()
    {
        return view('welcome_message');
    }
    public function see_all_data()
    {
        $data = $this->modelAccount->orderBy('account_id', 'asc')->findAll();
        return $this->respond($data, 200);
    }
    public function login()
    {
        
    }
}
?>