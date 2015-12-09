@echo off

if exist results.html (
	del results.html
)

if exist output (
	rd /S/Q output
)

if not exist source (
	md /Q source
	goto end
)

php process.php

:end