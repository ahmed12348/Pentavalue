<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RecommendationsController extends Controller
{
    public function index()
    {
        try {
            // Get recent sales (last 24 hours)
            $recentSales = DB::select("
                SELECT product_id, SUM(quantity) as total_sold
                FROM orders
                WHERE date >= datetime('now', '-1 day')
                GROUP BY product_id
                ORDER BY total_sold DESC
            ");

            // Fetch weather from OpenWeather API
            $weatherApiKey = env('OPENWEATHER_API_KEY');
            $temp = null;
            $weatherPrompt = "Weather data unavailable.";
            
            if ($weatherApiKey && $weatherApiKey !== 'your_openweather_api_key_here') {
                try {
                    $city = 'London'; // Change to your city
                    $weatherResponse = Http::withOptions(['verify' => false])->get("https://api.openweathermap.org/data/2.5/weather", [
                        'q' => $city,
                        'appid' => $weatherApiKey,
                        'units' => 'metric'
                    ]);
                    
                    if ($weatherResponse->successful()) {
                        $temp = $weatherResponse->json('main.temp');
                        $weatherPrompt = $temp > 25 ? "It's hot, promote cold drinks." : "It's cold, promote hot drinks.";
                    }
                } catch (\Exception $e) {
                    $weatherPrompt = "Weather service unavailable.";
                }
            }

            // Prepare prompt for OpenAI
            $prompt = "Given these recent sales: " . json_encode($recentSales) . ". $weatherPrompt Suggest product promotions or pricing strategies.";

            // Call OpenAI API
            $suggestion = null;
            $openaiApiKey = env('OPENAI_API_KEY');
            
            if ($openaiApiKey && $openaiApiKey !== 'your_openai_api_key_here') {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $openaiApiKey,
                    ])->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are a retail sales expert.'],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'max_tokens' => 150,
                    ]);
                    
                    if ($response->successful()) {
                        $suggestion = $response->json('choices.0.message.content');
                    }
                } catch (\Exception $e) {
                    $suggestion = "AI service temporarily unavailable.";
                }
            }

            return response()->json([
                'suggestion' => $suggestion ?? 'Please configure OpenAI API key for AI recommendations.',
                'weather' => $temp,
                'recent_sales' => $recentSales,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while processing recommendations.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
