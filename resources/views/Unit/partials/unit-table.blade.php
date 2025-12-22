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
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                        ID Unit
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Nama PIC
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Tipe Pengajian
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Total Pekerja
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>

            <tbody id="table-company-body" class="bg-white divide-y divide-gray-200">

                @include('Unit.partials.table-body', ['unit' => $unit])
            </tbody>
        </table>
    </div>

    @if ($unit->hasPages())
        <!-- Ensure this ID matches the JS selector -->
        <div id="search-pagination" class="border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
            {{ $unit->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
