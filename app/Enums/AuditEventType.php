<?php

namespace App\Enums;

enum AuditEventType: string
{
    case LoginSuccess = 'login_success';
    case LoginFailed = 'login_failed';
    case Logout = 'logout';
    case RecordDownload = 'record_download';
    case RecordView = 'record_view';
}
