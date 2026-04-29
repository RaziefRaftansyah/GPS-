const bodyDataset = document.body ? document.body.dataset : {};

        const state = {
            locations: [],
            activeUnits: [],
            markers: [],
            userMarker: null,
            userLatLng: null,
            map: null,
            hasAutoFramedMap: false,
        };

        const heroBannerImage = bodyDataset.heroBannerImage;

        if (heroBannerImage) {
            document.documentElement.style.setProperty('--hero-banner-image', `url('${heroBannerImage}')`);
        }

const endpoints = {
            latest: bodyDataset.trackerLatestEndpoint || '/api/location/latest',
        };

        const mobileNavToggle = document.getElementById('mobile-nav-toggle');
        const trackerNavActions = document.getElementById('tracker-nav-actions');
        const slideInElements = document.querySelectorAll('.slide-in-up');
        const mapCenterTrigger = document.querySelector('.js-scroll-map-center');
        const mapSection = document.getElementById('lacak');
        const trackerTopbar = document.querySelector('.tracker-topbar');
        const isDriverQrEnabled = bodyDataset.driverQrEnabled === '1';
        const qrOpenButtons = document.querySelectorAll('[data-driver-qr-open]');
        const qrModal = document.getElementById('driver-qr-modal');
        const qrCloseButton = document.getElementById('driver-qr-close');
        const qrStatus = document.getElementById('driver-qr-status');
        const qrAttendance = document.getElementById('driver-qr-attendance');
        const qrAttendanceValue = document.getElementById('driver-qr-attendance-value');
        const qrReaderElementId = 'driver-qr-reader';
        const menuCatalogPayloadElement = document.getElementById('menu-catalog-json');
        const initialAttendanceState = bodyDataset.driverAttendanceState || 'not_clocked_in';
        const initialAttendanceLabel = bodyDataset.driverAttendanceLabel || 'Belum absen masuk.';

        const qrState = {
            scanner: null,
            running: false,
            open: false,
            latestText: '',
            processing: false,
            lastProcessedText: '',
            lastProcessedAt: 0,
        };
        const attendanceState = {
            value: initialAttendanceState,
            label: initialAttendanceLabel,
        };
        const menuCatalog = (() => {
            if (!menuCatalogPayloadElement) {
                return [];
            }

            try {
                const payload = JSON.parse(menuCatalogPayloadElement.textContent || '[]');

                if (!Array.isArray(payload)) {
                    return [];
                }

                return payload;
            } catch (error) {
                return [];
            }
        })();
        const rupiahFormatter = new Intl.NumberFormat('id-ID');

        function closeMobileNav() {
            if (!mobileNavToggle || !trackerNavActions) {
                return;
            }

            mobileNavToggle.classList.remove('is-open');
            trackerNavActions.classList.remove('is-open');
            mobileNavToggle.setAttribute('aria-expanded', 'false');
        }

        if (mobileNavToggle && trackerNavActions) {
            mobileNavToggle.addEventListener('click', () => {
                const isOpen = trackerNavActions.classList.toggle('is-open');

                mobileNavToggle.classList.toggle('is-open', isOpen);
                mobileNavToggle.setAttribute('aria-expanded', String(isOpen));
            });

            trackerNavActions.querySelectorAll('a, button[type="submit"]').forEach((item) => {
                item.addEventListener('click', closeMobileNav);
            });

            document.addEventListener('click', (event) => {
                if (
                    !trackerNavActions.contains(event.target) &&
                    !mobileNavToggle.contains(event.target)
                ) {
                    closeMobileNav();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeMobileNav();
                }
            });
        }

        function setQrStatus(message, tone) {
            if (!qrStatus) {
                return;
            }

            qrStatus.textContent = message;
            qrStatus.classList.remove('is-info', 'is-success', 'is-error');
            qrStatus.classList.add(tone || 'is-info');
        }

        function attendanceLabelFromState(state) {
            switch (state) {
                case 'clocked_in':
                    return 'Sudah absen masuk.';
                case 'clocked_out':
                    return 'Sudah absen keluar.';
                case 'no_assignment':
                    return 'Belum ada assignment aktif.';
                case 'not_clocked_in':
                    return 'Belum absen masuk.';
                default:
                    return 'Status absensi tidak tersedia.';
            }
        }

        function setAttendanceStatus(state, label) {
            if (!qrAttendance || !qrAttendanceValue) {
                return;
            }

            const resolvedState = state || 'not_clocked_in';
            const resolvedLabel = label || attendanceLabelFromState(resolvedState);

            attendanceState.value = resolvedState;
            attendanceState.label = resolvedLabel;

            qrAttendance.dataset.state = resolvedState;
            qrAttendance.classList.remove('is-clocked-in', 'is-clocked-out', 'is-not-clocked-in', 'is-no-assignment');

            if (resolvedState === 'clocked_in') {
                qrAttendance.classList.add('is-clocked-in');
            } else if (resolvedState === 'clocked_out') {
                qrAttendance.classList.add('is-clocked-out');
            } else if (resolvedState === 'no_assignment') {
                qrAttendance.classList.add('is-no-assignment');
            } else {
                qrAttendance.classList.add('is-not-clocked-in');
            }

            qrAttendanceValue.textContent = resolvedLabel;
        }

        function escapeHtml(rawValue) {
            return String(rawValue ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function formatRupiah(value) {
            const numericValue = Number(value);

            if (!Number.isFinite(numericValue)) {
                return 'Rp-';
            }

            return `Rp${rupiahFormatter.format(Math.round(numericValue))}`;
        }

        function buildFallbackDriverAvatarUrl(driverName) {
            const seed = encodeURIComponent(driverName || 'Driver');
            return `https://ui-avatars.com/api/?name=${seed}&background=b56a3b&color=ffffff&size=160&rounded=true&bold=true`;
        }

        function resolveDriverAvatarUrl(unitLocation) {
            const rawUrl = typeof unitLocation?.driver_avatar_url === 'string'
                ? unitLocation.driver_avatar_url.trim()
                : '';

            if (rawUrl) {
                return rawUrl;
            }

            return buildFallbackDriverAvatarUrl(unitLocation?.driver_name || 'Driver');
        }

        function resolveMenuForPopup(unitLocation) {
            if (
                unitLocation &&
                Object.prototype.hasOwnProperty.call(unitLocation, 'menu_catalog') &&
                Array.isArray(unitLocation.menu_catalog)
            ) {
                return unitLocation.menu_catalog;
            }

            return menuCatalog;
        }

        function buildPopupMenuHtml(unitLocation) {
            const popupMenus = resolveMenuForPopup(unitLocation);

            if (!popupMenus.length) {
                return '<p class="driver-map-popup-menu-empty">Katalog menu sedang disiapkan.</p>';
            }

            const itemsHtml = popupMenus.map((item) => `
                <li>
                    <span class="menu-name">${escapeHtml(item.name || 'Menu')}</span>
                    <span class="menu-price">${formatRupiah(item.price)}</span>
                </li>
            `).join('');

            return `
                <ul class="driver-map-popup-menu-list">${itemsHtml}</ul>
            `;
        }

        function buildDriverPopupHtml(unitLocation) {
            const driverName = escapeHtml(unitLocation?.driver_name || 'Driver');
            const avatarUrl = escapeHtml(resolveDriverAvatarUrl(unitLocation));

            return `
                <div class="driver-map-popup">
                    <div class="driver-map-popup-avatar-wrap">
                        <img class="driver-map-popup-avatar" src="${avatarUrl}" alt="Foto profil ${driverName}" loading="lazy">
                    </div>
                    <strong class="driver-map-popup-menu-title">Katalog Menu</strong>
                    ${buildPopupMenuHtml(unitLocation)}
                    <a href="#menu" class="driver-map-popup-menu-link">Lihat katalog lengkap</a>
                </div>
            `;
        }

        function normalizeQrPayload(rawValue) {
            return String(rawValue ?? '')
                .trim()
                .replace(/&amp;/gi, '&');
        }

        function parseQrAsUrl(rawValue) {
            const normalizedValue = normalizeQrPayload(rawValue);

            if (!normalizedValue) {
                return null;
            }

            try {
                return new URL(normalizedValue);
            } catch (error) {
                try {
                    return new URL(normalizedValue, window.location.origin);
                } catch (nestedError) {
                    return null;
                }
            }
        }

        function sanitizeAttendanceQrUrl(parsedUrl) {
            const sanitizedUrl = new URL(parsedUrl.toString());
            const signature = sanitizedUrl.searchParams.get('signature');
            const expires = sanitizedUrl.searchParams.get('expires');

            if (!signature) {
                return sanitizedUrl;
            }

            sanitizedUrl.search = '';
            sanitizedUrl.searchParams.set('signature', signature);

            if (expires) {
                sanitizedUrl.searchParams.set('expires', expires);
            }

            return sanitizedUrl;
        }

        async function handleAttendanceQrUrl(scannedUrl) {
            const parsedUrl = parseQrAsUrl(scannedUrl);

            if (!parsedUrl || !parsedUrl.pathname.includes('/dashboard/driver/attendance/qr')) {
                setQrStatus(`QR berhasil dibaca: ${scannedUrl}`, 'is-success');
                return;
            }

            const attendanceUrl = sanitizeAttendanceQrUrl(parsedUrl);

            if (attendanceUrl.origin !== window.location.origin) {
                setQrStatus('Mengarahkan ke domain QR absensi...', 'is-info');
                window.location.href = attendanceUrl.toString();
                return;
            }

            setQrStatus('Memproses absensi dari QR...', 'is-info');

            try {
                const response = await fetch(attendanceUrl.toString(), {
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                const payload = await response.json();

                if (!response.ok || payload.success !== true) {
                    if (payload && payload.attendance_state) {
                        setAttendanceStatus(payload.attendance_state, payload.attendance_label);
                    }

                    throw new Error(payload.message || 'QR absensi gagal diproses.');
                }

                if (payload && payload.attendance_state) {
                    setAttendanceStatus(payload.attendance_state, payload.attendance_label);
                } else if (payload && payload.action === 'clock_in') {
                    setAttendanceStatus('clocked_in');
                } else if (payload && payload.action === 'clock_out') {
                    setAttendanceStatus('clocked_out');
                }

                setQrStatus(payload.message || 'Absensi berhasil diproses.', 'is-success');
                refreshLocations().catch(() => null);
            } catch (error) {
                setQrStatus(error.message || 'Gagal memproses link QR absensi.', 'is-error');
            }
        }

        async function stopQrScanner() {
            if (!qrState.scanner || !qrState.running) {
                return;
            }

            try {
                await qrState.scanner.stop();
                await qrState.scanner.clear();
            } catch (error) {
                // Keep silent because scanner may already be stopped by browser lifecycle.
            } finally {
                qrState.running = false;
            }
        }

        async function startQrScanner() {
            if (!isDriverQrEnabled || !qrModal) {
                return;
            }

            if (typeof window.Html5Qrcode !== 'function') {
                setQrStatus('Library QR scanner belum siap. Coba refresh halaman.', 'is-error');
                return;
            }

            if (!qrState.scanner) {
                qrState.scanner = new window.Html5Qrcode(qrReaderElementId);
            }

            if (qrState.running) {
                return;
            }

            const scannerConfig = {
                fps: 10,
                qrbox: { width: 240, height: 240 },
                rememberLastUsedCamera: true,
                aspectRatio: 1,
            };

            setQrStatus('Membuka kamera...', 'is-info');

            try {
                await qrState.scanner.start(
                    { facingMode: 'environment' },
                    scannerConfig,
                    async (decodedText) => {
                        const now = Date.now();
                        const isSameRecentScan =
                            decodedText === qrState.lastProcessedText &&
                            now - qrState.lastProcessedAt < 2500;

                        if (qrState.processing || isSameRecentScan) {
                            return;
                        }

                        qrState.processing = true;
                        qrState.latestText = decodedText;
                        try {
                            await handleAttendanceQrUrl(decodedText);
                            qrState.lastProcessedText = decodedText;
                            qrState.lastProcessedAt = Date.now();
                        } finally {
                            qrState.processing = false;
                        }
                    },
                    () => null
                );

                qrState.running = true;
                setQrStatus('Arahkan kamera ke QR code untuk membaca data.', 'is-info');
            } catch (error) {
                setQrStatus('Gagal mengakses kamera. Izinkan akses kamera lalu coba lagi.', 'is-error');
            }
        }

        function openQrModal() {
            if (!qrModal) {
                return;
            }

            qrState.open = true;
            qrState.lastProcessedText = '';
            qrState.lastProcessedAt = 0;
            setAttendanceStatus(attendanceState.value, attendanceState.label);
            qrModal.hidden = false;
            qrModal.classList.add('is-open');
            document.body.classList.add('driver-qr-open');

            if (trackerNavActions && trackerNavActions.classList.contains('is-open')) {
                closeMobileNav();
            }

            startQrScanner().catch(() => null);
        }

        function closeQrModal() {
            if (!qrModal) {
                return;
            }

            qrState.open = false;
            qrModal.classList.remove('is-open');
            qrModal.hidden = true;
            document.body.classList.remove('driver-qr-open');
            stopQrScanner().catch(() => null);
        }

        if (isDriverQrEnabled && qrModal && qrOpenButtons.length > 0) {
            setAttendanceStatus(initialAttendanceState, initialAttendanceLabel);

            qrOpenButtons.forEach((button) => {
                button.addEventListener('click', openQrModal);
            });

            if (qrCloseButton) {
                qrCloseButton.addEventListener('click', closeQrModal);
            }

            qrModal.addEventListener('click', (event) => {
                if (event.target === qrModal) {
                    closeQrModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && qrState.open) {
                    closeQrModal();
                }
            });

        }

        if (mapCenterTrigger && mapSection) {
            mapCenterTrigger.addEventListener('click', (event) => {
                event.preventDefault();

                const topbarHeight = trackerTopbar ? trackerTopbar.offsetHeight : 0;
                const sectionTop = mapSection.getBoundingClientRect().top + window.pageYOffset;
                const visibleViewportHeight = window.innerHeight - topbarHeight;
                const centeredOffset = topbarHeight + Math.max((visibleViewportHeight - mapSection.offsetHeight) / 2, 0);
                const targetTop = Math.max(sectionTop - centeredOffset, 0);

                window.scrollTo({
                    top: targetTop,
                    behavior: 'smooth',
                });
            });
        }

        document.addEventListener('click', (event) => {
            const menuLink = event.target.closest('.driver-map-popup-menu-link');
            const menuSection = document.getElementById('menu');

            if (!menuLink || !menuSection) {
                return;
            }

            event.preventDefault();

            const topbarHeight = trackerTopbar ? trackerTopbar.offsetHeight : 0;
            const sectionTop = menuSection.getBoundingClientRect().top + window.pageYOffset;

            window.scrollTo({
                top: Math.max(sectionTop - topbarHeight - 12, 0),
                behavior: 'smooth',
            });
        });

        if ('IntersectionObserver' in window) {
            const slideObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        slideObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.2,
            });

            slideInElements.forEach((element) => slideObserver.observe(element));
        } else {
            slideInElements.forEach((element) => element.classList.add('is-visible'));
        }

        const map = L.map('map', {
            zoomControl: false,
        }).setView([-2.5489, 118.0149], 5);
        state.map = map;

        L.control.zoom({
            position: 'bottomright',
        }).addTo(map);

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            detectRetina: true,
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, Maxar, Earthstar Geographics, and the GIS User Community',
        }).addTo(map);

        const driverIcon = L.divIcon({
            className: 'driver-location-marker-wrapper',
            html: `
                <div class="map-avatar-marker is-driver" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="4" fill="currentColor"></circle>
                        <path d="M5.5 19.5c1.8-3.4 4.2-5.1 6.5-5.1s4.7 1.7 6.5 5.1" fill="currentColor"></path>
                    </svg>
                </div>
            `,
            iconSize: [44, 54],
            iconAnchor: [22, 48],
            popupAnchor: [0, -40],
        });

        const userIcon = L.divIcon({
            className: 'user-location-marker-wrapper',
            html: `
                <div class="map-avatar-marker is-user" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="4" fill="currentColor"></circle>
                        <path d="M5.5 19.5c1.8-3.4 4.2-5.1 6.5-5.1s4.7 1.7 6.5 5.1" fill="currentColor"></path>
                    </svg>
                </div>
            `,
            iconSize: [44, 54],
            iconAnchor: [22, 48],
            popupAnchor: [0, -40],
        });

        function updateSummary() {
            const activeCountElement = document.getElementById('active-unit-count');
            const heroActiveCountElement = document.getElementById('active-unit-count-hero');

            if (activeCountElement) {
                activeCountElement.textContent = state.activeUnits.length;
            }

            if (heroActiveCountElement) {
                heroActiveCountElement.textContent = state.activeUnits.length;
            }
        }

        function calculateDistanceKm(from, to) {
            const earthRadiusKm = 6371;
            const toRadians = (degrees) => degrees * Math.PI / 180;
            const latDelta = toRadians(to.lat - from.lat);
            const lngDelta = toRadians(to.lng - from.lng);
            const startLat = toRadians(from.lat);
            const endLat = toRadians(to.lat);

            const haversine =
                Math.sin(latDelta / 2) ** 2 +
                Math.cos(startLat) * Math.cos(endLat) * Math.sin(lngDelta / 2) ** 2;

            return earthRadiusKm * 2 * Math.atan2(Math.sqrt(haversine), Math.sqrt(1 - haversine));
        }

        function formatDistance(distanceKm) {
            if (distanceKm < 1) {
                return `${Math.round(distanceKm * 1000)} m`;
            }

            return `${distanceKm.toFixed(distanceKm < 10 ? 1 : 0)} km`;
        }

        function updateNearestUnit() {
            const distanceEl = document.getElementById('nearest-distance');
            const copyEl = document.getElementById('nearest-copy');

            if (!distanceEl || !copyEl) {
                return;
            }

            if (!state.userLatLng) {
                distanceEl.textContent = 'Mencari...';
                copyEl.textContent = 'Aktifkan izin lokasi browser untuk melihat jarak gerobak paling dekat.';
                return;
            }

            if (!state.activeUnits.length) {
                distanceEl.textContent = '-';
                copyEl.textContent = 'Belum ada Kopling yang tersedia di peta saat ini.';
                return;
            }

            const nearest = state.activeUnits
                .filter((unit) => unit.latitude !== null && unit.longitude !== null)
                .map((unit) => ({
                    ...unit,
                    distanceKm: calculateDistanceKm(state.userLatLng, {
                        lat: Number(unit.latitude),
                        lng: Number(unit.longitude),
                    }),
                }))
                .sort((a, b) => a.distanceKm - b.distanceKm)[0];

            if (!nearest) {
                distanceEl.textContent = '-';
                copyEl.textContent = 'Data koordinat Kopling belum lengkap untuk menghitung jarak.';
                return;
            }

            distanceEl.textContent = formatDistance(nearest.distanceKm);
            copyEl.textContent = `${nearest.unit_name || nearest.device_id || 'Kopling'} adalah Kopling terdekat dari lokasimu.`;
        }

        function renderHistory() {
            const list = document.getElementById('history-list');
            if (!list) {
                return;
            }

            list.innerHTML = '';

            if (!state.activeUnits.length) {
                const item = document.createElement('li');
                item.textContent = 'Belum ada lokasi realtime yang masuk.';
                list.appendChild(item);
                return;
            }

            [...state.activeUnits]
                .sort((a, b) => (b.recorded_at || '').localeCompare(a.recorded_at || ''))
                .forEach((location) => {
                    const item = document.createElement('li');
                    item.innerHTML = `
                        <strong>${location.unit_name || location.device_id || 'Traccar Device'}</strong>
                        <div class="history-coords">${location.driver_name ? `Driver: ${location.driver_name}` : 'Driver belum di-assign'}</div>
                        <div class="history-coords">${location.unit_code ? `Kode: ${location.unit_code}` : 'Kode unit belum tersedia'}</div>
                        <div class="history-coords">${location.latitude}, ${location.longitude}</div>
                        <small>${location.recorded_at ?? 'Belum tersimpan ke database'}</small>
                    `;
                    list.appendChild(item);
                });
        }

        function buildDisplayPositions(locations) {
            const groups = new Map();

            locations.forEach((location) => {
                const key = `${location.latitude},${location.longitude}`;

                if (!groups.has(key)) {
                    groups.set(key, []);
                }

                groups.get(key).push(location);
            });

            return locations.map((location) => {
                const key = `${location.latitude},${location.longitude}`;
                const group = groups.get(key) || [location];
                const index = group.indexOf(location);

                if (group.length === 1 || index === -1) {
                    return {
                        ...location,
                        displayLatitude: location.latitude,
                        displayLongitude: location.longitude,
                    };
                }

                const angle = (Math.PI * 2 * index) / group.length;
                const distance = 0.000045;

                return {
                    ...location,
                    displayLatitude: location.latitude + Math.sin(angle) * distance,
                    displayLongitude: location.longitude + Math.cos(angle) * distance,
                };
            });
        }

        function renderMap() {
            const displayLocations = buildDisplayPositions(state.activeUnits);
            const latLngs = displayLocations.map((location) => [location.displayLatitude, location.displayLongitude]);
            const boundsLatLngs = [...latLngs];

            state.markers.forEach((marker) => marker.remove());
            state.markers = [];

            if (!latLngs.length) {
                if (state.userLatLng && !state.hasAutoFramedMap) {
                    map.setView([state.userLatLng.lat, state.userLatLng.lng], 15);
                    state.hasAutoFramedMap = true;
                }

                renderHistory();
                updateSummary();
                updateNearestUnit();
                return;
            }

            displayLocations.forEach((unitLocation) => {
                const marker = L.marker([unitLocation.displayLatitude, unitLocation.displayLongitude], {
                    icon: driverIcon,
                    zIndexOffset: 500,
                }).addTo(map).bindPopup(buildDriverPopupHtml(unitLocation), {
                    maxWidth: 320,
                    className: 'driver-popup-leaflet',
                });

                marker.on('click', (event) => {
                    if (event && event.originalEvent && typeof event.originalEvent.stopPropagation === 'function') {
                        event.originalEvent.stopPropagation();
                    }

                    marker.openPopup();
                    map.flyTo([unitLocation.displayLatitude, unitLocation.displayLongitude], Math.max(map.getZoom(), 18), {
                        animate: true,
                        duration: 0.8,
                    });
                });

                state.markers.push(marker);
            });

            if (state.userLatLng) {
                boundsLatLngs.push([state.userLatLng.lat, state.userLatLng.lng]);
            }

            if (!state.hasAutoFramedMap) {
                if (boundsLatLngs.length === 1) {
                    map.setView(latLngs[0], 17);
                } else if (boundsLatLngs.length > 1) {
                    map.fitBounds(boundsLatLngs, { padding: [34, 34] });
                }

                state.hasAutoFramedMap = true;
            }

            requestAnimationFrame(() => {
                map.invalidateSize();
            });

            renderHistory();
            updateSummary();
            updateNearestUnit();
        }

        function setUserLocation(position) {
            state.userLatLng = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            };

            if (!state.userMarker) {
                state.userMarker = L.marker([state.userLatLng.lat, state.userLatLng.lng], {
                    icon: userIcon,
                    zIndexOffset: 150,
                }).addTo(map).bindPopup('Posisi kamu saat ini');

                state.userMarker.on('click', () => {
                    map.flyTo([state.userLatLng.lat, state.userLatLng.lng], Math.max(map.getZoom(), 18), {
                        animate: true,
                        duration: 0.8,
                    });
                });
            } else {
                state.userMarker.setLatLng([state.userLatLng.lat, state.userLatLng.lng]);
            }

            if (!state.hasAutoFramedMap && state.activeUnits.length === 0) {
                map.setView([state.userLatLng.lat, state.userLatLng.lng], 15);
                state.hasAutoFramedMap = true;
            }

            updateNearestUnit();
        }

        function requestUserLocation() {
            if (!navigator.geolocation) {
                const distanceEl = document.getElementById('nearest-distance');
                const copyEl = document.getElementById('nearest-copy');

                if (distanceEl) {
                    distanceEl.textContent = '-';
                }

                if (copyEl) {
                    copyEl.textContent = 'Browser ini belum mendukung deteksi lokasi.';
                }

                return;
            }

            navigator.geolocation.watchPosition(
                setUserLocation,
                () => {
                    updateNearestUnit();
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 30000,
                    timeout: 10000,
                },
            );
        }

        async function refreshLocations() {
            const response = await fetch(endpoints.latest, {
                headers: {
                    Accept: 'application/json',
                },
            });

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Gagal mengambil lokasi terbaru.');
            }

            state.locations = payload.locations || [];
            state.activeUnits = payload.active_units || [];
            renderMap();
        }

        requestUserLocation();
        refreshLocations().catch(() => {
            renderMap();
        });
        setInterval(() => {
            refreshLocations().catch(() => null);
        }, 8000);
