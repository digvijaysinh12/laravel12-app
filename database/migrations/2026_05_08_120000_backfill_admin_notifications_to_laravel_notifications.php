<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Keep legacy admin rows readable while Laravel notifications become the source of truth.
     */
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('admin_notifications') || ! DB::getSchemaBuilder()->hasTable('notifications')) {
            return;
        }

        $admins = DB::table('users')
            ->where('role', 'admin')
            ->pluck('id');

        if ($admins->isEmpty()) {
            return;
        }

        $existingFingerprints = DB::table('notifications')
            ->where('type', 'App\\Notifications\\SystemNotification')
            ->pluck('data')
            ->map(function (string $data): string {
                $decoded = json_decode($data, true) ?: [];

                return sha1(($decoded['title'] ?? '').'|'.($decoded['message'] ?? '').'|'.($decoded['action_url'] ?? ''));
            })
            ->flip();

        DB::table('admin_notifications')
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($admins, $existingFingerprints): void {
                $inserts = [];

                foreach ($rows as $row) {
                    $payload = [
                        'type' => $row->type,
                        'title' => $row->title,
                        'message' => $row->message,
                        'icon' => $row->type === 'product' ? 'warning' : 'order',
                        'action_url' => null,
                        'user_id' => $row->user_id,
                        'is_read' => (bool) $row->is_read,
                    ];

                    $fingerprint = sha1($payload['title'].'|'.$payload['message'].'|');

                    if ($existingFingerprints->has($fingerprint)) {
                        continue;
                    }

                    foreach ($admins as $adminId) {
                        $inserts[] = [
                            'id' => (string) Str::uuid(),
                            'type' => 'App\\Notifications\\SystemNotification',
                            'notifiable_type' => 'App\\Models\\User',
                            'notifiable_id' => $adminId,
                            'data' => json_encode($payload, JSON_THROW_ON_ERROR),
                            'read_at' => $row->is_read ? $row->updated_at : null,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ];
                    }

                    $existingFingerprints->put($fingerprint, true);
                }

                if ($inserts !== []) {
                    DB::table('notifications')->insert($inserts);
                }
            });
    }

    public function down(): void
    {
        // Intentionally left empty to avoid deleting already-migrated production notifications.
    }
};
