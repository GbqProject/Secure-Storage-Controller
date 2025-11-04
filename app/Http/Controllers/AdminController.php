<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use App\Models\ForbiddenExtension;

class AdminController extends Controller
{
    public function index(Request $r){
        $groups = Group::all();
        $users = User::all();
        $forbidden = ForbiddenExtension::all();
        $globalQuota = (int)\DB::table('settings')->where('key','global_quota_bytes')->value('value') ?? 10*1024*1024;
        return view('admin.dashboard', compact('groups','users','forbidden','globalQuota'));
    }

    public function createGroup(Request $r){
        $r->validate(['name'=>'required|string','quota_mb'=>'nullable|numeric']);
        $quota = $r->input('quota_mb') ? intval($r->input('quota_mb')) * 1024 * 1024 : null;
        Group::create(['name'=>$r->name,'quota_bytes'=>$quota]);
        return redirect()->back()->with('success','Grupo creado');
    }

    public function assignGroup(Request $r, $id){
        $u = User::findOrFail($id);
        $u->group_id = $r->input('group_id');
        $u->save();
        return redirect()->back()->with('success','Usuario asignado');
    }

    public function updateLimits(Request $r){
        // global quota in MB
        $mb = intval($r->input('global_quota_mb',10));
        \DB::table('settings')->updateOrInsert(['key'=>'global_quota_bytes'], ['value' => $mb * 1024 * 1024]);
        return redirect()->back()->with('success','Límites actualizados');
    }

    public function addForbiddenExtension(Request $r){
        $r->validate(['ext'=>'required|string']);
        $ext = ltrim(strtolower($r->ext),'.');
        ForbiddenExtension::firstOrCreate(['ext'=>$ext]);
        return redirect()->back();
    }

    public function removeForbiddenExtension($id){
        ForbiddenExtension::findOrFail($id)->delete();
        return redirect()->back();
    }
}
