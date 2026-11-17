<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminQueueController extends Controller
{
    /**
     * Menampilkan daftar monitoring dan pengelolaan antrean pasien untuk Admin.
     */
    public function index(Request $request)
    {
        // Tanggal antrean (default hari ini)
        $tanggal = $request->filled('tanggal')
            ? $request->query('tanggal')
            : Carbon::today()->toDateString();

        $query = Queue::with([
            'patientProfile.user',
            'doctorSchedule.doctor.user',
            'doctorSchedule.poli',
        ])->whereDate('tanggal_antrean', $tanggal);

        // Filter Poli
        if ($request->filled('poli_id')) {
            $query->whereHas('doctorSchedule', function ($q) use ($request) {
                $q->where('poli_id', $request->query('poli_id'));
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        // Filter Pencarian (Nama Pasien / No Antrean)
        if ($request->filled('q')) {
            $keyword = $request->query('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_antrean', 'like', "%{$keyword}%")
                    ->orWhereHas('patientProfile.user', function ($uq) use ($keyword) {
                        $uq->where('name', 'like', "%{$keyword}%")
                            ->orWhere('no_hp', 'like', "%{$keyword}%");
                    });
            });
        }

        // Urutkan: sedang dilayani (serving) paling atas, lalu dipanggil, lalu menunggu, lalu nomor antrean
        $queues = $query->orderByRaw("
            CASE status
                WHEN 'serving' THEN 1
                WHEN 'called' THEN 2
                WHEN 'waiting' THEN 3
                WHEN 'completed' THEN 4
                WHEN 'skipped' THEN 5
                ELSE 6
            END ASC
        ")->orderBy('nomor_antrean', 'asc')->paginate(15)->withQueryString();

        // Statistik ringkas untuk tanggal terpilih
        $stats = [
            'total'     => Queue::whereDate('tanggal_antrean', $tanggal)->count(),
            'waiting'   => Queue::whereDate('tanggal_antrean', $tanggal)->where('status', Queue::STATUS_WAITING)->count(),
            'serving'   => Queue::whereDate('tanggal_antrean', $tanggal)->where('status', Queue::STATUS_SERVING)->count(),
            'completed' => Queue::whereDate('tanggal_antrean', $tanggal)->where('status', Queue::STATUS_COMPLETED)->count(),
        ];

        $polis = Poli::where('status', true)->orderBy('nama_poli')->get();

        return view('admin.antrean.index', compact('queues', 'tanggal', 'stats', 'polis'));
    }

    /**
     * Memperbarui status antrean pasien secara langsung oleh Admin.
     */
    public function updateStatus(Request $request, Queue $queue)
    {
        $validated = $request->validate([
            'status' => 'required|in:waiting,called,serving,completed,skipped',
        ], [
            'status.required' => 'Status antrean wajib dipilih.',
            'status.in'       => 'Status antrean tidak valid.',
        ]);

        $statusLama = $queue->status_label;
        $queue->update([
            'status' => $validated['status'],
        ]);

        $statusBaru = $queue->status_label;
        $namaPasien = $queue->patientProfile->user->name ?? 'Pasien';

        return redirect()->back()->with(
            'sukses',
            "Antrean #{$queue->formatted_nomor} ({$namaPasien}) berhasil diperbarui: '{$statusLama}' → '{$statusBaru}'."
        );
    }
}
