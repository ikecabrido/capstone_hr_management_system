import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { Doughnut } from 'react-chartjs-2';

ChartJS.register(ArcElement, Tooltip, Legend);

const ComplianceOverviewChart = ({ data }) => {
  const chartData = {
    labels: ['Compliant', 'Non-Compliant', 'Pending Review'],
    datasets: [
      {
        data: [75, 15, 10],
        backgroundColor: ['#22C55E', '#EF4444', '#F59E0B'],
        borderWidth: 0,
        hoverOffset: 4,
      },
    ],
  };

  if (data?.datasets?.[0]?.data) {
    chartData.datasets[0].data = data.datasets[0].data;
    if (data.labels) {
      chartData.labels = data.labels;
    }
  }

  const options = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '70%',
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          padding: 20,
          usePointStyle: true,
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
  };

  const centerValue = chartData.datasets[0].data[0] ?? 0;

  return (
    <div className="chart-wrapper">
      <Doughnut data={chartData} options={options} />
      <div className="chart-center-text">
        <span className="chart-center-value">{centerValue}%</span>
        <span className="chart-center-label">Compliance Rate</span>
      </div>
    </div>
  );
};

export default ComplianceOverviewChart;
