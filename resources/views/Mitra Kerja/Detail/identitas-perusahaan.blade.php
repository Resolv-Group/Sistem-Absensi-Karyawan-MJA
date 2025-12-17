<div>
    <div class="flex items-center gap-4 mb-6">
        <h3 class="text-lg font-bold text-gray-900 whitespace-nowrap">Identitas Perusahaan</h3>
        <div class="h-px bg-gray-200 w-full"></div>
    </div>

    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
        <div>
            <dt class="text-sm font-medium text-gray-500">Nama Perusahaan</dt>
            <dd class="mt-1 text-sm text-gray-900 font-semibold break-words">{{ ucwords($mitraKerja->nama_mitra) }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Nomor Telepon</dt>
            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $mitraKerja->telp_formatted }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Bidang Usaha</dt>
            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $mitraKerja->bidangUsaha->nama }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Status Pajak</dt>
            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $mitraKerja->status_pajak }}</dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-sm font-medium text-gray-500">Alamat Lengkap</dt>
            <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-100">
                {{ $mitraKerja->alamat_formatted }}
            </dd>
        </div>
    </dl>
</div>
