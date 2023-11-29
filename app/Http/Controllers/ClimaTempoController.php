<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\ClientException;

class ClimaTempoController extends Controller
{
    public function index()
    {
        $error = session('error');

        return view('weather.index', compact('error'));
    }

    public function search(Request $request)
    {
        $city = $request->input('city');

        if (empty($city)) {
            return view('weather.index')->with('error', 'O nome da cidade deve ser preenchido.');
        }
    
        $apiKey = '7f5c9a5d4c9d7a230eae36dacab55aae';
        $client = new Client();
    
        try {
            // Obter dados atuais do tempo
            $currentWeatherResponse = $client->get("http://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}");
            $currentWeatherData = json_decode($currentWeatherResponse->getBody(), true);
    
            if (isset($currentWeatherData['cod']) && $currentWeatherData['cod'] === 200) {
                // Cidade encontrada, continua com o processamento normal
                $forecastResponse = $client->get("http://api.openweathermap.org/data/2.5/forecast?q={$city}&appid={$apiKey}");
                $forecastData = json_decode($forecastResponse->getBody(), true);
    
                // Obtém as coordenadas geográficas da cidade
                $latitude = $currentWeatherData['coord']['lat'];
                $longitude = $currentWeatherData['coord']['lon'];
    
                // Extrai a temperatura máxima e mínima do objeto $forecastData
                $maxTemp = null;
                $minTemp = null;
                if (isset($forecastData['list'])) {
                    foreach ($forecastData['list'] as $forecast) {
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
                }
    
                return view('weather.index', [
                    'data' => $currentWeatherData,
                    'forecastData' => $forecastData,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'maxTempCelsius' => $maxTempCelsius,
                    'minTempCelsius' => $minTempCelsius,
                ]);
            } else {
                // Cidade não encontrada, retorna para a página inicial com a mensagem de erro
                return view('weather.index')->with('error', 'Cidade não encontrada ou dados não disponíveis.');
            }
        } catch (ClientException $e) {
            // Se a API retornar um erro 404 (Not Found), trata o erro e retorna para a página inicial
            return view('weather.index')->with('error', 'Cidade não encontrada ou dados não disponíveis.');
        }
    }
}    
