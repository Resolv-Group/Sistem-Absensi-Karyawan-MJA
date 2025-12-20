{{-- ====================== PERSONAL INFORMATION ====================== --}}
<div>
    {{-- Section 1: Identitas Pribadi --}}
    <div>
        <div class="flex items-center gap-4 mb-6">
            <h3 class="text-lg font-bold text-gray-900 whitespace-nowrap">Identitas Pribadi</h3>
            <div class="h-px bg-gray-200 w-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                <p class="text-base font-semibold text-gray-900 break-words">{{ ucwords($pekerja->nama) }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">No.NIK</label>
                <p class="text-base font-semibold text-gray-900">{{ $pekerja->nik }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tempat & Tanggal Lahir</label>
                <p class="text-base font-semibold text-gray-900">{{ ucwords($pekerja->tempat_lahir) }}, {{ formatTanggal($pekerja->tgl_lahir) }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">No.KPJ</label>
                <p class="text-base font-semibold text-gray-900">{{ $pekerja->kpj }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                <p class="text-base font-semibold text-gray-900">{{ $pekerja->kelamin->label()}}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Pendidikan Terakhir</label>
                <p class="text-base font-semibold text-gray-900">{{ $pekerja->pendidikan }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Status Kawin</label>
                <p class="text-base font-semibold text-gray-900">{{ $pekerja->status_kawin }} ({{ $pekerja->anak }} Anak)</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">No.KK</label>
                <p class="text-base font-semibold text-gray-900">{{ $pekerja->no_kk }}</p>
            </div>

        </div>
    </div>

    {{-- Section 2: Informasi Kontak & Alamat --}}
    <div class="mt-10">
        <div class="flex items-center gap-4 mb-6">
            <h3 class="text-lg font-bold text-gray-900 whitespace-nowrap">Informasi Kontak & Alamat</h3>
            <div class="h-px bg-gray-200 w-full"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Email</label>
                <p class="text-base font-semibold text-gray-900">{{ $pekerja->email }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nomor Telepon Pribadi</label>
                <p class="text-base font-semibold text-gray-900">{{ $pekerja->telp }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">No.Rekening (Nama Bank)</label>
                <p class="text-base font-semibold text-gray-900">{{ $pekerja->rekening }} ({{ $pekerja->nama_rek }})</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Alamat Lengkap</label>
                <p class="text-base font-semibold text-gray-900">{{ ucwords($pekerja->alamat) }}, {{"RT $pekerja->rt"}} {{"RW $pekerja->rw"}}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Kelurahan / Desa</label>
                <p class="text-base font-semibold text-gray-900">{{ ucwords($pekerja->desa) }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Kecamatan</label>
                <p class="text-base font-semibold text-gray-900">{{ ucwords($pekerja->kecamatan) }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Kota / Kabupaten</label>
                <p class="text-base font-semibold text-gray-900">{{ ucwords($pekerja->kota) }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Provinsi</label>
                <p class="text-base font-semibold text-gray-900">{{ ucwords($pekerja->provinsi) }}</p>
            </div>
        </div>
    </div>
</div>
