# 🏢 MJA Integrated HR & Payroll System

![Laravel](https://img.shields.io/badge/Laravel-PHP%208%2B-red)
![AlpineJS](https://img.shields.io/badge/Alpine.js-State%20Management-blue)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-UI%20Framework-38bdf8)
![Architecture](https://img.shields.io/badge/Architecture-Modular%20%7C%20Hierarchical-indigo)
![License](https://img.shields.io/badge/License-Private-inactive)

MJA is an enterprise-grade **Human Resource, Attendance, and Payroll Management System** built for structured workforce environments supporting both **Daily (Harian)** and **Piecework (Borongan)** payroll models.

The system emphasizes:

* Hierarchical configuration resolution
* Contract-based personalization (PKWT)
* Dynamic attendance logic
* Automated payroll & billing exports
* Clean, scalable Laravel architecture
* Glassmorphism-based UI consistency

---

# 📐 System Architecture

### Backend

* Laravel (PHP 8+)
* Modular Controller-Service Pattern
* AJAX-driven Payroll Review Engine

### Frontend

* Alpine.js (state management)
* Tailwind CSS (utility-first styling)
* Glassmorphism UI system

### Configuration Resolution Hierarchy

```
Existing Record > PKWT Contract > Unit Default
```

⚠️ Existing business logic and error handling must **never be removed** unless explicitly requested.

---

# 🎨 Design System

### UI Language

* Light Mode + Glassmorphism
* `rounded-[1.5rem]` (cards)
* `rounded-[2.5rem]` (modals)
* `px-12 py-16` spacious modal padding
* `tracking-[0.2em]` wide letter spacing

### Semantic Colors

| Color      | Purpose                          |
| ---------- | -------------------------------- |
| 🟢 Emerald | Additions / Success / Allowances |
| 🔴 Rose    | Deductions / Errors / Expired    |
| 🔵 Blue    | Attendance / HR / Primary Info   |
| 🟠 Orange  | Borongan / Production / Alerts   |
| 🟣 Indigo  | Scheduling / System Logic        |

---

# 🧱 Core Modules

---

## 1️⃣ Master Data Management

### Mitra Kerja

Parent organization entity.

### Units

Sub-entity under Mitra containing:

* Sistem Pengajian:

  * `1` → Harian
  * `2` → Borongan
* UMK Fee
* Management Fee %
* BPJS Health %
* BPJS Labor %
* Default Tunjangan (JSON)

---

### Staff

Internal management assigned as PIC to Units.

---

### Pekerja

Stored in Humaniora and assigned via PKWT contracts.

---

## 2️⃣ PKWT (Contract Engine)

Each PKWT defines:

* Gaji Bulanan
* Gaji Harian
* Gaji Overtime
* Gaji HBN (multiplier rate)
* Weekly Schedule (Mon–Sun, decimal format)
* BPJS calculation based on UMK
* Worker-specific allowance overrides

Editable BPJS fields only activate when KPJ/Naker ID exists.

---

## 3️⃣ Absensi Module

### 🟦 Harian System

Tracks:

* Daily hours
* Overtime
* HBN flag

Visual warning:

```
if jam_aktual < jam_normal → highlight "Jam Kurang"
```

---

### 🟠 Borongan System

Tracks production-based quantity.

**Total QTY Formula:**

```
Total QTY = FD + act_rej + good_mc
```

**Reject Logic:**

```
if act_rej > max_rej_subkon:
    rej_mc_dibebankan = act_rej - max_rej_subkon
```

Tunjangan = `Qty × Nominal`
Potongan = dynamic row-based deductions

Buttons are hidden until base attendance record exists (`has_absen` flag).

---

## 4️⃣ Payroll Engine

Multi-step wizard:

1. Select date range
2. Select workers
3. Exclude specific dates
4. Live adjustment review (AJAX)
5. Generate payroll output

### Biaya Admin

Transient field added during report generation (not stored in DB).

---

## 5️⃣ Document Generation

* MOU (PDF stream)
* Payslip (Worker breakdown)
* Invoice (Unit-level billing)
* Kwitansi
* Custom reference numbering (Resi)

---

# 🧩 Recurring UI Patterns

### Hybrid Combobox

Searchable dropdown with modal deep-search fallback.

### Worker Stepper

Carousel-based bulk processing with progress bar.

### Floating Action Bar

Glass-style bottom action bar for bulk actions.

### Deep Cloning Standard

```js
JSON.parse(JSON.stringify(object))
```

Required to prevent Alpine.js reference mutation bugs.

---

# 🗄 Database Schema Overview

High-level entity structure:

```
mitras
units
staff
pekerja
pkwt_contracts
absensi_harian
absensi_borongan
absensi_adjustments
payroll_batches
payroll_details
invoices
documents
```

---

## Core Relationship Mapping

```
Mitra
 └── Units
       ├── Staff (PIC)
       ├── PKWT Contracts
       │     └── Pekerja
       └── Absensi
              ├── Harian
              └── Borongan
                     └── Adjustments
```

---

## Key Table Highlights

### units

* mitra_id
* sistem_pengajian
* umk_fee
* management_fee_percent
* bpjs_health_percent
* bpjs_labor_percent
* tunjangan_default (JSON)

---

### pkwt_contracts

* pekerja_id
* unit_id
* gaji_bulanan
* gaji_harian
* gaji_overtime
* gaji_hbn_rate
* schedule_json
* bpjs_override_flag

---

### absensi_borongan

* pk_id
* date
* fd
* act_rej
* good_mc
* max_rej_subkon
* rej_mc_dibebankan
* total_qty

---

# ⚙️ Installation Guide

## Requirements

* PHP 8+
* Composer
* MySQL / MariaDB
* Node.js 18+
* NPM
* Web server (Nginx / Apache)

---

## 1️⃣ Clone Repository

```bash
git clone <repository-url>
cd mja-system
```

---

## 2️⃣ Install Dependencies

```bash
composer install
npm install
```

---

## 3️⃣ Environment Setup

Copy environment file:

```bash
cp .env.example .env
```

Configure:

```
APP_NAME=MJA
APP_ENV=local
APP_KEY=
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem-mja
DB_USERNAME=root
DB_PASSWORD=
```

Generate key:

```bash
php artisan key:generate
```

---

## 4️⃣ Database Migration

```bash
php artisan migrate
```

(Optional seed if available)

```bash
php artisan db:seed
```

---

## 5️⃣ Compile Frontend

```bash
npm run build
```

For development:

```bash
npm run dev
```

---

## 6️⃣ Run Application

```bash
php artisan serve
```

---

# 📈 System Philosophy

MJA is built to:

* Minimize payroll miscalculations
* Support contract-level flexibility
* Maintain transparent hierarchy logic
* Provide scalable HR infrastructure
* Deliver enterprise-grade reliability

---

