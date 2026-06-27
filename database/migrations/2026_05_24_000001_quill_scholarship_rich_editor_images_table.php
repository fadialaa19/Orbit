<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Optional: we don't strictly need a DB table for Quill images (we can store files only),
// but having a record can help future cleanup.
// For now, keep it empty/placeholder by not creating anything.
// (Left intentionally as a no-op migration file is not allowed; so we won't create this.)

return new class extends Migration {
    public function up(): void {}
    public function down(): void {}
};

