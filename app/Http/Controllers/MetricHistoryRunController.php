<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MetricHistoryRun;
use App\Models\Strategy;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class MetricHistoryRunController extends Controller
{
    public function index( Request $request )
    {

        if ($request->ajax()) {
            $metrics = MetricHistoryRun::orderBy('id', 'desc')->with('strategy')->paginate(10);
            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);
        }

        
        $categories = Category::all();
        $strategies = Strategy::all();
    
        return view('metric-history-run.index', compact('categories', 'strategies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'categories' => 'required|array',
            'strategy' => 'required|exists:strategies,id',
        ]);

        // Simula la lógica para obtener las métricas desde una API
        $metrics = [
            'accessibility_metric' => rand(0, 100),
            'pwa_metric' => rand(0, 100),
            'performance_metric' => rand(0, 100),
            'seo_metric' => rand(0, 100),
            'best_practices_metric' => rand(0, 100),
        ];

        // Guarda los datos en la base de datos
        \App\Models\MetricHistoryRun::create(array_merge($metrics, [
            'url' => $request->url,
        ]));

        return redirect()->back()->with('success', 'Métricas obtenidas y guardadas exitosamente.');
    }

    public function fetchMetrics(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'categories' => 'required|array',
            'strategy' => 'required|string',
        ]);

        $url = $request->input('url');
        $categories = $request->input('categories');
        $strategy = $request->input('strategy');

        //Example: https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=https://broobe.com&key=AIzaSyDCrPAzhzWxZbJxPYIEURODTvBFVVRNHbY&category=PERFORMANCE&category=SEO&category=BEST_PRACTICES&category=ACCESSIBILITY&strategy=MOBILE
        
        $apiKey = 'AIzaSyDCrPAzhzWxZbJxPYIEURODTvBFVVRNHbY'; // Reemplaza con tu API key
        $categoriesQuery = implode('&category=', $categories);
        $apiUrl = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url={$url}&key={$apiKey}&category={$categoriesQuery}&strategy={$strategy}";

        try {
            $client = new Client();
            $response = $client->get($apiUrl);

            $data = json_decode($response->getBody(), true);
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveMetrics(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'metrics' => 'required|array',
            'strategy_id' => 'required|exists:strategies,id',
        ]);

        try {
            \App\Models\MetricHistoryRun::create([
                'url' => $request->url,
                'strategy_id' => $request->strategy_id,
                'accessibility_metric' => $request->metrics['accessibility_metric'],
                'pwa_metric' => $request->metrics['pwa_metric'],
                'performance_metric' => $request->metrics['performance_metric'],
                'seo_metric' => $request->metrics['seo_metric'],
                'best_practices_metric' => $request->metrics['best_practices_metric'],
            ]);

            return response()->json(['success' => true, 'message' => 'Métricas guardadas exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

}