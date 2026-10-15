function attendanceDatePicker(initialDates) {
    return {
        open: false,
        dates: [...initialDates],
        draft: [...initialDates],
        month: new Date(initialDates[0] + 'T12:00:00').getMonth(),
        year: new Date(initialDates[0] + 'T12:00:00').getFullYear(),
        message: '',
        format(day) {
            return new Date(day + 'T12:00:00').toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
        },
        get label() { return this.dates.length === 1 ? this.format(this.dates[0]) : `${this.dates.length} tanggal dipilih`; },
        get monthLabel() { return new Date(this.year, this.month, 1).toLocaleDateString('id-ID', {month: 'long', year: 'numeric'}); },
        get days() {
            const offset = new Date(this.year, this.month, 1).getDay();
            return Array.from({length: 42}, (_, index) => {
                const day = new Date(this.year, this.month, index - offset + 1);
                const iso = `${day.getFullYear()}-${String(day.getMonth() + 1).padStart(2, '0')}-${String(day.getDate()).padStart(2, '0')}`;
                const today = new Date();
                return {iso, number: day.getDate(), current: day.getMonth() === this.month,
                    today: day.toDateString() === today.toDateString()};
            });
        },
        show() {
            this.draft = [...this.dates];
            this.message = '';
            this.open = !this.open;
        },
        moveMonth(offset) {
            const next = new Date(this.year, this.month + offset, 1);
            this.year = next.getFullYear();
            this.month = next.getMonth();
        },
        toggle(day) {
            this.message = '';
            if (this.draft.includes(day)) this.draft = this.draft.filter(value => value !== day);
            else if (this.draft.length < 7) this.draft = [...this.draft, day].sort();
            else this.message = 'Maksimal 7 tanggal. Batalkan salah satu pilihan untuk memilih tanggal lain.';
        },
    };
}
