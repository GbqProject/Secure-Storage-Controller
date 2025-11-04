<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Group;
use App\Models\ForbiddenExtension;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $g1 = Group::create(['name'=>'Marketing','quota_bytes'=>15*1024*1024]); // 15 MB
        $g2 = Group::create(['name'=>'Desarrolladores','quota_bytes'=>50*1024*1024]); // 50 MB

        User::create([
            'name'=>'Admin Demo',
            'email'=>'admin@example.com',
            'password'=>Hash::make('adminpass'),
            'role'=>'admin',
            'group_id'=> $g2->id
        ]);

        User::create([
            'name'=>'User Demo',
            'email'=>'user@example.com',
            'password'=>Hash::make('userpass'),
            'role'=>'user',
            'group_id'=> $g1->id
        ]);

        foreach(['exe','bat','js','php','sh'] as $e){
            ForbiddenExtension::create(['ext'=>$e]);
        }
    }
}
