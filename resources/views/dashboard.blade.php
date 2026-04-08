<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px;">
            <div>
                <p style="margin: 0; font-size: 0.8rem; letter-spacing: 0.08em; text-transform: uppercase; color: #9a6b4d;">
                    Dashboard Pelanggan
                </p>
                <h2 style="margin: 6px 0 0; font-size: 1.8rem; color: #3b2418;">
                    Profil dan histori pembelian kopi
                </h2>
            </div>
            <a
                href="{{ route('tracker.index') }}"
                style="display: inline-flex; align-items: center; justify-content: center; padding: 12px 18px; border-radius: 999px; background: linear-gradient(135deg, #6a412d, #b56a3b); color: #fff; text-decoration: none; font-weight: 700;"
            >
                Kembali ke Halaman Utama
            </a>
        </div>
    </x-slot>

    <div style="padding: 32px 0 48px; background: linear-gradient(180deg, #f8f2e8 0%, #f3f4f6 100%); min-height: 100vh;">
        <div style="max-width: 1120px; margin: 0 auto; padding: 0 16px;">
            <section style="display: grid; grid-template-columns: 0.92fr 1.08fr; gap: 24px; align-items: start;">
                <article style="padding: 26px; border-radius: 26px; border: 1px solid rgba(106, 65, 45, 0.12); background: rgba(255, 250, 242, 0.92); box-shadow: 0 18px 40px rgba(59, 36, 24, 0.08);">
                    <p style="margin: 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">
                        Profil User
                    </p>
                    <h3 style="margin: 12px 0 8px; font-size: 2rem; color: #3b2418;">{{ $user->name }}</h3>
                    <p style="margin: 0; color: #6c5244;">{{ $user->email }}</p>

                    <div style="margin-top: 22px; display: grid; gap: 12px;">
                        <div style="padding: 16px; border-radius: 18px; background: rgba(255,255,255,0.72); border: 1px solid rgba(106, 65, 45, 0.1);">
                            <strong style="display: block; margin-bottom: 6px; color: #5a3726;">Status Akun</strong>
                            <span style="color: #6c5244;">
                                {{ $user->email_verified_at ? 'Email terverifikasi' : 'Menunggu verifikasi email' }}
                            </span>
                        </div>
                        <div style="padding: 16px; border-radius: 18px; background: rgba(255,255,255,0.72); border: 1px solid rgba(106, 65, 45, 0.1);">
                            <strong style="display: block; margin-bottom: 6px; color: #5a3726;">Member Sejak</strong>
                            <span style="color: #6c5244;">{{ $user->created_at?->translatedFormat('d F Y') }}</span>
                        </div>
                        <div style="padding: 16px; border-radius: 18px; background: rgba(255,255,255,0.72); border: 1px solid rgba(106, 65, 45, 0.1);">
                            <strong style="display: block; margin-bottom: 6px; color: #5a3726;">Menu Favorit</strong>
                            <span style="color: #6c5244;">{{ $favoriteMenu ?? 'Belum ada pembelian' }}</span>
                        </div>
                    </div>
                </article>

                <article style="padding: 26px; border-radius: 26px; border: 1px solid rgba(106, 65, 45, 0.12); background: rgba(255, 250, 242, 0.92); box-shadow: 0 18px 40px rgba(59, 36, 24, 0.08);">
                    <p style="margin: 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">
                        Ringkasan Belanja
                    </p>
                    <div style="margin-top: 18px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px;">
                        <div style="padding: 18px; border-radius: 18px; background: linear-gradient(135deg, rgba(181, 106, 59, 0.12), rgba(255,255,255,0.72)); border: 1px solid rgba(106, 65, 45, 0.1);">
                            <span style="display: block; margin-bottom: 8px; font-size: 0.76rem; color: #8a634b; text-transform: uppercase; letter-spacing: 0.08em;">Total Pembelian</span>
                            <strong style="font-size: 1.6rem; color: #3b2418;">{{ $purchaseCount }}</strong>
                        </div>
                        <div style="padding: 18px; border-radius: 18px; background: linear-gradient(135deg, rgba(47, 107, 85, 0.1), rgba(255,255,255,0.72)); border: 1px solid rgba(106, 65, 45, 0.1);">
                            <span style="display: block; margin-bottom: 8px; font-size: 0.76rem; color: #8a634b; text-transform: uppercase; letter-spacing: 0.08em;">Total Belanja</span>
                            <strong style="font-size: 1.6rem; color: #3b2418;">Rp{{ number_format((float) $totalSpent, 0, ',', '.') }}</strong>
                        </div>
                        <div style="padding: 18px; border-radius: 18px; background: linear-gradient(135deg, rgba(106, 65, 45, 0.1), rgba(255,255,255,0.72)); border: 1px solid rgba(106, 65, 45, 0.1);">
                            <span style="display: block; margin-bottom: 8px; font-size: 0.76rem; color: #8a634b; text-transform: uppercase; letter-spacing: 0.08em;">Pembelian Terakhir</span>
                            <strong style="font-size: 1rem; color: #3b2418;">
                                {{ $purchases->first()?->purchased_at?->translatedFormat('d M Y') ?? 'Belum ada' }}
                            </strong>
                        </div>
                    </div>
                </article>
            </section>

            <section style="margin-top: 24px; padding: 26px; border-radius: 26px; border: 1px solid rgba(106, 65, 45, 0.12); background: rgba(255, 250, 242, 0.92); box-shadow: 0 18px 40px rgba(59, 36, 24, 0.08);">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 18px;">
                    <div>
                        <p style="margin: 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9a6b4d;">
                            Histori Pembelian
                        </p>
                        <h3 style="margin: 8px 0 0; font-size: 2rem; color: #3b2418;">Daftar pesanan kopi kamu</h3>
                    </div>
                </div>

                @if ($purchases->isEmpty())
                    <div style="padding: 22px; border-radius: 20px; background: rgba(255,255,255,0.72); border: 1px dashed rgba(106, 65, 45, 0.26); color: #6c5244;">
                        Belum ada histori pembelian. Nanti setiap transaksi kopi akan muncul di sini.
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 720px;">
                            <thead>
                                <tr style="text-align: left; color: #8a634b;">
                                    <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106, 65, 45, 0.12);">Menu</th>
                                    <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106, 65, 45, 0.12);">Jumlah</th>
                                    <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106, 65, 45, 0.12);">Total</th>
                                    <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106, 65, 45, 0.12);">Status</th>
                                    <th style="padding: 14px 12px; border-bottom: 1px solid rgba(106, 65, 45, 0.12);">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $purchase)
                                    <tr>
                                        <td style="padding: 16px 12px; border-bottom: 1px solid rgba(106, 65, 45, 0.08); font-weight: 700; color: #3b2418;">
                                            {{ $purchase->menu_name }}
                                        </td>
                                        <td style="padding: 16px 12px; border-bottom: 1px solid rgba(106, 65, 45, 0.08); color: #6c5244;">
                                            {{ $purchase->quantity }}
                                        </td>
                                        <td style="padding: 16px 12px; border-bottom: 1px solid rgba(106, 65, 45, 0.08); color: #6c5244;">
                                            Rp{{ number_format((float) $purchase->total_price, 0, ',', '.') }}
                                        </td>
                                        <td style="padding: 16px 12px; border-bottom: 1px solid rgba(106, 65, 45, 0.08);">
                                            <span style="display: inline-flex; padding: 8px 12px; border-radius: 999px; background: rgba(47, 107, 85, 0.12); color: #2f6b55; font-weight: 700;">
                                                {{ $purchase->status }}
                                            </span>
                                        </td>
                                        <td style="padding: 16px 12px; border-bottom: 1px solid rgba(106, 65, 45, 0.08); color: #6c5244;">
                                            {{ $purchase->purchased_at?->translatedFormat('d F Y, H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
