<x-app-layout>
    <style>
        .traccar-page {
            padding: 32px 0 48px;
            background: linear-gradient(180deg, #f8f2e8 0%, #f3f4f6 100%);
            min-height: 100vh;
        }

        .traccar-wrap {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 16px;
        }

        .traccar-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .traccar-card,
        .traccar-section,
        .traccar-device-card,
        .traccar-device-stat {
            border: 1px solid rgba(106, 65, 45, 0.12);
            box-shadow: 0 18px 40px rgba(59, 36, 24, 0.08);
        }

        .traccar-card {
            padding: 20px;
            border-radius: 22px;
            background: rgba(255, 250, 242, 0.92);
        }

        .traccar-card span {
            display: block;
            margin-bottom: 8px;
            color: #8a634b;
            text-transform: uppercase;
            font-size: 0.76rem;
            letter-spacing: 0.08em;
            line-height: 1.35;
        }

        .traccar-card strong {
            display: block;
            color: #3b2418;
            line-height: 1.2;
            word-break: break-word;
        }

        .traccar-card strong.is-lg {
            font-size: 2rem;
        }

        .traccar-card strong.is-md {
            font-size: 1.15rem;
        }

        .traccar-card strong.is-sm {
            font-size: 1rem;
        }

        .traccar-section {
            margin-top: 24px;
            padding: 26px;
            border-radius: 26px;
            background: rgba(255, 250, 242, 0.92);
        }

        .traccar-section-kicker {
            margin: 0;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #9a6b4d;
        }

        .traccar-section-title {
            margin: 8px 0 18px;
            font-size: 2rem;
            line-height: 1.12;
            color: #3b2418;
        }

        .traccar-device-list {
            display: grid;
            gap: 14px;
        }

        .traccar-device-card {
            padding: 18px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.7);
        }

        .traccar-device-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            align-items: start;
        }

        .traccar-device-meta strong {
            display: block;
            font-size: 1.15rem;
            color: #3b2418;
        }

        .traccar-device-meta span {
            display: block;
            margin-top: 4px;
            color: #8a634b;
            line-height: 1.45;
        }

        .traccar-device-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(47, 107, 85, 0.12);
            color: #2f6b55;
            font-weight: 700;
            flex-shrink: 0;
        }

        .traccar-device-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .traccar-device-stat {
            padding: 14px;
            border-radius: 16px;
            background: rgba(248, 242, 232, 0.8);
        }

        .traccar-device-stat span {
            display: block;
            margin-bottom: 6px;
            color: #8a634b;
            font-size: 0.76rem;
            text-transform: uppercase;
            line-height: 1.35;
        }

        .traccar-device-stat strong {
            display: block;
            font-size: 1.05rem;
            color: #3b2418;
            line-height: 1.35;
            word-break: break-word;
        }

        .traccar-log-card {
            margin-bottom: 16px;
            padding: 18px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(106, 65, 45, 0.1);
        }

        .traccar-log-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            align-items: center;
        }

        .traccar-log-title {
            color: #3b2418;
            font-weight: 700;
            font-size: 1.05rem;
        }

        .traccar-log-title span {
            color: #8a634b;
            font-weight: 500;
            margin-left: 8px;
        }

        .traccar-log-status {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            font-weight: 700;
        }

        .traccar-log-status.is-success {
            background: rgba(47, 107, 85, 0.12);
            color: #2f6b55;
        }

        .traccar-log-status.is-error {
            background: rgba(159, 47, 32, 0.12);
            color: #9f2f20;
        }

        .traccar-log-meta {
            margin-top: 14px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .traccar-log-meta-card {
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(248, 242, 232, 0.8);
        }

        .traccar-log-meta-card span {
            display: block;
            margin-bottom: 6px;
            color: #8a634b;
            font-size: 0.76rem;
            text-transform: uppercase;
            line-height: 1.35;
        }

        .traccar-log-meta-card strong {
            display: block;
            color: #3b2418;
            line-height: 1.45;
            word-break: break-word;
        }

        .traccar-log-error {
            margin-top: 14px;
            padding: 14px;
            border-radius: 16px;
            background: rgba(159, 47, 32, 0.08);
            color: #9f2f20;
        }

        .traccar-log-blocks {
            margin-top: 14px;
            display: grid;
            gap: 12px;
        }

        .traccar-log-block strong {
            display: block;
            margin-bottom: 8px;
            color: #5a3726;
        }

        .traccar-log-code {
            margin: 0;
            padding: 14px;
            border-radius: 16px;
            background: #2a211d;
            color: #f7efe3;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-word;
            font-size: 0.92rem;
            line-height: 1.5;
        }

        @media (max-width: 900px) {
            .traccar-stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .traccar-device-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .traccar-log-meta {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .traccar-page {
                padding: 20px 0 32px;
            }

            .traccar-wrap {
                padding: 0 12px;
            }

            .traccar-stat-grid {
                grid-template-columns: 1fr;
            }

            .traccar-card,
            .traccar-section {
                padding: 18px;
            }

            .traccar-section-title {
                font-size: 1.45rem;
            }

            .traccar-device-head {
                gap: 12px;
            }

            .traccar-device-stats {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .traccar-device-stat {
                padding: 12px;
            }

            .traccar-device-stat strong {
                font-size: 0.98rem;
            }

            .traccar-log-head {
                align-items: start;
            }

            .traccar-log-title span {
                display: block;
                margin: 6px 0 0;
            }

            .traccar-log-meta {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .traccar-log-meta-card {
                padding: 12px;
            }

            .traccar-log-code {
                font-size: 0.84rem;
                line-height: 1.45;
            }
        }
    </style>

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

    <div class="traccar-page">
        <div class="traccar-wrap">
            <section class="traccar-stat-grid">
                <article class="traccar-card">
                    <span>Device Aktif</span>
                    <strong class="is-lg">{{ $deviceSummaries->count() }}</strong>
                </article>
                <article class="traccar-card">
                    <span>Data Tanpa Device ID</span>
                    <strong class="is-lg">{{ $unknownDeviceCount }}</strong>
                </article>
                <article class="traccar-card">
                    <span>Device Terakhir</span>
                    <strong class="is-md">{{ $latestTrackedLocation?->device_id ?: '-' }}</strong>
                </article>
                <article class="traccar-card">
                    <span>Update Terakhir</span>
                    <strong class="is-sm">{{ $latestTrackedLocation?->recorded_at?->translatedFormat('d M Y H:i:s') ?? '-' }}</strong>
                </article>
            </section>

            <section class="traccar-section">
                <p class="traccar-section-kicker">Ringkasan Device</p>
                <h3 class="traccar-section-title">Apakah Traccar berhasil mengirim data?</h3>

                @if ($deviceSummaries->isEmpty())
                    <div style="padding: 22px; border-radius: 20px; background: rgba(255,255,255,0.72); border: 1px dashed rgba(106,65,45,0.26); color: #6c5244;">
                        Belum ada device Traccar dengan <code>device_id</code> yang masuk. Cek lagi pengaturan <strong>Device Identifier</strong> di aplikasi Traccar.
                    </div>
                @else
                    <div class="traccar-device-list">
                        @foreach ($deviceSummaries as $device)
                            <article class="traccar-device-card">
                                <div class="traccar-device-head">
                                    <div class="traccar-device-meta">
                                        <strong>{{ $device['unit_name'] ?: $device['device_id'] }}</strong>
                                        <span>Device: {{ $device['device_id'] }}</span>
                                        <span>Driver aktif: {{ $device['driver_name'] ?: 'Belum di-assign' }}</span>
                                        <span>Terakhir kirim: {{ optional($device['last_seen'])->translatedFormat('d F Y H:i:s') ?? '-' }}</span>
                                    </div>
                                    <span class="traccar-device-badge">
                                        {{ $device['total_logs'] }} log
                                    </span>
                                </div>

                                <div class="traccar-device-stats">
                                    <div class="traccar-device-stat">
                                        <span>Latitude</span>
                                        <strong>{{ $device['latitude'] }}</strong>
                                    </div>
                                    <div class="traccar-device-stat">
                                        <span>Longitude</span>
                                        <strong>{{ $device['longitude'] }}</strong>
                                    </div>
                                    <div class="traccar-device-stat">
                                        <span>Baterai</span>
                                        <strong>{{ $device['battery_level'] !== null ? $device['battery_level'].'%' : '-' }}</strong>
                                    </div>
                                    <div class="traccar-device-stat">
                                        <span>Status</span>
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
                    Data Masuk Terbaru
                </p>
                <h3 style="margin: 8px 0 10px; font-size: 2rem; color: #3b2418;">5 log request terbaru dengan driver berbeda</h3>
                <p style="margin: 0 0 18px; color: #8a634b;">
                    Format tabel tetap sama, tetapi hanya menampilkan 5 request terbaru dari 5 driver yang berbeda.
                </p>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; min-width: 1080px; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; color: #8a634b;">
                                <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.12);">ID</th>
                                <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.12);">ID Driver</th>
                                <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.12);">Nama Driver</th>
                                <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.12);">Device ID</th>
                                <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.12);">Latitude</th>
                                <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.12);">Longitude</th>
                                <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.12);">Akurasi</th>
                                <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.12);">Baterai</th>
                                <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.12);">Event</th>
                                <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.12);">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestDistinctDriverEntries as $row)
                                <tr>
                                    <td style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.08);">{{ $row['entry']->id }}</td>
                                    <td style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.08); font-weight: 700;">{{ $row['driver_id'] }}</td>
                                    <td style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.08);">{{ $row['driver_name'] }}</td>
                                    <td style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.08); font-weight: 700;">{{ $row['device_id'] }}</td>
                                    <td style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.08);">{{ $row['entry']->latitude }}</td>
                                    <td style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.08);">{{ $row['entry']->longitude }}</td>
                                    <td style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.08);">{{ $row['entry']->accuracy ?? '-' }}</td>
                                    <td style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.08);">{{ $row['entry']->battery_level !== null ? $row['entry']->battery_level.'%' : '-' }}</td>
                                    <td style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.08);">{{ $row['entry']->event_type ?? '-' }}</td>
                                    <td style="padding: 14px 12px; border-bottom: 1px solid rgba(106,65,45,0.08);">{{ $row['entry']->recorded_at?->translatedFormat('d M Y H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="padding: 18px 12px; color: #6c5244;">Belum ada data lokasi dari driver yang berbeda.</td>
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
                        <div class="traccar-log-head">
                            <div>
                                <div class="traccar-log-title">
                                    Log #{{ $log->id }}
                                    <span>{{ $log->created_at?->translatedFormat('d M Y H:i:s') }}</span>
                                </div>
                            </div>
                            <span class="traccar-log-status {{ $log->processed ? 'is-success' : 'is-error' }}">
                                {{ $log->processed ? 'Diproses' : 'Gagal diproses' }}
                            </span>
                        </div>

                        <div class="traccar-log-meta">
                            <div class="traccar-log-meta-card">
                                <span>Method</span>
                                <strong>{{ $log->method }}</strong>
                            </div>
                            <div class="traccar-log-meta-card">
                                <span>Path</span>
                                <strong>{{ $log->path }}</strong>
                            </div>
                            <div class="traccar-log-meta-card">
                                <span>Content Type</span>
                                <strong>{{ $log->content_type ?: '-' }}</strong>
                            </div>
                            <div class="traccar-log-meta-card">
                                <span>IP</span>
                                <strong>{{ $log->ip_address ?: '-' }}</strong>
                            </div>
                        </div>

                        @if ($log->error_message)
                            <div class="traccar-log-error">
                                <strong>Error:</strong>
                                <pre style="margin: 8px 0 0; white-space: pre-wrap;">{{ $log->error_message }}</pre>
                            </div>
                        @endif

                        <div class="traccar-log-blocks">
                            <div class="traccar-log-block">
                                <strong>Raw Body</strong>
                                <pre class="traccar-log-code">{{ $log->raw_body ?: '-' }}</pre>
                            </div>
                            <div class="traccar-log-block">
                                <strong>JSON Payload</strong>
                                <pre class="traccar-log-code">{{ json_encode($log->json_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '-' }}</pre>
                            </div>
                            <div class="traccar-log-block">
                                <strong>Query Payload</strong>
                                <pre class="traccar-log-code">{{ json_encode($log->query_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '-' }}</pre>
                            </div>
                            <div class="traccar-log-block">
                                <strong>Normalized Payload</strong>
                                <pre class="traccar-log-code">{{ json_encode($log->normalized_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '-' }}</pre>
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
