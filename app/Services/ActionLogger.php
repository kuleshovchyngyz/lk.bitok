<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Events\LogActionEvent;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class ActionLogger
{
    public static function log($mainVariable, $controller, $method)
    {
        $methods = array("store"=>"Добавил", "update"=>"Отредактировал", "destroy"=>"Удалил");
        
        if ($controller === 'AddedUserController') {
            $description = $methods[$method].' клиента: №'.$mainVariable->id.'; '.
                            'Имя: '.$mainVariable->last_name.' '.$mainVariable->first_name.' '.$mainVariable->middle_name.'; '.
                            'Дата рождения: '.$mainVariable->birth_date->format('d/m/Y').'; '.
                            'Страна: '.$mainVariable->country->name.'; '.
                            'Дата регистрации: '.$mainVariable->created_at->format('d/m/Y').'; '.
                            'ИНН: '.$mainVariable->pass_num_inn.'; '.
                            'Паспорт ID: '.$mainVariable->passport_id.'; '.
                            'Орган выдавший паспорт: '.$mainVariable->passport_authority.'; '.
                            'Код подразделения: '.$mainVariable->passport_authority_code.'; '.
                            'Дата выдачи паспорта: '.$mainVariable->passport_issued_at.'; '.
                            'Дата окончания актульности паспорта: '.$mainVariable->passport_expires_at.'; '.
                            'Уровень риска: '.$mainVariable->sanction.'.';
        }
        elseif ($controller === 'LegalEntityController') {
            if (!$mainVariable->stock) {
                $description = $methods[$method].' юридическое лицо: №'.$mainVariable->id.'; '.
                                'Тип: Юридическое лицо; '.
                                'Название: '.$mainVariable->name.'; '.
                                'Адрес: '.$mainVariable->address.'; '.
                                'Имя директора: '.$mainVariable->director_full_name.'; '.
                                'Дата рождения: '.$mainVariable->birth_date->format('d/m/Y').'; '.
                                'Страна: '.$mainVariable->country->name.'; '.
                                'Дата регистрации: '.$mainVariable->created_at->format('d/m/Y').'; '.
                                'Верификация: '.($mainVariable->verification ? $mainVariable->verification : 'не верифицирована').'; '.
                                'Дата верификации: '.($mainVariable->verification_date ? $mainVariable->verification_date->format('d/m/Y') : 'не верифицирована').'; '.
                                'Уровень риска: '.$mainVariable->sanction.'.';
            } 
            else {
                $description = $methods[$method].' юридическое лицо: №'.$mainVariable->id.'; '.
                                'Тип: Биржа; '.
                                'Название: '.$mainVariable->name.'; '.
                                'Адрес: '.$mainVariable->address.'; '.
                                'IBAN: '.$mainVariable->iban.'; '.
                                'Банковский счет: '.$mainVariable->bank_account.'; '.
                                'Название банка: '.$mainVariable->bank_name.'; '.
                                'Swift: '.$mainVariable->swift.'; '.
                                'Код аккаунта: '.$mainVariable->account_code.'; ';
            }
            
        }
        elseif ($controller === 'UserOperationController') {
            $description = $methods[$method].' операцию: №'.$mainVariable->id.'; '.
                            ($mainVariable->user_id ? ('Пользователь: '.$mainVariable->addedUser->last_name.' '.$mainVariable->addedUser->first_name.' '.$mainVariable->addedUser->middle_name.'; ') : 
                            'Юридическое лицо: '.$mainVariable->legalEntity->name).'; '.
                            'Направление: '.$mainVariable->operation_direction.'; '.
                            'Валюта: '.$mainVariable->currency.'; '.
                            'Дата операции: '.$mainVariable->operation_date->format('d/m/Y').'; '.
                            'Сумма: '.$mainVariable->operation_sum.'; '.
                            'Номер кошелька: '.$mainVariable->wallet_id.'; '.
                            'Уровень риска: '.$mainVariable->sanction.'.';
        }
        elseif ($controller === 'UserController') {
            if ($method === 'destroy') {        
                $description = $methods[$method].' пользователя: №'.$mainVariable->id.'; '.
                                'Имя: '.$mainVariable->name.'; '.
                                'Email: '.$mainVariable->email.'; '.
                                'Роль: '.$mainVariable->role.'.';
            } else {
                $description = $methods[$method].' пользователя: №'.$mainVariable->id.'; '.
                                'Имя: '.$mainVariable->name.'; '.
                                'Email: '.$mainVariable->email.'; '.
                                'Роль: '.$mainVariable->roles[0]->name.'.';
            }
        }
        
        $today = Carbon::today();
        $author = Auth::user()->id;
        event(new LogActionEvent($today, $author, $description));
    }
}
