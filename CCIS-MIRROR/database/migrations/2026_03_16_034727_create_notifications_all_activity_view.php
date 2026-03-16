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
        DB::statement("DROP VIEW IF EXISTS notifications_all_activity_view");
        
        DB::statement("
            CREATE VIEW notifications_all_activity_view AS
            
            /* 1. MANUALLY TRIGGERED NOTIFICATIONS */
            SELECT 
                n.notification_id,
                n.recipient_id,
                n.actor_id,
                n.event_id,
                n.announcement_id,
                n.type,
                n.message,
                n.is_read,
                n.created_at
            FROM table_notification n
            WHERE n.deleted_at IS NULL

            UNION ALL

            /* 2. AUTOMATIC UPCOMING EVENT REMINDERS (SQLite Syntax) */
            SELECT 
                NULL AS notification_id,
                e.user_id AS recipient_id,
                NULL AS actor_id,
                e.event_id,
                NULL AS announcement_id,
                'event_reminder' AS type,
                -- SQLite uses || for joining strings
                'Reminder: ' || e.title || ' starts within 24 hours!' AS message,
                0 AS is_read,
                -- SQLite uses datetime('now')
                datetime('now') AS created_at
            FROM table_events e
            WHERE e.deleted_at IS NULL
              -- Logic: Event date is between now and +1 day
              AND e.event_date BETWEEN datetime('now') AND datetime('now', '+1 day')
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS notifications_all_activity_view");
    }
};