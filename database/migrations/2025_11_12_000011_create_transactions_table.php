<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['income','expense']);
            $table->date('date');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('attachment_id')->nullable();
            $table->timestamps();
            $table->index(['user_id','date']);
            // Add foreign key after attachments table exists
            $table->foreign('attachment_id')->references('id')->on('attachments')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
