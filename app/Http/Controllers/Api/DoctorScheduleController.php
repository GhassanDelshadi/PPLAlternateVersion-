<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorScheduleController extends Controller
{
    /**
     * PBI-08: Tampilkan Jadwal Dokter (Bisa difilter per hari / poli / dokter)
     */
    public function index(Request $request): JsonResponse
    {
        $query = DoctorSchedule::with([
            'doctor.user:id,name,email,no_hp',
            'poli:id,nama_poli,kode_poli',
        ]);

        if (! $request->user() || $request->user()->role !== 'admin') {
            $query->where('status', true);
        } elseif ($request->has('status') && $request->status !== 'all') {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('hari')) {
            $query->where('hari', strtolower($request->hari));
        }

        if ($request->filled('poli_id')) {
            $query->where('poli_id', $request->poli_id);
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $schedules = $query->orderBy('hari', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $schedules,
        ]);
    }

    /**
     * Detail Jadwal Dokter
     */
    public function show(string $id): JsonResponse
    {
        $schedule = DoctorSchedule::with([
            'doctor.user:id,name,email,no_hp',
            'poli:id,nama_poli,kode_poli',
        ])->find($id);

        if (! $schedule) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal dokter tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $schedule,
        ]);
    }

    /**
     * PBI-07: Tambah Jadwal Praktik Baru (Khusus Admin)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => ['required', 'exists:doctors,id'],
            'poli_id' => ['required', 'exists:polis,id'],
            'hari' => ['required', 'in:senin,selasa,rabu,kamis,jumat,sabtu,minggu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'kuota' => ['required', 'integer', 'min:1'],
            'status' => ['sometimes', 'boolean'],
        ], [
            'doctor_id.required' => 'Dokter wajib dipilih.',
            'doctor_id.exists' => 'Dokter tidak valid.',
            'poli_id.required' => 'Poli wajib dipilih.',
            'hari.required' => 'Hari praktik wajib dipilih.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'kuota.required' => 'Kuota wajib diisi.',
            'kuota.min' => 'Kuota minimal 1.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Cek Bentrok Jadwal Dokter
        $isBentrok = DoctorSchedule::where('doctor_id', $request->doctor_id)
            ->where('hari', $request->hari)
            ->where(function ($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhere(function ($sub) use ($request) {
                        $sub->where('jam_mulai', '<=', $request->jam_mulai)
                            ->where('jam_selesai', '>=', $request->jam_selesai);
                    });
            })->exists();

        if ($isBentrok) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal bentrok dengan jadwal yang sudah ada untuk dokter ini.',
            ], 422);
        }

        $schedule = DoctorSchedule::create([
            'doctor_id' => $request->doctor_id,
            'poli_id' => $request->poli_id,
            'hari' => strtolower($request->hari),
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kuota' => $request->kuota,
            'status' => $request->boolean('status', true),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal dokter berhasil disimpan.',
            'data' => $schedule->load(['doctor.user', 'poli']),
        ], 201);
    }

    /**
     * PBI-07: Perbarui Jadwal Praktik (Khusus Admin)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $schedule = DoctorSchedule::find($id);

        if (! $schedule) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal dokter tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'doctor_id' => ['required', 'exists:doctors,id'],
            'poli_id' => ['required', 'exists:polis,id'],
            'hari' => ['required', 'in:senin,selasa,rabu,kamis,jumat,sabtu,minggu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'kuota' => ['required', 'integer', 'min:1'],
            'status' => ['sometimes', 'boolean'],
        ], [
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'kuota.min' => 'Kuota minimal 1.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Cek bentrok jadwal lain
        $isBentrok = DoctorSchedule::where('doctor_id', $request->doctor_id)
            ->where('hari', $request->hari)
            ->where('id', '!=', $schedule->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhere(function ($sub) use ($request) {
                        $sub->where('jam_mulai', '<=', $request->jam_mulai)
                            ->where('jam_selesai', '>=', $request->jam_selesai);
                    });
            })->exists();

        if ($isBentrok) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal bentrok dengan jadwal yang sudah ada untuk dokter ini.',
            ], 422);
        }

        $schedule->update([
            'doctor_id' => $request->doctor_id,
            'poli_id' => $request->poli_id,
            'hari' => strtolower($request->hari),
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kuota' => $request->kuota,
            'status' => $request->boolean('status', $schedule->status),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal dokter berhasil diperbarui.',
            'data' => $schedule->load(['doctor.user', 'poli']),
        ]);
    }

    /**
     * PBI-07: Toggle Status Jadwal
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $schedule = DoctorSchedule::find($id);

        if (! $schedule) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal dokter tidak ditemukan.',
            ], 404);
        }

        $schedule->status = ! $schedule->status;
        $schedule->save();

        $statusText = $schedule->status ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'status' => 'success',
            'message' => "Jadwal dokter berhasil {$statusText}.",
            'data' => $schedule,
        ]);
    }

    /**
     * PBI-07: Hapus Jadwal
     */
    public function destroy(string $id): JsonResponse
    {
        $schedule = DoctorSchedule::find($id);

        if (! $schedule) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal dokter tidak ditemukan.',
            ], 404);
        }

        $schedule->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal dokter berhasil dihapus.',
        ]);
    }
}
