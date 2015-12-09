@echo off
rem svn export https://postupayu.googlecode.com/svn/trunk/postupayu.ru here --force
rem svn info https://postupayu.googlecode.com/svn/trunk/postupayu.ru
rem svn export c:/www/postupayu.ru here --force
rem svn info c:/www/postupayu.ru

echo building project
rd /S/Q build
md build

echo creating database
call database.bat nopause
@if ERRORLEVEL 1 goto :error

echo exporting www
svn export www build\www --force

echo running build.php
php build\www\tools\build\build.php
@if ERRORLEVEL 1 goto :error

echo move build.log
move build\www\tools\build\build.log build\build.log

echo copy MathJax
md build\www\resources\scripts\MathJax
rem xcopy www\resources\scripts\MathJax build\www\resources\scripts\MathJax  /D /e
rem unzip.exe build/www/resources/scripts/MathJax.zip -d build/www/resources/scripts/

echo remove service dir
rd /S/Q build\www\tools

svn info db >build/info.txt

echo.
echo Build SUCCESS!
goto :end

:error
echo.
echo Build FAILED!

:end
@pause