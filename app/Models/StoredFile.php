<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoredFile extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','original_name','path','size_bytes','mime_type'];

    public function user() { return $this->belongsTo(User::class); }
}
