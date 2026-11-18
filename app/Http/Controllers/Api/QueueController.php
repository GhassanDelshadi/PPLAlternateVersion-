<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QueueController extends Controller
{
    /**
     * PBI-09: Daftar Antrean Pasien (atau Monitoring Antrean jika Admin)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // JIKA ADMIN: Monitoring Antrean Klinik
        if ($user->role === 'admin') {
            $query = Queue::with([
                'patientProfile.user:id,name,no_hp,email',
                'doctorSchedule.doctor.user:id,name',
                'doctorSchedule.poli:id,nama_poli,kode_poli',
            ]);

            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal_antrean', $request->tanggal);
            } else {
                $query->whereDate('tanggal_antrean', Carbon::today());
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('poli_id')) {
                $poliId = $request->poli_id;
                $query->whereHas('doctorSchedule', function ($q) use ($poliId) {
                    $q->where('poli_id', $poliId);
                });
            }

            $queues = $query->orderBy('nomor_antrean', 'asc')->get();

            return response()->json([
                'status' => 'success',
                'role' => 'admin',
                'data' => $queues,
            ]);
        }

        // JIKA PASIEN: Daftar Antrean Milik Sendiri
        $profile = $user->patientProfile;
        if (! $profile) {
            return response()->json([
                'status' => 'success',
                'data' => [],
            ]);
        }

        $queues = Queue::with([
            'doctorSchedule.doctor.user:id,name',
            'doctorSchedule.poli:id,nama_poli,kode_poli',
        ])
            ->where('patient_profile_id', $profile->id)
            ->orderBy('tanggal_antrean', 'desc')
            ->orderBy('nomor_antrean', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'role' => 'pasien',
            'data' => $queues,
        ]);
    }

    /**
     * PBI-09: Ambil / Booking Tiket Antrean Online
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'pasien') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya pasien yang dapat mengambil tiket antrean.',
            ], 403);
        }

        $profile = $user->patientProfile;
        if (! $profile || empty($profile->tanggal_lahir) || empty($profile->alamat) || empty($profile->jenis_pasien)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan lengkapi profil pasien terlebih dahulu sebelum mengambil tiket antrean.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'doctor_schedule_id' => ['required', 'exists:doctor_schedules,id'],
            'tanggal_antrean' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'doctor_schedule_id.required' => 'Jadwal dokter wajib dipilih.',
            'doctor_schedule_id.exists' => 'Jadwal dokter tidak ditemukan.',
            'tanggal_antrean.required' => 'Tanggal periksa antrean wajib diisi.',
            'tanggal_antrean.after_or_equal' => 'Tanggal antrean tidak boleh tanggal lampau.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $schedule = DoctorSchedule::with('doctor.user', 'poli')->find($request->doctor_schedule_id);

        if (! $schedule || ! $schedule->status) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal dokter sedang tidak aktif.',
            ], 422);
        }

        // Cek kecocokan hari periksa dengan hari praktik jadwal
        $tgl = Carbon::parse($request->tanggal_antrean);
        $namaHariMap = [
            0 => 'minggu',
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
        ];
        $hariTanggal = $namaHariMap[$tgl->dayOfWeek];

        if ($schedule->hari !== $hariTanggal) {
            return response()->json([
                'status' => 'error',
                'message' => "Dokter tidak berpraktik pada hari {$hariTanggal}. Jadwal dokter ini adalah hari {$schedule->hari}.",
            ], 422);
        }

        // Cek antrean aktif pasien pada tanggal tersebut
        $antreanAktif = Queue::where('patient_profile_id', $profile->id)
            ->whereDate('tanggal_antrean', $request->tanggal_antrean)
            ->whereIn('status', ['waiting', 'called', 'serving'])
            ->exists();

        if ($antreanAktif) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah memiliki antrean aktif pada tanggal tersebut.',
            ], 422);
        }

        // Cek sisa kuota dan hitung nomor antrean berikutnya secara atomic
        $antreanBaru = DB::transaction(function () use ($profile, $schedule, $request) {
            $totalTerdaftar = Queue::where('doctor_schedule_id', $schedule->id)
                ->whereDate('tanggal_antrean', $request->tanggal_antrean)
                ->count();

            if ($totalTerdaftar >= $schedule->kuota) {
                return null;
            }

            $nomorAntrean = $totalTerdaftar + 1;

            return Queue::create([
                'patient_profile_id' => $profile->id,
                'doctor_schedule_id' => $schedule->id,
                'tanggal_antrean' => $request->tanggal_antrean,
                'nomor_antrean' => $nomorAntrean,
                'status' => 'waiting',
            ]);
        });

        if (! $antreanBaru) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kuota antrean untuk jadwal dokter pada tanggal ini sudah penuh.',
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Tiket antrean berhasil diambil.',
            'data' => $antreanBaru->load([
                'doctorSchedule.doctor.user',
                'doctorSchedule.poli',
                'patientProfile.user',
            ]),
        ], 201);
    }

    /**
     * PBI-09: Detail Tiket Antrean
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $queue = Queue::with([
            'patientProfile.user:id,name,email,no_hp',
            'doctorSchedule.doctor.user:id,name',
            'doctorSchedule.poli:id,nama_poli,kode_poli',
        ])->find($id);

        if (! $queue) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket antrean tidak ditemukan.',
            ], 404);
        }

        // Otorisasi: hanya pemilik tiket atau admin yang boleh lihat
        $user = $request->user();
        if ($user->role === 'pasien' && $queue->patientProfile->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses tiket tidak diizinkan.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $queue,
        ]);
    }

    /**
     * Monitoring Antrean: Admin Update Status Antrean Pasien
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'in:waiting,called,serving,completed,skipped'],
        ], [
            'status.required' => 'Status antrean wajib diisi.',
            'status.in' => 'Status antrean tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $queue = Queue::find($id);

        if (! $queue) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket antrean tidak ditemukan.',
            ], 404);
        }

        $queue->status = $request->status;
        $queue->save();

        return response()->json([
            'status' => 'success',
            'message' => "Status antrean berhasil diubah menjadi {$request->status}.",
            'data' => $queue,
        ]);
    }
}
