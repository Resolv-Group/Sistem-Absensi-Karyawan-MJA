@if ($unit->sistem_pengajian === 2)

    {{-- Toolbar Borongan --}}
    <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/30">
        <div class="flex items-center gap-2">
            <p class="text-sm text-gray-500">Menampilkan daftar paket borongan.</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" placeholder="Cari item..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition bg-white">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            {{-- Add Button --}}
            <a href="{{ route('view.tambah.unit-borongan', $unit->id) }}"
                class="px-4 py-2 bg-orange-600 text-white text-xs font-bold rounded-lg hover:bg-orange-700 transition flex items-center gap-2 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Borongan
            </a>
        </div>
    </div>

    {{-- Table Borongan --}}
    <div class="overflow-x-auto rounded-b-2xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    {{-- 1. Checkbox --}}
                    <th class="pl-6 py-4 w-10">
                        <input type="checkbox" class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 cursor-pointer">
                    </th>
                    {{-- 2. Number --}}
                    <th class="px-2 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider w-10 text-center">#</th>

                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider w-[250px]">
                        Item Borongan
                    </th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        Kategori
                    </th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        Harga Satuan <span class="normal-case font-normal text-gray-300">(Client)</span>
                    </th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        Upah Pekerja <span class="normal-case font-normal text-gray-300">(Worker)</span>
                    </th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="pr-6 py-4 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white">
                @forelse ($borongan as $b)
                    <tr class="hover:bg-orange-50/30 transition group">

                        {{-- 1. Checkbox --}}
                        <td class="pl-6 py-5 align-top">
                            <input type="checkbox" class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 cursor-pointer mt-1">
                        </td>

                        {{-- 2. Number --}}
                        <td class="px-2 py-5 align-top text-center">
                            <span class="text-xs font-bold text-gray-400 font-mono">{{ $loop->iteration }}</span>
                        </td>

                        {{-- 3. Item Name & Code --}}
                        <td class="px-4 py-5 align-top">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900 group-hover:text-orange-700 transition truncate max-w-[200px]"
                                        title="{{ $b->nama_item }}">
                                        {{ $b->nama_item }}
                                    </p>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        <span class="text-[10px] font-mono text-gray-500 tracking-wide">
                                            {{ $b->id }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- 4. Kategori --}}
                        <td class="px-4 py-5 align-top">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-50 border border-gray-200 text-gray-600 shadow-sm">
                                {{ $b->kategoriRel->nama }}
                            </span>
                        </td>



                        {{-- 6. Harga Unit (Client) --}}
                        <td class="px-4 py-5 align-top">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900">
                                    Rp {{ number_format($b->harga_unit, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-gray-400 font-medium">per unit</span>
                            </div>
                        </td>

                        {{-- 7. Harga Pekerja (Wage) --}}
                        <td class="px-4 py-5 align-top">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-emerald-600">
                                    Rp {{ number_format($b->harga_pekerja, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-gray-400 font-medium">per unit</span>
                            </div>
                        </td>
                        {{-- 5. Status --}}
                        <td class="px-4 py-5 align-top">
                            @if($b->status_aktif)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <span class="relative flex h-1.5 w-1.5">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                    </span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        {{-- 8. Actions --}}
                        <td class="pr-6 py-5 align-middle text-right">
                            <div class="flex justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('view.ubah.unit-borongan', ['unitId' => $unit->id, 'boronganId' => $b->id]) }}"
                                class="p-2 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-14 w-14 bg-orange-50 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-7 h-7 text-orange-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <p class="font-medium text-gray-900">Belum ada data borongan</p>
                                <p class="text-xs text-gray-500 mt-1">Tambahkan paket borongan untuk unit ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($borongan->hasPages())
            <!-- Ensure this ID matches the JS selector -->
            <div id="search-pagination" class="border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
                {{ $borongan->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
@endif
