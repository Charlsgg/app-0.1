<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop it first just in case we need to re-run or update it later
        DB::statement("DROP VIEW IF EXISTS all_announcements_view");

        // Create the global view linking announcements, authors, and attachments
        DB::statement("
            CREATE VIEW all_announcements_view AS
            SELECT 
                a.announcement_id,
                a.board_id,
                a.author_id,
                u.name AS author_name,
                u.user_type AS author_type,
                a.title,
                a.content,
                a.topic,
                a.likes_count,
                a.created_at AS announcement_date,
                aa.attachment_id,
                aa.file_path,
                aa.file_type
            FROM table_announcement a
            JOIN table_users u 
                ON a.author_id = u.user_id
            LEFT JOIN table_announcement_attachment aa 
                ON a.announcement_id = aa.announcement_id 
                AND aa.deleted_at IS NULL
            WHERE a.deleted_at IS NULL 
              AND u.deleted_at IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS all_announcements_view");
    }
};