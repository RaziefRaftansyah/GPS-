<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/traccar-dashboard.css') }}">
@endpush

    <x-slot name="header">
        <div class="traccar-header">
            <div>
                <p class="traccar-header-kicker">
                    Monitoring Traccar
                </p>
                <h2 class="traccar-header-title">
                    Status data lokasi yang masuk dari Traccar
                </h2>
            </div>
            <div class="traccar-header-actions">
                <a href="{{ route('dashboard') }}" class="traccar-btn traccar-btn-secondary">
                    Profil User
                </a>
                <a href="{{ route('tracker.index') }}" class="traccar-btn traccar-btn-primary">
                    Buka Peta Publik
                </a>
            </div>
        </div>
    </x-slot>

    <div class="traccar-page">
        <div class="traccar-container">
            <section class="traccar-metric-grid">
                <article class="traccar-metric-card">
                    <span class="traccar-metric-label">Device Aktif</span>
                    <strong class="traccar-metric-value">{{ $deviceSummaries->count() }}</strong>
                </article>
                <article class="traccar-metric-card">
                    <span class="traccar-metric-label">Data Tanpa Device ID</span>
                    <strong class="traccar-metric-value">{{ $unknownDeviceCount }}</strong>
                </article>
                <article class="traccar-metric-card">
                    <span class="traccar-metric-label">Device Terakhir</span>
                    <strong class="traccar-metric-device">{{ $latestTrackedLocation?->device_id ?: '-' }}</strong>
                </article>
                <article class="traccar-metric-card">
                    <span class="traccar-metric-label">Update Terakhir</span>
                    <strong class="traccar-metric-time">{{ $latestTrackedLocation?->recorded_at?->translatedFormat('d M Y H:i:s') ?? '-' }}</strong>
                </article>
            </section>

            <section class="traccar-section">
                <p class="traccar-section-kicker">Ringkasan Device</p>
                <h3 class="traccar-section-title">Apakah Traccar berhasil mengirim data?</h3>

                @if ($deviceSummaries->isEmpty())
                    <div class="traccar-empty-box">
                        Belum ada device Traccar dengan <code>device_id</code> yang masuk. Cek lagi pengaturan <strong>Device Identifier</strong> di aplikasi Traccar.
                    </div>
                @else
                    <div class="traccar-list">
                        @foreach ($deviceSummaries as $device)
                            <article class="traccar-device-card">
                                <div class="traccar-device-top">
                                    <div>
                                        <strong class="traccar-device-title">{{ $device['unit_name'] ?: $device['device_id'] }}</strong>
                                        <span class="traccar-device-meta">Device: {{ $device['device_id'] }}</span>
                                        <span class="traccar-device-meta">Driver aktif: {{ $device['driver_name'] ?: 'Belum di-assign' }}</span>
                                        <span class="traccar-device-meta">Terakhir kirim: {{ optional($device['last_seen'])->translatedFormat('d F Y H:i:s') ?? '-' }}</span>
                                    </div>
                                    <span class="traccar-device-pill">
                                        {{ $device['total_logs'] }} log
                                    </span>
                                </div>

                                <div class="traccar-stat-grid">
                                    <div class="traccar-stat-item">
                                        <span class="traccar-stat-label">Latitude</span>
                                        <strong>{{ $device['latitude'] }}</strong>
                                    </div>
                                    <div class="traccar-stat-item">
                                        <span class="traccar-stat-label">Longitude</span>
                                        <strong>{{ $device['longitude'] }}</strong>
                                    </div>
                                    <div class="traccar-stat-item">
                                        <span class="traccar-stat-label">Baterai</span>
                                        <strong>{{ $device['battery_level'] !== null ? $device['battery_level'].'%' : '-' }}</strong>
                                    </div>
                                    <div class="traccar-stat-item">
                                        <span class="traccar-stat-label">Status</span>
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

            <section class="traccar-section">
                <p class="traccar-section-kicker">Data Masuk Terbaru</p>
                <h3 class="traccar-section-title traccar-section-title-tight">5 log request terbaru dengan driver berbeda</h3>
                <p class="traccar-section-description">
                    Format tabel tetap sama, tetapi hanya menampilkan 5 request terbaru dari 5 driver yang berbeda.
                </p>

                <div class="traccar-table-wrap">
                    <table class="traccar-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ID Driver</th>
                                <th>Nama Driver</th>
                                <th>Device ID</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Akurasi</th>
                                <th>Baterai</th>
                                <th>Event</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestDistinctDriverEntries as $row)
                                <tr>
                                    <td>{{ $row['entry']->id }}</td>
                                    <td class="traccar-cell-strong">{{ $row['driver_id'] }}</td>
                                    <td>{{ $row['driver_name'] }}</td>
                                    <td class="traccar-cell-strong">{{ $row['device_id'] }}</td>
                                    <td>{{ $row['entry']->latitude }}</td>
                                    <td>{{ $row['entry']->longitude }}</td>
                                    <td>{{ $row['entry']->accuracy ?? '-' }}</td>
                                    <td>{{ $row['entry']->battery_level !== null ? $row['entry']->battery_level.'%' : '-' }}</td>
                                    <td>{{ $row['entry']->event_type ?? '-' }}</td>
                                    <td>{{ $row['entry']->recorded_at?->translatedFormat('d M Y H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="traccar-empty-row">Belum ada data lokasi dari driver yang berbeda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="traccar-section">
                <p class="traccar-section-kicker">Request Mentah Traccar</p>
                <h3 class="traccar-section-title">Payload yang benar-benar dikirim aplikasi</h3>

                @forelse ($requestLogs as $log)
                    <article class="traccar-log-card">
                        <div class="traccar-log-header">
                            <div>
                                <strong class="traccar-log-title">Log #{{ $log->id }}</strong>
                                <span class="traccar-log-time">{{ $log->created_at?->translatedFormat('d M Y H:i:s') }}</span>
                            </div>
                            <span class="traccar-log-status {{ $log->processed ? 'is-processed' : 'is-failed' }}">
                                {{ $log->processed ? 'Diproses' : 'Gagal diproses' }}
                            </span>
                        </div>

                        <div class="traccar-stat-grid traccar-stat-grid-tight">
                            <div class="traccar-stat-item traccar-stat-item-tight">
                                <span class="traccar-stat-label">Method</span>
                                <strong>{{ $log->method }}</strong>
                            </div>
                            <div class="traccar-stat-item traccar-stat-item-tight">
                                <span class="traccar-stat-label">Path</span>
                                <strong>{{ $log->path }}</strong>
                            </div>
                            <div class="traccar-stat-item traccar-stat-item-tight">
                                <span class="traccar-stat-label">Content Type</span>
                                <strong>{{ $log->content_type ?: '-' }}</strong>
                            </div>
                            <div class="traccar-stat-item traccar-stat-item-tight">
                                <span class="traccar-stat-label">IP</span>
                                <strong>{{ $log->ip_address ?: '-' }}</strong>
                            </div>
                        </div>

                        @if ($log->error_message)
                            <div class="traccar-error-box">
                                <strong>Error:</strong>
                                <pre class="traccar-error-pre">{{ $log->error_message }}</pre>
                            </div>
                        @endif

                        <div class="traccar-payload-grid">
                            <div>
                                <strong class="traccar-payload-title">Raw Body</strong>
                                <pre class="traccar-payload-pre">{{ $log->raw_body ?: '-' }}</pre>
                            </div>
                            <div>
                                <strong class="traccar-payload-title">JSON Payload</strong>
                                <pre class="traccar-payload-pre">{{ json_encode($log->json_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '-' }}</pre>
                            </div>
                            <div>
                                <strong class="traccar-payload-title">Query Payload</strong>
                                <pre class="traccar-payload-pre">{{ json_encode($log->query_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '-' }}</pre>
                            </div>
                            <div>
                                <strong class="traccar-payload-title">Normalized Payload</strong>
                                <pre class="traccar-payload-pre">{{ json_encode($log->normalized_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '-' }}</pre>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="traccar-empty-box">
                        Belum ada request Traccar yang tercatat.
                    </div>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
