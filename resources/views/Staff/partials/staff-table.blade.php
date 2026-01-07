{{-- 1. Create a wrapper div that holds the "Card" styling (Shadow, Border, Rounded) --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    {{-- 2. The Table (Remove border/rounded classes from here, keep layout classes) --}}
    <div class="overflow-x-auto"> {{-- Added overflow-x-auto for responsive scrolling on small screens --}}
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            No.
                        </th>

                        {{-- COLUMN 1: IDENTITY  --}}
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Pegawai
                        </th>

                        {{-- COLUMN 2: Perusahaan --}}
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Perusahaan
                        </th>

                        {{-- COLUMN 3: KPJ --}}
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Nomor KPJ
                        </th>

                        {{-- COLUMN 3: TENURE --}}
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Masa Kerja
                        </th>

                        {{-- COLUMN 4: STATUS  --}}
                        {{-- <th scope="col"
                            class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Status PKWT
                        </th> --}}

                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Status Keaktifan
                        </th>

                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>

            <tbody id="table-staff-body" class="bg-white divide-y divide-gray-200">
                @include('staff.partials.table-body', ['staff' => $staff])
            </tbody>
        </table>
    </div>

    @if ($staff->hasPages())
        <!-- Ensure this ID matches the JS selector -->
        <div id="search-pagination" class="border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
            {{ $staff->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
