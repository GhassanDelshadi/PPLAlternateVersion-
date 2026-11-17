<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('dusk:clean-data', function () {
    $this->info('Membersihkan data uji Dusk dari database...');

    $deletedQueues = \App\Models\Queue::query()->delete();
    $deletedMessages = \App\Models\ConsultationMessage::query()->delete();
    $deletedConsultations = \App\Models\Consultation::query()->delete();
    $deletedLayanans = \App\Models\Layanan::where('nama_layanan', 'like', '%Dusk%')->orWhere('kode_layanan', 'like', '%DUSK%')->delete();
    $deletedPolis = \App\Models\Poli::withoutGlobalScopes()->where('kode_poli', 'PMT')->orWhere('nama_poli', 'like', '%Dusk%')->orWhere('nama_poli', 'like', '%Mata%')->delete();
    $deletedSchedules = \App\Models\DoctorSchedule::where('jam_mulai', '13:00:00')->where('jam_selesai', '17:00:00')->where('kuota', 15)->delete();

    $duskUsers = \App\Models\User::where('name', 'like', '%Dusk%')
        ->orWhere('email', 'like', '%dusk%')
        ->orWhere('no_hp', '081288887777')
        ->orWhere('no_hp', '081999888777')
        ->get();

    $deletedUsersCount = 0;
    foreach ($duskUsers as $u) {
        \App\Models\PatientProfile::where('user_id', $u->id)->delete();
        \App\Models\Doctor::where('user_id', $u->id)->delete();
        $u->delete();
        $deletedUsersCount++;
    }

    $this->line("• User uji terhapus: {$deletedUsersCount}");
    $this->line("• Antrean terhapus: {$deletedQueues}");
    $this->line("• Konsultasi terhapus: {$deletedConsultations}");
    $this->line("• Pesan konsultasi terhapus: {$deletedMessages}");
    $this->line("• Layanan uji terhapus: {$deletedLayanans}");
    $this->line("• Poli uji terhapus: {$deletedPolis}");
    $this->line("• Jadwal dokter uji terhapus: {$deletedSchedules}");

    $this->info('Database bersih! Hanya data master dasar (seeder) yang dipertahankan.');
})->purpose('Bersihkan data uji dummy sisa pengujian Laravel Dusk');
