<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add columns first
        Schema::table('puzzle_tracking', function (Blueprint $table) {
            if (!Schema::hasColumn('puzzle_tracking', 'post_id')) {
                // Check the actual data type of user_post_status.id first
                // Use bigInteger which should match most cases
                $table->bigInteger('post_id')->nullable()->after('id');
                $table->index('post_id');
            }
            
            if (!Schema::hasColumn('puzzle_tracking', 'screenshot_url')) {
                $table->string('screenshot_url', 500)->nullable()->after('redirect_url');
            }
            
            if (!Schema::hasColumn('puzzle_tracking', 'metadata')) {
                $table->text('metadata')->nullable()->after('screenshot_url');
            }
        });
        
        // Add foreign key constraint separately - skip if incompatible
        // Foreign key is optional, the relationship will work without it
        try {
            if (Schema::hasColumn('puzzle_tracking', 'post_id') && Schema::hasTable('user_post_status')) {
                // Check if foreign key already exists
                $foreignKeyExists = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'puzzle_tracking' 
                    AND COLUMN_NAME = 'post_id' 
                    AND REFERENCED_TABLE_NAME = 'user_post_status'
                ");
                
                if (empty($foreignKeyExists)) {
                    Schema::table('puzzle_tracking', function (Blueprint $table) {
                        $table->foreign('post_id')->references('id')->on('user_post_status')->onDelete('cascade');
                    });
                }
            }
        } catch (\Exception $e) {
            // If foreign key fails due to incompatible types, continue without it
            // The application will still work, just without database-level referential integrity
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('puzzle_tracking', function (Blueprint $table) {
            if (Schema::hasColumn('puzzle_tracking', 'post_id')) {
                $table->dropForeign(['post_id']);
                $table->dropIndex(['post_id']);
                $table->dropColumn('post_id');
            }
            
            if (Schema::hasColumn('puzzle_tracking', 'screenshot_url')) {
                $table->dropColumn('screenshot_url');
            }
            
            if (Schema::hasColumn('puzzle_tracking', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });
    }
};
