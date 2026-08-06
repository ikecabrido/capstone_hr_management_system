import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js';
import { Bar } from 'react-chartjs-2';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

const MonthlyComplianceTrendChart = ({ data }) => {
  const defaultData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [
      {
        label: 'Compliant',
        data: [65, 72, 78, 82, 85, 87],
        backgroundColor: '#22C55E',
        borderRadius: 4,
      },
      {
        label: 'Non-Compliant',
        data: [35, 28, 22, 18, 15, 13],
        backgroundColor: '#EF4444',
        borderRadius: 4,
      },
    ],
  };

  const chartData = data && data.datasets ? data : defaultData;

  const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'top',
        align: 'end',
        labels: {
          usePointStyle: true,
          padding: 20,
          font: {
            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
            size: 12,
          },
        },
      },
      tooltip: {
        backgroundColor: 'rgba(10, 77, 155, 0.9)',
        padding: 12,
        cornerRadius: 8,
      },
    },
    scales: {
      x: {
        grid: {
          display: false,
        },
        ticks: {
          font: {
            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
            size: 12,
          },
        },
      },
      y: {
        beginAtZero: true,
        max: 100,
        grid: {
          color: '#f0f0f0',
        },
        ticks: {
          font: {
            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
            size: 12,
          },
        },
      },
    },
  };

  return (
    <div className="chart-wrapper">
      <Bar data={chartData} options={options} />
    </div>
  );
};

export default MonthlyComplianceTrendChart;
