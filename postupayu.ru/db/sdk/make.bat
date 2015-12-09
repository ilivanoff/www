@echo off

set SDK_DB_DIR=%CD%
cd ..
set MAIN_DB_DIR=%CD%

echo.
echo Sdk  db dir: [%SDK_DB_DIR%]
echo Main db dir: [%MAIN_DB_DIR%]

echo.
echo Updating main db directory
rem svn update

cd ..\www\sdk\processes\makeschema
set MAKESCHEMA_DIR=%CD%
set MAKESCHEMA_DIR_TMP=%CD%\temp

echo.
echo Make schema path: [%MAKESCHEMA_DIR%]
echo Make schema temp: [%MAKESCHEMA_DIR_TMP%]

rd /S/Q %MAKESCHEMA_DIR_TMP%
md %MAKESCHEMA_DIR_TMP%

echo.
echo Exporting [%MAIN_DB_DIR%] to [%MAKESCHEMA_DIR_TMP%]
svn export %MAIN_DB_DIR% %MAKESCHEMA_DIR_TMP% --force

echo.
echo Running dbexport.php
php dbexport.php
@if ERRORLEVEL 1 goto :error

echo.
echo Schema successfully made
echo Copy [%MAKESCHEMA_DIR_TMP%] to [%MAIN_DB_DIR%]

xcopy /s/y %MAKESCHEMA_DIR_TMP% %MAIN_DB_DIR%

echo.
echo Database SUCCESSFULLY processed!
goto :end

:error
echo.
echo Database export failed!

:end

rem Если передан какой-либо параметр, то не ожидаем - нужно для build.bat
if "%1"=="" (
@pause
)
