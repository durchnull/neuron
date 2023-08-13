@props([
    'begin' => now(),
    'end' => now(),
    'events' => []
])

@php
    $dayWidth = 100 / $begin->startOfDay()->diffInDays($end->startOfDay());

     usort($events, function ($event1, $event2) {
        return $event1['begin'] > $event2['begin'];
    });

    $tempEvents = [];

    $groupIndex = 0;

    foreach ($events as $event) {
        if (empty($tempEvents)) {
            $tempEvents[$groupIndex][] = $event;
        } else {
            foreach ($tempEvents as $group => $groupedEvents) {
                $overlap = false;

                foreach ($groupedEvents as $groupedEvent) {
                    $period = \Carbon\CarbonPeriod::create($event['begin'], $event['end']);
                    $groupedPeriod = \Carbon\CarbonPeriod::create($groupedEvent['begin'], $groupedEvent['end']);

                    $overlap = $groupedPeriod->overlaps($period);

                    if ($overlap) {
                        break;
                    }
                }

                if (!$overlap) {
                    $tempEvents[$group][] = $event;
                }
            }
        }
    }

    $events = $tempEvents;
@endphp
<div class="flex flex-col overflow-hidden">
    <div class="flex items-center">
        @foreach(\Carbon\CarbonPeriod::create($begin, $end) as $index => $date)
            <div class="group relative px-2 border-l {{ $loop->last ? 'border-r' : '' }} hover:border-gray-400"
                 style="width:{{ $dayWidth }}%"
            >
                <div>
                    <x-parsing.date :value="$date"
                                    class="{{ $date->isToday() ? '' : 'opacity-0 group-hover:opacity-100' }}"/>
                </div>
                <span class="text-xs">{{ $date->shortDayName }}</span>
            </div>
        @endforeach
    </div>
    <div class="my-4"
    >
        @foreach($events as $group => $_events)
            <div class="relative">
                @foreach($_events as $event)
                    @php
                        $leftDays = $event['begin']->copy()->startOfDay()->diffInDays($begin->copy()->startOfDay());
                        $durationDays = $event['begin']->copy()->startOfDay()->diffInDays($event['end']->copy()->startOfDay());

                        if ($event['begin']->isSameDay($event['end'])) {
                            $durationDays = 1;
                        }
                        $active = now() >= $event['begin'] && now() < $event['end'];
                    @endphp
                    <div class="{{ $loop->first ? 'relative' : 'absolute top-0 left-0' }} group py-1"
                         style="left:{{ $leftDays * $dayWidth }}%;"
                    >
                        <div class="my-2">
                            <x-typography.title>{{ $event['title'] }}</x-typography.title>
                        </div>
                        <div class="flex h-1 {{ $active ? 'bg-green-500' : 'bg-gray-400' }} rounded-full"
                             style="width:{{ $durationDays * $dayWidth }}%;"
                        ></div>
                        <div class="my-2 text-gray-400 opacity-0 group-hover:opacity-100">
                            <x-parsing.date-time :value="$event['begin']"/>
                            -
                            <x-parsing.date-time :value="$event['end']"/>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
