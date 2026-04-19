<?php

require_once __DIR__ . '/../models/EnrollmentProgram.php';

class EnrollmentProgramController
{
    private $enrollmentModel;
    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentProgram();
    }
}
