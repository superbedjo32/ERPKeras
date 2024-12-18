<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POModel extends Model
{
    use HasFactory;
    protected $table = "t_po";
    protected $primaryKey = 'id';
    protected $fillable = [
        'kode_po',
        'tgl_pembayaran',
        'pembayaran',
        'gambar',
        'rfq_id',
    ];
    public $timestamps = false;

    public function rfq()
    {
        return $this->belongsTo(RfqModel::class, 'rfq_id');
    }
}
