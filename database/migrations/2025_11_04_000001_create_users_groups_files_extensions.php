<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersGroupsFilesExtensions extends Migration
{
    public function up()
    {
        Schema::create('groups', function(Blueprint $t){
            $t->id();
            $t->string('name')->unique();
            $t->bigInteger('quota_bytes')->nullable();
            $t->timestamps();
        });

        Schema::create('users', function(Blueprint $t){
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->string('role')->default('user'); // 'user' or 'admin'
            $t->bigInteger('quota_bytes')->nullable(); // per-user override
            $t->unsignedBigInteger('group_id')->nullable();
            $t->timestamps();

            $t->foreign('group_id')->references('id')->on('groups')->onDelete('set null');
        });

        Schema::create('stored_files', function(Blueprint $t){
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->string('original_name');
            $t->string('path');
            $t->bigInteger('size_bytes');
            $t->string('mime_type')->nullable();
            $t->timestamps();

            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('forbidden_extensions', function(Blueprint $t){
            $t->id();
            $t->string('ext')->unique();
        });

        Schema::create('settings', function(Blueprint $t){
            $t->string('key')->primary();
            $t->text('value')->nullable();
        });

        // init default global quota & forbidden extensions
        \DB::table('settings')->insertOrIgnore([
            ['key'=>'global_quota_bytes','value'=> 10 * 1024 * 1024] // 10 MB
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('forbidden_extensions');
        Schema::dropIfExists('stored_files');
        Schema::dropIfExists('users');
        Schema::dropIfExists('groups');
    }
}
