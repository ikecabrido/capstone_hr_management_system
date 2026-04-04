<?php
namespace App\Controllers;

use App\Models\Employee;

class EmployeeController
{
    private $employee;

    public function __construct()
    {
        $this->employee = new Employee();
    }

    public function index()
    {
        return $this->employee->all();
    }

    public function show($id)
    {
        return $this->employee->find($id);
    }

    public function store($data)
    {
        return $this->employee->create($data);
    }

    public function update($id, $data)
    {
        return $this->employee->updateEmployee($id, $data);
    }

    public function destroy($id)
    {
        return $this->employee->deleteEmployee($id);
    }
}
