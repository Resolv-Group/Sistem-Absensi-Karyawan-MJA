<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BulkDailyAttendanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Isolated in-memory fixture: never migrate or modify the application database.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        foreach (['2026_01_02_143649_create_absensi_table.php', '2026_01_02_144226_create_detil_harian_table.php',
            '2026_02_18_124534_create_tunjangan_table.php', '2026_02_18_124927_create_potongan_table.php'] as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }
        Schema::create('unit', function (Blueprint $table) {
            $table->id();
            $table->integer('sistem_pengajian')->default(1);
            $table->integer('id_mitra_kerja')->nullable();
        });
        Schema::create('mitra_kerja', fn (Blueprint $table) => $table->id());
        Schema::create('pekerja', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik')->nullable();
        });
        Schema::create('pkwt_pekerja', function (Blueprint $table) {
            $table->id();
            $table->integer('id_pekerja');
            $table->integer('id_unit');
            $table->integer('status_aktif')->default(1);
        });
        Schema::create('pkwt_hari_kerja', function (Blueprint $table) {
            $table->id();
            $table->integer('pkwt_id');
            $table->string('hari');
            $table->decimal('jam_kerja');
        });
        Schema::create('pic_unit', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->integer('id_pic');
        });
        DB::table('unit')->insert(['id' => 1, 'sistem_pengajian' => 1]);
        DB::table('pekerja')->insert(['id' => 1, 'nama' => 'Bani']);
        DB::table('pkwt_pekerja')->insert(['id' => 1, 'id_pekerja' => 1, 'id_unit' => 1]);
        DB::table('pkwt_hari_kerja')->insert([
            ['pkwt_id' => 1, 'hari' => 'fri', 'jam_kerja' => 8],
            ['pkwt_id' => 1, 'hari' => 'sat', 'jam_kerja' => 5],
        ]);
        $user = new User(['role' => 'admin']);
        $user->id = 1;
        $user->setRelation('staff', new Staff(['jabatan' => 'admin']));
        $user->staff->id = 1;
        $this->actingAs($user);
    }

    private function payload(array $dates, bool $confirmed = false): array
    {
        return ['date' => $dates[0] ?? '2026-09-25', 'dates' => $dates,
            'overwrite_confirmed' => $confirmed,
            'data' => [1 => ['jam_aktual' => 9, 'jam_normal' => 99, 'overtime' => 99, 'is_paid' => 1]]];
    }

    private function save(array $payload)
    {
        return $this->putJson('/absensi/1/harian/2026-09-25/bulk-update-harian', $payload);
    }

    public function test_sparse_dates_do_not_fill_gaps_and_hours_are_calculated_per_day(): void
    {
        $this->save($this->payload(['2026-09-25', '2026-09-27']))->assertRedirect();
        $this->assertDatabaseCount('absensi', 2);
        $this->assertDatabaseMissing('absensi', ['tgl_absensi' => '2026-09-26']);
        $this->assertDatabaseHas('detil_harian', ['jam_kerja_normal' => 8, 'overtime' => 1]);
        $this->assertDatabaseHas('detil_harian', ['jam_kerja_normal' => 0, 'overtime' => 9]);
    }

    public function test_seven_dates_are_allowed_and_eight_are_rejected(): void
    {
        $dates = array_map(fn ($day) => '2026-09-'.$day, range(20, 27));
        $this->save($this->payload($dates))->assertUnprocessable()->assertJsonValidationErrors('dates');
        $this->assertDatabaseCount('absensi', 0);
        $this->save($this->payload(array_slice($dates, 0, 7)))->assertRedirect();
        $this->assertDatabaseCount('absensi', 7);
    }

    public function test_invalid_duplicate_and_empty_dates_are_rejected(): void
    {
        foreach ([[], ['2026-02-30'], ['2026-09-25', '2026-09-25']] as $dates) {
            $this->save($this->payload($dates))->assertUnprocessable();
        }
        $this->assertDatabaseCount('absensi', 0);
    }

    public function test_existing_dates_require_confirmation_then_update_without_duplicates(): void
    {
        $this->save($this->payload(['2026-09-25', '2026-09-26']))->assertRedirect();
        $ids = Absensi::pluck('id', 'tgl_absensi');
        Absensi::query()->update(['verifikasi' => 1]);
        $payload = $this->payload(['2026-09-23', '2026-09-24', '2026-09-25', '2026-09-26', '2026-09-27']);
        $payload['data'][1]['jam_aktual'] = 7;
        $this->postJson('/absensi/1/harian/preview-bulk', $payload)->assertOk()
            ->assertJson(['count' => 2, 'dates' => ['2026-09-25', '2026-09-26']]);
        $this->save($payload)->assertUnprocessable()->assertJsonValidationErrors('overwrite_confirmed');
        $this->assertDatabaseCount('absensi', 2);
        $this->assertDatabaseHas('detil_harian', ['jam_kerja_harian' => 9]);
        $payload['overwrite_confirmed'] = true;
        $this->save($payload)->assertRedirect();
        $this->save($payload)->assertRedirect();
        $this->assertDatabaseCount('absensi', 5);
        $this->assertDatabaseCount('detil_harian', 5);
        foreach ($ids as $date => $id) {
            $this->assertDatabaseHas('absensi', ['id' => $id, 'tgl_absensi' => $date, 'verifikasi' => 0]);
            $this->assertDatabaseHas('detil_harian', ['id_absensi' => $id, 'jam_kerja_harian' => 7]);
        }
    }

    public function test_status_and_adjustments_apply_per_selected_date(): void
    {
        $payload = $this->payload(['2026-09-25', '2026-09-26'], true);
        $payload['data'] = [1 => ['status_kehadiran' => 3, 'is_paid_leave' => 1]];
        $this->putJson('/absensi/1/harian/2026-09-25/bulk-update-status-harian', $payload)->assertRedirect();
        $this->assertDatabaseCount('detil_harian', 2);
        $this->assertDatabaseHas('detil_harian', ['jam_kerja_normal' => 5, 'paidLeave' => 1]);
        $payload['data'] = [1 => ['kategori' => '{"makan":10000}', 'total' => 10000]];
        foreach (['tunjangan', 'potongan'] as $kind) {
            $this->postJson('/absensi/1/harian/2026-09-25/bulk-update-'.$kind, $payload)->assertRedirect();
            $this->assertDatabaseCount($kind, 2);
            $this->assertEquals(20000, DB::table($kind)->sum('total'));
        }
    }

    public function test_failed_adjustment_rolls_back_all_dates(): void
    {
        $this->save($this->payload(['2026-09-25']))->assertRedirect();
        $payload = $this->payload(['2026-09-25', '2026-09-26'], true);
        $payload['data'] = [1 => ['kategori' => '{"makan":10000}', 'total' => 10000]];
        $this->postJson('/absensi/1/harian/2026-09-25/bulk-update-tunjangan', $payload)->assertRedirect();
        $this->assertDatabaseCount('tunjangan', 0);
    }

    public function test_workers_from_other_units_and_oversized_batches_are_rejected(): void
    {
        DB::table('pkwt_pekerja')->where('id', 1)->update(['id_unit' => 2]);
        $this->save($this->payload(['2026-09-25']))->assertUnprocessable()->assertJsonValidationErrors('data');
        $payload = $this->payload(['2026-09-25']);
        $payload['data'] = array_fill(1, 26, ['jam_aktual' => 8]);
        $this->save($payload)->assertUnprocessable()->assertJsonValidationErrors('data');
        $this->assertDatabaseCount('absensi', 0);
    }

    public function test_pic_cannot_update_unassigned_unit(): void
    {
        auth()->user()->staff->jabatan = 'PIC';
        $this->save($this->payload(['2026-09-25']))->assertForbidden();
    }

    public function test_legacy_single_date_payload_still_works(): void
    {
        $payload = $this->payload(['2026-09-25']);
        unset($payload['dates']);
        $this->save($payload)->assertRedirect();
        $this->assertDatabaseCount('absensi', 1);
    }

    public function test_table_loads_only_current_page_and_retains_selected_dates(): void
    {
        foreach (range(2, 30) as $id) {
            DB::table('pekerja')->insert(['id' => $id, 'nama' => 'Pekerja '.$id]);
            DB::table('pkwt_pekerja')->insert(['id' => $id, 'id_pekerja' => $id, 'id_unit' => 1]);
        }
        $response = $this->getJson('/absensi/1/harian/2026-09-25?dates[]=2026-09-25&dates[]=2026-09-27',
            ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertOk()->assertJsonCount(25, 'workers');
        $this->assertStringContainsString('dates%5B1%5D=2026-09-27', $response->json('next'));
        $response = $this->getJson('/absensi/1/harian/2026-09-25?dates[]=2026-09-25&dates[]=2026-09-27&page=2',
            ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertOk()->assertJsonCount(5, 'workers');
    }

    public function test_daily_page_uses_rule_editor_only_for_multiple_dates(): void
    {
        $this->get('/absensi/1/harian/2026-09-25?dates[]=2026-09-25&dates[]=2026-09-27')
            ->assertOk()->assertSee('Aturan default')->assertSee('Atur pengecualian')
            ->assertSee('Lewati yang sudah ada')->assertSee('Perbarui yang sudah ada');
        $this->get('/absensi/1/harian/2026-09-25?dates[]=2026-09-25')
            ->assertOk()->assertSee('absenJamForm', false)->assertDontSee('Aturan default');
    }

    private function rulePayload(): array
    {
        return ['dates' => ['2026-09-25', '2026-09-26'], 'worker_ids' => [1],
            'rule' => ['status_kehadiran' => 1, 'hours_mode' => 'schedule'], 'exceptions' => []];
    }

    private function saveRule(array $payload)
    {
        $preview = $this->postJson('/absensi/1/harian/rules/preview', $payload)->assertOk()->json();
        return $this->postJson('/absensi/1/harian/rules', $payload + ['preview_version' => $preview['preview_version']]);
    }

    public function test_rule_preview_counts_worker_date_combinations(): void
    {
        foreach (range(2, 12) as $id) {
            DB::table('pekerja')->insert(['id' => $id, 'nama' => 'Pekerja '.$id]);
            DB::table('pkwt_pekerja')->insert(['id' => $id, 'id_pekerja' => $id, 'id_unit' => 1]);
        }
        $dates = ['2026-09-21', '2026-09-22', '2026-09-23', '2026-09-24', '2026-09-25'];
        $this->save($this->payload($dates))->assertRedirect();
        $seed = $this->payload(['2026-09-25']);
        $seed['data'] = [2 => ['jam_aktual' => 8]];
        $this->save($seed)->assertRedirect();
        $payload = $this->rulePayload();
        $payload['dates'] = $dates;
        $payload['worker_ids'] = range(1, 12);
        $this->postJson('/absensi/1/harian/rules/preview', $payload)->assertOk()
            ->assertJson(['total' => 60, 'new' => 54, 'existing' => 6])->assertJsonCount(6, 'existing_records');
        $this->saveRule($payload)->assertOk()->assertJson(['created' => 54, 'updated' => 0, 'skipped' => 6])
            ->assertSessionHas('success', 'Absensi massal selesai: 54 dibuat, 0 diperbarui, 6 dilewati.');
        $this->assertDatabaseCount('absensi', 60);
    }

    public function test_rule_skip_is_default_and_preserves_existing_verification(): void
    {
        $this->save($this->payload(['2026-09-25']))->assertRedirect();
        Absensi::query()->update(['verifikasi' => 1]);
        $existingId = Absensi::first()->id;
        $this->saveRule($this->rulePayload())->assertOk()->assertJson(['created' => 1, 'updated' => 0, 'skipped' => 1]);
        $this->assertDatabaseHas('absensi', ['id' => $existingId, 'verifikasi' => 1]);
        $this->assertDatabaseHas('detil_harian', ['id_absensi' => $existingId, 'jam_kerja_harian' => 9]);
        $this->assertDatabaseHas('detil_harian', ['jam_kerja_harian' => 5, 'jam_kerja_normal' => 5]);
    }

    public function test_rule_exceptions_apply_to_only_the_selected_worker_and_date(): void
    {
        DB::table('pekerja')->insert(['id' => 2, 'nama' => 'Citra']);
        DB::table('pkwt_pekerja')->insert(['id' => 2, 'id_pekerja' => 2, 'id_unit' => 1]);
        $payload = $this->rulePayload();
        $payload['worker_ids'] = [1, 2];
        $payload['rule'] = ['status_kehadiran' => 1, 'hours_mode' => 'custom', 'jam_aktual' => 9];
        $payload['exceptions'] = [
            ['worker_id' => 1, 'date' => '2026-09-25', 'rule' => ['status_kehadiran' => 4, 'catatan' => 'Sakit']],
            ['worker_id' => 2, 'date' => '2026-09-26', 'rule' => ['status_kehadiran' => 3, 'is_paid_leave' => true]],
        ];
        $this->saveRule($payload)->assertOk()->assertJson(['created' => 4]);
        $rows = Absensi::with('detilHarian')->get()->keyBy(fn ($row) => $row->id_pekerja.'|'.$row->tgl_absensi);
        $this->assertEquals(4, $rows['1|2026-09-25']->detilHarian->status_kehadiran);
        $this->assertEquals(0, $rows['1|2026-09-25']->detilHarian->jam_kerja_harian);
        $this->assertEquals(4, $rows['1|2026-09-26']->detilHarian->overtime);
        $this->assertEquals(1, $rows['2|2026-09-25']->detilHarian->status_kehadiran);
        $this->assertEquals(3, $rows['2|2026-09-26']->detilHarian->status_kehadiran);
        $this->assertEquals(1, $rows['2|2026-09-26']->detilHarian->paidLeave);
    }

    public function test_rule_update_requires_confirmation_and_reuses_records(): void
    {
        $payload = $this->rulePayload();
        $this->saveRule($payload)->assertOk();
        $ids = Absensi::pluck('id')->all();
        $payload['existing_policy'] = 'update';
        $payload['rule'] = ['status_kehadiran' => 6];
        $this->saveRule($payload)->assertUnprocessable()->assertJsonValidationErrors('overwrite_confirmed');
        $payload['overwrite_confirmed'] = true;
        $this->saveRule($payload)->assertOk()->assertJson(['created' => 0, 'updated' => 2, 'skipped' => 0]);
        $this->assertEquals($ids, Absensi::pluck('id')->all());
        $this->assertDatabaseCount('detil_harian', 2);
        $this->assertDatabaseHas('detil_harian', ['status_kehadiran' => 6, 'jam_kerja_harian' => 0]);
    }

    public function test_rule_rechecks_existence_before_saving(): void
    {
        $payload = $this->rulePayload();
        $preview = $this->postJson('/absensi/1/harian/rules/preview', $payload)->assertOk()->json();
        $this->save($this->payload(['2026-09-25']))->assertRedirect();
        $this->postJson('/absensi/1/harian/rules', $payload + ['preview_version' => $preview['preview_version']])
            ->assertStatus(409)->assertJsonPath('preview.existing', 1);
        $this->assertDatabaseCount('absensi', 1);
    }

    public function test_rule_rejects_exceptions_outside_selection_or_duplicates(): void
    {
        foreach ([
            [['worker_id' => 2, 'date' => '2026-09-25', 'rule' => ['status_kehadiran' => 4]]],
            [['worker_id' => 1, 'date' => '2026-09-27', 'rule' => ['status_kehadiran' => 4]]],
            array_fill(0, 2, ['worker_id' => 1, 'date' => '2026-09-25', 'rule' => ['status_kehadiran' => 4]]),
        ] as $exceptions) {
            $payload = $this->rulePayload();
            $payload['exceptions'] = $exceptions;
            $this->postJson('/absensi/1/harian/rules/preview', $payload)->assertUnprocessable()->assertJsonValidationErrors('exceptions');
        }
    }

    public function test_rule_validates_limits_access_and_custom_hours(): void
    {
        $payload = $this->rulePayload();
        $payload['dates'] = array_map(fn ($day) => '2026-09-'.$day, range(20, 27));
        $this->postJson('/absensi/1/harian/rules/preview', $payload)->assertUnprocessable()->assertJsonValidationErrors('dates');
        $payload = $this->rulePayload();
        $payload['worker_ids'] = range(1, 26);
        $this->postJson('/absensi/1/harian/rules/preview', $payload)->assertUnprocessable()->assertJsonValidationErrors('worker_ids');
        $payload = $this->rulePayload();
        $payload['rule']['hours_mode'] = 'custom';
        $this->postJson('/absensi/1/harian/rules/preview', $payload)->assertUnprocessable()->assertJsonValidationErrors('rule.jam_aktual');
        auth()->user()->staff->jabatan = 'PIC';
        $this->postJson('/absensi/1/harian/rules/preview', $this->rulePayload())->assertForbidden();
    }
}
