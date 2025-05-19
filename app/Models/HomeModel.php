<?php

namespace App\Models;

use CodeIgniter\Model;

class HomeModel extends Model
{

    public function countVendors()
    {
        return $this->db->table('tb_vendor')->countAll();  
    }

    public function countParts()
    {
        return $this->db->table('tb_part')->countAll();  
    }

    public function countQtyPass()
    {
        return $this->db->table('tb_inspection_test_detail')
                        ->selectSum('qty_pass')  
                        ->get()
                        ->getRow()
                        ->qty_pass;
    }

    public function countQtyFail()
    {
        return $this->db->table('tb_inspection_test_detail')
                        ->selectSum('qty_fail') 
                        ->get()
                        ->getRow()
                        ->qty_fail;
    }   
    }
    


