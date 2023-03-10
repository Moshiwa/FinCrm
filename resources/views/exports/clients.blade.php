<table>
    <thead>
    <tr>
        <th colspan="5" style="text-align: center; vertical-align: middle; background: #3ba787">
            <b>Клиенты</b>
        </th>
    </tr>
    <tr>
        <th style="text-align: center; vertical-align: middle; width: 200px; background: #d5d5d5">
            <i>Имя</i>
        </th>
        <th style="text-align: center; vertical-align: middle; width: 200px; background: #d5d5d5">
            <i>Дата создания</i>
        </th>
        <th style="text-align: center; vertical-align: middle; width: 200px; background: #d5d5d5">
            <i>Поля</i>
        </th>
        <th style="text-align: center; vertical-align: middle; width: 200px; background: #d5d5d5">
            <i>Значение поля</i>
        </th>
        <th style="text-align: center; width: 200px; background: #d5d5d5">
            <i>Сделки</i>
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($clients as $client)
            <?php
                $max = max($client->fields->count(), $client->deals->count());
                $max = $max === 0 ? 1 : $max;
            ?>
        @for($i = 0; $i < $max; $i++)
            <tr>
                @if($i === 0)
                    <td rowspan="{{ $max }}" style="height: 35px; text-align: center; vertical-align: middle;" >{{ $client->name }}</td>
                    <td rowspan="{{ $max }}" style="height: 35px; text-align: center; vertical-align: middle;">{{ $client->created_at }}</td>
                    <td style="height: 35px; text-align: center; vertical-align: middle;">{{$client->fields[$i]?->name ?? ''}}</td>
                    <td style="height: 35px; text-align: center; vertical-align: middle;">{{$client->fields[$i]?->pivot?->value ?? ''}}</td>
                    <td style="height: 35px; text-align: center; vertical-align: middle;">{{ ($client->deals[$i]?->name ?? '') . (empty($client->deals[$i]?->id) ? '' : "(id:{$client->deals[$i]?->id})") }}</td>
                @else
                    <td style="height: 35px; text-align: center; vertical-align: middle;">{{$client->fields[$i]?->name ?? ''}}</td>
                    <td style="height: 35px; text-align: center; vertical-align: middle;">{{$client->fields[$i]?->pivot?->value ?? ''}}</td>
                    <td style="height: 35px; text-align: center; vertical-align: middle;">{{ ($client->deals[$i]?->name ?? '') . (empty($client->deals[$i]?->id) ? '' : "(id:{$client->deals[$i]?->id})") }}</td>
                @endif
            </tr>
        @endfor
        <tr>
            <td colspan="5" style="background: #d5d5d5;"></td>
        </tr>
    @endforeach
    </tbody>
</table>
