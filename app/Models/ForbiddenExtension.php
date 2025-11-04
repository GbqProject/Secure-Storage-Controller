<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ForbiddenExtension extends Model
{
    use HasFactory;
    protected $fillable = ['ext'];
    public $timestamps = false;
}
