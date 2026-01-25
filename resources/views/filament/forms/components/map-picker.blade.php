<div
    x-data="{
        latitude: 40.1644,
        longitude: 25.9014,
        map: null,
        marker: null,
        init() {
            this.$nextTick(() => {
                const livewire = this.$wire;

                // Livewire'da kayıtlı koordinatlar varsa onları kullan
                let lat = parseFloat(livewire.get('data.businessProfile.latitude') ?? '');
                let lng = parseFloat(livewire.get('data.businessProfile.longitude') ?? '');

                if (Number.isNaN(lat) || Number.isNaN(lng)) {
                    lat = 40.1644;
                    lng = 25.9014;
                }

                this.latitude = lat;
                this.longitude = lng;

                // Leaflet haritasını oluştur
                this.map = L.map(this.$refs.mapContainer, {
                    scrollWheelZoom: true,
                    zoomControl: true,
                }).setView([lat, lng], 13);

                // OpenStreetMap tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap',
                    maxZoom: 19,
                }).addTo(this.map);

                setTimeout(() => this.map.invalidateSize(), 100);

                // Marker ekle
                this.marker = L.marker([lat, lng], {
                    draggable: true,
                }).addTo(this.map);

                const updateFromLatLng = (lat, lng, doReverse = true) => {
                    this.latitude = lat.toFixed(7);
                    this.longitude = lng.toFixed(7);

                    // Livewire state'i güncelle (form alanları güncellensin)
                    livewire.set('data.businessProfile.latitude', this.latitude);
                    livewire.set('data.businessProfile.longitude', this.longitude);

                    if (doReverse) {
                        this.reverseGeocode(lat, lng, livewire);
                    }
                };

                // Marker sürüklendiğinde koordinatları ve adresi güncelle
                this.marker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    updateFromLatLng(pos.lat, pos.lng, true);
                });

                // Haritaya tıklayınca marker'ı taşı
                this.map.on('click', (e) => {
                    this.marker.setLatLng(e.latlng);
                    updateFromLatLng(e.latlng.lat, e.latlng.lng, true);
                });
            });
        },
        reverseGeocode(lat, lng, livewire) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    if (!data) return;

                    // Adresi ve yapılandırılmış alanları daha temiz ve Türkçe odaklı formatla
                    const addr = data.address ?? {};
                    let parts = [];

                    const street = addr.road || addr.pedestrian || addr.footway || '';
                    const mahalleRaw = addr.suburb || addr.neighbourhood || addr.residential || '';
                    const townLike = addr.town || addr.village || addr.city_district || '';

                    if (street) parts.push(street);
                    if (mahalleRaw) parts.push(mahalleRaw);
                    if (townLike) parts.push(townLike);
                    // Gökçeada ismini her zaman sabit kullan
                    parts.push('Gökçeada');
                    if (addr.postcode) parts.push(addr.postcode);
                    parts.push('Türkiye');

                    let formatted = parts.filter(Boolean).join(', ');

                    // Bazı saçma karakterleri düzelt (ör: Ѓокчеада → Gökçeada)
                    formatted = formatted.replace(/Ѓокчеада/gi, 'Gökçeada');

                    livewire.set('data.businessProfile.address', formatted);

                    // Mahalle alanını doldur (özel mahalle alanı)
                    if (mahalleRaw) {
                        let mahalle = mahalleRaw.trim();
                        if (!/mahallesi$/i.test(mahalle)) {
                            mahalle = mahalle + ' Mahallesi';
                        }
                        livewire.set('data.businessProfile.neighborhood', mahalle);
                    }

                    // Cadde / sokak alanını doldur
                    if (street) {
                        livewire.set('data.businessProfile.street', street);
                    }

                    // Posta kodu alanını doldur
                    if (addr.postcode) {
                        livewire.set('data.businessProfile.postal_code', addr.postcode);
                    }

                    // Bölge (district) alanı artık sadece kullanıcı tarafından
                    // açılır kutudan seçilecek; harita otomatik doldurmaz.
                })
                .catch(error => console.error('Reverse geocoding hatası:', error));
        },
        normalizeDistrict(value) {
            const txt = String(value)
                .toLowerCase()
                .normalize('NFD')
                .replace(/\p{Diacritic}/gu, '')
                .replace(/köyü|koyu|mahallesi|mah\.|sahil|plaji|plaj/gu, '')
                .trim();

            if (txt.includes('kalekoy')) return 'Kaleköy';
            if (txt.includes('yeni bademli')) return 'Yeni Bademli';
            if (txt.includes('eski bademli')) return 'Eski Bademli';
            if (txt.includes('tepe')) return 'Tepeköy';
            if (txt.includes('zeytin')) return 'Zeytinli';
            if (txt.includes('sirinkoy') || txt.includes('sirink')) return 'Şirinköy';
            if (txt.includes('ugurlu')) return 'Uğurlu';
            if (txt.includes('derekoy') || txt.includes('derek')) return 'Dereköy';
            if (txt.includes('kefalos') || txt.includes('aydincik')) return 'Aydıncık (Kefalos)';
            if (txt.includes('kuzuliman') || txt.includes('kuzu liman')) return 'Kuzuliman';

            // Varsayılan: Merkez
            return 'Merkez';
        },
    }"
    wire:ignore
    class="space-y-2 relative z-0"
>
    <style>
        .leaflet-container {
            z-index: 0 !important;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin="" />
    <div
        x-ref="mapContainer"
        style="height: 400px; width: 100%; border-radius: 8px; border: 1px solid #e5e7eb; background: white;"
    ></div>
    <p class="text-sm text-gray-500">
        📍 Haritada bir nokta seçin veya pini sürükleyin. Enlem, boylam ve adres otomatik güncellenir.
    </p>
</div>
