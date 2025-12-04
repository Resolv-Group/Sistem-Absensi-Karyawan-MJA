<div>
    <div class="flex items-center gap-4 mb-6">
        <h3 class="text-lg font-bold text-gray-900 whitespace-nowrap">Emergency Contact</h3>
        <div class="h-px bg-gray-200 w-full"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Kontak</label>
            <p class="text-base font-semibold text-gray-900">{{ $pekerja->emergency_nama ?? '-' }}</p>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hubungan</label>
            <p class="text-base font-semibold text-gray-900">{{ $pekerja->emergency_hubungan ?? '-' }}</p>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nomor Telepon</label>
            <p class="text-base font-semibold text-gray-900">{{ $pekerja->emergency_telp ?? '-' }}</p>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Ibu Kandung</label>
            <p class="text-base font-semibold text-gray-900">{{ $pekerja->ibu_kandung ?? '-' }}</p>
        </div>
    </div>
</div>
