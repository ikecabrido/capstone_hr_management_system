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
            'lifetime' => $this->model->getLifetimePayroll()
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
            }
        }

        return $stats;
    }
}
