<?php

require_once __DIR__ . '/../models/dashboardModel.php';

class DashboardController
{
    private DashboardModel $model;

    public function __construct($db)
    {
        $this->model = new DashboardModel($db);
    }


    // public function getStats()
    // {
    // $stats = [];

    /* Employees */
    // $stats['employees'] = $this->model->getEmployeeCount();

    /* Period */
    // $period = $this->model->getLatestPeriod();
    // $stats['period'] = $period;

    /* Defaults */
    // $stats['total_payroll'] = 0;
    // $stats['pending_runs'] = 0;

    // $stats['progress'] = [
    //     'processed' => 0,
    //     'pending'   => 0,
    //     'total'     => 0
    // ];

    // if ($period) {

    //     $runId = $period['id'];

    //     $paid    = $this->model->getPaidCount($runId);
    //     $pending = $this->model->getPendingCount($runId);

    //     $stats['total_payroll'] =
    //         $this->model->getTotalPayroll($runId);

    //     $stats['pending_runs'] = $pending;

    //     $stats['progress'] = [
    //         'processed' => $paid,
    //         'pending'   => $pending,
    //         'total'     => $paid + $pending
    //     ];
    // }

    /* Chart */
    // $stats['chart'] = $this->model->getMonthlyTotals();

    /* Lifetime */
    // $stats['lifetime'] = $this->model->getLifetimePayroll();

    // return $stats;
    public function getStats()
    {
        $stats = [
            'employees' => $this->model->getEmployeeCount(),
            'period' => null,
            'total_payroll' => 0,
            'pending_runs' => 0,
            'progress' => [
                'processed' => 0,
                'pending' => 0,
                'total' => 0
            ],

            // IMPORTANT
            'chart' => $this->model->getMonthlyTotals(),
            'lifetime' => $this->model->getLifetimePayroll(),
            'average_salary' => $this->model->getAverageSalary(),
            'total_allowances' => 0,
            'total_deductions' => 0
        ];

        /* ===== ACTIVE PERIOD (optional) ===== */

        $period = $this->model->getActivePeriod();

        if ($period) {

            $stats['period'] = $period;

            $run = $this->model->getCurrentRun($period['id']);

            if ($run) {

                $runId = $run['id'];

                $stats['progress'] =
                    $this->model->getRunProgress($runId);

                $stats['pending_runs'] =
                    $stats['progress']['pending'];

                $total = $this->model->getLatestFinalizedRun();


                $stats['total_payroll'] = $total['totals'] ?? 0;
            } else {
                // No run for active period, show latest finalized run stats instead
                $latestRun = $this->model->getLatestFinalizedRunWithDetails();
                if ($latestRun) {
                    $stats['progress'] = [
                        'total' => $latestRun['total_employees'],
                        'processed' => $latestRun['processed'],
                        'pending' => $latestRun['pending']
                    ];
                    $stats['pending_runs'] = $latestRun['pending'];
                    $stats['total_payroll'] = $latestRun['total_payroll'];
                    $stats['period'] = [
                        'period_name' => $latestRun['period_name'],
                        'start_date' => $latestRun['start_date'],
                        'end_date' => $latestRun['end_date']
                    ];
                }
            }

            // Add allowances and deductions for the period
            $stats['total_allowances'] = $this->model->getTotalAllowances($period['id']);
            $stats['total_deductions'] = $this->model->getTotalDeductions($period['id']);
        } else {
            // No active period, show latest finalized run stats
            $latestRun = $this->model->getLatestFinalizedRunWithDetails();
            if ($latestRun) {
                $stats['progress'] = [
                    'total' => $latestRun['total_employees'],
                    'processed' => $latestRun['processed'],
                    'pending' => $latestRun['pending']
                ];
                $stats['pending_runs'] = $latestRun['pending'];
                $stats['total_payroll'] = $latestRun['total_payroll'];
                $stats['period'] = [
                    'period_name' => $latestRun['period_name'],
                    'start_date' => $latestRun['start_date'],
                    'end_date' => $latestRun['end_date']
                ];
            }
        }

        return $stats;
    }
}
