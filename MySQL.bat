@ECHO OFF
rem MysSQL bat has 4 arguments
rem 1 - full path to mysql executable
rem 2 - mysql user
rem 3 - mysql password
rem 4 - path to htdocs where hotelmis is installed.
setlocal EnableDelayedExpansion
if exist "%~4\hotelmis\version.txt" (
	ERASE "%~4\hotelmis\version.txt"
)

SET count=0

SET major_version=0
SET minor_version=0
SET patch_version=0

SET new_implementation=true

SET db_exists = 0
SET db_count = 0

%1 --user=%2 --password=%3 < "%~4\hotelmis\checkdb.sql" > "%~4\hotelmis\db.txt"
for /f "delims=" %%x in ('type "%~4\hotelmis\db.txt"') do (
		SET /a db_count = %%x
		if !db_count! GEQ 1 (
			SET /a db_exists = 1
		)
)

if !db_exists!==1 (
	%1 --user=%2 --password=%3 < "%~4\hotelmis\get_version.sql" > "%~4\hotelmis\version.txt"

	for /f "delims=" %%x in ('type "%~4\hotelmis\version.txt"') do (	
		SET /a count=!count! + 1
		if !count! == 2 (
			SET /a major_version = %%x
		)
		if !count! == 4 (
			SET /a minor_version = %%x
		)
		if !count! == 6 (
			SET /a patch_version = %%x
		)
	)
)

if !major_version! GTR 0 (
	SET /a new_implementation=false
)
if !minor_version! GTR 0 (
	SET /a new_implementation=false
)
if !patch_version! GTR 0 (
	SET /a new_implementation=false
)

if !new_implementation!==true (
%1 --user=%2 --password=%3 < "%~4\hotelmis\hotelmis.sql"
) ELSE (
SET patch_major_version=0
SET patch_minor_version=0
SET patch_patch_version=0
SET "r=%~4"
FOR /R  "%~4\hotelmis\patches" %%F IN (*) DO (
  SET "fullfilename=%%F"
  SETLOCAL EnableDelayedExpansion
  rem ECHO(!filename:%r%=!
  for %%F in ("%%F") do SET "filename=%%~nxF"
for /F "delims=" %%a in (^"!filename:_v^=^

!^") do (
	  set "patch=%%a"
	)
  set ^"str=!patch!"
  set i=0
for /F "delims=" %%a in (^"!str:.^=^

!^") do (
	  SET /a i=!i! + 1
	  set "lead=%%a"
	  if !i! == 1 (
		SET /a patch_major_version = %%a
	  )
	  if !i! == 2 (
		SET /a patch_minor_version = %%a
	  )
	  if !i! == 3 (
		SET /a patch_patch_version = %%a@ECHO OFF
rem MysSQL bat has 4 arguments
rem 1 - full path to mysql executable
rem 2 - mysql user
rem 3 - mysql password
rem 4 - path to htdocs where hotelmis is installed.
setlocal EnableDelayedExpansion
if exist "%~4\hotelmis\version.txt" (
	ERASE "%~4\hotelmis\version.txt"
)

if not exist "%~4\SDK\PHP" (
	DEL "%~4\hotelmis\OTA\*.php" /S /Q /F
)

SET count=0

SET major_version=0
SET minor_version=0
SET patch_version=0

SET new_implementation=true

SET db_exists = 0
SET db_count = 0

%1 --user=%2 --password=%3 < "%~4\hotelmis\checkdb.sql" > "%~4\hotelmis\db.txt"
for /f "delims=" %%x in ('type "%~4\hotelmis\db.txt"') do (
		SET /a db_count = %%x
		if !db_count! GEQ 1 (
			SET /a db_exists = 1
		)
)

if !db_exists!==1 (
	%1 --user=%2 --password=%3 < "%~4\hotelmis\get_version.sql" > "%~4\hotelmis\version.txt"

	for /f "delims=" %%x in ('type "%~4\hotelmis\version.txt"') do (	
		SET /a count=!count! + 1
		if !count! == 2 (
			SET /a major_version = %%x
		)
		if !count! == 4 (
			SET /a minor_version = %%x
		)
		if !count! == 6 (
			SET /a patch_version = %%x
		)
	)
)

if !major_version! GTR 0 (
	SET /a new_implementation=false
)
if !minor_version! GTR 0 (
	SET /a new_implementation=false
)
if !patch_version! GTR 0 (
	SET /a new_implementation=false
)

if !new_implementation!==true (
%1 --user=%2 --password=%3 < "%~4\hotelmis\hotelmis.sql"
) ELSE (
SET patch_major_version=0
SET patch_minor_version=0
SET patch_patch_version=0
SET "r=%~4"
FOR /R  "%~4\hotelmis\patches" %%F IN (*) DO (
  SET "fullfilename=%%F"
  SETLOCAL EnableDelayedExpansion
  rem ECHO(!filename:%r%=!
  for %%F in ("%%F") do SET "filename=%%~nxF"
for /F "delims=" %%a in (^"!filename:_v^=^

!^") do (
	  set "patch=%%a"
	)
  set ^"str=!patch!"
  set i=0
for /F "delims=" %%a in (^"!str:.^=^

!^") do (
	  SET /a i=!i! + 1
	  set "lead=%%a"
	  if !i! == 1 (
		SET /a patch_major_version = %%a
	  )
	  if !i! == 2 (
		SET /a patch_minor_version = %%a
	  )
	  if !i! == 3 (
		SET /a patch_patch_version = %%a
	  )
	)

if !patch_major_version! GEQ !major_version! (
	if !patch_major_version! GTR !major_version! (
		%1 --user=%2 --password=%3 < !fullfilename!
	) else (
		if !patch_minor_version! GEQ !minor_version! (
			if !patch_minor_version! GTR !minor_version! (
				%1 --user=%2 --password=%3 < !fullfilename!
			) else (
				if !patch_patch_version! GTR !patch_version! (
					%1 --user=%2 --password=%3 < !fullfilename!
				)
			)
		)
	)
)

  ENDLOCAL
)

)

	  )
	)

if !patch_major_version! GEQ !major_version! (
	if !patch_major_version! GTR !major_version! (
		%1 --user=%2 --password=%3 < !fullfilename!
	) else (
		if !patch_minor_version! GEQ !minor_version! (
			if !patch_minor_version! GTR !minor_version! (
				%1 --user=%2 --password=%3 < !fullfilename!
			) else (
				if !patch_patch_version! GTR !patch_version! (
					%1 --user=%2 --password=%3 < !fullfilename!
				)
			)
		)
	)
)
  ENDLOCAL
)

)
