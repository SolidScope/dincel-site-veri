# Barkod Eşleştirme — Atlanan Kodlar Raporu

Kaynak: `Dincel_Celik_Urun_Listesi_Tum_DNC_Doldurulmus.xlsx` → "Ürün Listesi" (201 satır)

- **Yazılan:** 163 kod → `barkod_eslesme_163.csv`
- **Atlanan:** 38 satır (aşağıda)

## 1. Çift barkodlu kodlar (8 satır)

| Kod | Ürün adı | Barkod |
|---|---|---|
| DNC-024 | PLATİN 36 PRÇ GOLD BEYAZ Ç.K.B SET | 8683581657553 |
| DNC-024 | PLATİN 24 PRÇ TİTANİUM SET | 8683581657935 |
| DNC-119 | 6'LI MİDYE KAŞIK GOLD SİYAH SET | 8683581657607 |
| DNC-119 | 1 DZ ZEYN LÜX ÇAY KAŞIK | 8683581657980 |
| DNC-122 | Gümüş 30 Parça Çatal-Kaşık-Bıçak Seti | 8683581657713 |
| DNC-122 | 6'LI MİDYE GOLD (ALTIN) | 8683581657980 |
| DNC-132 | 1 DZ ÇUBUK ÇAY KAŞIK | 8683613550548 |
| DNC-132 | SİYAH TİTANİUM DONDURMA KAŞIK 6,LI | 8683613550593 |

Not: 8683581657980 hem DNC-119 hem DNC-122'de geçiyor.

## 2. Aynı barkodu paylaşan kodlar (26 satır)

| Barkod | Kodlar |
|---|---|
| 8683581657003 | DNC-001 (Platin Yemek Kaşığı 12'li), DNC-077 (Okyanus 72 Parça Set) |
| 8683613550029 | DNC-094 (Platin Yemek Bıçağı 3'lü), DNC-209 (Zerafet 5'li Sunum Servis Seti Antik Gold) |
| 8683581657942 | DNC-026 (PLATİN 36 PRÇ MAT TİTANİUM SET), DNC-117 (6'LI LÜX ÇAY KAŞIK) |
| 8683613550722 | DNC-036 (Damla Makarna Maşası), DNC-176 (Okyanus Sos Kevgiri Mira Gold) |
| 8683613550715 | DNC-038 (Damla Pasta Maşası), DNC-175 (Okyanus Sos Kepçesi Mira Gold) |
| 8683613550760 | DNC-141 (OKYANUS SOS SERVİS KEPÇE), DNC-153 (5,Lİ SOS SERVİS TÜY GOLD), DNC-180 (İSTİRİDYE GOLD ÇAY KAŞIK 6,LI) |
| 8683613550777 | DNC-142 (OKYANUS SOS SERVİS KEVGİR), DNC-154 (5,Lİ SOS SERVİS SET GÜMÜŞ TÜY) |
| 8683613550791 | DNC-144 (OKYANUS SOS SERVİS SPATULA), DNC-156 (6,LI LÜX ÇAY KAŞIK GOLD), DNC-183 (LİLİUM GOLD WHİTE SOS SERVİS SET) |
| 8683613550807 | DNC-145 (OKYANUS SOS SERVİS ÇATAL), DNC-184 (LİLİUM GOLD BLACK SOS SERVİS SET) |
| 8683613550678 | DNC-167 (Okyanus Sos Kevgiri Titanium), DNC-171 (Okyanus 5'li Sos Servis Seti Titanium) |
| 8683613550685 | DNC-168 (Okyanus Sos Servis Kaşığı Titanium), DNC-172 (Okyanus 5'li Sos Servis Seti Mat Titanium) |
| 8683613550692 | DNC-169 (Okyanus Sos Spatulası Titanium), DNC-173 (Okyanus 5'li Sos Servis Seti Mira Gold) |

## 3. 14 haneli, geçersiz barkodlar (2 satır)

| Kod | Ürün adı | Barkod |
|---|---|---|
| DNC-146 | OKYANUS SOS SERVİS SETİ SADE | 86836135508142 |
| DNC-170 | Okyanus Sos Servis Çatalı Titanium | 86836135500777 |

## 4. "— YOK —" satırları (ürün kodu atanmamış, 2 satır)

| Ürün adı | Barkod |
|---|---|
| Gümüş 84 Parça Çatal-Kaşık-Bıçak Seti | 8683581657751 |
| Gümüş 89 Parça Çatal-Kaşık-Bıçak Seti | 8683581657768 |

## Yazılan 163 kod için kontroller

- Tekrar eden ürün kodu: yok
- Tekrar eden barkod: yok
- Atlanan satırlardaki bir barkodu kullanan kod: yok
- 13 hane ve EAN-13 kontrol hanesi: 162 kod geçerli. **1 uyarı:**
  - **DNC-160** (Okyanus Sos Kepçesi) — `8683613550547` kontrol hanesi hatalı (doğrusu `…548` olmalı; o da DNC-132'nin barkodu). Kapsam dışında bırakılmadı, CSV'ye yazıldı; teyit edilmesi önerilir.
