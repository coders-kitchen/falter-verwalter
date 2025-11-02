<div class="space-y-6">
    <div>
        <h3 class="text-2xl font-bold mb-4">🔄 Lebenszykluskalender</h3>

        @if (count($calendarData) > 0)
            <!-- Legend -->
            <div class="mb-6 p-4 bg-base-200 rounded-lg">
                <h4 class="font-semibold mb-3">Legende:</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-blue-500 rounded"></div>
                        <span class="text-sm">Raupe</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-green-500 rounded"></div>
                        <span class="text-sm">Flugmonate</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-gray-300 rounded"></div>
                        <span class="text-sm">Inaktiv</span>
                    </div>
                </div>
            </div>

            <!-- Calendar Grid for each Generation -->
            <div class="space-y-8">
                @foreach ($calendarData as $generation)
                    <div class="space-y-2">
                        <h4 class="font-semibold text-lg">
                            {{ $generation['number'] }}. Generation
                        </h4>

                        <!-- Responsive Calendar -->
                        <div class="overflow-x-auto">
                            <table class="table table-compact w-full border-collapse">
                                <thead>
                                    <tr class="bg-base-200 border-b-2 border-base-300">
                                        @foreach ($months as $month)
                                            <th class="text-center text-xs md:text-sm p-2 w-1/12">
                                                {{ $month }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach ($generation['months'] as $monthData)
                                            <td class="p-1 md:p-2 border border-base-300 text-center h-16 md:h-20">
                                                <div class="h-full flex flex-col items-center justify-center gap-1">
                                                    @if (in_array('larva', $monthData['types']))
                                                        <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-500 rounded flex items-center justify-center text-white text-sm" title="Raupe">
                                                            🐛
                                                        </div>
                                                    @endif
                                                    @if (in_array('flight', $monthData['types']))
                                                        <div class="w-8 h-8 md:w-10 md:h-10 bg-green-500 rounded flex items-center justify-center text-white text-sm" title="Flugmonate">
                                                            🦋
                                                        </div>
                                                    @endif
                                                    @if (empty($monthData['types']))
                                                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gray-300 rounded"></div>
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Info Box -->
            <div class="mt-8 alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h3 class="font-bold">Hinweis</h3>
                    <div class="text-sm">
                        <ul class="list-disc pl-4 mt-2">
                            <li>🐛 <strong>Raupe:</strong> Zeitraum, in dem die Raupen aktiv sind und fressen</li>
                            <li>🦋 <strong>Flugmonate:</strong> Zeitraum, in dem die erwachsenen Schmetterlinge aktiv und flugfähig sind</li>
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M7.08 6.47A9 9 0 119.5 20.5M7 12a5 5 0 1110 0"></path></svg>
                <div>
                    <h3 class="font-bold">Keine Generationsdaten</h3>
                    <div class="text-sm">
                        Es sind noch keine Generationsdaten für diese Art verfügbar.
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
