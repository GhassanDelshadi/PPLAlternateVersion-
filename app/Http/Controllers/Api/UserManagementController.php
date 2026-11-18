<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\KlinikCabang;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    /**
     * PBI-03: Admin Membuat Akun Dokter atau Staf Admin Baru
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'no_hp' => ['required', 'string', 'max:20', 'unique:users,no_hp'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:dokter,admin'],
            'nomor_sip' => ['nullable', 'required_if:role,dokter', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dapat dibuat admin hanya dokter atau admin.',
            'nomor_sip.required_if' => 'Nomor SIP wajib diisi untuk akun dokter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $cabangId = $request->user()?->klinik_cabang_id ?? KlinikCabang::first()?->id ?? 1;

        $user = DB::transaction(function () use ($request, $cabangId) {
            $u = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_active' => true,
                'klinik_cabang_id' => $cabangId,
            ]);

            if ($request->role === 'dokter') {
                Doctor::create([
                    'user_id' => $u->id,
                    'nomor_sip' => $request->nomor_sip,
                    'deskripsi' => $request->deskripsi,
                ]);
            }

            return $u;
        });

        return response()->json([
            'status' => 'success',
            'message' => "Akun {$request->role} berhasil dibuat.",
            'data' => $user->load('doctor'),
        ], 201);
    }
}
