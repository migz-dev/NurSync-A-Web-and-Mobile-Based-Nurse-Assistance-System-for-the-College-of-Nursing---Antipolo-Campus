<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireStudentStatuses extends Command
{
    protected $signature = 'students:expire-status';
    protected $description = 'Deactivate students whose active status has expired';

    public function handle(): int
    {
        $now = now();

        $rows = DB::table('student_semester_statuses')
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->get(['id','student_id','term_id']);

        foreach ($rows as $r) {
            DB::table('student_semester_statuses')->where('id', $r->id)->update([
                'status'     => 'inactive',
                'reason'     => null,         // keep user-facing reason generic
                'updated_at' => $now,
            ]);

            DB::table('validation_audit_log')->insert([
                'student_id' => $r->student_id,
                'term_id'    => $r->term_id,
                'action'     => 'status_changed',
                'actor_type' => 'system',
                'actor_id'   => null,
                'details'    => json_encode(['cause' => 'expired_4m']),
                'created_at' => $now,
            ]);
        }

        $this->info("Expired {$rows->count()} student statuses.");
        return self::SUCCESS;
    }
}
