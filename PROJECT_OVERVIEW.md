# 🩺 Kebidan-Yuk System

## 📌 Deskripsi Project

Kebidan-Yuk adalah sistem berbasis web untuk membantu orang tua memantau imunisasi anak, melakukan booking layanan bidan, dan mengelola data kesehatan anak secara digital.

---

## 🎯 Tujuan Utama

- Monitoring status imunisasi anak
- Mempermudah booking layanan bidan
- Memberikan data terpusat untuk orang tua & bidan

---

## 👥 Role System (Spatie Permission)

### 👨‍👩‍👧 Parent

- Login
- Kelola data anak
- Melihat status imunisasi
- Booking layanan
- Melakukan pembayaran

### 🧑‍⚕️ Midwife

- Input data imunisasi
- Melihat booking

### 🏥 Admin

- Monitoring sistem
- Laporan data

---

## 🔥 Fitur yang Sudah Dibangun

### 🔐 Authentication

- Login system
- Role-based redirect (Spatie)

---

### 👶 Data Anak (Children)

- Tambah anak
- List anak per user
- Relasi ke user

---

### 💉 Imunisasi

- Input imunisasi oleh bidan
- Relasi ke:
    - child
    - vaccine
    - midwife

---

### 📊 Status Imunisasi (CORE FEATURE)

- Menampilkan vaksin:
    - sudah dilakukan
    - belum dilakukan
- Berdasarkan perbandingan:
    - vaccines (master)
    - immunizations (record)

---

### 📅 Booking System

- Pilih layanan
- Pilih jadwal
- Simpan booking

---

### 💳 Payment System (Basic)

- Generate transaction
- Update status booking

---

### 🏠 Homepage (Dynamic)

- Menampilkan layanan dari database
- Menampilkan total vaksin
- CTA ke:
    - data anak
    - booking

---

## 🧱 Struktur Database (Simplified)

- users
- children
- vaccines
- immunizations
- services
- schedules
- bookings
- transactions
- notifications

---

## ⚙️ Tech Stack

- Laravel
- Blade
- Tailwind CSS
- Spatie Laravel Permission

---

## 🧠 Flow Utama Sistem

1. User login
2. User tambah data anak
3. User melihat status imunisasi
4. User booking layanan
5. Bidan input imunisasi
6. Status otomatis terupdate

---

## ⚠️ Catatan Penting

- Role menggunakan Spatie (tidak menggunakan enum)
- Semua route dilindungi middleware
- Relasi Eloquent sudah digunakan
- Homepage sudah dynamic

---

## 🚀 Next Development Plan

- Dashboard parent (status + summary)
- Notification system (reminder imunisasi)
- Payment gateway (Midtrans/Xendit)
- Immunization recommendation by age
- UI improvement

---
