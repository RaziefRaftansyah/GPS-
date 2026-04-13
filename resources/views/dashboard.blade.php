<x-app-layout>
    <style>
        @media (max-width: 960px) {
            #admin-dashboard-summary,
            .admin-session-stats {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <x-slot name="header">
        <div>
            <p style="margin: 0; font-size: 0.8rem; letter-spacing: 0.08em; text-transform: uppercase; color: #9a6b4d;">
                Dashboard Admin
            </p>
            <h2 style="margin: 6px 0 0; font-size: 1.8rem; color: #3b2418;">
                Pantau user yang sedang login
            </h2>
        </div>
    </x-slot>

    <div style="padding: 32px 0 48px; background: linear-gradient(180deg, #f8f2e8 0%, #f3f4f6 100%); min-height: 100vh;">
        <div style="max-width: 1120px; margin: 0 auto; padding: 0 16px;">
            @if (session('dashboard_status'))
                <section style="margin-bottom: 18px; padding: 18px 20px; border-radius: 22px; border: 1px solid rgba(47, 107, 85, 0.16); background: rgba(233, 247, 240, 0.9); color: #24503f; box-shadow: 0 14px 30px rgba(36, 80, 63, 0.08);">
                    {{ session('dashboard_status') }}
                </section>
            @endif

            <section id="admin-dashboard-summary" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px;">
                <article style="padding: 24px; border-radius: 26px; border: 1px solid rgba(106, 65, 45, 0.12); background: rgba(255, 250, 242, 0.92); box-shadow: 0 18px 40px rgba(59, 36, 24, 0.08);">
                    <p style="margin: 0; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">User Aktif</p>
                    <strong style="display: block; margin-top: 10px; font-size: 2.4rem; color: #3b2418;">{{ $activeUserCount }}</strong>
                    <span style="display: block; margin-top: 8px; color: #6c5244;">Jumlah akun yang sedang login sekarang.</span>
                </article>

                <article style="padding: 24px; border-radius: 26px; border: 1px solid rgba(106, 65, 45, 0.12); background: rgba(255, 250, 242, 0.92); box-shadow: 0 18px 40px rgba(59, 36, 24, 0.08);">
                    <p style="margin: 0; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">Total Sesi Aktif</p>
                    <strong style="display: block; margin-top: 10px; font-size: 2.4rem; color: #3b2418;">{{ $activeSessionCount }}</strong>
                    <span style="display: block; margin-top: 8px; color: #6c5244;">Semua sesi browser yang masih aktif di server.</span>
                </article>

                <article style="padding: 24px; border-radius: 26px; border: 1px solid rgba(106, 65, 45, 0.12); background: rgba(255, 250, 242, 0.92); box-shadow: 0 18px 40px rgba(59, 36, 24, 0.08);">
                    <p style="margin: 0; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">Update Terakhir</p>
                    <strong style="display: block; margin-top: 10px; font-size: 1.45rem; color: #3b2418;">
                        {{ $latestLoginAt?->translatedFormat('d M Y, H:i') ?? 'Belum ada sesi aktif' }}
                    </strong>
                    <span style="display: block; margin-top: 8px; color: #6c5244;">Admin utama: {{ $adminEmail }}</span>
                </article>
            </section>

            <section style="margin-top: 24px;">
                <article style="padding: 26px; border-radius: 26px; border: 1px solid rgba(106, 65, 45, 0.12); background: rgba(255, 250, 242, 0.92); box-shadow: 0 18px 40px rgba(59, 36, 24, 0.08);">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 18px; flex-wrap: wrap;">
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">
                                User Login
                            </p>
                            <h3 style="margin: 8px 0 0; font-size: 2rem; color: #3b2418;">Daftar user yang sedang aktif</h3>
                        </div>
                        <span style="display: inline-flex; align-items: center; padding: 10px 14px; border-radius: 999px; background: rgba(47, 107, 85, 0.12); color: #2f6b55; font-weight: 700;">
                            {{ $activeUserCount }} user online
                        </span>
                    </div>

                    @if ($activeUsers->isEmpty())
                        <div style="padding: 22px; border-radius: 20px; background: rgba(255,255,255,0.72); border: 1px dashed rgba(106, 65, 45, 0.26); color: #6c5244;">
                            Belum ada user lain yang sedang login. Saat ada user aktif, namanya akan muncul di sini.
                        </div>
                    @else
                        <div style="display: grid; gap: 14px;">
                            @foreach ($activeUsers as $activeUser)
                                <article style="padding: 18px; border-radius: 20px; background: rgba(255,255,255,0.74); border: 1px solid rgba(106, 65, 45, 0.1);">
                                    <div style="display: flex; justify-content: space-between; gap: 16px; align-items: start; flex-wrap: wrap;">
                                        <div style="flex: 1 1 320px;">
                                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                                <strong style="font-size: 1.12rem; color: #3b2418;">{{ $activeUser['name'] }}</strong>
                                                @if ($activeUser['is_current_admin'])
                                                    <span style="display: inline-flex; align-items: center; padding: 6px 10px; border-radius: 999px; background: rgba(47, 107, 85, 0.12); color: #2f6b55; font-size: 0.82rem; font-weight: 700;">
                                                        Sesi kamu
                                                    </span>
                                                @endif
                                            </div>
                                            <p style="margin: 6px 0 0; color: #6c5244;">{{ $activeUser['email'] }}</p>

                                            <div class="admin-session-stats" style="margin-top: 14px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px;">
                                                <div style="padding: 12px; border-radius: 16px; background: rgba(248, 242, 232, 0.92);">
                                                    <span style="display: block; font-size: 0.72rem; letter-spacing: 0.06em; text-transform: uppercase; color: #9a6b4d;">Sesi Aktif</span>
                                                    <strong style="display: block; margin-top: 6px; color: #3b2418;">{{ $activeUser['active_sessions'] }}</strong>
                                                </div>
                                                <div style="padding: 12px; border-radius: 16px; background: rgba(248, 242, 232, 0.92);">
                                                    <span style="display: block; font-size: 0.72rem; letter-spacing: 0.06em; text-transform: uppercase; color: #9a6b4d;">IP Terakhir</span>
                                                    <strong style="display: block; margin-top: 6px; color: #3b2418;">{{ $activeUser['ip_address'] }}</strong>
                                                </div>
                                                <div style="padding: 12px; border-radius: 16px; background: rgba(248, 242, 232, 0.92);">
                                                    <span style="display: block; font-size: 0.72rem; letter-spacing: 0.06em; text-transform: uppercase; color: #9a6b4d;">Terlihat</span>
                                                    <strong style="display: block; margin-top: 6px; color: #3b2418;">{{ $activeUser['last_seen']->diffForHumans() }}</strong>
                                                </div>
                                            </div>

                                            <p style="margin: 14px 0 0; color: #6c5244; line-height: 1.6;">
                                                <strong style="color: #5a3726;">Browser:</strong> {{ $activeUser['user_agent'] }}
                                            </p>
                                        </div>

                                        <div style="display: flex; align-items: center;">
                                            @if ($activeUser['is_current_admin'])
                                                <button
                                                    type="button"
                                                    disabled
                                                    style="padding: 12px 18px; border: 0; border-radius: 999px; background: rgba(106, 65, 45, 0.08); color: #8a634b; font-weight: 800; cursor: not-allowed;"
                                                >
                                                    Admin Aktif
                                                </button>
                                            @else
                                                <form method="POST" action="{{ route('dashboard.users.kick', $activeUser['user_id']) }}" style="margin: 0;">
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        style="padding: 12px 18px; border: 0; border-radius: 999px; background: linear-gradient(135deg, #7b2f2f, #b55454); color: #fff; font-weight: 800; cursor: pointer;"
                                                    >
                                                        Kick User
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </article>
            </section>
        </div>
    </div>
</x-app-layout>
