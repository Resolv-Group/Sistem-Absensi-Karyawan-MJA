{{-- Section 3: Riwayat Aktivitas / History --}}
<div>
    {{-- Section Header --}}
    <div class="flex items-center gap-4 mb-6">
        <h3 class="text-lg font-bold text-gray-900 whitespace-nowrap">Riwayat Perubahan</h3>
        <div class="h-px bg-gray-200 w-full"></div>
    </div>

    {{-- History Table --}}
    <div class="overflow-hidden border border-gray-200 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    {{-- Column 1: Activity Name --}}
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Aktivitas
                    </th>

                    {{-- Column 2: Updated By --}}
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Diperbarui Oleh
                    </th>

                    {{-- Column 3: When --}}
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Waktu
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">

                {{-- Example Loop: Replace $histories with your actual variable --}}
                {{-- @forelse($pekerja->logs ?? [] as $log)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $log->activity ?? 'Update Data' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $log->description ?? 'Melakukan perubahan data profil' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $log->user->name ?? 'Admin' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 text-sm">
                                Belum ada riwayat aktivitas yang tercatat.
                            </td>
                        </tr>
                    @endforelse --}}

                {{-- STATIC EXAMPLE (Delete this block when integrating backend) --}}
                <!-- Static Example Item 1 -->
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">Perubahan Data Diri</div>
                        <div class="text-xs text-gray-500">Update alamat domisili</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            HRD Staff
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                        09 Des 2025, 10:30
                    </td>
                </tr>
                <!-- Static Example Item 2 -->
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">Pendaftaran Karyawan</div>
                        <div class="text-xs text-gray-500">Input data awal ke sistem</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Super Admin
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                        01 Jan 2024, 08:00
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">Pendaftaran Karyawan</div>
                        <div class="text-xs text-gray-500">Input data awal ke sistem</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Super Admin
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                        01 Jan 2024, 08:00
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">Pendaftaran Karyawan</div>
                        <div class="text-xs text-gray-500">Input data awal ke sistem</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Super Admin
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                        01 Jan 2024, 08:00
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">Pendaftaran Karyawan</div>
                        <div class="text-xs text-gray-500">Input data awal ke sistem</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Super Admin
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                        01 Jan 2024, 08:00
                    </td>
                </tr>
                {{-- END STATIC EXAMPLE --}}

            </tbody>
        </table>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            {{-- Replace '#' with route('name') later --}}
            <a href="#"
                class="w-full flex items-center justify-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors group">
                Lihat Seluruh Riwayat
                {{-- Animated Arrow Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 transform group-hover:translate-x-1 transition-transform" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>


</div>
