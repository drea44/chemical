<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Chemical;
use App\Http\Controllers\TransactionController;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('chemicals', 'sort_order')) {
            Schema::table('chemicals', function (Blueprint $table) {
                $table->integer('sort_order')->nullable()->default(9999)->after('id');
            });
        }

        $prioritizedNames = TransactionController::getReferenceChemicalNames();
        $caseOrder = 'CASE ';
        foreach ($prioritizedNames as $pos => $name) {
            $escaped = addslashes($name);
            $caseOrder .= "WHEN chemical_name = '{$escaped}' THEN {$pos} ";
        }
        $caseOrder .= 'ELSE 9999 END, id ASC';

        $chems = Chemical::orderByRaw($caseOrder)->get();
        foreach ($chems as $i => $c) {
            $c->sort_order = $i + 1;
            $c->saveQuietly();
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('chemicals', 'sort_order')) {
            Schema::table('chemicals', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};

