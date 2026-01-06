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
                        Nama Unit
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Total Pekerja
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Jenis Unit
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Total Belum Absen
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Total Perlu Penilaian
                    </th>
                    @if (Auth::user()->staff?->jabatan === 'PIC')
                        <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    @endif
                </tr>
            </thead>

            <tbody id="table-company-body" class="bg-white divide-y divide-gray-200">

                @include('Absensi.partials.table-body', ['units' => $units])
            </tbody>
        </table>
    </div>

    @if ($units->hasPages())
        <!-- Ensure this ID matches the JS selector -->
        <div id="search-pagination" class="border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
            {{ $units->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
