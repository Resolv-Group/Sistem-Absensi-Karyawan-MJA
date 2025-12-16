{{-- 1. Create a wrapper div that holds the "Card" styling (Shadow, Border, Rounded) --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    {{-- 2. The Table (Remove border/rounded classes from here, keep layout classes) --}}
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-10">
                    No.
                </th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Identitas Perusahaan
                </th>
                {{-- RESTORED COLUMN --}}
                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Total Pekerja
                </th>
                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Bidang Usaha
                </th>
                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Mulai Kerjasama
                </th>
                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Berakhir
                </th>
                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Status
                </th>
                <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
            </tr>
        </thead>

        <tbody id="table-company-body" class="bg-white divide-y divide-gray-200">

            {{-- DUMMY ROW 1: Multi-PIC Example --}}
            <tr class="hover:bg-gray-50 transition-colors duration-150 group">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 align-top">
                    1.
                </td>

                {{-- Identitas --}}
                <td class="px-6 py-4 whitespace-nowrap align-top">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                G
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-bold text-gray-900">PT. Global Solusi Digital</div>
                            <div class="text-xs text-gray-500 mt-0.5">NPWP: 01.234.567.8-901.000</div>
                            <div class="text-xs text-gray-500">Surabaya, Jawa Timur</div>
                        </div>
                    </div>
                </td>

                {{-- Total Pekerja --}}
                <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-bold bg-gray-100 text-gray-800">
                        128
                    </span>
                    <div class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">Orang</div>
                </td>

                {{-- Multiple PICs Container --}}
                {{-- <td class="px-6 py-4 whitespace-nowrap align-top">
                    <div class="flex flex-col gap-4">

                        <!-- PIC 1 -->
                        <div class="flex gap-3">
                            <img class="h-8 w-8 rounded-full bg-gray-200" src="https://ui-avatars.com/api/?name=Budi+Santoso&background=random&color=fff" alt="">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900">Budi Santoso</span>
                                <span class="text-xs text-gray-500 mb-0.5">HR Manager</span>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span class="flex items-center gap-1 hover:text-blue-600 cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        Email
                                    </span>
                                    <span class="text-gray-300">|</span>
                                    <span class="flex items-center gap-1 hover:text-blue-600 cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        0812-3456...
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- PIC 2 -->
                        <div class="flex gap-3 pt-3 border-t border-gray-100 border-dashed">
                            <img class="h-8 w-8 rounded-full bg-gray-200" src="https://ui-avatars.com/api/?name=Sarah+W&background=random&color=fff" alt="">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900">Sarah Wijaya</span>
                                <span class="text-xs text-gray-500 mb-0.5">Finance</span>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span class="flex items-center gap-1 hover:text-blue-600 cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        Email
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </td> --}}

                <td class="px-6 py-4 whitespace-nowrap text-center align-top ">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                        IT & Software
                    </span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600 align-top">
                    10 Jan 2024
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600 align-top">
                    10 Jan 2026
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                        Aktif
                    </span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex justify-end gap-2">
                <!-- Edit -->
                <a href="#"
                    class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50
               rounded-lg p-2 transition"
                    title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </a>

                <!-- Detail -->
                <a href="{{route('view.detail.mitra-kerja')}}"
                    class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50
               rounded-lg p-2 transition"
                    title="Detail">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                </a>
            </div>

        </td>
            </tr>

            {{-- DUMMY ROW 2: Single PIC Example --}}
            <tr class="hover:bg-gray-50 transition-colors duration-150 group">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 align-top">
                    2.
                </td>

                <td class="px-6 py-4 whitespace-nowrap align-top">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 font-bold">
                                N
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-bold text-gray-900">PT. Nusantara Makmur</div>
                            <div class="text-xs text-gray-500 mt-0.5">NPWP: 98.765.432.1-000</div>
                            <div class="text-xs text-gray-500">Sidoarjo, Jawa Timur</div>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-bold bg-gray-100 text-gray-800">
                        45
                    </span>
                    <div class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">Orang</div>
                </td>

                {{-- <td class="px-6 py-4 whitespace-nowrap align-top">
                    <div class="flex flex-col gap-4">
                        <!-- PIC 1 -->
                        <div class="flex gap-3">
                            <img class="h-8 w-8 rounded-full bg-gray-200" src="https://ui-avatars.com/api/?name=Siti+A&background=random&color=fff" alt="">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900">Siti Aminah</span>
                                <span class="text-xs text-gray-500 mb-0.5">Direktur Utama</span>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span class="flex items-center gap-1 hover:text-blue-600 cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        Email
                                    </span>
                                    <span class="text-gray-300">|</span>
                                    <span class="flex items-center gap-1 hover:text-blue-600 cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        0813-9876...
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </td> --}}

                <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                        Manufaktur
                    </span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600 align-top">
                    01 Feb 2023
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-red-600 font-medium align-top">
                    01 Feb 2024
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span>
                        Segera Habis
                    </span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex justify-end gap-2">
                <!-- Edit -->
                <a href="#"
                    class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50
               rounded-lg p-2 transition"
                    title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </a>

                <!-- Detail -->
                <a href="#"
                    class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50
               rounded-lg p-2 transition"
                    title="Detail">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                </a>
            </div>

        </td>
            </tr>

        </tbody>
    </table>
</div>

    {{-- @if ($pekerja->hasPages())
        <!-- Ensure this ID matches the JS selector -->
        <div id="search-pagination" class="border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
            {{ $pekerja->links('vendor.pagination.custom') }}
        </div>
    @endif --}}
</div>
