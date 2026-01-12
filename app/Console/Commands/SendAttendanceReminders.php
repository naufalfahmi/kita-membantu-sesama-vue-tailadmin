<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\AttendanceReminderLog;
use App\Models\Holiday;
use App\Models\User;
use App\Services\OneSignalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendAttendanceReminders extends Command
{
    protected $signature = 'absensi:reminders';

    protected $description = 'Send push reminders for clock-in and clock-out based on tipe absensi schedule';

    public function handle(OneSignalService $push): int
    {
        $now = now('Asia/Jakarta');
        $today = $now->toDateString();

        // Skip weekend or holidays
        if ($now->isWeekend() || Holiday::isHoliday($today)) {
            $this->info('Skip reminders on weekend/holiday');
            return self::SUCCESS;
        }

        $users = User::with(['tipeAbsensi', 'pushSubscriptions'])->whereNotNull('tipe_absensi_id')->get();
        $sentCount = 0;

        foreach ($users as $user) {
            $tipe = $user->tipeAbsensi;
            if (!$tipe) {
                continue;
            }

            $attendance = Absensi::where('user_id', $user->id)
                ->whereDate('jam_masuk', $today)
                ->first();

            // Reminder absen masuk
            if ($tipe->jam_masuk && ! $attendance) {
                $jamMasuk = $this->parseTimeToday($tipe->jam_masuk, $now->timezoneName);
                if ($this->shouldSendReminder($user->id, $today, 'masuk', $now, $jamMasuk)) {
                    if ($this->notifyUser($push, $user, 'Reminder Absen Masuk', "Hai {$user->name}, mohon lakukan absen masuk kerja.")) {
                        $this->logReminder($user->id, $today, 'masuk');
                        $sentCount++;
                    }
                }
            }

            // Reminder absen keluar
            if ($tipe->jam_keluar && $attendance && $attendance->jam_keluar === null) {
                $jamKeluar = $this->parseTimeToday($tipe->jam_keluar, $now->timezoneName);
                if ($this->shouldSendReminder($user->id, $today, 'keluar', $now, $jamKeluar)) {
                    if ($this->notifyUser($push, $user, 'Reminder Absen Keluar', "Hai {$user->name}, mohon lakukan absen keluar kerja.")) {
                        $this->logReminder($user->id, $today, 'keluar');
                        $sentCount++;
                    }
                }
            }
        }

        $this->info("Sent {$sentCount} reminders");
        return self::SUCCESS;
    }

    private function parseTimeToday($timeValue, string $tz)
    {
        if (!$timeValue) {
            return null;
        }

        try {
            $timeString = is_string($timeValue) ? $timeValue : $timeValue->format('H:i');
            return now($tz)->setTimeFromTimeString($timeString);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function shouldSendReminder(int $userId, string $tanggal, string $type, $now, $targetTime): bool
    {
        if (! $targetTime) {
            return false;
        }

        // send if within 5-minute window after target time and not yet logged today
        $alreadySent = AttendanceReminderLog::where('user_id', $userId)
            ->whereDate('tanggal', $tanggal)
            ->where('type', $type)
            ->exists();

        if ($alreadySent) {
            return false;
        }

        return $now->greaterThanOrEqualTo($targetTime) && $now->diffInMinutes($targetTime) <= 5;
    }

    private function notifyUser(OneSignalService $push, User $user, string $title, string $description): bool
    {
        $url = url('/admin/absensi');
        $success = $push->sendToExternalId((string) $user->id, $title, $description, $url);

        if (! $success) {
            Log::warning('No push sent for user', [
                'user_id' => $user->id,
                'title' => $title,
            ]);
        }

        return $success;
    }

    private function logReminder(int $userId, string $tanggal, string $type): void
    {
        AttendanceReminderLog::create([
            'user_id' => $userId,
            'tanggal' => $tanggal,
            'type' => $type,
            'sent_at' => now('Asia/Jakarta'),
        ]);
    }
}
