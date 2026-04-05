<div class="w-full ml-8">
    <div class="content-wrapper w-full">
        <div class="card shadow-lg border-0 rounded-4 w-full">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

                <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                    <h2 class="text-6xl font-semibold flex items-center gap-2">
                        Medical Records
                    </h2>
                    <p class="text-sm text-blue-100">Patient visits and consultations</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">

                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3 text-left">Patient</th>
                                <th class="px-6 py-3 text-left">Visit</th>
                                <th class="px-6 py-3 text-left">Complaint</th>
                                <th class="px-6 py-3 text-left">Diagnosis</th>
                                <th class="px-6 py-3 text-left">Type</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Medical Assistant</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            <?php if (!empty($records)): ?>
                                <?php foreach ($records as $r): ?>
                                    <tr class="hover:bg-blue-50 transition duration-150">

                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-800">
                                                <?= htmlspecialchars($r['full_name'] ?? 'N/A') ?>
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                ID: <?= htmlspecialchars($r['patient_id']) ?>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-gray-600">
                                            <?= !empty($r['visit_date'])
                                                ? date('M d, Y', strtotime($r['visit_date']))
                                                : 'N/A' ?>
                                            <div class="text-xs text-gray-400">
                                                <?= !empty($r['visit_date'])
                                                    ? date('h:i A', strtotime($r['visit_date']))
                                                    : '' ?>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-gray-700 max-w-[200px] truncate">
                                            <?= htmlspecialchars($r['chief_complaint'] ?? 'N/A') ?>
                                        </td>

                                        <td class="px-6 py-4 text-gray-700 max-w-[200px] truncate">
                                            <?= htmlspecialchars($r['diagnosis'] ?? 'N/A') ?>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded-full font-semibold
                                    <?= $r['consultation_type'] === 'Emergency' ? 'bg-red-100 text-red-700' : ($r['consultation_type'] === 'Walk-in' ? 'bg-blue-100 text-blue-700' : ($r['consultation_type'] === 'Appointment' ? 'bg-purple-100 text-purple-700' :
                                        'bg-gray-100 text-gray-700')) ?>">
                                                <?= htmlspecialchars($r['consultation_type'] ?? 'N/A') ?>
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded-full font-semibold
                                    <?= $r['status'] === 'Completed' ? 'bg-green-100 text-green-700' : ($r['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-700' :
                                        'bg-indigo-100 text-indigo-700') ?>">
                                                <?= htmlspecialchars($r['status']) ?>
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-gray-600">
                                            <?= htmlspecialchars($r['attending_physician'] ?? 'N/A') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-10 text-gray-400">
                                        🩺 No medical records found
                                    </td>
                                </tr>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>