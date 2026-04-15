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
