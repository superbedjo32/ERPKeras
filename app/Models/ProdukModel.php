<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukModel extends Model
{
    use HasFactory;
    protected $table = "t_produk";
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_produk',
        'id_reference',
        'deskripsi',
        'gambar',
        'qty',
        'harga',
        'status'
    ];
    public $timestamps = false;

    public function boms()
    {
        return $this->belongsToMany(BomModel::class, 'bom_bahan', 'bahan_id', 'bom_id');
    }

    public function bahans()
    {
        return $this->belongsToMany(
            ProdukModel::class,
            't_bom_list', // Nama tabel pivot
            'kode_bom', // Foreign key di pivot untuk BOM
            'kode_produk', // Foreign key di pivot untuk Produk
            'id', // Primary key di BOM
            'id' // Primary key di Produk
        )->withPivot('qty', 'satuan'); // Atribut tambahan di pivot
    }
    public function vendor()
    {
        return $this->belongsToMany(VendorModel::class, 'vendor_produk', 'produk_id', 'vendor_id');
    }
}
