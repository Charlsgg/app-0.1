<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
   public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS user_announcements_attachments_view");
        DB::statement("
            CREATE VIEW user_announcements_attachments_view AS
            SELECT 
                a.announcement_id,
                a.author_id,
                a.title,
                a.content,
                a.topic,
                a.likes_count,
                a.created_at AS announcement_date,
                aa.attachment_id,
                aa.file_path,
                aa.file_type
            FROM table_announcement a
            LEFT JOIN table_announcement_attachment aa 
                ON a.announcement_id = aa.announcement_id
            WHERE a.deleted_at IS NULL 
              AND (aa.deleted_at IS NULL OR aa.attachment_id IS NULL)
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS user_announcements_attachments_view");
    }
};