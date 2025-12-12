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

@if($pekerja->hasPages())
    <!-- Ensure this ID matches the JS selector -->
    <div id="search-pagination" class="p-4 bg-white border-t border-gray-200">
        {{ $pekerja->links('vendor.pagination.custom') }}
    </div>
@endif
