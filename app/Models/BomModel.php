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
    protected $fillable = ['kode_bom', 'kode_produk', 'tanggal', 'total_harga'];
    public $timestamps = false;

    public function produk()
    {
        return $this->belongsTo(ProdukModel::class, 'kode_produk', 'id');
    }

    public function bahans()
    {
        return $this->belongsToMany(ProdukModel::class, 'bom_bahan', 'bom_id', 'bahan_id');
    }

    public function boms()
    {
        return $this->belongsToMany(
            BomModel::class,
            't_bom_list', // Nama tabel pivot
            'kode_produk', // Foreign key di pivot untuk Produk
            'kode_bom', // Foreign key di pivot untuk BOM
            'id', // Primary key di Produk
            'id' // Primary key di BOM
        )->withPivot('qty', 'satuan'); // Atribut tambahan di pivot
    }
}
