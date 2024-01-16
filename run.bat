@echo off
IF "%1" EQU "1" (
    php -S 0.0.0.0:3000   
) ELSE (
    php -S localhost:3000
)