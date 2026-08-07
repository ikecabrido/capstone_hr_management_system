<?php

require_once __DIR__ . '/../models/dashboardModel.php';

class DashboardController
{
    private DashboardModel $model;

    public function __construct($db)
    {
        $this->model = new DashboardModel($db);
    }



    public function getStats()
    {
        $stats = [
            'employees' => $this->model->getEmployeeCount(),
            'period' => null,
            'total_payroll' => 0,
            'progress' => [
                'processed' => 0,
                'total' => 0
            ],

            // IMPORTANT
            'chart' => $this->model->getMonthlyTotals(),
            'lifetime' => $this->model->getLifetimePayroll(),
            'average_salary' => $this->model->getAverageSalary(),
            'total_allowances' => 0,
            'total_deductions' => 0,

            // NEW CHARTS
            'deductions_chart' => $this->model->getMonthlyDeductions(),
            'gross_vs_net_chart' => $this->model->getMonthlyGrossVsNet(),
            'processing_status' => $this->model->getPayrollProcessingStatus(),

            // NEW CARDS
            'total_gross_pay' => 0,
            'pending_clearances' => 0,
            'period_run_status' => null
        ];

        /* ===== ACTIVE PERIOD (optional) ===== */

        $period = $this->model->getActivePeriod();

        if ($period) {

            $stats['period'] = $period;

            $run = $this->model->getCurrentRun($period['period_id']);

            if ($run) {

                $runId = $run['run_id'];

                $stats['progress'] =
                    $this->model->getRunProgress($runId);

                $total = $this->model->getLatestFinalizedRun();
                $stats['total_payroll'] = $total['totals'] ?? 0;
                $stats['total_allowances'] = $this->model->getTotalAllowances($period['period_id']);
                $stats['total_deductions'] = $this->model->getTotalDeductions($period['period_id']);
            } else {
                // No run for active period, show latest finalized run stats instead
                $latestRun = $this->model->getLatestFinalizedRunWithDetails();
                if ($latestRun) {
                    $stats['progress'] = [
                        'total' => $latestRun['total_employees'],
                        'processed' => $latestRun['processed']
                    ];
                    $stats['total_payroll'] = $latestRun['total_payroll'];
                    $stats['period'] = [
                        'period_name' => $latestRun['period_name'],
                        'start_date' => $latestRun['start_date'],
                        'end_date' => $latestRun['end_date']
                    ];
                    $stats['total_allowances'] = $this->model->getTotalAllowances($latestRun['period_id']);
                    $stats['total_deductions'] = $this->model->getTotalDeductions($latestRun['period_id']);
                }
            }
        } else {
            // No active period, show latest finalized run stats
            $latestRun = $this->model->getLatestFinalizedRunWithDetails();
            if ($latestRun) {
                $stats['progress'] = [
                    'total' => $latestRun['total_employees'],
                    'processed' => $latestRun['processed']
                ];
                $stats['total_payroll'] = $latestRun['total_payroll'];
                $stats['period'] = [
                    'period_name' => $latestRun['period_name'],
                    'start_date' => $latestRun['start_date'],
                    'end_date' => $latestRun['end_date']
                ];
                $stats['total_allowances'] = $this->model->getTotalAllowances($latestRun['period_id']);
                $stats['total_deductions'] = $this->model->getTotalDeductions($latestRun['period_id']);
            }
        }

        /* ===== ADDITIONAL METRICS ===== */

        // Period/Run status
        $stats['period_run_status'] = $this->model->getPeriodRunStatus();

        // Pending clearances
        $stats['pending_clearances'] = $this->model->getPendingClearancesCount();

        // Total gross pay for active period
        if ($period) {
            $stats['total_gross_pay'] = $this->model->getTotalGrossPay($period['period_id']);
        } else {
            $latestRun = $this->model->getLatestFinalizedRunWithDetails();
            if ($latestRun) {
                $stats['total_gross_pay'] = $this->model->getTotalGrossPay($latestRun['period_id']);
            }
        }

        return $stats;
    }
}
