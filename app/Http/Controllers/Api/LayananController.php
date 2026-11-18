<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KlinikCabang;
use App\Models\Layanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LayananController extends Controller
{
    /**
     * PBI-12: Tampilkan Daftar Layanan & Tarif Klinik
     */
    public function index(Request $request): JsonResponse
    {
        $query = Layanan::with(['poli:id,nama_poli', 'klinikCabang:id,nama_cabang']);

        if (! $request->user() || $request->user()->role !== 'admin') {
            $query->where('status', true);
        } elseif ($request->has('status') && $request->status !== 'all') {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('poli_id')) {
            $query->where('poli_id', $request->poli_id);
        }

        if ($request->filled('q')) {
            $kw = $request->q;
            $query->where(function ($q) use ($kw) {
                $q->where('nama_layanan', 'like', "%{$kw}%")
                    ->orWhere('kode_layanan', 'like', "%{$kw}%");
            });
        }

        $layanans = $query->orderBy('nama_layanan', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $layanans,
        ]);
    }

    /**
     * Detail Layanan & Tarif
     */
    public function show(string $id): JsonResponse
    {
        $layanan = Layanan::with(['poli', 'klinikCabang'])->find($id);

        if (! $layanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan & tarif tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $layanan,
        ]);
    }

    /**
     * PBI-12: Tambah Layanan Baru (Khusus Admin)
     */
    public function store(Request $request): JsonResponse
    {
        $cabangId = $request->user()?->klinik_cabang_id ?? KlinikCabang::first()?->id ?? 1;

        $validator = Validator::make($request->all(), [
            'nama_layanan' => [
                'required',
                'string',
                'max:150',
                Rule::unique('layanans', 'nama_layanan')->where('klinik_cabang_id', $cabangId),
            ],
            'kode_layanan' => ['nullable', 'string', 'max:30'],
            'poli_id' => ['nullable', 'exists:polis,id'],
            'tarif_umum' => ['required', 'numeric', 'min:0'],
            'tarif_bpjs' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['sometimes', 'boolean'],
        ], [
            'nama_layanan.required' => 'Nama layanan wajib diisi.',
            'nama_layanan.unique' => 'Nama layanan sudah digunakan di cabang ini.',
            'tarif_umum.required' => 'Tarif umum wajib diisi.',
            'tarif_umum.min' => 'Tarif umum tidak boleh kurang dari 0.',
            'tarif_bpjs.required' => 'Tarif BPJS wajib diisi.',
            'tarif_bpjs.min' => 'Tarif BPJS tidak boleh kurang dari 0.',
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

        $layanan = Layanan::create($payload);

        return response()->json([
            'status' => 'success',
            'message' => 'Layanan dan tarif berhasil ditambahkan.',
            'data' => $layanan->load('poli'),
        ], 201);
    }

    /**
     * PBI-12: Perbarui Data Layanan & Tarif (Khusus Admin)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $layanan = Layanan::find($id);

        if (! $layanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan & tarif tidak ditemukan.',
            ], 404);
        }

        $cabangId = $layanan->klinik_cabang_id;

        $validator = Validator::make($request->all(), [
            'nama_layanan' => [
                'required',
                'string',
                'max:150',
                Rule::unique('layanans', 'nama_layanan')->ignore($layanan->id)->where('klinik_cabang_id', $cabangId),
            ],
            'kode_layanan' => ['nullable', 'string', 'max:30'],
            'poli_id' => ['nullable', 'exists:polis,id'],
            'tarif_umum' => ['required', 'numeric', 'min:0'],
            'tarif_bpjs' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['sometimes', 'boolean'],
        ], [
            'nama_layanan.required' => 'Nama layanan wajib diisi.',
            'nama_layanan.unique' => 'Nama layanan sudah digunakan di cabang ini.',
            'tarif_umum.min' => 'Tarif umum tidak boleh kurang dari 0.',
            'tarif_bpjs.min' => 'Tarif BPJS tidak boleh kurang dari 0.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $layanan->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Layanan dan tarif berhasil diperbarui.',
            'data' => $layanan->load('poli'),
        ]);
    }

    /**
     * PBI-12: Toggle Status Aktif/Nonaktif Layanan
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $layanan = Layanan::find($id);

        if (! $layanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan & tarif tidak ditemukan.',
            ], 404);
        }

        $layanan->status = ! $layanan->status;
        $layanan->save();

        $statusText = $layanan->status ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'status' => 'success',
            'message' => "Layanan berhasil {$statusText}.",
            'data' => $layanan,
        ]);
    }
}
