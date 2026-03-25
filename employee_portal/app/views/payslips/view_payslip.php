<div class="w-full ml-32">
    <div class="content-wrapper w-full">

        <!-- Card Container -->
        <div class="card shadow-lg border-0 rounded-4 w-full">

            <!-- Back Button -->
            <button onclick="window.history.back()"
                class="mb-6 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back
            </button>

            <!-- Payslip Content -->
            <div class="w-full flex justify-center py-8 bg-gray-100" id="payslip-card-div">
                <div class="bg-white shadow-lg rounded-xl p-6 w-full max-w-md border border-gray-200">

                    <!-- Employee Info -->
                    <div class="mb-4 text-gray-700 text-sm">
                        <table class="w-full text-sm">
                            <tr>
                                <td class="font-semibold">Employee:</td>
                                <td><?= htmlspecialchars($payslip['first_name'] . ' ' . $payslip['last_name']) ?></td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Position:</td>
                                <td><?= htmlspecialchars($payslip['position'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Type:</td>
                                <td><?= htmlspecialchars($payslip['employment_type'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Date:</td>
                                <td><?= date("M d, Y", strtotime($payslip['created_at'] ?? '')) ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Payslip Table -->
                    <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-blue-500 text-white text-left">
                            <tr>
                                <th class="p-2">Description</th>
                                <th class="p-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">

                            <!-- Gross Pay -->
                            <tr class="bg-gray-100 font-semibold">
                                <td class="p-2">Gross Pay</td>
                                <td class="p-2 text-right">₱<?= number_format($payslip['gross_pay'], 2) ?></td>
                            </tr>

                            <!-- Earnings -->
                            <?php if (!empty($payslip['earnings'])): ?>
                                <tr class="bg-gray-50 font-semibold">
                                    <td colspan="2" class="p-2">Allowances / Earnings</td>
                                </tr>
                                <?php foreach ($payslip['earnings'] as $e): ?>
                                    <tr>
                                        <td class="p-2"><?= htmlspecialchars($e['description']) ?></td>
                                        <td class="p-2 text-right text-green-600">₱<?= number_format($e['amount'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Deductions -->
                            <?php if (!empty($payslip['deductions'])): ?>
                                <tr class="bg-gray-50 font-semibold">
                                    <td colspan="2" class="p-2">Deductions</td>
                                </tr>
                                <?php foreach ($payslip['deductions'] as $d): ?>
                                    <tr>
                                        <td class="p-2"><?= htmlspecialchars($d['description']) ?></td>
                                        <td class="p-2 text-right text-red-500">₱<?= number_format($d['amount'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Net Pay -->
                            <tr class="bg-gray-100 font-bold">
                                <td class="p-2">Net Pay</td>
                                <td class="p-2 text-right text-green-700">₱<?= number_format($payslip['net_pay'], 2) ?></td>
                            </tr>

                            <!-- Fallback -->
                            <?php if (empty($payslip['earnings']) && empty($payslip['deductions'])): ?>
                                <tr>
                                    <td colspan="2" class="p-2 text-center text-gray-500">No items found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Footer -->
                    <div class="mt-6 text-center text-gray-500 text-xs">
                        This is a computer-generated payslip and does not require a signature.
                    </div>

                    <!-- Print Button -->
                    <div class="mt-4 text-center">
                        <button onclick="printDiv('payslip-card-div')"
                            class="no-print inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-print mr-2"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>