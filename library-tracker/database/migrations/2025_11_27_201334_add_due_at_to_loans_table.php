<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->timestamp('due_at')->nullable()->after('loaned_at');
        });

        DB::table('loans')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                $loanedAt = $row->loaned_at ?? $row->created_at ?? null;
                $dueAt = $loanedAt ? Carbon::parse($loanedAt)->addDays(14) : Carbon::now()->addDays(14);
                DB::table('loans')->where('id', $row->id)->update([
                    'due_at' => $dueAt->toDateTimeString()
                ]);
            }
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->timestamp('due_at')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('due_at');
        });
    }
};
