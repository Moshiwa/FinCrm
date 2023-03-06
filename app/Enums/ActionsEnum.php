<?php

namespace App\Enums;

enum ActionsEnum: string
{
    case COMMENT = 'comment';
    case CHANGE_PIPELINE = 'change_pipeline';
    case CHANGE_STAGE = 'change_stage';
    case CHANGE_TASK_STAGE = 'change_task_stage';
    case CHANGE_RESPONSIBLE = 'change_responsible';
    case CHANGE_MANAGER = 'change_manager';
    case CHANGE_EXECUTOR = 'change_executor';
    case CHANGE_START_TIME = 'change_start_time';
    case CHANGE_END_TIME = 'change_end_time';

    public static function fromName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if( $name === $case->name ){
                return $case;
            }
        }

        return null;
    }

    public static function getEntity($value): ?string
    {
        return match ($value) {
            ActionsEnum::CHANGE_PIPELINE->value => 'Pipeline',
            ActionsEnum::CHANGE_STAGE->value => 'Stage',
            ActionsEnum::CHANGE_TASK_STAGE->value => 'TaskStage',
            ActionsEnum::CHANGE_RESPONSIBLE->value,
            ActionsEnum::CHANGE_MANAGER->value,
            ActionsEnum::CHANGE_EXECUTOR->value => 'User',
            default => null
        };
    }

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
            default => null
        };
    }

    public static function getMessageTemplate($value): string
    {
        return match ($value) {
            ActionsEnum::COMMENT->value => 'Комментарий',
            ActionsEnum::CHANGE_PIPELINE->value => 'Смена воронки с <i style="color: #0B90C4">[ActionValue]</i> на <i style="color: #0B90C4">[ActionValue]</i><br>',
            ActionsEnum::CHANGE_STAGE->value => 'Смена стадии с <i style="color: #0B90C4">[ActionValue]</i> на <i style="color: #0B90C4">[ActionValue]</i><br>',
            ActionsEnum::CHANGE_TASK_STAGE->value => 'Смена статуса с <i style="color: #0B90C4">[ActionValue]</i> на <i style="color: #0B90C4">[ActionValue]</i><br>',
            ActionsEnum::CHANGE_RESPONSIBLE->value => 'Смена ответственного с <i style="color: #0B90C4">[ActionValue]</i> на <i style="color: #0B90C4">[ActionValue]</i><br>',
            ActionsEnum::CHANGE_MANAGER->value => 'Смена наблюдателя с <i style="color: #0B90C4">[ActionValue]</i> на <i style="color: #0B90C4">[ActionValue]</i><br>',
            ActionsEnum::CHANGE_EXECUTOR->value => 'Смена исполнителя с <i style="color: #0B90C4">[ActionValue]</i> на <i style="color: #0B90C4">[ActionValue]</i><br>',
            ActionsEnum::CHANGE_START_TIME->value => 'Смена даты старта задачи с <i style="color: #0B90C4">[ActionValue]</i> на <i style="color: #0B90C4">[ActionValue]</i><br>',
            ActionsEnum::CHANGE_END_TIME->value => 'Смена даты окончания задачи с <i style="color: #0B90C4">[ActionValue]</i> на <i style="color: #0B90C4">[ActionValue]</i><br>',
            default => ''
        };
    }
}
