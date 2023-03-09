<?php

namespace App\Enums;

use App\Models\Pipeline;
use App\Models\Stage;
use App\Models\TaskStage;
use App\Models\User;
use App\Traits\EnumUpdateTrait;

enum ActionsEnum: string
{
    use EnumUpdateTrait;

    case COMMENT = 'comment';
    case CHANGE_PIPELINE = 'change_pipeline';
    case CHANGE_STAGE = 'change_stage';
    case CHANGE_TASK_STAGE = 'change_task_stage';
    case CHANGE_RESPONSIBLE = 'change_responsible';
    case CHANGE_MANAGER = 'change_manager';
    case CHANGE_EXECUTOR = 'change_executor';
    case CHANGE_START_TIME = 'change_start_time';
    case CHANGE_END_TIME = 'change_end_time';
    case CHANGE_NAME = 'change_name';
    case CHANGE_DESCRIPTION = 'change_description';
    case CHANGE_CLIENT_NAME = 'change_client_name';

    public static function definitionActionsByRequest(): ?array
    {
        return [
            ActionsEnum::COMMENT->value => 'new_comment.content',
            ActionsEnum::CHANGE_PIPELINE->value => 'pipeline_id',
            ActionsEnum::CHANGE_STAGE->value => 'stage_id',
            ActionsEnum::CHANGE_TASK_STAGE->value => 'task_stage_id',
            ActionsEnum::CHANGE_RESPONSIBLE->value => 'responsible_id',
            ActionsEnum::CHANGE_MANAGER->value => 'manager_id',
            ActionsEnum::CHANGE_EXECUTOR->value => 'executor_id',
            ActionsEnum::CHANGE_START_TIME->value => 'start',
            ActionsEnum::CHANGE_END_TIME->value => 'end',
            ActionsEnum::CHANGE_NAME->value => 'name',
            ActionsEnum::CHANGE_DESCRIPTION->value => 'description',
            ActionsEnum::CHANGE_CLIENT_NAME->value => 'client.name',
        ];
    }

    //Для получения наименования нового значения если передан id сущности
    public static function getEntity($value): ?string
    {
        return match ($value) {
            ActionsEnum::CHANGE_PIPELINE->value => Pipeline::class,
            ActionsEnum::CHANGE_STAGE->value => Stage::class,
            ActionsEnum::CHANGE_TASK_STAGE->value => TaskStage::class,
            ActionsEnum::CHANGE_RESPONSIBLE->value,
            ActionsEnum::CHANGE_MANAGER->value,
            ActionsEnum::CHANGE_EXECUTOR->value => User::class,
            default => null
        };
    }

    //Для получения старых значений
    public static function getRelation($value): ?string
    {
        return match ($value) {
            ActionsEnum::CHANGE_PIPELINE->value => 'pipeline.name',
            ActionsEnum::CHANGE_STAGE->value, ActionsEnum::CHANGE_TASK_STAGE->value => 'stage.name',
            ActionsEnum::CHANGE_RESPONSIBLE->value => 'responsible.name',
            ActionsEnum::CHANGE_MANAGER->value => 'manager.name',
            ActionsEnum::CHANGE_EXECUTOR->value => 'executor.name',
            ActionsEnum::CHANGE_START_TIME->value => 'start',
            ActionsEnum::CHANGE_END_TIME->value => 'end',
            ActionsEnum::CHANGE_NAME->value => 'name',
            ActionsEnum::CHANGE_DESCRIPTION->value => 'description',
            ActionsEnum::CHANGE_CLIENT_NAME->value => 'client.name',
            default => null
        };
    }

    //Определение сообщения
    public static function getMessageTemplate($value): string
    {
        return match ($value) {
            ActionsEnum::COMMENT->value => 'Комментарий',
            ActionsEnum::CHANGE_PIPELINE->value => 'Смена воронки',
            ActionsEnum::CHANGE_STAGE->value => 'Смена стадии',
            ActionsEnum::CHANGE_TASK_STAGE->value => 'Смена статуса',
            ActionsEnum::CHANGE_RESPONSIBLE->value => 'Смена ответственного',
            ActionsEnum::CHANGE_MANAGER->value => 'Смена наблюдателя',
            ActionsEnum::CHANGE_EXECUTOR->value => 'Смена исполнителя',
            ActionsEnum::CHANGE_START_TIME->value => 'Смена даты старта задачи',
            ActionsEnum::CHANGE_END_TIME->value => 'Смена даты окончания задачи',
            ActionsEnum::CHANGE_NAME->value => 'Смена наименования',
            ActionsEnum::CHANGE_DESCRIPTION->value => 'Смена описания',
            ActionsEnum::CHANGE_CLIENT_NAME->value => 'Смена имени клиента',
            default => ''
        };
    }
}
