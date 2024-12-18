<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqModel extends Model
{
    use HasFactory;
    protected $table = 't_rfq';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'vendor_id',
        'tgl_transaksi',
        'total_harga',
        'pembayaran',
        'status',
    ];
    public $timestamps = false;

    public function vendor()
    {
        return $this->belongsTo(VendorModel::class, 'vendor_id');
    }

    // Relasi ke RfqList
    public function rfqList()
    {
        return $this->hasMany(RfqListModel::class, 'rfq_id');
    }

    public function po()
    {
        return $this->hasMany(POModel::class, 'rfq_id');
    }
}
