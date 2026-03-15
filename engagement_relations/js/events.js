document.addEventListener('DOMContentLoaded', function () {
  const container = document.getElementById('events-container');
  if (!container) return;

  const apiBase = `${window.location.origin}${window.location.pathname.replace(/\/[^\/]*$/, '')}/api`;

  const userId = document.querySelector('script[data-user-id]')?.getAttribute('data-user-id');
  if (!userId || userId === 'null') {
    container.innerHTML = '<div style="padding:20px; color:#a00;">User not authenticated.</div>';
    return;
  }

  container.innerHTML = '<div style="padding:20px; color:#666;">Loading events...</div>';

  function formatDate(dateStr) {
    try {
      return new Date(dateStr).toLocaleDateString(undefined, {
        year: 'numeric', month: 'short', day: 'numeric',
      });
    } catch (e) {
      return dateStr || 'N/A';
    }
  }

  function fetchEvents() {
    return fetch(`${apiBase}/events.php`, { cache: 'no-store', credentials: 'same-origin' })
      .then((res) => res.text().then((text) => {
        if (!res.ok) throw new Error(`HTTP ${res.status} ${res.statusText}: ${text}`);
        try {
          return JSON.parse(text);
        } catch (err) {
          throw new Error(`Invalid JSON from server: ${err.message} | ${text}`);
        }
      }));
  }

  function fetchRegistrations() {
    return fetch(`${apiBase}/event-registrations.php?employee_id=${encodeURIComponent(userId)}`, { cache: 'no-store', credentials: 'same-origin' })
      .then((res) => res.text().then((text) => {
        if (!res.ok) throw new Error(`HTTP ${res.status} ${res.statusText}: ${text}`);
        try {
          return JSON.parse(text);
        } catch (err) {
          throw new Error(`Invalid JSON from server: ${err.message} | ${text}`);
        }
      }));
  }

  function render(events, registrations) {
    if (!Array.isArray(events)) {
      container.innerHTML = '<div style="padding:20px; color:#a00;">Invalid events data</div>';
      return;
    }

    if (events.length === 0) {
      container.innerHTML = '<div style="padding:20px; color:#666;">No events scheduled.</div>';
      return;
    }

    const registeredEventIds = new Set((registrations?.registrations || []).map((r) => Number(r.event_id)));

    const cards = events.map((event) => {
      const eventId = Number(event.id);
      const isRegistered = registeredEventIds.has(eventId);
      const desc = event.description ? event.description.replace(/\n/g, '<br>') : '';
      const dateStr = event.event_date ? formatDate(event.event_date) : 'TBD';
      const createdBy = event.created_by || 'Admin';
      const location = event.location || 'TBD';

      return `
        <div class="event-card ${isRegistered ? 'registered' : 'not-registered'}" data-event-id="${eventId}">
          <div class="registration-badge ${isRegistered ? 'registered' : 'not-registered'}">${isRegistered ? '✓ Registered' : 'Not Registered'}</div>
          <div class="event-date-box">${dateStr}</div>
          <div class="event-title">${event.title || 'Untitled Event'}</div>
          <div class="event-description">${desc}</div>
          <div class="event-details">
            <div class="detail-item"><div class="detail-label">Created By</div><div class="detail-value">${createdBy}</div></div>
            <div class="detail-item"><div class="detail-label">Location</div><div class="detail-value">${location}</div></div>
          </div>
          <button class="registration-btn ${isRegistered ? 'registered' : 'register'}" ${isRegistered ? 'disabled' : ''} data-event-id="${eventId}">
            ${isRegistered ? 'Registered' : 'Register for Event'}
          </button>
        </div>
      `;
    }).join('');

    container.innerHTML = cards;

    container.querySelectorAll('.registration-btn.register').forEach((btn) => {
      btn.addEventListener('click', () => {
        const eventId = btn.getAttribute('data-event-id');
        if (!eventId) return;
        btn.disabled = true;
        btn.textContent = 'Registering...';

        fetch('api/event-registrations.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ event_id: eventId, employee_id: userId }),
        })
          .then((res) => res.json())
          .then((data) => {
            if (data.success) {
              fetchAndRender();
            } else {
              throw new Error(data.error || 'Registration failed');
            }
          })
          .catch((err) => {
            console.error(err);
            btn.disabled = false;
            btn.textContent = 'Register for Event';
            container.insertAdjacentHTML('afterbegin', `<div style="color:#a00;padding:10px;">Error: ${err.message}</div>`);
          });
      });
    });
  }

  function fetchAndRender() {
    Promise.all([fetchEvents(), fetchRegistrations()])
      .then(([eventsData, registrationsData]) => {
        const events = eventsData.events || [];
        render(events, registrationsData);
      })
      .catch((err) => {
        container.innerHTML = `<div style="padding:20px; color:#a00;">Error loading data: ${err.message}</div>`;
        console.error(err);
      });
  }

  fetchAndRender();
});
