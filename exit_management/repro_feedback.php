<?php
require 'models/ExitInterviewModel.php';
$model = new ExitInterviewModel();
var_dump($model->getFeedbackByInterview(1));
