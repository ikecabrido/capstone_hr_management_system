import FullCalendar from '@fullcalendar/react';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

const ComplianceCalendar = () => {
  const events = [
    { id: '1', title: 'SSS Submission', start: '2025-08-15', backgroundColor: '#0A4D9B', borderColor: '#0A4D9B' },
    { id: '2', title: 'PhilHealth Remittance', start: '2025-08-20', backgroundColor: '#2F80ED', borderColor: '#2F80ED' },
    { id: '3', title: 'Pag-IBIG Contribution', start: '2025-08-25', backgroundColor: '#22C55E', borderColor: '#22C55E' },
    { id: '4', title: 'BIR Filing', start: '2025-09-05', backgroundColor: '#F59E0B', borderColor: '#F59E0B' },
    { id: '5', title: 'Internal Audit', start: '2025-09-10', backgroundColor: '#EF4444', borderColor: '#EF4444' },
    { id: '6', title: 'Policy Review', start: '2025-09-15', backgroundColor: '#8B5CF6', borderColor: '#8B5CF6' },
    { id: '7', title: 'Employee Training', start: '2025-09-20', backgroundColor: '#22C55E', borderColor: '#22C55E' },
  ];

  return (
    <div className="table-container">
      <div className="table-header">
        <h5 className="table-title">Compliance Calendar</h5>
      </div>
      <div className="p-3">
        <FullCalendar
          plugins={[dayGridPlugin, timeGridPlugin, interactionPlugin]}
          initialView="dayGridMonth"
          events={events}
          headerToolbar={{
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek',
          }}
          height="400px"
          eventDisplay="block"
          dayMaxEvents={3}
        />
      </div>
    </div>
  );
};

export default ComplianceCalendar;
