document.addEventListener('DOMContentLoaded', () => {
    var calendarEl = document.getElementById('calendar-holder');
    //var session= document.querySelector('[data-entry-id]').dataset.entryId

    var calendar = new FullCalendar.Calendar(calendarEl, {
        themeSystem: 'bootstrap',
        locale: 'fr',
        height: 400,
        contentHeight: 400,
        aspectRatio: 2.5,

        defaultView: 'dayGridMonth',
        editable: true,
        eventSources: [
            {
                url: "/fc-load-events",
                method: "POST",
                extraParams: {
                    filters: JSON.stringify({})
                },
                failure: () => {
                    alert("Un probléme est servenu lors du fetching du FullCalendar!");
                },
            },
        ],
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        plugins: ['interaction', 'dayGrid', 'timeGrid'], // https://fullcalendar.io/docs/plugin-index
        timeZone: 'UTC',
    });
    console.log(calendar)
    calendar.render();
});