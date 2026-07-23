' ================================================================
' Dental Clinic System - Silent Launcher
' No terminal windows, no flashing - runs completely invisibly
' ================================================================

Set objShell = CreateObject("WScript.Shell")
Set objFSO = CreateObject("Scripting.FileSystemObject")

strPath = objFSO.GetParentFolderName(WScript.ScriptFullName)
phpExe = strPath & "\php\php.exe"
dbFile = strPath & "\database\database.sqlite"

envFile = strPath & "\.env"
envExample = strPath & "\.env.example"

objShell.CurrentDirectory = strPath

' --- ALWAYS clear any stale cached config first ---
' Cached config from the machine that BUILT the installer contains
' absolute paths that won't exist on this PC. Clearing forces Laravel
' to resolve paths fresh, relative to wherever it's actually running.
objShell.Run """" & phpExe & """ artisan config:clear", 0, True
objShell.Run """" & phpExe & """ artisan route:clear", 0, True
objShell.Run """" & phpExe & """ artisan view:clear", 0, True

' --- Ensure .env exists ---
If Not objFSO.FileExists(envFile) Then
    If objFSO.FileExists(envExample) Then
        objFSO.CopyFile envExample, envFile, True
    End If
End If

' --- Check if APP_KEY is actually set (not just whether .env exists) ---
' A previous interrupted/failed install may have left a .env with a
' blank APP_KEY= line, which file-existence checks alone would miss.
Dim needsKey
needsKey = True
If objFSO.FileExists(envFile) Then
    Set envRead = objFSO.OpenTextFile(envFile, 1)
    Do While Not envRead.AtEndOfStream
        line = envRead.ReadLine
        If InStr(line, "APP_KEY=") = 1 Then
            If Len(Trim(line)) > Len("APP_KEY=") Then
                needsKey = False
            End If
        End If
    Loop
    envRead.Close
End If

If needsKey Then
    objShell.Run """" & phpExe & """ artisan key:generate --force", 0, True
End If

' --- Check if database file exists AND has actual tables (not just a blank file) ---
Dim needsMigration
needsMigration = True
If objFSO.FileExists(dbFile) Then
    Set dbFileObj = objFSO.GetFile(dbFile)
    If dbFileObj.Size > 1000 Then
        ' A migrated SQLite db is always well over 1KB - a blank/empty
        ' file from a failed attempt will be 0 bytes or very small
        needsMigration = False
    End If
End If

If needsMigration Then
    If Not objFSO.FileExists(dbFile) Then
        Set dbCreate = objFSO.CreateTextFile(dbFile, True)
        dbCreate.Close
    End If
    objShell.Run """" & phpExe & """ artisan migrate --force", 0, True
End If

' --- Now cache fresh, using THIS machine's correct paths ---
objShell.Run """" & phpExe & """ artisan config:cache", 0, True
objShell.Run """" & phpExe & """ artisan route:cache", 0, True
objShell.Run """" & phpExe & """ artisan view:cache", 0, True

' --- Set up daily automatic backup task (only creates once) ---
checkTask = objShell.Run("schtasks /Query /TN ""ClinicSystemBackup""", 0, True)
If checkTask <> 0 Then
    createCmd = "schtasks /Create /SC DAILY /TN ""ClinicSystemBackup"" /TR """ & _
                phpExe & " " & strPath & "\artisan backup:auto"" /ST 02:00 /F /RL HIGHEST"
    objShell.Run createCmd, 0, True
End If

' --- Start the Laravel server completely hidden ---
objShell.CurrentDirectory = strPath
objShell.Run """" & phpExe & """ artisan serve --port=8001", 0, False

' --- Open the loading page using the system's actual default browser,
'     not just whatever program is associated with .html files ---
Function GetDefaultBrowser()
    On Error Resume Next
    Dim regKey, browserPath
    regKey = "HKCR\http\shell\open\command\"
    browserPath = objShell.RegRead(regKey)
    ' Clean up the registry value - strip quotes and trailing params like "%1"
    browserPath = Replace(browserPath, """", "")
    If InStr(browserPath, ".exe") > 0 Then
        browserPath = Left(browserPath, InStr(browserPath, ".exe") + 3)
    End If
    GetDefaultBrowser = browserPath
    On Error Goto 0
End Function

loadingUrl = "file:///" & Replace(strPath, "\", "/") & "/loading.html"
defaultBrowser = GetDefaultBrowser()

If defaultBrowser <> "" And objFSO.FileExists(defaultBrowser) Then
    objShell.Run """" & defaultBrowser & """ """ & loadingUrl & """", 1, False
Else
    ' Fallback - let Windows figure it out via the http:// protocol handler
    objShell.Run loadingUrl, 1, False
End If

