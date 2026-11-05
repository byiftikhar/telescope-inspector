<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Telescope Inspector</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" integrity="sha384-L/vw44MdBs/7hovWr2rBVSRKqdti4Vw077yuSGQ2PO3tQARRLiLuMh8xhVmVrR0I" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet" integrity="sha384-RkASv+6KfBMW9eknReJIJ6b3UnjKOKC5bOUaNgIY778NFbQ8MtWq9Lr/khUgqtTt" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css" rel="stylesheet" integrity="sha384-xF1Y2i6HgC34+4EWddbDhlQuru7cLSKRcPT3hoL3mPoKoV+624vVSZJmegPX77vS" crossorigin="anonymous">
    @include('telescope-inspector::partials.styles')
</head>

<body>
    <script id="ti-settings" type="application/json">@json($settings)</script>

    @include('telescope-inspector::partials.header')

    @include('telescope-inspector::partials.list-template')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js" integrity="sha384-0O3sg2SQIGn4393xwamQISjphC8DIXjCzlhj1gPAMC5xGg+2perF5Mehr5njv0fZ" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js" integrity="sha384-5JqMv4L/Xa0hfvtF06qboNdhvuYXUku9ZrhZh3bSk8VXF0A/RuSLHpLsSV9Zqhl6" crossorigin="anonymous"></script>
    <script>
        (function () {
            'use strict';

            @include('telescope-inspector::partials.scripts.config')
            @include('telescope-inspector::partials.scripts.helpers')
            @include('telescope-inspector::partials.scripts.entry-types')
            @include('telescope-inspector::partials.scripts.list-state')
            @include('telescope-inspector::partials.scripts.list-view')
            @include('telescope-inspector::partials.scripts.detail-view')
            @include('telescope-inspector::partials.scripts.related')
            @include('telescope-inspector::partials.scripts.router')
        })();
    </script>
</body>

</html>
