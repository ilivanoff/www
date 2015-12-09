@echo off

rd /S/Q www\database\temp
md www\database\temp

echo.
echo exporting db
svn export db www\database\temp\db --force

echo.
echo running dbexport.php
php www\tools\dbexport\dbexport.php
@if ERRORLEVEL 1 goto :error

if exist www\database\temp\ps_test.sql (
	echo creating test schema
	mysql --default-character-set=utf8 --user=root --password=1111 < www\database\temp\ps_test.sql
) else (
	echo test init script was not created
	goto error
)

if not exist www\database\temp\ps.sql (
	echo init script was not created
	goto error
)

if not exist build (
	md build
)

if not exist build\db (
	md build\db
)

echo.
echo copy ps.sql
copy www\database\temp\ps.sql db\ps.sql

echo.
echo moving ps.sql
move www\database\temp\ps.sql build\db\ps.sql

echo.
echo moving ps_test.sql
move www\database\temp\ps_test.sql build\db\ps_test.sql

echo.
echo move dbexport.log
move www\tools\dbexport\dbexport.log build\dbexport.log

echo.
echo remove temp dir
rd /S/Q www\database\temp

echo.
echo Database export success!

goto :end

:error
echo.
echo Database export failed!

:end

rem Если передан какой-либо параметр, то не ожидаем - нужно для nuild.bat
if "%1"=="" (
@pause
)
