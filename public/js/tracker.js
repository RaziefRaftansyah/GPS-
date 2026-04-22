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
                }).addTo(map).bindPopup(`
                    <strong>${unitLocation.unit_name || 'Gerobak Kopi'}</strong><br>
                    Driver: ${unitLocation.driver_name || '-'}<br>
                    Device: ${unitLocation.device_id || '-'}<br>
                    Lat: ${unitLocation.latitude}<br>
                    Lng: ${unitLocation.longitude}<br>
                    Battery: ${unitLocation.battery_level !== null ? `${unitLocation.battery_level}%` : '-'}<br>
                    Updated: ${unitLocation.recorded_at || '-'}
                `);

                marker.on('click', () => {
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
                    zIndexOffset: 1000,
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

            renderMap();
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
