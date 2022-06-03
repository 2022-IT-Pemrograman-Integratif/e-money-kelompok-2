<?php

namespace App\Models;

use CodeIgniter\Model;
use Codeigniter\API\ResponseTrait;
use Exception;

class ModelAccount_m extends Model
{
    use ResponseTrait;
    protected $table = "account_m";
    protected $primaryKey = "id_user";
    protected $allowedFields = [
        'username',
        'password',
        'email',
        'phone',
    ];

    protected $validationRules = [
        'username'  => 'required|is_unique[account_m.username]',
        'password'  => 'required|is_unique[account_m.password]',
        'email'       => 'required|is_unique[account_m.email]|valid_email',
        'phone'       => 'required|is_unique[account_m.phone]',
        // 'nomer_hp'          => 'required|is_unique[account.nomer_hp]'
    ];

    protected $validationMessages = [
        'username'  => [
            'required'  => 'Silahkan masukkan username',
            'is_unique' => 'username tersebut sudah terdaftar'
        ],
        'password'  => [
            'required'  => 'Silahkan masukkan password',
            'is_unique' => 'password tersebut sudah terpakai'
        ],
        'email'  => [
            'required'  => 'Silahkan masukkan email',
            'is_unique' => 'email tersebut sudah terpakai',
            'valid_email'   => 'ini bukan email woi'
        ],
        'phone'       => [
            'required'  => 'Silahkan masukkan Telepon',
            'is_unique' => 'Telepon tersebut sudah ada'
        ],
        // 'nomer_hp'       => [
        //     'required'  => 'Silahkan masukkan nomer hp',
        //     'is_unique' => 'nomer hp tersebut sudah terdaftar'
        // ]
    ];

    function verifyLogin($username, $password)
    {
        $builder = $this->table('account_m');
        $builder->where('username', $username);
        $builder->where('password', $password);
        $data = $builder->first();
        if (empty($data)) {
            return "username / password salah";
        }
        return $data;
    }

    function getDataWhere($where, $whering)
    {
        $builder = $this->table('account');
        $builder->where($where, $whering);
        $data = $builder->first();
        if (empty($data)) {
            return "data tidak ada";
        }
        return $data;
    }
    function updateData($where, $whering, $set, $seting)
    {
        $builder = $this->table('account');
        $builder->set($set, $seting);
        $builder->where($where, $whering);
        $builder->update();
    }
}
