<div class="w-full ml-16 mt-6">
    <div class="content-wrapper w-auto">
        <div class="card shadow-lg border-0 rounded-4">

            <div class="px-6 py-4 flex items-center justify-between">
                <h2 class="text-2xl font-semibold flex items-center gap-2">
                    <i class="fas fa-receipt"></i>
                    My Payslip Records
                </h2>
            </div>

            <div class="p-6 overflow-x-auto" id="payslip-table-div">
                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">

                    <thead class="bg-blue-400 text-white text-sm uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Employee</th>
                            <th class="px-4 py-3 text-left">Payroll Run</th>
                            <th class="px-4 py-3 text-left">Gross Pay</th>
                            <th class="px-4 py-3 text-left">Deductions</th>
                            <th class="px-4 py-3 text-left">Net Pay</th>
                            <th class="px-4 py-3 text-left">Date Generated</th>
                            <th class="no-print px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        <?php if (!empty($records)): ?>
                            <?php foreach ($records as $r): ?>
                                <tr class="hover:bg-gray-50 transition">

                                    <!-- Employee -->
                                    <td class="px-4 py-3 font-medium text-gray-700">
                                        <?= htmlspecialchars($r['full_name']) ?>
                                    </td>

                                    <!-- Payroll Run -->
                                    <td class="px-4 py-3">
                                        <?= htmlspecialchars($r['payroll_run_id'] ?? 'N/A') ?>
                                    </td>

                                    <!-- Gross Pay -->
                                    <td class="px-4 py-3 text-blue-600 font-semibold">
                                        ₱<?= number_format($r['gross_pay'] ?? 0, 2) ?>
                                    </td>

                                    <!-- Deductions -->
                                    <td class="px-4 py-3 text-red-500 font-semibold">
                                        ₱<?= number_format($r['total_deductions'] ?? 0, 2) ?>
                                    </td>

                                    <!-- Net Pay -->
                                    <td class="px-4 py-3 text-green-600 font-bold">
                                        ₱<?= number_format($r['net_pay'] ?? 0, 2) ?>
                                    </td>

                                    <!-- Date -->
                                    <td class="px-4 py-3 text-gray-500">
                                        <?= !empty($r['generated_at'])
                                            ? date('M d, Y', strtotime($r['generated_at']))
                                            : 'N/A' ?>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-3 text-center space-x-2 no-print flex">

                                        <!-- View -->
                                        <a href="index.php?url=view-payslip&id=<?= $r['payslip_id'] ?>"
                                            class="inline-flex items-center h-9 mt-4 px-3 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                    No payslips found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <!-- Print -->
                <div class="flex justify-end">
                    <div class="mt-4 text-right no-print">
                        <button onclick="printDiv('payslip-table-div')"
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                            <i class="fas fa-print mr-1"></i> Print
                        </button>
                    </div>
                    <div class="mt-8 text-right no-print ml-2">
                        <a href="index.php?url=export-payslip-csv"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                            <i class="fas fa-file-csv mr-1"></i> Export CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>