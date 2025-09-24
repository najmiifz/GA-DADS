<!-- Service Feature Checkbox -->
<div class="md:col-span-2 bg-blue-50 p-4 rounded-lg border border-blue-200" x-show="!isTipeKendaraan() && !isTipeSplicer()">
    <label class="inline-flex items-center cursor-pointer">
        <input type="checkbox" name="has_service" x-model="hasService" class="form-checkbox h-5 w-5 text-blue-600 rounded">
        <span class="ml-3 flex items-center text-sm font-medium text-gray-700">
            <i class="fas fa-tools text-blue-500 mr-2"></i>
            Memiliki Riwayat Servis
        </span>
    </label>
    <p class="text-xs text-gray-500 mt-2 ml-8">Centang jika asset ini memiliki riwayat maintenance atau servis</p>
</div>
<template x-if="isTipeKendaraan() || isTipeSplicer()">
    <input type="hidden" name="has_service" value="1">
</template>

<!-- Service history section -->
<div x-show="shouldShowService()" class="border-t-4 border-orange-500 bg-orange-50 p-4 rounded-lg mt-6" id="service-section">
    <div class="mb-4">
        <h3 class="flex items-center text-lg font-semibold text-orange-700">
            <i class="fas fa-tools text-orange-500 mr-3"></i>
            Riwayat Service & Maintenance
        </h3>
    </div>
            <div id="service-rows" class="space-y-3">
        <!-- Baris service default -->
    <div class="service-row bg-white border-l-4 border-orange-400 rounded-lg p-3 shadow-sm">
            <input type="hidden" name="service_id[]" value="">
            <input type="hidden" name="existing_service_file[]" value="">

            <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-4 gap-3 mb-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Service</label>
                    <input type="date" name="service_date[]" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bengkel</label>
                    <input type="text" name="service_vendor[]" class="form-input w-full" placeholder="Nama Bengkel">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Service</label>
                    <input type="text" name="service_cost[]" class="form-input w-full rupiah" placeholder="Rp 0">
                </div>
                <div class="md:col-span-4 lg:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Service</label>
                    <input type="text" name="service_desc[]" class="form-input w-full" placeholder="Contoh: Service berkala, Ganti oli">
                </div>
            </div>

            <div class="flex items-center justify-between bg-gray-50 p-2 rounded-lg">
                <div class="flex items-center gap-3">
                    <label class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md border cursor-pointer transition-colors">
                        <i class="fas fa-file-upload mr-2"></i>
                        Upload Dokumen
                        <input type="file" name="service_file[]" class="hidden" onchange="updateFileName(this)">
                    </label>
                    <span class="text-sm text-gray-600 file-name">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        Tidak ada file dipilih
                    </span>
                </div>
                <button type="button" class="remove-service inline-flex items-center bg-red-500 hover:bg-red-600 text-white rounded-md px-4 py-2 font-medium transition-colors" title="Hapus Riwayat Service">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateFileName(input) {
    const fileName = input.files && input.files.length > 0 ? input.files[0].name : 'Tidak ada file';
    const container = input.closest('label').parentElement;
    const fileNameEl = container.querySelector('.file-name');
    if (fileNameEl) {
        fileNameEl.textContent = fileName;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const tipeSelect = document.getElementById('tipe');
    const hasServiceCheckbox = document.querySelector('input[name="has_service"]:not([type="hidden"])');
    const serviceSection = document.getElementById('service-section');

    function addNewServiceRow() {
        const serviceRowsContainer = document.getElementById('service-rows');
        if (!serviceRowsContainer) return;
        ...existing JS from create...
    }

    if (tipeSelect) {...}
    if (hasServiceCheckbox) {...}
    document.addEventListener('click', function(e) {...});
});
</script>
@endpush

@push('scripts')
<script>
// Rupiah formatter for inputs with class 'rupiah'
function formatRupiahValue(value) {
    if (!value) return '';
    // remove non-digits
    const digits = value.toString().replace(/[^0-9]/g, '');
    if (digits === '') return '';
    // format with dot as thousand separator
    return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function attachRupiahFormatting(root = document) {
    const inputs = root.querySelectorAll('input.rupiah');
    inputs.forEach(function(inp){
        // Initialize display if there's a value
        if (inp.value) {
            inp.value = 'Rp ' + formatRupiahValue(inp.value);
        }

        inp.addEventListener('input', function(e){
            const caret = inp.selectionStart;
            const raw = inp.value.replace(/[^0-9]/g, '');
            inp.value = raw ? ('Rp ' + formatRupiahValue(raw)) : '';
            // try to restore caret (best-effort)
            try { inp.setSelectionRange(caret, caret); } catch (err) {}
        });

        inp.addEventListener('blur', function(){
            // ensure consistent formatting on blur
            const raw = inp.value.replace(/[^0-9]/g, '');
            inp.value = raw ? ('Rp ' + formatRupiahValue(raw)) : '';
        });
    });
}

document.addEventListener('DOMContentLoaded', function(){
    attachRupiahFormatting();

    // Sanitize rupiah inputs before any form submission (remove Rp and dots)
    document.querySelectorAll('form').forEach(function(form){
        form.addEventListener('submit', function(){
            form.querySelectorAll('input.rupiah').forEach(function(inp){
                const raw = inp.value ? inp.value.toString().replace(/[^0-9]/g, '') : '';
                // replace value with numeric-only for server
                inp.value = raw;
            });
        });
    });
});
</script>
@endpush
