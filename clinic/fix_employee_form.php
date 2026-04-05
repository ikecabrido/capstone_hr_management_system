<?php
// Fix for Employee Creation Form - Complete Add Employee Modal
echo "<h2>🔧 Fixed Employee Creation Form</h2>";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px;'>";
echo "<h3>✅ Complete Employee Form Fields:</h3>";
echo "<p>The following fields should be in your Add Employee Modal:</p>";

echo "<div style='background: white; padding: 15px; border-radius: 8px; border: 1px solid #ddd;'>";
echo "<form>";
echo "<div style='display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;'>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>First Name *</label>";
echo "<input type='text' name='first_name' required style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Last Name *</label>";
echo "<input type='text' name='last_name' required style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Middle Name</label>";
echo "<input type='text' name='middle_name' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "</div>";

echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;'>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Email</label>";
echo "<input type='email' name='email' placeholder='employee@bcp.edu.ph' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Phone</label>";
echo "<input type='text' name='phone' placeholder='09XXXXXXXXX' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "</div>";

echo "<div style='margin-bottom: 15px;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Address</label>";
echo "<textarea name='address' rows='2' placeholder='Complete address...' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'></textarea>";
echo "</div>";

echo "<div style='display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;'>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Birth Date</label>";
echo "<input type='date' name='birth_date' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Gender</label>";
echo "<select name='gender' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "<option value=''>Select Gender</option>";
echo "<option value='Male'>Male</option>";
echo "<option value='Female'>Female</option>";
echo "<option value='Other'>Other</option>";
echo "</select>";
echo "</div>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Blood Type</label>";
echo "<select name='blood_type' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "<option value=''>Select Blood Type</option>";
echo "<option value='A+'>A+</option>";
echo "<option value='A-'>A-</option>";
echo "<option value='B+'>B+</option>";
echo "<option value='B-'>B-</option>";
echo "<option value='O+'>O+</option>";
echo "<option value='O-'>O-</option>";
echo "<option value='AB+'>AB+</option>";
echo "<option value='AB-'>AB-</option>";
echo "</select>";
echo "</div>";
echo "</div>";

echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;'>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Department</label>";
echo "<input type='text' name='department' placeholder='e.g., Academic Affairs, Administration' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Position</label>";
echo "<input type='text' name='position' placeholder='e.g., Teacher, Administrative Assistant' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "</div>";

echo "<div style='display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;'>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Employee Type</label>";
echo "<select name='employee_type' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "<option value=''>Select Type</option>";
echo "<option value='Faculty'>Faculty</option>";
echo "<option value='Staff'>Staff</option>";
echo "<option value='Admin'>Admin</option>";
echo "<option value='Support'>Support</option>";
echo "</select>";
echo "</div>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Employment Status</label>";
echo "<select name='employment_status' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "<option value='Active'>Active</option>";
echo "<option value='Inactive'>Inactive</option>";
echo "<option value='Terminated'>Terminated</option>";
echo "</select>";
echo "</div>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Hire Date</label>";
echo "<input type='date' name='hire_date' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "</div>";

echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;'>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Salary</label>";
echo "<input type='number' name='salary' step='0.01' placeholder='0.00' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "<div>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Emergency Contact Name</label>";
echo "<input type='text' name='emergency_contact_name' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "</div>";

echo "<div style='margin-bottom: 15px;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Emergency Contact Phone</label>";
echo "<input type='text' name='emergency_contact_phone' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";

echo "<div style='margin-bottom: 15px;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Allergies</label>";
echo "<textarea name='allergies' rows='2' placeholder='List any known allergies...' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'></textarea>";
echo "</div>";

echo "<div style='margin-bottom: 15px;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Medical Conditions</label>";
echo "<textarea name='medical_conditions' rows='2' placeholder='List any chronic or significant medical conditions...' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'></textarea>";
echo "</div>";

echo "<div style='margin-bottom: 15px;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Current Medications</label>";
echo "<textarea name='current_medications' rows='2' placeholder='List any medications currently being taken...' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'></textarea>";
echo "</div>";

echo "</form>";
echo "</div>";
echo "</div>";

echo "<h3>🔧 Issues Fixed:</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px;'>";
echo "<ul>";
echo "<li><strong>✅ Method Fixed:</strong> Changed from create() to createEmployeeWithPatient()</li>";
echo "<li><strong>✅ Added Missing Fields:</strong> Email, Phone, Address, Birth Date, Gender, Department, Position, Employee Type, Employment Status, Hire Date, Salary</li>";
echo "<li><strong>✅ Enhanced Debugging:</strong> Added comprehensive error logging</li>";
echo "<li><strong>✅ Form Structure:</strong> Proper field organization with labels</li>";
echo "</ul>";
echo "</div>";

echo "<h3>📋 Required HTML for Employee_Patient.php:</h3>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px;'>";
echo "<p>Replace your current Add Employee Modal (around line 407-476) with this complete form:</p>";
echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;'>";
echo "<!-- Add Employee Modal - COMPLETE VERSION -->
<div class=\"modal fade\" id=\"addEmployeeModal\">
    <div class=\"modal-dialog modal-lg\">
        <div class=\"modal-content\">
            <div class=\"modal-header\">
                <h4 class=\"modal-title\">Add New Employee</h4>
                <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
            </div>
            <form method=\"POST\" action=\"Employee_Patient.php\">
                <input type=\"hidden\" name=\"action\" value=\"add_employee\">
                <div class=\"modal-body\">
                    <div class=\"row\">
                        <div class=\"col-md-4\">
                            <div class=\"form-group\">
                                <label>First Name</label>
                                <input type=\"text\" name=\"first_name\" class=\"form-control\" required>
                            </div>
                        </div>
                        <div class=\"col-md-4\">
                            <div class=\"form-group\">
                                <label>Last Name</label>
                                <input type=\"text\" name=\"last_name\" class=\"form-control\" required>
                            </div>
                        </div>
                        <div class=\"col-md-4\">
                            <div class=\"form-group\">
                                <label>Middle Name</label>
                                <input type=\"text\" name=\"middle_name\" class=\"form-control\">
                            </div>
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-md-6\">
                            <div class=\"form-group\">
                                <label>Email</label>
                                <input type=\"email\" name=\"email\" class=\"form-control\" placeholder=\"employee@bcp.edu.ph\">
                            </div>
                        </div>
                        <div class=\"col-md-6\">
                            <div class=\"form-group\">
                                <label>Phone</label>
                                <input type=\"text\" name=\"phone\" class=\"form-control\" placeholder=\"09XXXXXXXXX\">
                            </div>
                        </div>
                    </div>
                    <div class=\"form-group\">
                        <label>Address</label>
                        <textarea name=\"address\" class=\"form-control\" rows=\"2\" placeholder=\"Complete address...\"></textarea>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-md-4\">
                            <div class=\"form-group\">
                                <label>Birth Date</label>
                                <input type=\"date\" name=\"birth_date\" class=\"form-control\">
                            </div>
                        </div>
                        <div class=\"col-md-4\">
                            <div class=\"form-group\">
                                <label>Gender</label>
                                <select name=\"gender\" class=\"form-control\">
                                    <option value=\"\">Select Gender</option>
                                    <option value=\"Male\">Male</option>
                                    <option value=\"Female\">Female</option>
                                    <option value=\"Other\">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class=\"col-md-4\">
                            <div class=\"form-group\">
                                <label>Blood Type</label>
                                <select name=\"blood_type\" class=\"form-control\">
                                    <option value=\"\">Select Blood Type</option>
                                    <option value=\"A+\">A+</option>
                                    <option value=\"A-\">A-</option>
                                    <option value=\"B+\">B+</option>
                                    <option value=\"B-\">B-</option>
                                    <option value=\"O+\">O+</option>
                                    <option value=\"O-\">O-</option>
                                    <option value=\"AB+\">AB+</option>
                                    <option value=\"AB-\">AB-</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-md-6\">
                            <div class=\"form-group\">
                                <label>Department</label>
                                <input type=\"text\" name=\"department\" class=\"form-control\" placeholder=\"e.g., Academic Affairs, Administration\">
                            </div>
                        </div>
                        <div class=\"col-md-6\">
                            <div class=\"form-group\">
                                <label>Position</label>
                                <input type=\"text\" name=\"position\" class=\"form-control\" placeholder=\"e.g., Teacher, Administrative Assistant\">
                            </div>
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-md-4\">
                            <div class=\"form-group\">
                                <label>Employee Type</label>
                                <select name=\"employee_type\" class=\"form-control\">
                                    <option value=\"\">Select Type</option>
                                    <option value=\"Faculty\">Faculty</option>
                                    <option value=\"Staff\">Staff</option>
                                    <option value=\"Admin\">Admin</option>
                                    <option value=\"Support\">Support</option>
                                </select>
                            </div>
                        </div>
                        <div class=\"col-md-4\">
                            <div class=\"form-group\">
                                <label>Employment Status</label>
                                <select name=\"employment_status\" class=\"form-control\">
                                    <option value=\"Active\">Active</option>
                                    <option value=\"Inactive\">Inactive</option>
                                    <option value=\"Terminated\">Terminated</option>
                                </select>
                            </div>
                        </div>
                        <div class=\"col-md-4\">
                            <div class=\"form-group\">
                                <label>Hire Date</label>
                                <input type=\"date\" name=\"hire_date\" class=\"form-control\">
                            </div>
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-md-6\">
                            <div class=\"form-group\">
                                <label>Salary</label>
                                <input type=\"number\" name=\"salary\" class=\"form-control\" step=\"0.01\" placeholder=\"0.00\">
                            </div>
                        </div>
                        <div class=\"col-md-6\">
                            <div class=\"form-group\">
                                <label>Emergency Contact Name</label>
                                <input type=\"text\" name=\"emergency_contact_name\" class=\"form-control\">
                            </div>
                        </div>
                    </div>
                    <div class=\"form-group\">
                        <label>Emergency Contact Phone</label>
                        <input type=\"text\" name=\"emergency_contact_phone\" class=\"form-control\">
                    </div>
                    <div class=\"form-group\">
                        <label>Allergies</label>
                        <textarea name=\"allergies\" class=\"form-control\" rows=\"2\" placeholder=\"List any known allergies...\"></textarea>
                    </div>
                    <div class=\"form-group\">
                        <label>Medical Conditions</label>
                        <textarea name=\"medical_conditions\" class=\"form-control\" rows=\"2\" placeholder=\"List any chronic or significant medical conditions...\"></textarea>
                    </div>
                    <div class=\"form-group\">
                        <label>Current Medications</label>
                        <textarea name=\"current_medications\" class=\"form-control\" rows=\"2\" placeholder=\"List any medications currently being taken...\"></textarea>
                    </div>
                </div>
                <div class=\"modal-footer\">
                    <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Cancel</button>
                    <button type=\"submit\" class=\"btn btn-primary\">Add Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>";
echo "</pre>";
echo "</div>";

echo "<p><strong>🚀 Result:</strong> Employee creation should now work properly with all required fields!</p>";
echo "<p><a href='Employee_Patient.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Back to Employee Management</a></p>";
?>
