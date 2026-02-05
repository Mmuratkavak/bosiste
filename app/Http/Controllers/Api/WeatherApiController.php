<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\WeatherSnapshot;
use Illuminate\Http\Request;

class WeatherApiController extends Controller {
    public function store(Request $request) {
        $data = $request->input('hava_durumu');
        if (is_string($data)) $data = json_decode($data, true);
        WeatherSnapshot::create(['payload' => $data]);
        return response()->json(['status' => 'success']);
    }
}
