CalcWorkDays.php
================

Class for doing work day calculations.

###Limitations:
This class works with Unix timestamps and as such is limited to years beetwean 1970 and 2037

###Samples
How many days off work until xmas:

`
CalcWorkDays::workDaysBetween(
  strtotime('today'),
  strtotime('dec 24')
)
`

Is tomorrow a work day:

`
CalcWorkDays::isWorkDay(strtotime('tomorrow'))
`

On what date will we have had 9 dayes of work from next monday:

`
CalcWorkDays::addWorkDays(9, strtotime('monday'))
`
