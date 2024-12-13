<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProdukModel;

class BomModel extends Model
{
    use HasFactory;
    protected $table = "t_bom";
    protected $primaryKey = 'id';
    protected $fillable = ['kode_bom','kode_produk','tanggal','total_harga'];
    public $timestamps = false;

    public function produk(){
        return $this->belongsTo(ProdukModel::class, 'kode_produk', 'id');
    }
}
