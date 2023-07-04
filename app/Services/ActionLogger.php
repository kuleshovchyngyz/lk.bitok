<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Events\LogActionEvent;

class ActionLogger
{
    public static function log($user)
    {
        $description = 'Добавил клиента: №'.$user->id.'; '.
                        'Имя: '.$user->last_name.' '.$user->first_name.' '.$user->middle_name.'; '.
                        'Дата рождения: '.$user->birth_date.'; '.
                        'Страна: '.$user->country->name.'; '.
                        'Дата регистрации: '.$user->created_at.'; '.
                        'ИНН: '.$user->pass_num_inn.'; '.
                        'Паспорт ID: '.$user->passport_id.'; '.
                        'Орган выдавший паспорт: '.$user->passport_authority.'; '.
                        'Код подразделения: '.$user->passport_authority_code.'; '.
                        'Дата выдачи паспорта: '.$user->passport_issued_at.'; '.
                        'Дата окончания актульности паспорта: '.$user->passport_expires_at.'; '.
                        'Уровень риска: '.$user->sanction.';';
        $today = Carbon::today();
        $author = Auth::user()->id;
        event(new LogActionEvent($today, $author, $description));
    }
}
