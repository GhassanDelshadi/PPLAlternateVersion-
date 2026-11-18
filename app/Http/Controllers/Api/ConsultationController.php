<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationMessage;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConsultationController extends Controller
{
    /**
     * PBI-10 & PBI-11: Daftar Konsultasi Sesuai Role
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'pasien') {
            $profile = $user->patientProfile;
            if (! $profile) {
                return response()->json(['status' => 'success', 'data' => []]);
            }

            $consultations = Consultation::with([
                'doctor.user:id,name,email,no_hp',
                'doctor.doctorSchedules.poli:id,nama_poli,kode_poli',
                'messages' => fn ($q) => $q->latest()->limit(1),
            ])
                ->where('patient_profile_id', $profile->id)
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'role' => 'pasien',
                'data' => $consultations,
            ]);
        }

        if ($user->role === 'dokter') {
            $doctor = $user->doctor;
            if (! $doctor) {
                return response()->json(['status' => 'success', 'data' => []]);
            }

            $consultations = Consultation::with([
                'patientProfile.user:id,name,email,no_hp',
                'messages' => fn ($q) => $q->latest()->limit(1),
            ])
                ->where('doctor_id', $doctor->id)
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'role' => 'dokter',
                'data' => $consultations,
            ]);
        }

        // Admin: semua konsultasi
        $consultations = Consultation::with([
            'patientProfile.user:id,name,email',
            'doctor.user:id,name,email',
        ])->latest()->get();

        return response()->json([
            'status' => 'success',
            'role' => 'admin',
            'data' => $consultations,
        ]);
    }

    /**
     * PBI-10: Buat Konsultasi Baru oleh Pasien
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'pasien') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya pasien yang dapat memulai konsultasi medis.',
            ], 403);
        }

        $profile = $user->patientProfile;
        if (! $profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Profil pasien belum ditemukan.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'doctor_id' => ['required', 'exists:doctors,id'],
            'subject' => ['required', 'string', 'max:255'],
            'keluhan' => ['required', 'string', 'min:5'],
        ], [
            'doctor_id.required' => 'Dokter tujuan wajib dipilih.',
            'doctor_id.exists' => 'Dokter tidak valid.',
            'subject.required' => 'Subjek / judul konsultasi wajib diisi.',
            'keluhan.required' => 'Keluhan awal wajib diisi minimal 5 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $consultation = DB::transaction(function () use ($profile, $user, $request) {
            $c = Consultation::create([
                'patient_profile_id' => $profile->id,
                'doctor_id' => $request->doctor_id,
                'subject' => $request->subject,
                'status' => 'ongoing',
            ]);

            ConsultationMessage::create([
                'consultation_id' => $c->id,
                'sender_user_id' => $user->id,
                'message' => $request->keluhan,
            ]);

            return $c;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Konsultasi berhasil dibuat.',
            'data' => $consultation->load(['doctor.user', 'messages']),
        ], 201);
    }

    /**
     * PBI-10 & PBI-11: Ruang Konsultasi (Detail & Riwayat Percakapan)
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $consultation = Consultation::with([
            'patientProfile.user:id,name,email,no_hp',
            'doctor.user:id,name,email,no_hp',
            'messages.sender:id,name,role',
        ])->find($id);

        if (! $consultation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi konsultasi tidak ditemukan.',
            ], 404);
        }

        $user = $request->user();

        // Cek Izin Akses
        $bolehAkses = false;
        if ($user->role === 'admin') {
            $bolehAkses = true;
        } elseif ($user->role === 'pasien' && $consultation->patientProfile->user_id === $user->id) {
            $bolehAkses = true;
        } elseif ($user->role === 'dokter' && $consultation->doctor->user_id === $user->id) {
            $bolehAkses = true;
        }

        if (! $bolehAkses) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki hak akses ke ruang konsultasi ini.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $consultation,
        ]);
    }

    /**
     * PBI-10 & PBI-11: Kirim Pesan Chat Baru
     */
    public function sendMessage(Request $request, string $id): JsonResponse
    {
        $consultation = Consultation::find($id);

        if (! $consultation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi konsultasi tidak ditemukan.',
            ], 404);
        }

        if ($consultation->status === 'completed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi konsultasi telah selesai. Tidak dapat mengirim pesan lagi.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'message' => ['required', 'string', 'max:1000'],
        ], [
            'message.required' => 'Pesan tidak boleh kosong.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        $chat = ConsultationMessage::create([
            'consultation_id' => $consultation->id,
            'sender_user_id' => $user->id,
            'message' => $request->message,
        ]);

        $consultation->touch(); // Perbarui updated_at

        return response()->json([
            'status' => 'success',
            'message' => 'Pesan berhasil terkirim.',
            'data' => $chat->load('sender:id,name,role'),
        ], 201);
    }

    /**
     * PBI-11: Dokter Mengubah Status Sesi (Mulai / Selesai)
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        if ($user->role !== 'dokter' && $user->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya dokter atau admin yang dapat mengubah status konsultasi.',
            ], 403);
        }

        $consultation = Consultation::find($id);
        if (! $consultation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi konsultasi tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => ['required', 'in:ongoing,completed,rejected'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status tidak valid.',
            ], 422);
        }

        $consultation->status = $request->status;
        $consultation->save();

        return response()->json([
            'status' => 'success',
            'message' => "Status sesi konsultasi berhasil diubah menjadi {$request->status}.",
            'data' => $consultation,
        ]);
    }
}
