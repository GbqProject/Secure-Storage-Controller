<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $fillable = ['name','email','password','role','quota_bytes','group_id'];
    protected $hidden = ['password'];

    public function group() { return $this->belongsTo(Group::class); }
    public function files() { return $this->hasMany(StoredFile::class); }

    // helper: effective quota: user -> group -> global
    public function effectiveQuota()
    {
        if ($this->quota_bytes) return (int)$this->quota_bytes;
        if ($this->group && $this->group->quota_bytes) return (int)$this->group->quota_bytes;
        // fallback global default from DB settings table isn't implemented for brevity.
        // We'll read from ForbiddenExtension/global default configured in admin settings row.
        $setting = \DB::table('settings')->where('key','global_quota_bytes')->first();
        return $setting ? (int)$setting->value : 10*1024*1024;
    }

    public function usedBytes()
    {
        return (int)$this->files()->sum('size_bytes');
    }

    public function isAdmin() { return $this->role === 'admin'; }
}
