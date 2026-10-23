<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up(){
Schema::create('upbringing_document_links',function(Blueprint $t){$t->id();$t->foreignId('official_document_id')->constrained()->cascadeOnDelete();$t->string('entity_type');$t->unsignedBigInteger('entity_id');$t->string('relation')->default('basis');$t->timestamps();$t->unique(['official_document_id','entity_type','entity_id']);$t->index(['entity_type','entity_id']);});
Schema::create('upbringing_notifications',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained('users')->cascadeOnDelete();$t->string('type');$t->string('title');$t->text('message')->nullable();$t->string('url')->nullable();$t->string('fingerprint')->unique();$t->dateTime('read_at')->nullable();$t->timestamps();});
}public function down(){Schema::dropIfExists('upbringing_notifications');Schema::dropIfExists('upbringing_document_links');}};
