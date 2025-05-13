<?php
require_once __DIR__ . '/../config/database.php';

class WeatherModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function saveWeatherData($city, $apiKey) {
        $url = "http://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=" . $apiKey . "&units=metric";
        $response = @file_get_contents($url);
        if ($response === FALSE) {
            return ["status" => "error", "message" => "Failed to fetch weather data"];
        }

        $weatherData = json_decode($response, true);
        if ($weatherData && $weatherData['cod'] == 200) {
            $temperature = $weatherData['main']['temp'];
            $weather_state = $weatherData['weather'][0]['description'];
            $humidity = $weatherData['main']['humidity'];
            $wind_speed = $weatherData['wind']['speed'];

            $sql = "INSERT INTO weather_data (city_name, temperature, weather_state, humidity, wind_speed) 
                    VALUES (:city_name, :temperature, :weather_state, :humidity, :wind_speed)";
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':city_name', $city);
            $stmt->bindParam(':temperature', $temperature);
            $stmt->bindParam(':weather_state', $weather_state);
            $stmt->bindParam(':humidity', $humidity);
            $stmt->bindParam(':wind_speed', $wind_speed);

            try {
                $stmt->execute();
                return ["status" => "success", "message" => "Weather data saved successfully"];
            } catch(PDOException $e) {
                return ["status" => "error", "message" => "Error: " . $e->getMessage()];
            }
        }
        return ["status" => "error", "message" => "Invalid weather data"];
    }
}
?>