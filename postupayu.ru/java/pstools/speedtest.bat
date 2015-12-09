@echo off
cd bin\
rem javac SpeedTest.java
echo starting java process
java SpeedTest

rem echo error level: %errorlevel%
if not %errorlevel% == 0 goto end
echo opening report
cd ..\SpeedReports\
start last.html
:end
