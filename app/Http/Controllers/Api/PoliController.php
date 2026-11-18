<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KlinikCabang;
use App\Models\Poli;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PoliController extends Controller
{
    /**
     * PBI-05: Tampilkan Daftar Poli Klinik
     */
    public function index(Request $request): JsonResponse
    {
        $query = Poli::with('klinikCabang');

        // Jika user bukan admin, hanya tampilkan poli yang aktif
        if (! $request->user() || $request->user()->role !== 'admin') {
            $query->where('status', true);
        } elseif ($request->has('status') && $request->status !== 'all') {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_poli', 'like', "%{$keyword}%")
                    ->orWhere('kode_poli', 'like', "%{$keyword}%");
            });
        }

        $polis = $query->orderBy('nama_poli', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $polis,
        ]);
    }

    /**
     * PBI-05: Detail Satu Poli
     */
    public function show(string $id): JsonResponse
    {
        $poli = Poli::with(['klinikCabang', 'doctorSchedules.doctor.user'])->find($id);

        if (! $poli) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data poli tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $poli,
        ]);
    }

    /**
     * PBI-05: Tambah Data Poli Baru (Khusus Admin)
     */
    public function store(Request $request): JsonResponse
    {
        $cabangId = $request->user()?->klinik_cabang_id ?? KlinikCabang::first()?->id ?? 1;

        $validator = Validator::make($request->all(), [
            'kode_poli' => [
                'required',
                'string',
                'max:20',
                Rule::unique('polis', 'kode_poli')->where('klinik_cabang_id', $cabangId),
            ],
            'nama_poli' => [
                'required',
                'string',
                'max:100',
                Rule::unique('polis', 'nama_poli')->where('klinik_cabang_id', $cabangId),
            ],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['sometimes', 'boolean'],
        ], [
            'kode_poli.required' => 'Kode poli wajib diisi.',
            'kode_poli.unique' => 'Kode poli sudah digunakan di cabang ini.',
            'nama_poli.required' => 'Nama poli wajib diisi.',
            'nama_poli.unique' => 'Nama poli sudah digunakan di cabang ini.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = $validator->validated();
        $payload['klinik_cabang_id'] = $cabangId;
        $payload['status'] = $request->boolean('status', true);

        $poli = Poli::create($payload);

        return response()->json([
            'status' => 'success',
            'message' => 'Poli berhasil ditambahkan.',
            'data' => $poli,
        ], 201);
    }

    /**
     * PBI-05: Perbarui Data Poli (Khusus Admin)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $poli = Poli::find($id);

        if (! $poli) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data poli tidak ditemukan.',
            ], 404);
        }

        $cabangId = $poli->klinik_cabang_id;

        $validator = Validator::make($request->all(), [
            'kode_poli' => [
                'required',
                'string',
                'max:20',
                Rule::unique('polis', 'kode_poli')->ignore($poli->id)->where('klinik_cabang_id', $cabangId),
            ],
            'nama_poli' => [
                'required',
                'string',
                'max:100',
                Rule::unique('polis', 'nama_poli')->ignore($poli->id)->where('klinik_cabang_id', $cabangId),
            ],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['sometimes', 'boolean'],
        ], [
            'kode_poli.required' => 'Kode poli wajib diisi.',
            'kode_poli.unique' => 'Kode poli sudah digunakan di cabang ini.',
            'nama_poli.required' => 'Nama poli wajib diisi.',
            'nama_poli.unique' => 'Nama poli sudah digunakan di cabang ini.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $poli->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Poli berhasil diperbarui.',
            'data' => $poli,
        ]);
    }

    /**
     * PBI-05: Toggle Status Aktif/Nonaktif Poli
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $poli = Poli::find($id);

        if (! $poli) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data poli tidak ditemukan.',
            ], 404);
        }

        $poli->status = ! $poli->status;
        $poli->save();

        $statusText = $poli->status ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'status' => 'success',
            'message' => "Poli berhasil {$statusText}.",
            'data' => $poli,
        ]);
    }
}
