# Zero-Knowledge One-Time Secret Sharer (Web Tabanlı Sır Paylaşım Uygulaması)

Bu proje, istemci tarafında (Client-Side) uçtan uca şifreleme ve sunucu tarafında (Server-Side) tek seferlik veri imhası (Self-Destruct) prensiplerini birleştiren güvenli bir sır paylaşım uygulamasıdır.

## 🛠️ Kullanılan Teknolojiler & Standartlar
- **Frontend:** HTML5, CSS3, Modern JavaScript (Web Crypto API)
- **Backend:** PHP 8.x
- **Veritabanı:** MySQL / MariaDB (PDO Sürücüsü)
- **Şifreleme Algoritması:** AES-GCM (256-bit)

## 🔒 Güvenlik Mimarisi ve Çalışma Mantığı
1. **İstemci Tarafı Şifreleme (Zero-Knowledge):** Kullanıcı gizli veriyi girdiğinde, tarayıcının yerleşik `window.crypto.subtle` kütüphanesi kullanılarak rastgele bir 12-byte IV (Initialization Vector) ve 256-bit AES-GCM anahtarı üretilir. Veri, sunucuya gönderilmeden önce tarayıcıda şifrelenir.
2. **Güvenli Veri Transferi:** Sunucuya (PHP) yalnızca şifreli metin (`ciphertext`) ve `iv` gönderilir. Şifre çözme anahtarı (Secret Key), üretilen URL'in **Hash Fragment (`#`)** kısmına eklenir. HTTP protokolü gereği `#` işaretinden sonraki veriler asla sunucuya iletilmez. Bu sayede sunucu ve veritabanı yöneticisi sırrın orijinal içeriğini asla göremez.
3. **Tek Seferlik Okuma (Self-Destruct):** Alıcı bağlantıya tıkladığında, PHP veritabanından şifreli veriyi çekerken aynı anda `DELETE` sorgusu (Transaction ACID kurallarına uygun olarak) çalıştırarak kaydı kalıcı olarak imha eder. Veri istemciye ulaştıktan sonra tarayıcıda, URL'deki `#` fragmentından okunan anahtarla deşifre edilerek kullanıcıya gösterilir. Sayfa yenilendiğinde veri sunucudan silindiği için sırra tekrar erişilemez.

## 📁 Proje Yapısı
- `index.html`: Şifreleme, deşifre etme ve ön yüz (UI) mantığını barındıran ana dosya.
- `secret.php`: Sırrı kaydetme ve tek seferlik okuma/silme işlemlerini yürüten API.
- `db.php`: Veritabanı PDO bağlantı yapılandırması.
- `schema.sql`: Veritabanı ve tablo şemasını oluşturan SQL sorguları.
