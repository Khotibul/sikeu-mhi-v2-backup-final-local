const jenjangSekolah = document.getElementById('jenjangSekolah');
        const jurusanWrap = document.getElementById('jurusanWrap');
        const jurusanSelect = document.getElementById('jurusanSelect');
        const kelasDiniyah = document.getElementById('kelasDiniyah');
        const kelasDiniyahWrap = document.getElementById('kelasDiniyahWrap');
        const form = document.getElementById('ppdbOnlineForm');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitIcon = document.getElementById('submitIcon');

        const oldJurusan = jenjangSekolah.dataset.oldJurusan || '';
        const defaultKelasDiniyah = kelasDiniyah.dataset.defaultKelas || '';

        const jurusanOptions = {
            SMK: [
                'TKJ',
                'AK',
                'BDP'
            ],
            MA: [
                'IPA',
                'IPS',
                'Keagamaan',
                'Lainnya'
            ]
        };

        function renderJurusan() {
            const jenjang = jenjangSekolah.value;

            jurusanSelect.innerHTML = '<option value="">-- Pilih Jurusan --</option>';

            if (jenjang === 'SMK' || jenjang === 'MA') {
                jurusanWrap.classList.remove('hidden');
                jurusanSelect.required = true;

                jurusanOptions[jenjang].forEach(function(item) {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;

                    if (oldJurusan === item) {
                        option.selected = true;
                    }

                    jurusanSelect.appendChild(option);
                });
            } else {
                jurusanWrap.classList.add('hidden');
                jurusanSelect.required = false;
                jurusanSelect.value = '';
            }
        }

        function updateDiniyah() {
            const status = document.querySelector('input[name="status_pondok"]:checked')?.value || 'Mukim';

            if (status === 'Pulang Pergi') {
                kelasDiniyah.value = '';
                kelasDiniyah.disabled = true;
                kelasDiniyah.required = false;
                kelasDiniyahWrap.style.opacity = '.55';
            } else {
                kelasDiniyah.disabled = false;
                kelasDiniyah.required = true;
                kelasDiniyahWrap.style.opacity = '1';

                if (!kelasDiniyah.value) {
                    kelasDiniyah.value = defaultKelasDiniyah;
                }
            }
        }

        if (jenjangSekolah) {
            jenjangSekolah.addEventListener('change', renderJurusan);
            renderJurusan();
        }

        document.querySelectorAll('input[name="status_pondok"]').forEach(function(item) {
            item.addEventListener('change', updateDiniyah);
        });

        updateDiniyah();

        let ppdbIsSubmitting = false;

        if (form) {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    return;
                }

                if (ppdbIsSubmitting) {
                    e.preventDefault();
                    return false;
                }

                ppdbIsSubmitting = true;

                if (loadingOverlay) {
                    loadingOverlay.classList.add('show');
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                }

                if (submitIcon) {
                    submitIcon.textContent = '⏳';
                }

                if (submitText) {
                    submitText.textContent = 'Mengirim...';
                }
            });
        }

