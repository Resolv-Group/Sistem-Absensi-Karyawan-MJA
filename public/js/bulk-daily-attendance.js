function bulkDailyAttendance(config) {
    return {
        dates: config.dates,
        workerPage: config.workerPage,
        selected: [],
        names: {},
        search: '',
        loading: false,
        requestNumber: 0,
        busy: false,
        error: '',
        result: null,
        rule: {status_kehadiran: 1, hours_mode: 'schedule', jam_aktual: '', is_hbn: false, is_paid: true, is_paid_leave: false, catatan: ''},
        exceptions: [],
        showExceptions: false,
        exceptionDraft: {},
        exceptionWorker: '',
        exceptionDate: '',
        exceptionError: '',
        editingKey: null,
        preview: null,
        previewPayload: null,
        existingPolicy: 'skip',
        statuses: {1: 'Hadir', 2: 'Izin', 3: 'Cuti', 4: 'Sakit', 5: 'Rencana Cuti', 6: 'Absen'},
        init() { this.rememberWorkers(); },
        rememberWorkers() { this.workerPage.workers.forEach(worker => { this.names[worker.id] = worker.name; }); },
        get total() { return this.dates.length * this.selected.length; },
        get exceptionGroups() {
            return this.dates.map(date => ({date, items: this.exceptions.filter(item => item.date === date)})).filter(group => group.items.length);
        },
        dateLabel(date) { return new Date(date + 'T12:00:00').toLocaleDateString('id-ID', {day: 'numeric', month: 'short'}); },
        ruleLabel(rule) {
            if (Number(rule.status_kehadiran) !== 1) return this.statuses[rule.status_kehadiran] + (rule.is_paid_leave ? ' · Berbayar' : '');
            return 'Hadir · ' + (rule.hours_mode === 'schedule' ? 'Sesuai jadwal' : `${rule.jam_aktual || 0} jam`) + (rule.is_hbn ? ' · HBN' : '');
        },
        changed() { this.preview = null; this.previewPayload = null; this.result = null; this.error = ''; },
        toggleWorker(id) {
            this.changed();
            if (this.selected.includes(id)) {
                this.selected = this.selected.filter(value => value !== id);
                this.exceptions = this.exceptions.filter(item => item.worker_id !== id);
            } else if (this.selected.length < 25) this.selected.push(id);
            else this.error = 'Maksimal 25 pekerja per penyimpanan. Simpan kelompok ini terlebih dahulu.';
        },
        togglePage() {
            const ids = this.workerPage.workers.map(worker => worker.id);
            this.changed();
            if (ids.every(id => this.selected.includes(id))) {
                this.selected = this.selected.filter(id => !ids.includes(id));
                this.exceptions = this.exceptions.filter(item => this.selected.includes(item.worker_id));
            } else {
                const combined = [...new Set([...this.selected, ...ids])];
                if (combined.length > 25) this.error = 'Pilihan melebihi 25 pekerja. Pilih pekerja satu per satu atau hapus pilihan sebelumnya.';
                else this.selected = combined;
            }
        },
        clearWorkers() { this.selected = []; this.exceptions = []; this.changed(); },
        async loadWorkers(target = null) {
            const requestNumber = ++this.requestNumber;
            this.loading = true;
            const url = new URL(target || config.listUrl, window.location.origin);
            url.searchParams.set('search', this.search);
            if (!target) url.searchParams.set('page', '1');
            try {
                const response = await fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}});
                if (!response.ok) throw new Error('Gagal memuat pekerja. Coba lagi.');
                const page = await response.json();
                if (requestNumber !== this.requestNumber) return;
                this.workerPage = page;
                this.rememberWorkers();
            } catch (error) { if (requestNumber === this.requestNumber) this.error = error.message; }
            finally { if (requestNumber === this.requestNumber) this.loading = false; }
        },
        openException(item = null) {
            if (!this.selected.length) return;
            this.editingKey = item ? `${item.worker_id}|${item.date}` : null;
            this.exceptionWorker = item?.worker_id ?? this.selected[0];
            this.exceptionDate = item?.date ?? this.dates[0];
            this.exceptionDraft = {...(item?.rule ?? this.rule)};
            this.exceptionError = '';
            this.$refs.exceptionDialog.showModal();
        },
        ruleError(rule) {
            if (Number(rule.status_kehadiran) === 1 && rule.hours_mode === 'custom'
                && (rule.jam_aktual === '' || rule.jam_aktual === null || !Number.isFinite(Number(rule.jam_aktual))
                    || Number(rule.jam_aktual) < 0 || Number(rule.jam_aktual) > 24)) return 'Isi jam kerja antara 0 dan 24 jam.';
            return '';
        },
        saveException() {
            const key = `${this.exceptionWorker}|${this.exceptionDate}`;
            this.exceptionError = this.ruleError(this.exceptionDraft);
            if (this.exceptionError) return;
            if (this.exceptions.some(item => `${item.worker_id}|${item.date}` === key && key !== this.editingKey)) {
                this.exceptionError = 'Pengecualian untuk pekerja dan tanggal ini sudah ada. Edit pengecualian tersebut.';
                return;
            }
            this.exceptions = this.exceptions.filter(item => `${item.worker_id}|${item.date}` !== this.editingKey);
            this.exceptions.push({worker_id: Number(this.exceptionWorker), date: this.exceptionDate, rule: {...this.exceptionDraft}});
            this.changed();
            this.$refs.exceptionDialog.close();
        },
        removeException(item) {
            this.exceptions = this.exceptions.filter(value => value.worker_id !== item.worker_id || value.date !== item.date);
            this.changed();
        },
        normalizedRule(rule) { return {...rule, jam_aktual: rule.jam_aktual === '' ? null : Number(rule.jam_aktual)}; },
        payload() {
            return {dates: this.dates, worker_ids: this.selected, rule: this.normalizedRule(this.rule),
                exceptions: this.exceptions.map(item => ({...item, rule: this.normalizedRule(item.rule)}))};
        },
        async post(url, data) {
            const response = await fetch(url, {method: 'POST', headers: {
                'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrf,
            }, body: JSON.stringify(data)});
            const body = await response.json();
            if (response.status === 409 && body.preview) return {stale: true, ...body};
            if (!response.ok) throw new Error(body.errors ? Object.values(body.errors).flat().join('\n') : (body.message || 'Gagal memproses absensi.'));
            return body;
        },
        async review() {
            if (this.busy || !this.total) return;
            this.error = this.ruleError(this.rule);
            if (this.error) return;
            this.busy = true;
            try {
                this.previewPayload = JSON.parse(JSON.stringify(this.payload()));
                this.preview = await this.post(config.previewUrl, this.previewPayload);
                this.existingPolicy = 'skip';
                this.$refs.previewDialog.showModal();
            } catch (error) { this.error = error.message; }
            finally { this.busy = false; }
        },
        async save() {
            if (this.busy || !this.preview) return;
            this.busy = true;
            this.error = '';
            try {
                // Choosing Update is an explicit decision; the final button confirms that decision.
                const response = await this.post(config.saveUrl, {...this.previewPayload,
                    existing_policy: this.existingPolicy, overwrite_confirmed: this.existingPolicy === 'update',
                    preview_version: this.preview.preview_version});
                if (response.stale) {
                    this.preview = response.preview;
                    this.existingPolicy = 'skip';
                    this.error = response.message;
                    return;
                }
                this.result = response;
                this.$refs.previewDialog.close();
                this.preview = null;
                this.previewPayload = null;
                this.selected = [];
                this.exceptions = [];
                window.location.assign(config.redirectUrl);
            } catch (error) { this.error = error.message; }
            finally { this.busy = false; }
        },
    };
}
