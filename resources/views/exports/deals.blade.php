<table>
    <thead>
    <tr>
        <th colspan="8" style="text-align: center;  vertical-align: middle; background: #3ba787">
            <b>Сделки</b>
        </th>
    </tr>
    <tr>
        <th style="text-align: center;  vertical-align: middle; width: 150px; background: #d5d5d5">
            <i>Наименование</i>
        </th>
        <th style="text-align: center;  vertical-align: middle; width: 100px; background: #d5d5d5">
            <i>Воронка</i>
        </th>
        <th style="text-align: center;  vertical-align: middle; width: 100px; background: #d5d5d5">
            <i>Стадия</i>
        </th>
        <th style="text-align: center;  vertical-align: middle; width: 150px; background: #d5d5d5">
            <i>Ответственный</i>
        </th>
        <th style="text-align: center;  vertical-align: middle; width: 150px; background: #d5d5d5">
            <i>Клиент</i>
        </th>
        <th style="text-align: center;  vertical-align: middle; width: 150px; background: #d5d5d5">
            <i>Дата создания</i>
        </th>
        <th style="text-align: center;  vertical-align: middle; width: 150px; background: #d5d5d5">
            <i>Поля</i>
        </th>
        <th style="text-align: center;  vertical-align: middle; width: 150px; background: #d5d5d5">
            <i>Значение поля</i>
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($deals as $deal)
        <?php
            $max = $deal->fields->count() === 0 ? 1 : $deal->fields->count();
        ?>
        @for($i = 0; $i < $max; $i++)
            <tr>
                @if($i === 0)
                    <td rowspan="{{ $max }}" style="height: 35px; text-align: center;  vertical-align: middle;" >{{ $deal->name }}</td>
                    <td rowspan="{{ $max }}" style="height: 35px; text-align: center;  vertical-align: middle;">{{ $deal->pipeline->name }}</td>
                    <td rowspan="{{ $max }}" style="height: 35px; text-align: center;  vertical-align: middle;">{{ $deal->stage->name }}</td>
                    <td rowspan="{{ $max }}" style="height: 35px; text-align: center;  vertical-align: middle;">{{ $deal->responsible->name }}</td>
                    <td rowspan="{{ $max }}" style="height: 35px; text-align: center;  vertical-align: middle;">{{ $deal->client->name }}</td>
                    <td rowspan="{{ $max }}" style="height: 35px; text-align: center;  vertical-align: middle;">{{ $deal->created_at }}</td>
                    <td style="height: 35px; text-align: center;  vertical-align: middle;">{{$deal->fields[$i]?->name ?? ''}}</td>
                    <td style="height: 35px; text-align: center;  vertical-align: middle;">{{$deal->fields[$i]?->pivot?->value ?? ''}}</td>
                @else
                    <td style="height: 35px; text-align: center;  vertical-align: middle;">{{$deal->fields[$i]?->name ?? ''}}</td>
                    <td style="height: 35px; text-align: center;  vertical-align: middle;">{{$deal->fields[$i]?->pivot?->value ?? ''}}</td>
                @endif
            </tr>
        @endfor
        <tr>
            <td colspan="8" style="background: #d5d5d5;"></td>
        </tr>
    @endforeach
    </tbody>
</table>
