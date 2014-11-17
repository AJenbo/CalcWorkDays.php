<?php
require 'CalcWorkDays.php';

echo 'How many days off work until xmas: ';
echo CalcWorkDays::workDaysBetween(strtotime('today'), strtotime('dec 24')) . "\n";

echo 'Is tomorrow a work day: ';
echo (CalcWorkDays::isWorkDay(strtotime('tomorrow')) ? 'Yes' : 'No') . "\n";

echo 'On what date will we have had 9 dayes of work from next monday: ';
echo date('Y-m-d', CalcWorkDays::addWorkDays(9, strtotime('monday'))) . "\n";