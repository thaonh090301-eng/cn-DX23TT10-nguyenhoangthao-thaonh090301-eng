<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Schedule Calendar') ?></title>
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'calendar'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow">Calendar</p>
                <h1>Schedule Calendar</h1>
            </div>
            <a class="button primary" href="/schedules/create">New Schedule</a>
        </section>

        <section class="panel calendar-panel">
            <div id="calendar"></div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const calendarElement = document.getElementById('calendar');

            if (!calendarElement || typeof FullCalendar === 'undefined') {
                return;
            }

            const calendar = new FullCalendar.Calendar(calendarElement, {
                initialView: 'timeGridWeek',
                height: 'auto',
                nowIndicator: true,
                eventDisplay: 'block',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                events: '/api/schedules',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                eventClick: (info) => {
                    window.location.href = `/schedules/${encodeURIComponent(info.event.id)}/edit`;
                }
            });

            calendar.render();
        });
    </script>
    <script src="../assets/js/app.js"></script>
</body>
</html>
