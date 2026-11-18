<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientProfileController extends Controller
{
    /**
     * PBI-04: Lihat Profil Pasien Login
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'pasien') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya pasien yang memiliki profil rekam data pasien.',
            ], 403);
        }

        $profile = PatientProfile::firstOrCreate(['user_id' => $user->id]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'no_hp' => $user->no_hp,
                ],
                'profile' => $profile,
            ],
        ]);
    }

    /**
     * PBI-04: Lengkapi / Perbarui Data Profil Pasien
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'pasien') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya pasien yang diizinkan memperbarui profil ini.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'alamat' => ['required', 'string', 'max:500'],
            'jenis_pasien' => ['required', 'in:umum,bpjs'],
            'no_bpjs' => [
                'nullable',
                'required_if:jenis_pasien,bpjs',
                'string',
                'max:30',
            ],
        ], [
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih (L / P).',
            'alamat.required' => 'Alamat lengkap wajib diisi.',
            'jenis_pasien.required' => 'Tipe pasien (umum / bpjs) wajib dipilih.',
            'no_bpjs.required_if' => 'Nomor BPJS wajib diisi jika memilih jenis pasien BPJS.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        if ($data['jenis_pasien'] === 'umum') {
            $data['no_bpjs'] = null;
        }

        $profile = PatientProfile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Profil pasien berhasil diperbarui.',
            'data' => $profile,
        ]);
    }
}
