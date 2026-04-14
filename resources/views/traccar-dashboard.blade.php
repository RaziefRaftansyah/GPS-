<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div>
                <p style="margin: 0; font-size: 0.8rem; letter-spacing: 0.08em; text-transform: uppercase; color: #9a6b4d;">
                    Monitoring Traccar
                </p>
                <h2 style="margin: 6px 0 0; font-size: 1.8rem; color: #3b2418;">
                    Status data lokasi yang masuk dari Traccar
                </h2>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a
                    href="{{ route('dashboard') }}"
                    style="display: inline-flex; align-items: center; justify-content: center; padding: 12px 18px; border-radius: 999px; background: rgba(255,255,255,0.8); color: #6a412d; text-decoration: none; font-weight: 700; border: 1px solid rgba(106,65,45,0.12);"
                >
                    Profil User
                </a>
                <a
                    href="{{ route('tracker.index') }}"
                    style="display: inline-flex; align-items: center; justify-content: center; padding: 12px 18px; border-radius: 999px; background: linear-gradient(135deg, #6a412d, #b56a3b); color: #fff; text-decoration: none; font-weight: 700;"
                >
                    Buka Peta Publik
                </a>
            </div>
        </div>
    </x-slot>

    <div style="padding: 32px 0 48px; background: linear-gradient(180deg, #f8f2e8 0%, #f3f4f6 100%); min-height: 100vh;">
        <div style="max-width: 1120px; margin: 0 auto; padding: 0 16px;">
            @if (session('dashboard_status'))
                <div style="margin-bottom: 18px; padding: 16px 18px; border-radius: 18px; background: rgba(47,107,85,0.12); color: #2f6b55; border: 1px solid rgba(47,107,85,0.14); font-weight: 700;">
                    {{ session('dashboard_status') }}
                </div>
            @endif

            @if ($errors->any())
                <div style="margin-bottom: 18px; padding: 16px 18px; border-radius: 18px; background: rgba(159,47,32,0.08); color: #9f2f20; border: 1px solid rgba(159,47,32,0.14);">
                    <strong style="display: block; margin-bottom: 8px;">Data monitoring belum bisa disimpan.</strong>
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px;">
                <article style="padding: 20px; border-radius: 22px; background: rgba(255,250,242,0.92); border: 1px solid rgba(106,65,45,0.12); box-shadow: 0 18px 40px rgba(59,36,24,0.08);">
                    <span style="display: block; margin-bottom: 8px; color: #8a634b; text-transform: uppercase; font-size: 0.76rem; letter-spacing: 0.08em;">Device Aktif</span>
                    <strong style="font-size: 2rem; color: #3b2418;">{{ $deviceSummaries->count() }}</strong>
                </article>
                <article style="padding: 20px; border-radius: 22px; background: rgba(255,250,242,0.92); border: 1px solid rgba(106,65,45,0.12); box-shadow: 0 18px 40px rgba(59,36,24,0.08);">
                    <span style="display: block; margin-bottom: 8px; color: #8a634b; text-transform: uppercase; font-size: 0.76rem; letter-spacing: 0.08em;">Data Tanpa Device ID</span>
                    <strong style="font-size: 2rem; color: #3b2418;">{{ $unknownDeviceCount }}</strong>
                </article>
                <article style="padding: 20px; border-radius: 22px; background: rgba(255,250,242,0.92); border: 1px solid rgba(106,65,45,0.12); box-shadow: 0 18px 40px rgba(59,36,24,0.08);">
                    <span style="display: block; margin-bottom: 8px; color: #8a634b; text-transform: uppercase; font-size: 0.76rem; letter-spacing: 0.08em;">Device Terakhir</span>
                    <strong style="font-size: 1.15rem; color: #3b2418;">{{ $latestTrackedLocation?->device_id ?: '-' }}</strong>
                </article>
                <article style="padding: 20px; border-radius: 22px; background: rgba(255,250,242,0.92); border: 1px solid rgba(106,65,45,0.12); box-shadow: 0 18px 40px rgba(59,36,24,0.08);">
                    <span style="display: block; margin-bottom: 8px; color: #8a634b; text-transform: uppercase; font-size: 0.76rem; letter-spacing: 0.08em;">Update Terakhir</span>
                    <strong style="font-size: 1rem; color: #3b2418;">{{ $latestTrackedLocation?->recorded_at?->translatedFormat('d M Y H:i:s') ?? '-' }}</strong>
                </article>
            </section>

            <section style="margin-top: 24px; padding: 26px; border-radius: 26px; border: 1px solid rgba(106,65,45,0.12); background: rgba(255,250,242,0.92); box-shadow: 0 18px 40px rgba(59,36,24,0.08);">
                <p style="margin: 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">
                    Ringkasan Device
                </p>
                <h3 style="margin: 8px 0 18px; font-size: 2rem; color: #3b2418;">Apakah Traccar berhasil mengirim data?</h3>

                @if ($deviceSummaries->isEmpty())
                    <div style="padding: 22px; border-radius: 20px; background: rgba(255,255,255,0.72); border: 1px dashed rgba(106,65,45,0.26); color: #6c5244;">
                        Belum ada device Traccar dengan <code>device_id</code> yang masuk. Cek lagi pengaturan <strong>Device Identifier</strong> di aplikasi Traccar.
                    </div>
                @else
                    <div style="display: grid; gap: 14px;">
                        @foreach ($deviceSummaries as $device)
                            <article style="padding: 18px; border-radius: 20px; background: rgba(255,255,255,0.7); border: 1px solid rgba(106,65,45,0.1);">
                                <div style="display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; align-items: start;">
                                    <div>
                                        <strong style="display: block; font-size: 1.15rem; color: #3b2418;">{{ $device['unit_name'] ?: $device['device_id'] }}</strong>
                                        <span style="display: block; margin-top: 4px; color: #8a634b;">Device: {{ $device['device_id'] }}</span>
                                        <span style="display: block; margin-top: 4px; color: #8a634b;">Driver aktif: {{ $device['driver_name'] ?: 'Belum di-assign' }}</span>
                                        <span style="display: block; margin-top: 4px; color: #8a634b;">Terakhir kirim: {{ optional($device['last_seen'])->translatedFormat('d F Y H:i:s') ?? '-' }}</span>
                                    </div>
                                    <span style="display: inline-flex; padding: 8px 12px; border-radius: 999px; background: rgba(47,107,85,0.12); color: #2f6b55; font-weight: 700;">
                                        {{ $device['total_logs'] }} log
                                    </span>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-top: 16px;">
                                    <div style="padding: 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                        <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">Latitude</span>
                                        <strong>{{ $device['latitude'] }}</strong>
                                    </div>
                                    <div style="padding: 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                        <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">Longitude</span>
                                        <strong>{{ $device['longitude'] }}</strong>
                                    </div>
                                    <div style="padding: 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                        <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">Baterai</span>
                                        <strong>{{ $device['battery_level'] !== null ? $device['battery_level'].'%' : '-' }}</strong>
                                    </div>
                                    <div style="padding: 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                        <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">Status</span>
                                        <strong>
                                            @if ($device['is_moving'] === null)
                                                -
                                            @else
                                                {{ $device['is_moving'] ? 'Bergerak' : 'Diam' }}
                                            @endif
                                        </strong>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section style="margin-top: 24px; padding: 26px; border-radius: 26px; border: 1px solid rgba(106,65,45,0.12); background: rgba(255,250,242,0.92); box-shadow: 0 18px 40px rgba(59,36,24,0.08);">
                <p style="margin: 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">
                    Kelola Data Lokasi
                </p>
                <h3 style="margin: 8px 0 18px; font-size: 2rem; color: #3b2418;">Tambah dan perbarui data monitoring Traccar</h3>

                <form method="POST" action="{{ route('dashboard.traccar.locations.store') }}" style="display: grid; gap: 18px; padding: 22px; border-radius: 22px; background: rgba(255,255,255,0.76); border: 1px solid rgba(106,65,45,0.1);">
                    @csrf

                    <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px;">
                        <label style="display: grid; gap: 6px; color: #6a412d;">
                            <span style="font-weight: 700;">Device ID</span>
                            <input type="text" name="device_id" value="{{ old('device_id') }}" placeholder="gerobak-kopi-01" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                        </label>
                        <label style="display: grid; gap: 6px; color: #6a412d;">
                            <span style="font-weight: 700;">Latitude</span>
                            <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude') }}" required style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                        </label>
                        <label style="display: grid; gap: 6px; color: #6a412d;">
                            <span style="font-weight: 700;">Longitude</span>
                            <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude') }}" required style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                        </label>
                        <label style="display: grid; gap: 6px; color: #6a412d;">
                            <span style="font-weight: 700;">Waktu Rekam</span>
                            <input type="datetime-local" name="recorded_at" value="{{ old('recorded_at', now()->format('Y-m-d\\TH:i')) }}" required style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                        </label>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 14px;">
                        <label style="display: grid; gap: 6px; color: #6a412d;">
                            <span style="font-weight: 700;">Akurasi</span>
                            <input type="number" step="0.01" name="accuracy" value="{{ old('accuracy') }}" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                        </label>
                        <label style="display: grid; gap: 6px; color: #6a412d;">
                            <span style="font-weight: 700;">Speed</span>
                            <input type="number" step="0.01" name="speed" value="{{ old('speed') }}" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                        </label>
                        <label style="display: grid; gap: 6px; color: #6a412d;">
                            <span style="font-weight: 700;">Heading</span>
                            <input type="number" step="0.01" name="heading" value="{{ old('heading') }}" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                        </label>
                        <label style="display: grid; gap: 6px; color: #6a412d;">
                            <span style="font-weight: 700;">Altitude</span>
                            <input type="number" step="0.01" name="altitude" value="{{ old('altitude') }}" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                        </label>
                        <label style="display: grid; gap: 6px; color: #6a412d;">
                            <span style="font-weight: 700;">Battery (%)</span>
                            <input type="number" step="0.01" min="0" max="100" name="battery_level" value="{{ old('battery_level') }}" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                        </label>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px;">
                        <label style="display: grid; gap: 6px; color: #6a412d;">
                            <span style="font-weight: 700;">Charging</span>
                            <select name="is_charging" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                <option value="" @selected(old('is_charging') === null || old('is_charging') === '')>Tidak diketahui</option>
                                <option value="1" @selected(old('is_charging') === '1')>Ya</option>
                                <option value="0" @selected(old('is_charging') === '0')>Tidak</option>
                            </select>
                        </label>
                        <label style="display: grid; gap: 6px; color: #6a412d;">
                            <span style="font-weight: 700;">Sedang Bergerak</span>
                            <select name="is_moving" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                <option value="" @selected(old('is_moving') === null || old('is_moving') === '')>Tidak diketahui</option>
                                <option value="1" @selected(old('is_moving') === '1')>Ya</option>
                                <option value="0" @selected(old('is_moving') === '0')>Tidak</option>
                            </select>
                        </label>
                        <label style="display: grid; gap: 6px; color: #6a412d;">
                            <span style="font-weight: 700;">Activity</span>
                            <input type="text" name="activity" value="{{ old('activity') }}" placeholder="walking / on_foot" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                        </label>
                        <label style="display: grid; gap: 6px; color: #6a412d;">
                            <span style="font-weight: 700;">Event</span>
                            <input type="text" name="event_type" value="{{ old('event_type') }}" placeholder="heartbeat" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                        </label>
                    </div>

                    <div style="display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; align-items: center;">
                        <p style="margin: 0; color: #8a634b;">Form ini bisa dipakai untuk menambah data manual saat testing atau koreksi monitoring.</p>
                        <button type="submit" style="padding: 12px 20px; border: none; border-radius: 999px; background: linear-gradient(135deg, #6a412d, #b56a3b); color: #fff; font-weight: 700; cursor: pointer;">
                            Simpan Data Monitoring
                        </button>
                    </div>
                </form>

                <div style="margin-top: 22px; display: grid; gap: 14px;">
                    @forelse ($managedLocations as $location)
                        <article style="padding: 20px; border-radius: 22px; background: rgba(255,255,255,0.7); border: 1px solid rgba(106,65,45,0.1);">
                            <div style="display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; align-items: start;">
                                <div>
                                    <strong style="display: block; font-size: 1.1rem; color: #3b2418;">
                                        {{ $location->device_id ?: 'Tanpa device_id' }}
                                    </strong>
                                    <span style="display: block; margin-top: 4px; color: #8a634b;">
                                        Titik: {{ $location->latitude }}, {{ $location->longitude }}
                                    </span>
                                    <span style="display: block; margin-top: 4px; color: #8a634b;">
                                        Terekam: {{ $location->recorded_at?->translatedFormat('d M Y H:i:s') ?? '-' }}
                                    </span>
                                </div>

                                <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                                    <span style="display: inline-flex; padding: 8px 12px; border-radius: 999px; background: rgba(248,242,232,0.9); color: #6a412d; font-weight: 700;">
                                        ID #{{ $location->id }}
                                    </span>
                                    <form method="POST" action="{{ route('dashboard.traccar.locations.destroy', $location) }}" onsubmit="return confirm('Hapus data lokasi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: 10px 16px; border: 1px solid rgba(159,47,32,0.2); border-radius: 999px; background: rgba(159,47,32,0.08); color: #9f2f20; font-weight: 700; cursor: pointer;">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 12px; margin-top: 16px;">
                                <div style="padding: 12px 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                    <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">Akurasi</span>
                                    <strong>{{ $location->accuracy ?? '-' }}</strong>
                                </div>
                                <div style="padding: 12px 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                    <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">Speed</span>
                                    <strong>{{ $location->speed ?? '-' }}</strong>
                                </div>
                                <div style="padding: 12px 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                    <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">Heading</span>
                                    <strong>{{ $location->heading ?? '-' }}</strong>
                                </div>
                                <div style="padding: 12px 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                    <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">Battery</span>
                                    <strong>{{ $location->battery_level !== null ? $location->battery_level.'%' : '-' }}</strong>
                                </div>
                                <div style="padding: 12px 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                    <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">Charging</span>
                                    <strong>{{ $location->is_charging === null ? '-' : ($location->is_charging ? 'Ya' : 'Tidak') }}</strong>
                                </div>
                                <div style="padding: 12px 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                    <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">Status</span>
                                    <strong>{{ $location->is_moving === null ? '-' : ($location->is_moving ? 'Bergerak' : 'Diam') }}</strong>
                                </div>
                            </div>

                            <details style="margin-top: 16px;">
                                <summary style="cursor: pointer; color: #6a412d; font-weight: 700;">Edit data lokasi</summary>

                                <form method="POST" action="{{ route('dashboard.traccar.locations.update', $location) }}" style="margin-top: 16px; display: grid; gap: 16px;">
                                    @csrf
                                    @method('PATCH')

                                    <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px;">
                                        <label style="display: grid; gap: 6px; color: #6a412d;">
                                            <span style="font-weight: 700;">Device ID</span>
                                            <input type="text" name="device_id" value="{{ old('device_id', $location->device_id) }}" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                        </label>
                                        <label style="display: grid; gap: 6px; color: #6a412d;">
                                            <span style="font-weight: 700;">Latitude</span>
                                            <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $location->latitude) }}" required style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                        </label>
                                        <label style="display: grid; gap: 6px; color: #6a412d;">
                                            <span style="font-weight: 700;">Longitude</span>
                                            <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $location->longitude) }}" required style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                        </label>
                                        <label style="display: grid; gap: 6px; color: #6a412d;">
                                            <span style="font-weight: 700;">Waktu Rekam</span>
                                            <input type="datetime-local" name="recorded_at" value="{{ old('recorded_at', $location->recorded_at?->format('Y-m-d\\TH:i')) }}" required style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                        </label>
                                    </div>

                                    <div style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 14px;">
                                        <label style="display: grid; gap: 6px; color: #6a412d;">
                                            <span style="font-weight: 700;">Akurasi</span>
                                            <input type="number" step="0.01" name="accuracy" value="{{ old('accuracy', $location->accuracy) }}" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                        </label>
                                        <label style="display: grid; gap: 6px; color: #6a412d;">
                                            <span style="font-weight: 700;">Speed</span>
                                            <input type="number" step="0.01" name="speed" value="{{ old('speed', $location->speed) }}" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                        </label>
                                        <label style="display: grid; gap: 6px; color: #6a412d;">
                                            <span style="font-weight: 700;">Heading</span>
                                            <input type="number" step="0.01" name="heading" value="{{ old('heading', $location->heading) }}" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                        </label>
                                        <label style="display: grid; gap: 6px; color: #6a412d;">
                                            <span style="font-weight: 700;">Altitude</span>
                                            <input type="number" step="0.01" name="altitude" value="{{ old('altitude', $location->altitude) }}" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                        </label>
                                        <label style="display: grid; gap: 6px; color: #6a412d;">
                                            <span style="font-weight: 700;">Battery (%)</span>
                                            <input type="number" step="0.01" min="0" max="100" name="battery_level" value="{{ old('battery_level', $location->battery_level) }}" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                        </label>
                                    </div>

                                    <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px;">
                                        <label style="display: grid; gap: 6px; color: #6a412d;">
                                            <span style="font-weight: 700;">Charging</span>
                                            <select name="is_charging" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                                <option value="" @selected(old('is_charging', $location->is_charging === null ? '' : ($location->is_charging ? '1' : '0')) === '')>Tidak diketahui</option>
                                                <option value="1" @selected(old('is_charging', $location->is_charging === null ? '' : ($location->is_charging ? '1' : '0')) === '1')>Ya</option>
                                                <option value="0" @selected(old('is_charging', $location->is_charging === null ? '' : ($location->is_charging ? '1' : '0')) === '0')>Tidak</option>
                                            </select>
                                        </label>
                                        <label style="display: grid; gap: 6px; color: #6a412d;">
                                            <span style="font-weight: 700;">Sedang Bergerak</span>
                                            <select name="is_moving" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                                <option value="" @selected(old('is_moving', $location->is_moving === null ? '' : ($location->is_moving ? '1' : '0')) === '')>Tidak diketahui</option>
                                                <option value="1" @selected(old('is_moving', $location->is_moving === null ? '' : ($location->is_moving ? '1' : '0')) === '1')>Ya</option>
                                                <option value="0" @selected(old('is_moving', $location->is_moving === null ? '' : ($location->is_moving ? '1' : '0')) === '0')>Tidak</option>
                                            </select>
                                        </label>
                                        <label style="display: grid; gap: 6px; color: #6a412d;">
                                            <span style="font-weight: 700;">Activity</span>
                                            <input type="text" name="activity" value="{{ old('activity', $location->activity) }}" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                        </label>
                                        <label style="display: grid; gap: 6px; color: #6a412d;">
                                            <span style="font-weight: 700;">Event</span>
                                            <input type="text" name="event_type" value="{{ old('event_type', $location->event_type) }}" style="padding: 12px 14px; border-radius: 14px; border: 1px solid rgba(106,65,45,0.16); background: #fffaf5;">
                                        </label>
                                    </div>

                                    <div style="display: flex; justify-content: flex-end;">
                                        <button type="submit" style="padding: 12px 20px; border: none; border-radius: 999px; background: linear-gradient(135deg, #6a412d, #b56a3b); color: #fff; font-weight: 700; cursor: pointer;">
                                            Update Data
                                        </button>
                                    </div>
                                </form>
                            </details>
                        </article>
                    @empty
                        <div style="padding: 22px; border-radius: 20px; background: rgba(255,255,255,0.72); border: 1px dashed rgba(106,65,45,0.26); color: #6c5244;">
                            Belum ada data lokasi yang bisa dikelola.
                        </div>
                    @endforelse
                </div>

                @if ($managedLocations->hasPages())
                    <div style="margin-top: 18px; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; align-items: center;">
                        <span style="color: #8a634b;">
                            Menampilkan {{ $managedLocations->firstItem() }} - {{ $managedLocations->lastItem() }} dari {{ $managedLocations->total() }} data
                        </span>
                        <div style="display: flex; gap: 10px;">
                            @if ($managedLocations->onFirstPage())
                                <span style="padding: 10px 14px; border-radius: 999px; background: rgba(106,65,45,0.08); color: #b19788;">Sebelumnya</span>
                            @else
                                <a href="{{ $managedLocations->previousPageUrl() }}" style="padding: 10px 14px; border-radius: 999px; background: rgba(255,255,255,0.86); color: #6a412d; text-decoration: none; font-weight: 700; border: 1px solid rgba(106,65,45,0.1);">Sebelumnya</a>
                            @endif

                            @if ($managedLocations->hasMorePages())
                                <a href="{{ $managedLocations->nextPageUrl() }}" style="padding: 10px 14px; border-radius: 999px; background: rgba(255,255,255,0.86); color: #6a412d; text-decoration: none; font-weight: 700; border: 1px solid rgba(106,65,45,0.1);">Berikutnya</a>
                            @else
                                <span style="padding: 10px 14px; border-radius: 999px; background: rgba(106,65,45,0.08); color: #b19788;">Berikutnya</span>
                            @endif
                        </div>
                    </div>
                @endif
            </section>

            <section style="margin-top: 24px; padding: 26px; border-radius: 26px; border: 1px solid rgba(106,65,45,0.12); background: rgba(255,250,242,0.92); box-shadow: 0 18px 40px rgba(59,36,24,0.08);">
                <p style="margin: 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">
                    Request Mentah Traccar
                </p>
                <h3 style="margin: 8px 0 18px; font-size: 2rem; color: #3b2418;">Payload yang benar-benar dikirim aplikasi</h3>

                @forelse ($requestLogs as $log)
                    <article style="margin-bottom: 16px; padding: 18px; border-radius: 20px; background: rgba(255,255,255,0.7); border: 1px solid rgba(106,65,45,0.1);">
                        <div style="display: flex; justify-content: space-between; gap: 14px; flex-wrap: wrap; align-items: center;">
                            <div>
                                <strong style="color: #3b2418;">Log #{{ $log->id }}</strong>
                                <span style="margin-left: 8px; color: #8a634b;">{{ $log->created_at?->translatedFormat('d M Y H:i:s') }}</span>
                            </div>
                            <span style="display: inline-flex; padding: 8px 12px; border-radius: 999px; font-weight: 700; {{ $log->processed ? 'background: rgba(47,107,85,0.12); color: #2f6b55;' : 'background: rgba(159,47,32,0.12); color: #9f2f20;' }}">
                                {{ $log->processed ? 'Diproses' : 'Gagal diproses' }}
                            </span>
                        </div>

                        <div style="margin-top: 14px; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px;">
                            <div style="padding: 12px 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">Method</span>
                                <strong>{{ $log->method }}</strong>
                            </div>
                            <div style="padding: 12px 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">Path</span>
                                <strong>{{ $log->path }}</strong>
                            </div>
                            <div style="padding: 12px 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">Content Type</span>
                                <strong>{{ $log->content_type ?: '-' }}</strong>
                            </div>
                            <div style="padding: 12px 14px; border-radius: 16px; background: rgba(248,242,232,0.8);">
                                <span style="display: block; margin-bottom: 6px; color: #8a634b; font-size: 0.76rem; text-transform: uppercase;">IP</span>
                                <strong>{{ $log->ip_address ?: '-' }}</strong>
                            </div>
                        </div>

                        @if ($log->error_message)
                            <div style="margin-top: 14px; padding: 14px; border-radius: 16px; background: rgba(159,47,32,0.08); color: #9f2f20;">
                                <strong>Error:</strong>
                                <pre style="margin: 8px 0 0; white-space: pre-wrap;">{{ $log->error_message }}</pre>
                            </div>
                        @endif

                        <div style="margin-top: 14px; display: grid; gap: 12px;">
                            <div>
                                <strong style="display: block; margin-bottom: 8px; color: #5a3726;">Raw Body</strong>
                                <pre style="margin: 0; padding: 14px; border-radius: 16px; background: #2a211d; color: #f7efe3; overflow: auto; white-space: pre-wrap;">{{ $log->raw_body ?: '-' }}</pre>
                            </div>
                            <div>
                                <strong style="display: block; margin-bottom: 8px; color: #5a3726;">JSON Payload</strong>
                                <pre style="margin: 0; padding: 14px; border-radius: 16px; background: #2a211d; color: #f7efe3; overflow: auto; white-space: pre-wrap;">{{ json_encode($log->json_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '-' }}</pre>
                            </div>
                            <div>
                                <strong style="display: block; margin-bottom: 8px; color: #5a3726;">Query Payload</strong>
                                <pre style="margin: 0; padding: 14px; border-radius: 16px; background: #2a211d; color: #f7efe3; overflow: auto; white-space: pre-wrap;">{{ json_encode($log->query_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '-' }}</pre>
                            </div>
                            <div>
                                <strong style="display: block; margin-bottom: 8px; color: #5a3726;">Normalized Payload</strong>
                                <pre style="margin: 0; padding: 14px; border-radius: 16px; background: #2a211d; color: #f7efe3; overflow: auto; white-space: pre-wrap;">{{ json_encode($log->normalized_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '-' }}</pre>
                            </div>
                        </div>
                    </article>
                @empty
                    <div style="padding: 22px; border-radius: 20px; background: rgba(255,255,255,0.72); border: 1px dashed rgba(106,65,45,0.26); color: #6c5244;">
                        Belum ada request Traccar yang tercatat.
                    </div>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
