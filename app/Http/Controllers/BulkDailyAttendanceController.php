<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\PKWT;
use App\Models\Unit;
use App\Support\AttendanceDates;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BulkDailyAttendanceController extends Controller
{
    private function unit(Request $request): Unit
    {
        $unit = Unit::findOrFail($request->route('id_unit'));
        $staff = $request->user()->staff;
        abort_unless($staff && (strtolower($staff->jabatan) === 'admin'
            || $unit->picUnit()->where('id_pic', $staff->id)->exists()), 403);
        abort_unless((int) $unit->sistem_pengajian === 1, 422);

        return $unit;
    }

    public function show(Request $request, Unit $unit, array $dates)
    {
        $query = PKWT::with('pekerja:id,nama,nik')->where('id_unit', $unit->id)->where('status_aktif', 1);
        if ($request->filled('search')) {
            $search = mb_substr((string) $request->input('search'), 0, 100);
            $query->whereHas('pekerja', fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%"));
        }
        $page = $query->orderBy('id')->paginate(25)->withQueryString();
        $workerPage = [
            'workers' => $page->map(fn ($pkwt) => ['id' => $pkwt->id, 'name' => $pkwt->pekerja->nama, 'nik' => $pkwt->pekerja->nik])->values(),
            'page' => $page->currentPage(), 'total' => $page->total(),
            'next' => $page->nextPageUrl(), 'previous' => $page->previousPageUrl(),
        ];
        if ($request->ajax()) {
            return response()->json($workerPage);
        }

        return view('Absensi.detail.bulk-harian', compact('unit', 'dates', 'workerPage'));
    }

    private function selection(Request $request): array
    {
        $dates = AttendanceDates::fromRequest($request);
        $unit = $this->unit($request);
        $fields = 'status_kehadiran,hours_mode,jam_aktual,is_hbn,is_paid,is_paid_leave,catatan';
        $rules = [
            'dates' => 'required|array|min:2|max:7',
            'worker_ids' => 'required|array|min:1|max:25',
            'worker_ids.*' => 'required|integer|distinct',
            'rule' => 'required|array:'.$fields,
            'rule.status_kehadiran' => 'required|integer|between:1,6',
            'exceptions' => 'sometimes|array|max:175',
            'exceptions.*' => 'required|array:worker_id,date,rule',
            'exceptions.*.worker_id' => 'required|integer',
            'exceptions.*.date' => 'required|date_format:Y-m-d',
            'exceptions.*.rule' => 'required|array:'.$fields,
            'existing_policy' => 'sometimes|in:skip,update',
        ];
        foreach (['rule', 'exceptions.*.rule'] as $prefix) {
            $rules += [
                "$prefix.status_kehadiran" => 'sometimes|integer|between:1,6',
                "$prefix.hours_mode" => 'sometimes|in:schedule,custom',
                "$prefix.jam_aktual" => 'nullable|numeric|min:0|max:24',
                "$prefix.is_hbn" => 'sometimes|boolean',
                "$prefix.is_paid" => 'sometimes|boolean',
                "$prefix.is_paid_leave" => 'sometimes|boolean',
                "$prefix.catatan" => 'nullable|string|max:255',
            ];
        }
        $data = $request->validate($rules);
        $workers = PKWT::with(['pekerja', 'hariKerja'])->where('id_unit', $unit->id)->where('status_aktif', 1)
            ->whereIn('id', $data['worker_ids'])->get()->keyBy('id');
        if ($workers->count() !== count($data['worker_ids']) || $workers->pluck('id_pekerja')->unique()->count() !== $workers->count()) {
            throw ValidationException::withMessages(['worker_ids' => 'Pilih pekerja aktif yang berbeda dari unit ini.']);
        }
        $rule = array_replace(['hours_mode' => 'schedule', 'is_hbn' => false, 'is_paid' => true,
            'is_paid_leave' => false, 'catatan' => null], $data['rule']);
        $this->validateHours($rule, 'rule');
        $exceptions = [];
        foreach ($data['exceptions'] ?? [] as $index => $exception) {
            $key = $exception['worker_id'].'|'.$exception['date'];
            if (!$workers->has($exception['worker_id']) || !in_array($exception['date'], $dates, true) || isset($exceptions[$key])) {
                throw ValidationException::withMessages(['exceptions' => 'Pengecualian harus unik dan hanya untuk pekerja serta tanggal terpilih.']);
            }
            $resolved = array_replace($rule, $exception['rule']);
            $this->validateHours($resolved, "exceptions.$index.rule");
            $exceptions[$key] = $resolved;
        }

        return compact('unit', 'dates', 'workers', 'rule', 'exceptions');
    }

    private function validateHours(array $rule, string $key): void
    {
        if ((int) $rule['status_kehadiran'] === 1 && $rule['hours_mode'] === 'custom'
            && !isset($rule['jam_aktual'])) {
            throw ValidationException::withMessages(["$key.jam_aktual" => 'Isi jam kerja untuk aturan Hadir dengan jam khusus.']);
        }
    }

    private function existing(array $selection)
    {
        return Absensi::where('id_unit', $selection['unit']->id)->whereIn('tgl_absensi', $selection['dates'])
            ->whereIn('id_pekerja', $selection['workers']->pluck('id_pekerja'))
            ->orderBy('id')->get()->keyBy(fn ($row) => $row->id_pekerja.'|'.$row->tgl_absensi);
    }

    private function summary(array $selection, $existing): array
    {
        $total = count($selection['dates']) * $selection['workers']->count();
        $workers = $selection['workers']->keyBy('id_pekerja');
        return [
            'total' => $total, 'new' => $total - $existing->count(), 'existing' => $existing->count(),
            // An existence snapshot catches records created after the user reviewed the counts.
            'preview_version' => hash('sha256', json_encode([
                $selection['unit']->id, $selection['dates'], $selection['workers']->keys()->sort()->values(), $existing->keys()->sort()->values(),
            ])),
            'existing_records' => $existing->map(fn ($row) => [
                'date' => $row->tgl_absensi, 'name' => $workers[$row->id_pekerja]->pekerja->nama,
            ])->values(),
        ];
    }

    public function preview(Request $request)
    {
        $selection = $this->selection($request);
        return response()->json($this->summary($selection, $this->existing($selection)));
    }

    public function store(Request $request)
    {
        $selection = $this->selection($request);
        $request->validate(['preview_version' => 'required|string', 'overwrite_confirmed' => 'sometimes|boolean']);
        $response = DB::transaction(function () use ($request, $selection) {
            Unit::whereKey($selection['unit']->id)->lockForUpdate()->firstOrFail();
            $existing = $this->existing($selection);
            $summary = $this->summary($selection, $existing);
            if (!hash_equals($summary['preview_version'], $request->input('preview_version'))) {
                return response()->json(['message' => 'Jumlah absensi berubah. Periksa ringkasan terbaru sebelum menyimpan.', 'preview' => $summary], 409);
            }
            $update = $request->input('existing_policy', 'skip') === 'update';
            if ($update && $existing->isNotEmpty() && !$request->boolean('overwrite_confirmed')) {
                throw ValidationException::withMessages(['overwrite_confirmed' => 'Konfirmasi penggantian absensi yang sudah ada.']);
            }
            $result = ['created' => 0, 'updated' => 0, 'skipped' => 0];
            foreach ($selection['workers'] as $worker) {
                foreach ($selection['dates'] as $date) {
                    $attendance = $existing->get($worker->id_pekerja.'|'.$date);
                    if ($attendance && !$update) {
                        $result['skipped']++;
                        continue;
                    }
                    $rule = $selection['exceptions'][$worker->id.'|'.$date] ?? $selection['rule'];
                    $normal = (float) ($worker->hariKerja->firstWhere('hari', strtolower(Carbon::parse($date)->format('D')))?->jam_kerja ?? 0);
                    $present = (int) $rule['status_kehadiran'] === 1;
                    $actual = $present ? ($rule['hours_mode'] === 'schedule' ? $normal : (float) $rule['jam_aktual']) : 0;
                    $hbn = $present && $rule['is_hbn'];
                    $result[$attendance ? 'updated' : 'created']++;
                    $attendance ??= Absensi::create([
                        'id_unit' => $selection['unit']->id, 'id_pekerja' => $worker->id_pekerja,
                        'tgl_absensi' => $date, 'id_pic' => $request->user()->staff->id, 'tipe' => 1, 'verifikasi' => 0,
                    ]);
                    $attendance->detilHarian()->updateOrCreate(['id_absensi' => $attendance->id], [
                        'status_kehadiran' => $rule['status_kehadiran'], 'jam_kerja_normal' => $normal,
                        'jam_kerja_harian' => $actual, 'overtime' => $present ? max(0, $actual - ($hbn ? 0 : $normal)) : 0,
                        'hbn' => (int) $hbn, 'isPaid' => $present ? (int) $rule['is_paid'] : 0,
                        'paidLeave' => $present ? 0 : (int) $rule['is_paid_leave'],
                        'catatan' => $rule['catatan'], 'updated_by' => $request->user()->id,
                    ]);
                    $attendance->update(['verifikasi' => 0]);
                }
            }
            return response()->json($result);
        });
        if ($response->getStatusCode() === 200) {
            $counts = $response->getData(true);
            $request->session()->flash('success', "Absensi massal selesai: {$counts['created']} dibuat, {$counts['updated']} diperbarui, {$counts['skipped']} dilewati.");
        }

        return $response;
    }
}
