@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../se/selenium-server-standalone/bin/selenium-server-standalone.jar
java -jar "%BIN_TARGET%" %*
