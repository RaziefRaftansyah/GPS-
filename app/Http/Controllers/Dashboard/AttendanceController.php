<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttendanceController extends BaseDashboardController
{
    public function clockIn(Request $request): RedirectResponse
    {
        $driver = $request->user();

        if (! $driver?->isDriver()) {
            abort(403);
        }

        $assignment = $driver->activeDriverAssignment()->first();

        if (! $assignment) {
            return $this->redirectWithDashboardStatus($request, 'Kamu belum punya assignment aktif, jadi belum bisa absen masuk.');
        }

        if ($assignment->checked_in_at !== null && $assignment->checked_out_at === null) {
            return $this->redirectWithDashboardStatus($request, 'Kamu sudah absen masuk. Lokasi GPS sekarang tampil di peta publik.');
        }

        $assignment->update([
            'checked_in_at' => now(),
            'checked_out_at' => null,
        ]);
        $this->logDriverClockIn($driver, $assignment);

        return $this->redirectWithDashboardStatus($request, 'Absen masuk berhasil. Lokasi GPS kamu sekarang muncul di peta publik.');
    }

    public function clockOut(Request $request): RedirectResponse
    {
        $driver = $request->user();

        if (! $driver?->isDriver()) {
            abort(403);
        }

        $assignment = $driver->activeDriverAssignment()->first();

        if (! $assignment) {
            return $this->redirectWithDashboardStatus($request, 'Kamu belum punya assignment aktif.');
        }

        if ($assignment->checked_in_at === null) {
            return $this->redirectWithDashboardStatus($request, 'Kamu belum absen masuk, jadi tidak bisa absen keluar.');
        }

        if ($assignment->checked_out_at !== null) {
            return $this->redirectWithDashboardStatus($request, 'Kamu sudah absen keluar.');
        }

        $assignment->update([
            'checked_out_at' => now(),
        ]);
        $this->logDriverClockOut($driver, $assignment);

        return $this->redirectWithDashboardStatus($request, 'Absen keluar berhasil. Lokasi GPS kamu disembunyikan dari peta publik.');
    }

    public function viaQr(Request $request): RedirectResponse|JsonResponse
    {
        $driver = $request->user();

        if (! $driver?->isDriver()) {
            abort(403);
        }

        $assignment = $driver->activeDriverAssignment()->with('unit')->first();

        if (! $assignment) {
            return $this->attendanceResponse(
                $request,
                false,
                null,
                'Kamu belum punya assignment aktif, jadi QR absensi belum bisa dipakai.',
                'no_assignment',
                'Belum ada assignment aktif.'
            );
        }

        if ($assignment->checked_in_at === null || $assignment->checked_out_at !== null) {
            $assignment->update([
                'checked_in_at' => now(),
                'checked_out_at' => null,
            ]);
            $this->logDriverClockIn($driver, $assignment);

            return $this->attendanceResponse(
                $request,
                true,
                'clock_in',
                'QR berhasil dipindai. Absen masuk tercatat dan lokasi GPS kamu sekarang tampil di peta publik.',
                'clocked_in',
                'Sudah absen masuk.'
            );
        }

        $assignment->update([
            'checked_out_at' => now(),
        ]);
        $this->logDriverClockOut($driver, $assignment);

        return $this->attendanceResponse(
            $request,
            true,
            'clock_out',
            'QR berhasil dipindai. Absen keluar tercatat dan lokasi GPS kamu disembunyikan dari peta publik.',
            'clocked_out',
            'Sudah absen keluar.'
        );
    }
}
