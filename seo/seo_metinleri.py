"""Dincel Çelik SEO metinleri (başlık / meta açıklama).

Kurallar (Google Search Central önerileri):
- Her sayfaya özel, sayfayı doğru anlatan kısa başlık; marka sonda.
- Meta açıklama 1–2 cümle, sayfaya özel, en önemli bilgileri içerir; anahtar kelime doldurma yok.
- Fiyat gibi sık değişen bilgiler açıklamaya yazılmaz.
"""
import json
import re
import sys

MARKA = 'Dincel Çelik'

# ---- Ana sayfa, mağaza ve kurumsal sayfalar: (post id, başlık, açıklama) ----
SAYFALAR = [
    (24, 'Dincel Çelik | Paslanmaz Çelik Çatal, Kaşık ve Bıçak',
     "2008'den beri Bayrampaşa'daki atölyemizde paslanmaz çelik çatal, kaşık, bıçak takımları ve servis ürünleri üretiyoruz. Sade, gold ve titanyum seçenekleri."),
    (18, 'Tüm Ürünler: Çatal, Kaşık, Bıçak ve Takımlar | Dincel Çelik',
     'Paslanmaz çelik kaşık, çatal, bıçak, 30–89 parça takımlar, servis setleri ve maşalar. Platin, Gümüş, Okyanus ve diğer modellerde üreticiden direkt satış.'),
    (988066, "Hakkımızda | 2008'den Beri Atölyeden Sofraya | Dincel Çelik",
     "Dincel Çelik, 2008'den beri İstanbul Bayrampaşa'daki atölyesinde paslanmaz çelik çatal, kaşık, bıçak ve servis ürünleri üretir. Hikayemiz ve değerlerimiz."),
    (988069, 'İletişim | Bayrampaşa Atölyesi | Dincel Çelik',
     'Dincel Çelik atölyesi: Sarı Dökümcüler Sanayi Sitesi, Bayrampaşa / İstanbul. Sipariş, toptan satış ve sorularınız için 0536 427 0557 ya da WhatsApp.'),
    (988070, 'Sıkça Sorulan Sorular | Dincel Çelik',
     'Malzeme, kaplamalı ürünlerin bakımı, eksik parça alımı, takım içerikleri ve toptan sipariş hakkında merak ettiklerinizin yanıtları.'),
    (988068, 'Toptan ve Kurumsal Çatal Kaşık Bıçak | Dincel Çelik',
     'Otel, restoran, kafe ve kurumlar için paslanmaz çelik çatal, kaşık ve bıçakta toptan satış. Üreticiden direkt fiyat ve proje bazlı teklif için bize ulaşın.'),
    (988067, 'Kaplama Seçenekleri: Gold, Titanyum, Saten | Dincel Çelik',
     'Sade krom, saten, gold, titanyum ve mat titanyum yüzeyler arasındaki farklar ve kaplamalı çatal kaşık bıçakların ışıltısını korumak için bakım önerileri.'),
    (988172, 'İade ve Değişim Koşulları | Dincel Çelik',
     'Teslimattan itibaren 14 gün içinde cayma hakkı, iade koşulları, ücretsiz iade gönderimi ve ücret iadesi süreci hakkında bilgiler.'),
    (988214, 'Çatal Kaşık Bıçak Takımı Seçerken Nelere Dikkat Etmeli?',
     'Kişi sayısı, parça sayısı, malzeme ve kaplama: çatal kaşık bıçak takımı seçerken bakmanız gereken noktaları üretici gözüyle anlattık.'),
    (988215, 'Kaplamalı Çatal Bıçak Bakımı: Parıltıyı Koruma Rehberi',
     'Gold ve titanyum kaplamalı çatal, kaşık ve bıçakların ilk günkü parlaklığını korumak için yıkama, kurulama ve saklama önerileri.'),
    (988216, 'Çeyiz İçin Çatal Kaşık Bıçak Takımı: Kaç Parça Yeterli?',
     'Çeyiz için 36, 60, 72, 84 ve 89 parçalık takımlar arasındaki farklar; kişi sayısına ve kullanım alışkanlığına göre doğru takımı seçme rehberi.'),
]

# ---- Ürün kategorileri: (term id, başlık, açıklama) ----
KATEGORILER = [
    (194, 'Paslanmaz Çelik Kaşık Çeşitleri | Dincel Çelik',
     "Yemek, tatlı, çay, nescafe ve dondurma kaşıkları; 6'lı ve 12'li paketler. Platin, Gümüş, Okyanus ve Gökkuşağı modellerinde sade, gold ve titanyum."),
    (200, 'Paslanmaz Çelik Yemek ve Tatlı Çatalı | Dincel Çelik',
     "Yemek ve tatlı çatalları 6'lı ve 12'li paketlerde. Aynı modeldeki kaşık ve bıçaklarla uyumlu; sade ve titanyum seçenekli paslanmaz çelik çatallar."),
    (201, 'Paslanmaz Çelik Yemek ve Pasta Bıçağı | Dincel Çelik',
     "Yemek ve pasta bıçakları 3'lü ve 12'li paketlerde. Platin, Gümüş, Okyanus ve Gökkuşağı modellerinde, takımınızı tamamlayan paslanmaz çelik bıçaklar."),
    (202, 'Çatal Kaşık Bıçak Takımı (30–89 Parça) | Dincel Çelik',
     '6 ve 12 kişilik, 30, 36, 60, 72, 84 ve 89 parçalık paslanmaz çelik çatal kaşık bıçak takımları. Günlük kullanım, misafir sofraları ve çeyiz için.'),
    (219, 'Servis Kepçesi, Kevgir ve Servis Seti | Dincel Çelik',
     "Servis kepçesi, kevgir, servis kaşığı, çatalı ve spatulası; tekli ya da 5'li set. Okyanus sos servis ürünleri sade, titanyum ve Mira Gold seçenekli."),
    (220, 'Makarna, Salata ve Pasta Maşası | Dincel Çelik',
     "Damla ve Kelebek modellerinde makarna, salata ve pasta maşaları ile 3'lü maşa setleri. Sade, titanyum, mat titanyum, gold ve gümüş seçenekleri."),
    (223, 'Sunum Seti ve Dekoratif Sosluk | Dincel Çelik',
     "Kelebek sunum sosluk ve Zerafet 5'li sunum servis seti; gold, antik gold ve gümüş seçenekleriyle kahvaltı ve davet sofralarına zarif bir dokunuş."),
]

# ---- Modeller (ürün etiketleri): (term id, başlık, açıklama) ----
MODELLER = [
    (195, 'Platin Model Çatal Kaşık Bıçak | Dincel Çelik',
     "Platin: en geniş seri. Kaşık, çatal, bıçak, servis ürünleri ve 36–89 parça takımlar; sade ve titanyum, çay kaşığında gold seçeneği."),
    (198, 'Gümüş Model Çatal Kaşık Bıçak | Dincel Çelik',
     'Gümüş: klasik ve zamansız seri. Tekli kaşık, çatal ve bıçaklardan servis ürünlerine, 30–89 parçalık takımlara kadar geniş seçenek.'),
    (199, 'Okyanus Model Çatal Kaşık Bıçak | Dincel Çelik',
     'Okyanus: çatal kaşık bıçak takımları ve sos servis ürünleri. Sade, titanyum, mat titanyum ve Mira Gold kaplama seçenekleri.'),
    (197, 'Gökkuşağı Model Çatal Kaşık Bıçak | Dincel Çelik',
     'Gökkuşağı: sofraya karakter katan seri. Kaşık, çatal ve bıçaklar ile 30, 36, 60 ve 72 parçalık çatal kaşık bıçak takımları.'),
    (221, 'Damla Model Maşa ve Maşa Seti | Dincel Çelik',
     "Damla: makarna, salata ve pasta maşaları ile sade, titanyum, mat titanyum ve gold seçenekli 3'lü maşa setleri."),
    (222, 'Kelebek Model Maşa Seti ve Sosluk | Dincel Çelik',
     'Kelebek: dekoratif kelebek detaylı maşa seti ve sunum sosluk; gold ve gümüş seçenekleriyle davet sofralarına özel.'),
    (224, 'Zerafet Model Sunum Setleri | Dincel Çelik',
     "Zerafet: gümüş, gold ve antik gold seçenekli 5'li sunum servis seti. Kahvaltı ve davet sofraları için zarif bir sunum."),
]


def tr_lower(s):
    return s.replace('I', 'ı').replace('İ', 'i').lower()


def paket_ozeti(attrs):
    """'Paket' / 'Parça Sayısı' seçeneklerinden kısa ifade üret."""
    paket = attrs.get('Paket', [])
    parca = attrs.get('Parça Sayısı', [])
    if parca:
        nums = sorted(int(re.search(r'\d+', p).group()) for p in parca)
        return f'{nums[0]}–{nums[-1]} Parça' if len(nums) > 1 else f'{nums[0]} Parça'
    nums = sorted({int(re.search(r'\d+', p).group()) for p in paket if re.search(r'\d+', p)})
    if len(nums) > 1:
        ek = {3: "3'lü", 6: "6'lı", 12: "12'li"}
        return ' ve '.join(ek.get(n, f'{n}') for n in nums)
    return ''


def kaplama_ozeti(attrs):
    k = [x for x in attrs.get('Kaplama', []) if x != 'Sade']
    if not k:
        return ''
    k = [x.replace('Titanium', 'Titanyum') for x in k]
    k = [tr_lower(x) if x not in ('Mira Gold', 'Antik Gold') else x for x in k]
    return ', '.join((['sade'] if 'Sade' in attrs.get('Kaplama', []) else []) + k)


KULLANIM = {
    'salata maşası': 'Salatayı dağıtmadan servis etmek için.',
    'pasta maşası': 'Pasta ve tatlı dilimlerini kolayca servis etmek için.',
    'makarna maşası': 'Makarna ve erişteyi kolayca servis etmek için.',
    'maşa seti': 'Dekoratif kelebek detaylı, davet sofralarına özel.',
    'servis çatalı': 'Et, salata ve meze servisinde kullanılır.',
    'servis spatulası': 'Börek, pasta ve kızartma servisinde kullanılır.',
    'servis kevgiri': 'Yiyecekleri süzerek servis etmek için.',
    'servis kepçesi': 'Çorba ve sulu yemek servisinde kullanılır.',
    'servis kaşığı': 'Pilav, garnitür ve salata servisinde kullanılır.',
}


def urun_metni(p):
    ad = p['name']
    attrs = p['attrs']
    model = (attrs.get('Model') or p['tags'] or [''])[0]
    paket = paket_ozeti(attrs)
    kap = kaplama_ozeti(attrs)
    kat = p['cats'][0] if p['cats'] else ''
    urun_turu = tr_lower(ad.replace(model, '', 1).strip()) if model and ad.startswith(model) else tr_lower(ad)

    # Başlık
    if paket and 'Parça' in paket:
        baslik = f'{ad} {paket} | {MARKA}'
    elif paket:
        baslik = f'{ad} {paket} | {MARKA}'
    else:
        baslik = f'{ad} | Paslanmaz Çelik | {MARKA}'
    if len(baslik) > 60:
        baslik = f'{ad} | {MARKA}'

    # Açıklama
    parcalar = []
    if kat == 'Çatal Kaşık Bıçak Takımları':
        parcalar.append(f'{model} model paslanmaz çelik çatal kaşık bıçak takımı, {paket.lower()} seçenekli.')
        parcalar.append('Günlük kullanım, misafir sofraları ve çeyiz için.')
        if kap:
            parcalar.append(f'Kaplama: {kap}.')
        return baslik, ' '.join(parcalar)
    elif paket:
        parcalar.append(f'{model} model paslanmaz çelik {urun_turu}, {paket} paket seçenekleriyle.')
    elif "5'li servis seti" in tr_lower(ad):
        parcalar.append(f'{model} model paslanmaz çelik {urun_turu}: servis kepçesi, kevgir, servis kaşığı, servis çatalı ve spatula.')
    elif re.search(r"5'li|3'lü|seti", tr_lower(ad)):
        parcalar.append(f'{model} model paslanmaz çelik {urun_turu}.')
    else:
        parcalar.append(f'{model} model paslanmaz çelik {urun_turu}, tekli olarak satılır.')
    for anahtar, cumle in KULLANIM.items():
        if urun_turu.endswith(anahtar) and not urun_turu.startswith('sos'):
            if anahtar == 'maşa seti' and model != 'Kelebek':
                cumle = 'Makarna, salata ve pasta maşasından oluşur.'
            parcalar.append(cumle)
            break
    if kap:
        parcalar.append(f'Kaplama: {kap}.')
    atolye = "Bayrampaşa'daki atölyemizde üretilir."
    if len(' '.join(parcalar + [atolye])) <= 160:
        parcalar.append(atolye)
    aciklama = ' '.join(parcalar)
    aciklama = aciklama[0].upper() + aciklama[1:]
    return baslik, aciklama


def kontrol(ad, b, a):
    sorun = []
    if len(b) > 60:
        sorun.append(f'başlık {len(b)} karakter')
    if not (110 <= len(a) <= 165):
        sorun.append(f'açıklama {len(a)} karakter')
    return sorun


if __name__ == '__main__':
    products = json.load(open(sys.argv[1]))
    rows = []
    for pid, b, a in SAYFALAR:
        rows.append(dict(type='post', id=pid, ad=f'sayfa {pid}', baslik=b, aciklama=a))
    for tid, b, a in KATEGORILER + MODELLER:
        rows.append(dict(type='term', id=tid, ad=f'terim {tid}', baslik=b, aciklama=a))
    for p in products:
        b, a = urun_metni(p)
        rows.append(dict(type='post', id=p['id'], ad=p['name'], baslik=b, aciklama=a))
    basliklar = [r['baslik'] for r in rows]
    for r in rows:
        s = kontrol(r['ad'], r['baslik'], r['aciklama'])
        if basliklar.count(r['baslik']) > 1:
            s.append('başlık tekrar ediyor')
        r['sorun'] = s
    json.dump(rows, open(sys.argv[2], 'w'), ensure_ascii=False, indent=1)
    for r in rows:
        print(('!! ' + ', '.join(r['sorun']) + ' | ') if r['sorun'] else '', r['ad'], '|', r['baslik'], '|', r['aciklama'])
