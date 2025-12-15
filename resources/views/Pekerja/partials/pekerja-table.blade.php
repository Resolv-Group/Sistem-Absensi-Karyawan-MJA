{{-- 1. Create a wrapper div that holds the "Card" styling (Shadow, Border, Rounded) --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    {{-- 2. The Table (Remove border/rounded classes from here, keep layout classes) --}}
    <div class="overflow-x-auto"> {{-- Added overflow-x-auto for responsive scrolling on small screens --}}
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        No.
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Identitas Pekerja
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Kontak
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Domisili
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>

            <tbody id="table-pekerja-body" class="bg-white divide-y divide-gray-200">
                @include('pekerja.partials.table-body', ['pekerja' => $pekerja])


            </tbody>
        </table>
    </div>

    @if ($pekerja->hasPages())
        <!-- Ensure this ID matches the JS selector -->
        <div id="search-pagination" class="border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
            {{ $pekerja->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
