@echo off

if exist output (
	rd /S/Q output
)

if not exist source (
	md source
	goto end
)

php process.php

:end