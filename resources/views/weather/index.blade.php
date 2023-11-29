<?php
    date_default_timezone_set('America/Sao_Paulo');
    $dataHoraAtual = date('d/m/Y H:i:s');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>Previsão do Tempo</title>
  
</head>
<body>

    @php
        // Tradução das descrições do clima
        $descricoesClima = [
            'clear sky' => 'Céu limpo',
            'few clouds' => 'Poucas nuvens',
            'scattered clouds' => 'Nuvens dispersas',
            'broken clouds' => 'Nuvens quebradas',
            'overcast clouds' => 'Céu nublado',
            'shower rain' => 'Chuva',
            'rain' => 'Chuva',
            'thunderstorm' => 'Trovoadas',
            'snow' => 'Neve',
            'mist' => 'Neblina',
            'light rain' => 'Chuva leve',
        ];
    @endphp

    <div id="busca">
        <form method="POST" action="{{ route('clima.search') }}">
            @csrf
            <input type="text" name="city" placeholder="Digite o nome da cidade">
            <button type="submit">Buscar</button>
        </form>
    </div>

    @if (isset($error))
        <p id="erro">{{ $error }}</p>
    @endif

    @if(empty($data))
        <div id="inicial">
            <h1>Bem-vindo ao meu site de Clima Tempo</h1>
            <p>Busque por uma cidade para ver os detalhes da previsão do tempo.</p>
            <img id="img_inicio" src="{{ asset('img/inicio.png') }}" alt="Ícone do clima"> 
        </div>
    @endif

    @if(!empty($data))
        <div id="container">
            @if(isset($data) && $data !== null)
                @if(isset($data['name']))
                    <h2>Tempo agora em {{ $data['name'] }}  </h2>
                    
                @else
                    <p>Cidade não encontrada ou dados não disponíveis.</p>
                @endif

                @if(isset($data['main']['temp']))
                    @php
                        $temperatureCelsius = $data['main']['temp'] - 273.15;
                    @endphp
                    <p><img src="{{ asset('img/clima.png') }}" alt="Ícone do clima"> <span id="temperatura"> {{ number_format($temperatureCelsius, 2) }}°</span></p>
                @endif
            
                @if (isset($data['weather'][0]['description']))
                    @php
                        $descricaoClima = $data['weather'][0]['description'];
                        $descricaoClimaTraduzida = isset($descricoesClima[$descricaoClima]) ? $descricoesClima[$descricaoClima] : $descricaoClima;
                    @endphp
                        <p><span>Descrição:</span> {{ $descricaoClimaTraduzida }}</p>
                @endif

                @if (isset($maxTempCelsius) && isset($minTempCelsius))
                    <p><span>Temperatura Máxima:</span> {{ number_format($maxTempCelsius, 2) }}°C</p>
                    <p><span>Temperatura Mínima:</span> {{ number_format($minTempCelsius, 2) }}°C</p>
                @endif

                <p id="data"><?php echo $dataHoraAtual; ?></p>

                <p id="informacoes"><a href="#" id="linkMaisInformacoes" onclick="toggleMaisInformacoes()">Mais Informações</a></p>

                <div id="maisInformacoes" style="display: none;">

                    @if (isset($data['sys']['sunrise']))
                        @php
                            $sunriseTimeUtc = date('H:i', $data['sys']['sunrise']);
                            $sunriseTimeBrasilia = date('H:i', strtotime("$sunriseTimeUtc"));
                        @endphp
                            <p><span>Nascer do Sol (Brasília):</span> {{ $sunriseTimeBrasilia }}</p>
                    @endif
            
                    @if (isset($data['sys']['sunset']))
                        @php
                            $sunsetTimeUtc = date('H:i', $data['sys']['sunset']);
                            $sunsetTimeBrasilia = date('H:i', strtotime("$sunsetTimeUtc"));
                        @endphp
                            <p><span>Pôr do Sol (Brasília):</span> {{ $sunsetTimeBrasilia }}</p>
                    @endif

                    @if (isset($data['wind']['speed']))
                        @php
                            $windSpeedKmPerHour = $data['wind']['speed'] * 3.6;
                        @endphp
                            <p><span>Velocidade do Vento:</span> {{ number_format($windSpeedKmPerHour, 2) }} km/h</p>
                    @endif

                    @if(isset($data['main']['humidity']))
                        <p><span>Umidade:</span> {{ $data['main']['humidity'] }}%</p>
                    @endif

                    @if (isset($data['main']['feels_like']))
                        @php
                            $feelsLikeCelsius = $data['main']['feels_like'] - 273.15;
                        @endphp
                            <p><span>Sensação Térmica:</span> {{ number_format($feelsLikeCelsius, 2) }}°C</p><br>
                    @endif
                </div>
            
                @if (isset($forecastData))

                <h3>Previsão do Tempo para os próximos 3 dias:</h3>

                <ul>
                    @php
                        $diasSemanaPortugues = [
                            'Sunday' => 'Domingo',
                            'Monday' => 'Segunda-feira',
                            'Tuesday' => 'Terça-feira',
                            'Wednesday' => 'Quarta-feira',
                            'Thursday' => 'Quinta-feira',
                            'Friday' => 'Sexta-feira',
                            'Saturday' => 'Sábado',
                        ];

                        $groupedForecasts = collect($forecastData['list'])->groupBy(function ($item) {
                            // Agrupa as previsões pelo dia (ignorando a hora)
                            return date('Y-m-d', strtotime($item['dt_txt']));
                        });
                    @endphp

                    @php
                        $daysToShow = 0;
                    @endphp

                    @foreach ($groupedForecasts as $date => $forecasts)
                        @if ($date !== date('Y-m-d'))
                            @if ($daysToShow < 3)
                                @php
                                  // Calcula a temperatura média para o dia atual
                                    $temperatureSum = 0;
                                    foreach ($forecasts as $forecast) {
                                        $temperatureSum += ($forecast['main']['temp'] - 273.15);
                                    }

                                    $averageTemperature = $temperatureSum / count($forecasts);

                                    // Pega a descrição do clima em português
                                    $descricaoClima = $forecasts[0]['weather'][0]['description'];
                                    $descricaoClimaTraduzida = isset($descricoesClima[$descricaoClima]) ? $descricoesClima[$descricaoClima] : $descricaoClima;
                                
                                    $dia_semana = date('l', strtotime($date));
                                    $diasemanaportugues = $diasSemanaPortugues[$dia_semana];

                                    // Calcula a temperatura máxima e mínima para o dia atual
                                    $maxTemp = null;
                                    $minTemp = null;
                                    foreach ($forecasts as $forecast) {
                                        $temp = $forecast['main']['temp'];
                                        if ($maxTemp === null || $temp > $maxTemp) {
                                            $maxTemp = $temp;
                                        }
                                        if ($minTemp === null || $temp < $minTemp) {
                                            $minTemp = $temp;
                                        }
                                    }
                                    $maxTempCelsius = ($maxTemp - 273.15);
                                    $minTempCelsius = ($minTemp - 273.15);
                                @endphp

                                <li>
                                    <p>Data: {{ date('d/m/Y', strtotime($date)) }} ({{ $diasemanaportugues }})</p>
                                    <p>Condição: {{ $descricaoClimaTraduzida }}</p>
                                    <p>Temperatura Máxima: {{ number_format($maxTempCelsius, 2) }}°C</p>
                                    <p>Temperatura Mínima: {{ number_format($minTempCelsius, 2) }}°C</p>
                                    <p>Temperatura média: {{ number_format($averageTemperature, 2) }}°C</p><br>
                                    
                                </li>

                                @php
                                    $daysToShow++;
                                @endphp
                            @endif
                            
                        @endif
                    @endforeach
                </ul>
            @endif
            
            @endif
        </div>
    @endif

    <div id="map"></div>

    @if(isset($data) && $data !== null)
        
        <script>

            $(document).ready(function() {
                var latitude = {{ $latitude }}; 
                var longitude = {{ $longitude }}; 
                var apiKey = '7f5c9a5d4c9d7a230eae36dacab55aae'; //chave de API do OpenWeatherMap

                var apiUrl = 'https://api.openweathermap.org/data/2.5/weather?lat=' + latitude + '&lon=' + longitude + '&appid=' + apiKey;

                var map = L.map('map').setView([latitude, longitude], 10);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                $.getJSON(apiUrl, function(data) {
                    var temperature = (data.main.temp - 273.15).toFixed(2) + '°C';
                    var weatherDescription = data.weather[0].description;

                    var marker = L.marker([latitude, longitude]).addTo(map);
                    marker.bindPopup('Temperatura: ' + temperature + '<br>Condição: ' + weatherDescription).openPopup();
                });
            });

            function toggleMaisInformacoes() {
                $('#maisInformacoes').toggle(); // Mostrar ou ocultar a área de mais informações
            }

        </script>
    @endif

    <footer>
        <div id="rodape"><span>Criado por Ricardo Simões - 2023</span></div>    
    </footer>

</body>
</html>
