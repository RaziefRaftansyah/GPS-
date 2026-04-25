@props([
    'redirectTo',
    'driverPage' => null,
    'unitPage' => null,
])

<input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

@if ($driverPage !== null)
    <input type="hidden" name="driver_page" value="{{ $driverPage }}">
@endif

@if ($unitPage !== null)
    <input type="hidden" name="unit_page" value="{{ $unitPage }}">
@endif
