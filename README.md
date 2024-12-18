# Sistem Pemantauan dan Kontrol Ruang dengan IoT Berbasis NodeMCU dan MQTT

## Deskripsi Proyek

Proyek ini bertujuan untuk memantau suhu dan kelembapan suatu ruangan secara real-time serta memberikan kontrol sederhana melalui protokol MQTT. Sistem menggunakan NodeMCU ESP8266 untuk membaca data dari sensor DHT11, menampilkan data pada display LCD, dan mengontrol LED sebagai indikator kondisi ruangan.

## Fitur Utama

- **Pemantauan Suhu dan Kelembapan**
  - Membaca suhu dan kelembapan dengan sensor DHT11.
  - Menampilkan data secara real-time pada LCD I2C.
- **Indikator Visual**
  - LED otomatis menyala berdasarkan kondisi suhu:
    - Hijau (D0): suhu aman.
    - Kuning (D3): suhu sedang.
    - Merah (D4): suhu tinggi.
- **Integrasi MQTT**
  - Data dikirim ke server MQTT pada topik:
    - `G.231.22.0023/temperature`
    - `G.231.22.0023/humidity`
  - Menerima pesan dari server MQTT untuk mengontrol LED terpisah:
    - `G.231.22.0023/control`.
- **Dashboard Monitoring**
  - Data suhu dan kelembapan dapat diakses melalui dashboard berbasis web.
  - Data disimpan dalam database MySQL dan ditampilkan dalam bentuk grafik.

## Komponen yang Dibutuhkan

- **Hardware:**

  - NodeMCU ESP8266
  - Sensor DHT11
  - LCD I2C (SCL: D1, SDA: D2)
  - LED (Hijau: D0, Kuning: D3, Merah: D4, Kontrol Manual: D6, D7, D8)

- **Software:**
  - Arduino IDE
  - MQTT Server (`x2.revolusi-it.com`):
    - Username: `usm`
    - Password: `usmjaya001`
  - PHP, HTML, CSS, JavaScript untuk dashboard web
  - MySQL untuk penyimpanan data

## Instalasi dan Penggunaan

1. **Persiapkan NodeMCU ESP8266:**

   - Pasang DHT11, LED, dan LCD sesuai pin konfigurasi:
     - DHT11: D5
     - LCD I2C: D1 (SCL), D2 (SDA)
     - LED: D0 (hijau), D3 (kuning), D4 (merah), D6, D7, D8 (kontrol manual).
   - Upload kode ke NodeMCU melalui Arduino IDE.

2. **Set Up Server MQTT:**

   - Gunakan server `x2.revolusi-it.com` dengan format nama klien: `client-G.231.22.0023`.

3. **Set Up Dashboard Web:**

   - Jalankan server PHP untuk membaca data dari MQTT dan menyimpan ke MySQL.
   - Akses dashboard untuk memantau grafik suhu dan kelembapan secara real-time.

4. **Kontrol Manual LED:**
   - Kirim perintah MQTT melalui aplikasi MQTT seperti MQTT Dash dengan topik `G.231.22.0023/control` dan payload `on` atau `off`.

## Struktur Topik MQTT

- `G.231.22.0023/temperature`: Data suhu.
- `G.231.22.0023/humidity`: Data kelembapan.
- `G.231.22.0023/control`: Kontrol LED manual.

## Ketentuan Sistem

- **Suhu:**
  - < 29°C: Tidak ada beep pada dashboard.
  - 29°C ≤ Suhu < 30°C: Beep 1x.
  - 30°C ≤ Suhu ≤ 31°C: Beep 2x.
  - > 31°C: Beep 3x.
- **Kelembapan:**
  - 30% ≤ RH < 60%: Kering/Aman.
  - 60% ≤ RH < 70%: Mulai banyak uap air/Normal (beep 1x).
  - RH ≥ 70%: Terdapat banyak uap air (beep 3x).

## Dokumentasi

- Video Demo: [https://youtu.be/nGiSx0rWfb8](#)
- Source Code: [https://github.com/ibrammalik/ProjectIOT](#)

## Penutup

Proyek ini diharapkan dapat membantu meningkatkan efisiensi dalam memantau dan mengontrol kondisi lingkungan ruangan secara jarak jauh menggunakan teknologi IoT.

Jika ada pertanyaan, silakan hubungi melalui email atau komentar pada repository ini.

**Selamat mencoba dan semoga bermanfaat!**
