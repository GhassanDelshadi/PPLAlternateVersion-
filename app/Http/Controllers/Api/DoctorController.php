<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * PBI-06: Tampilkan Daftar Dokter
     */
    public function index(Request $request): JsonResponse
    {
        $query = Doctor::with([
            'user:id,name,email,no_hp,is_active',
            'doctorSchedules.poli:id,nama_poli,kode_poli',
        ])->whereHas('user', function ($q) {
            $q->where('is_active', true);
        });

        if ($request->filled('poli_id')) {
            $poliId = $request->poli_id;
            $query->whereHas('doctorSchedules', function ($q) use ($poliId) {
                $q->where('poli_id', $poliId)->where('status', true);
            });
        }

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_sip', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($qu) use ($keyword) {
                        $qu->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        $doctors = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $doctors,
        ]);
    }

    /**
     * PBI-06: Detail Profil Dokter
     */
    public function show(string $id): JsonResponse
    {
        $doctor = Doctor::with([
            'user:id,name,email,no_hp,is_active',
            'doctorSchedules' => function ($q) {
                $q->where('status', true)->with('poli:id,nama_poli,kode_poli');
            },
        ])->find($id);

        if (! $doctor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data dokter tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $doctor,
        ]);
    }
}
