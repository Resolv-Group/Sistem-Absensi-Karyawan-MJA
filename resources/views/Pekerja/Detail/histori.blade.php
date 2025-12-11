{{-- History Table --}}
    <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    {{-- Column 1: Aktivitas (Left Aligned) --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-[40%]">
                        Aktivitas
                    </th>

                    {{-- Column 2: Diperbarui Oleh (CENTER ALIGNED) --}}
                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-[35%]">
                        Diperbarui Oleh
                    </th>

                    {{-- Column 3: Waktu (Right Aligned) --}}
                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider w-[25%]">
                        Waktu
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">

                @forelse($historiPekerja as $log)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">

                        {{-- 1. Aktivitas --}}
                        <td class="px-6 py-4 align-top">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900 leading-snug">
                                    {{ $log->activity ?? 'Update Data' }}
                                </span>
                                <span class="text-xs text-gray-500 mt-1 leading-relaxed">
                                    {{ $log->description ?? 'Melakukan perubahan data' }}
                                </span>
                            </div>
                        </td>

                        {{-- 2. Diperbarui Oleh (CENTERED) --}}
                        <td class="px-6 py-4 align-top">
                            {{-- Added 'items-center' to center content horizontally --}}
                            <div class="flex flex-col items-center justify-center gap-1.5">
                                {{-- Nama --}}
                                <span class="text-sm font-bold text-gray-800 tracking-tight">
                                    {{ $log->staff->nama ?? 'System' }}
                                </span>

                                {{-- Jabatan (Centered Badge) --}}
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $log->jabatan ?? 'ADMIN' }}
                                </span>
                            </div>
                        </td>

                        {{-- 3. Waktu --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right align-top">
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($log->waktu)->format('d M Y') }}
                                </span>
                                <span class="text-xs text-gray-500 mt-0.5 font-mono">
                                    {{ \Carbon\Carbon::parse($log->waktu)->format('H:i') }}
                                </span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-gray-500 text-sm bg-gray-50">
                            Belum ada riwayat aktivitas.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>

        {{-- Footer Button --}}
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <a href="#"
                class="w-full flex items-center justify-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors group">
            </a>
        </div>
    </div>
