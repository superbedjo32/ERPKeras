<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomListModel extends Model
{
    use HasFactory;
    protected $table = "t_bom_list";
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = ['kode_bom','kode_produk','qty', 'satuan', 'harga_total'];
    public $timestamps = false;

    public function bom()
    {
        return $this->belongsTo(BomModel::class, 'kode_bom', 'id');
    }

    // Relasi ke tabel Produk
    public function produk()
    {
        return $this->belongsTo(ProdukModel::class, 'kode_produk', 'id');
    }
}
