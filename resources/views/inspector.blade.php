<!doctype html>
<html lang="en" data-theme="light" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Telescope Inspector</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0' y1='1' x2='1' y2='0'%3E%3Cstop offset='0' stop-color='%234f46e5'/%3E%3Cstop offset='1' stop-color='%2338bdf8'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='32' height='32' rx='8' fill='url(%23g)'/%3E%3Crect x='1.5' y='1.5' width='29' height='29' rx='6.5' fill='%230d1117'/%3E%3Cg fill='none' stroke='%2338bdf8' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='16' cy='16' r='8.5'/%3E%3Cpath d='M7.5 16h17M16 7.5a13 13 0 0 1 3.4 8.5 13 13 0 0 1-3.4 8.5 13 13 0 0 1-3.4-8.5 13 13 0 0 1 3.4-8.5z'/%3E%3C/g%3E%3C/svg%3E">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" integrity="sha384-L/vw44MdBs/7hovWr2rBVSRKqdti4Vw077yuSGQ2PO3tQARRLiLuMh8xhVmVrR0I" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet" integrity="sha384-RkASv+6KfBMW9eknReJIJ6b3UnjKOKC5bOUaNgIY778NFbQ8MtWq9Lr/khUgqtTt" crossorigin="anonymous">
    <link id="ti-flatpickr-dark" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/themes/dark.css" rel="stylesheet" integrity="sha384-heUUtXw0Djj2DElfLOVPlASSWFKNL3JDY6s3FEqQD03GYyWOeophY5DsfEG40sYX" crossorigin="anonymous" disabled>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css" rel="stylesheet" integrity="sha384-xF1Y2i6HgC34+4EWddbDhlQuru7cLSKRcPT3hoL3mPoKoV+624vVSZJmegPX77vS" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.5/dist/sweetalert2.min.css" rel="stylesheet" integrity="sha384-9/j3fuy8X270BtxhWuoluMgFdqcqWjMHrB2G9G0wJQcd3TYbHX578LaOA5h4Hjlt" crossorigin="anonymous">
    @include('telescope-inspector::partials.theme-boot')
    @include('telescope-inspector::partials.styles')
</head>

<body>
    <script id="ti-settings" type="application/json">@json($settings)</script>

    @include('telescope-inspector::partials.header')

    @include('telescope-inspector::partials.list-template')

    @if ($settings['prune'])
        @include('telescope-inspector::partials.prune-modal')
    @endif

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js" integrity="sha384-0O3sg2SQIGn4393xwamQISjphC8DIXjCzlhj1gPAMC5xGg+2perF5Mehr5njv0fZ" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js" integrity="sha384-5JqMv4L/Xa0hfvtF06qboNdhvuYXUku9ZrhZh3bSk8VXF0A/RuSLHpLsSV9Zqhl6" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.5/dist/sweetalert2.min.js" integrity="sha384-U8bBGVfr1HPUUw4Lr8Z0apbN9y5jzZPcH5WA/3FIlzmsN0tXsY3T63RhM/09fa6b" crossorigin="anonymous"></script>
    <script>
        (function () {
            'use strict';

            @include('telescope-inspector::partials.scripts.theme')
            @include('telescope-inspector::partials.scripts.config')
            @include('telescope-inspector::partials.scripts.helpers')
            @include('telescope-inspector::partials.scripts.entry-types')
            @include('telescope-inspector::partials.scripts.list-state')
            @include('telescope-inspector::partials.scripts.list-view')
            @include('telescope-inspector::partials.scripts.detail-view')
            @include('telescope-inspector::partials.scripts.related')
            @include('telescope-inspector::partials.scripts.prune')
            @include('telescope-inspector::partials.scripts.router')
        })();
    </script>
</body>

</html>
