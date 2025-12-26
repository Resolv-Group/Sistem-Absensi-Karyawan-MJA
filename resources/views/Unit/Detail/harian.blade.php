{{-- Toolbar Pekerja --}}
<div
    class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/30">
    <div class="flex items-center gap-2">
        <p class="text-sm text-gray-500">Menampilkan daftar pekerja harian/kontrak.</p>
    </div>
    <div class="flex items-center gap-3 w-full sm:w-auto">
        <div class="relative w-full sm:w-64">
            <input type="text" placeholder="Cari pekerja..."
                class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition bg-white">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        {{-- Add Button for Pekerja --}}
        <a href="{{ route('view.tambah.unit-pekerja', $unit->id) }}"
            class="px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Pekerja
        </a>
    </div>
</div>

{{-- Table Pekerja --}}
<div class="overflow-x-auto rounded-b-2xl">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
                {{-- 1. Checkbox --}}
                <th class="pl-6 py-4 w-10">
                    <input type="checkbox"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 cursor-pointer">
                </th>
                {{-- 2. Number --}}
                <th class="px-2 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider w-10 text-center">#
                </th>

                <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider w-[250px]">Nama & NIK
                </th>
                <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Jabatan & Divisi</th>

                {{-- PKWT Document --}}
                <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">PKWT</th>

                {{-- Date --}}
                <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Periode
                    PKWT</th>

                {{-- Status --}}
                <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Status
                </th>

                <th class="pr-6 py-4 text-right"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 bg-white">
            @forelse($pkwtPekerja as $pkwt)
                <tr class="hover:bg-blue-50/20 transition group">

                    {{-- 1. Checkbox --}}
                    <td class="pl-6 py-5 align-top">
                        <input type="checkbox"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 cursor-pointer mt-1">
                    </td>

                    {{-- 2. Number --}}
                    <td class="px-2 py-5 align-top text-center">
                        <span class="text-xs font-bold text-gray-400 font-mono">{{ $loop->iteration }}</span>
                    </td>

                    {{-- 3. Name & NIK --}}
                    <td class="px-4 py-5 align-top">
                        <div class="flex flex-col gap-0.5">
                            <span
                                class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition truncate max-w-[200px]"
                                title="{{ $pkwt->pekerja->nama }}">
                                {{ $pkwt->pekerja->nama }}
                            </span>
                            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <span class="font-mono tracking-tight">{{ $pkwt->pekerja->nik }}</span>
                            </div>
                        </div>
                    </td>

                    {{-- 4. Jabatan & Divisi (Clean Badge Style) --}}
                    <td class="px-4 py-5 align-top">
                        <div class="flex flex-col gap-2">
                            <span
                                class="inline-flex items-center text-xs font-semibold text-gray-800 bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200 w-fit max-w-[180px] truncate"
                                title="{{ $pkwt->jabatan->nama }}">
                                {{ $pkwt->jabatan->nama }}
                            </span>
                            <div class="flex items-center gap-1.5 text-xs text-gray-500 pl-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                <span class="truncate max-w-[180px]"
                                    title="{{ $pkwt->divisi->nama }}">{{ $pkwt->divisi->nama }}</span>
                            </div>
                        </div>
                    </td>

                    {{-- 5. PKWT Document --}}
                    @php $mime = $pkwt->dokumen_mime; @endphp
                    <td class="px-4 py-5 align-top text-center">
                        @if ($mime)
                            <a href="{{ route('stream.pkwt', $pkwt->id) }}" target="_blank"
                                class="inline-flex flex-col items-center justify-center gap-1 p-2 rounded-lg hover:bg-gray-50 transition group/doc border border-transparent hover:border-gray-200">
                                @if (Str::startsWith($mime, 'application/pdf'))
                                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <span
                                        class="text-[9px] font-bold text-gray-500 group-hover/doc:text-red-600">PDF</span>
                                @else
                                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span
                                        class="text-[9px] font-bold text-gray-500 group-hover/doc:text-blue-600">IMG</span>
                                @endif
                            </a>
                        @else
                            <span class="text-xs text-gray-300 italic">-</span>
                        @endif
                    </td>

                    {{-- 6. Periode PKWT --}}
                    <td class="px-4 py-5 align-top text-center">
                        <div class="flex flex-col items-center gap-1">
                            <span class="text-xs font-medium text-gray-700 bg-gray-50 px-2 py-1 rounded">
                                {{ \Carbon\Carbon::parse($pkwt->tgl_mulai_pkwt)->format('d M Y') }}
                            </span>
                            <svg class="w-3 h-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            <span
                                class="text-xs font-bold {{ $pkwt->status_pkwt['color'] === 'red' ? 'text-red-600 bg-red-50' : 'text-emerald-600 bg-emerald-50' }} px-2 py-1 rounded">
                                {{ \Carbon\Carbon::parse($pkwt->tgl_akhir_pkwt)->format('d M Y') }}
                            </span>
                        </div>
                    </td>

                    {{-- 7. Status --}}
                    <td class="px-4 py-5 align-top text-center">
                        @if ($pkwt->status_aktif == 1)
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <span class="relative flex h-1.5 w-1.5">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                </span>
                                Aktif
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                Nonaktif
                            </span>
                        @endif
                    </td>

                    {{-- 8. Actions --}}
                    <td class="pr-6 py-5 align-top text-right">
                        <div class="flex justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('view.ubah.unit-pekerja', [
                                    'unitId' => $unit->id,
                                    'pekerjaId' => $pkwt->id
                                ]) }}"
                                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <button class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                title="Delete">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>

            @empty
                {{-- Empty State --}}
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <div class="h-12 w-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <p class="font-medium text-sm">Belum ada pekerja terdaftar.</p>
                        </div>
                    </td>
                </tr>

            @endforelse
        </tbody>
    </table>

    @if ($pkwtPekerja->hasPages())
        <!-- Ensure this ID matches the JS selector -->
        <div id="search-pagination" class="border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
            {{ $pkwtPekerja->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
