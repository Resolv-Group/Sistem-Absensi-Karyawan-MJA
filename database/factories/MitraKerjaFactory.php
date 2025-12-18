<?php

namespace Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MitraKerja>
 */
class MitraKerjaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // ambil image random (logo style)
        $imageUrl = "https://ui-avatars.com/api/?name=" . urlencode($this->faker->company) . "&size=256";
        $imageBinary = Http::get($imageUrl)->body();

        return [
            'nama_mitra'          => $this->faker->company,
            'pimpinan'            => $this->faker->name,
            'alamat'              => $this->faker->address,
            'foto'                => $imageBinary,
            'bidang_usaha_id'     => $this->faker->numberBetween(1, 10),
            'telp_perusahaan'     => $this->faker->numerify('08##-####-####'),
            'status_aktif'        => $this->faker->boolean(90),
            'status_mou'          => $this->faker->randomElement(['Aktif', 'Tidak Aktif', 'Segera Habis']),
            'status_pajak'        => $this->faker->randomElement(['PKP', 'Non PKP']),
            'tgl_mulai_kerjasama' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'tgl_akhir_mou'       => $this->faker->boolean(70)
                ? $this->faker->dateTimeBetween('now', '+3 years')
                : null,
        ];
    }
}
