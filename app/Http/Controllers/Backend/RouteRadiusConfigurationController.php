<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RouteRadiusConfiguration;
use App\Models\Distributor;
use Illuminate\Support\Facades\Auth;

class RouteRadiusConfigurationController extends Controller
{
    public function index()
    {
        $configurations = RouteRadiusConfiguration::with(['distributor', 'tso', 'routes'])->get();
        return view('pages.RouteRadiusConfigurations.index', compact('configurations'));
    }

    public function create()
    {
        $distributors = Distributor::status()->get();
        return view('pages.RouteRadiusConfigurations.create', compact('distributors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'tso_id' => 'required|exists:tso,id',
            'radius' => 'required|numeric|min:1',
            'route_ids' => 'required|array',
            'route_ids.*' => 'exists:routes,id',
        ]);

        $config = RouteRadiusConfiguration::updateOrCreate(
            ['tso_id' => $request->tso_id, 'distributor_id' => $request->distributor_id],
            [
                'radius' => $request->radius,
                'created_by' => Auth::id()
            ]
        );

        $config->routes()->sync($request->route_ids);

        return redirect()->route('route-radius-configurations.index')->with('success', 'Configuration saved successfully.');
    }

    public function edit($id)
    {
        $configuration = RouteRadiusConfiguration::with('routes')->findOrFail($id);
        $distributors = Distributor::status()->get();
        return view('pages.RouteRadiusConfigurations.edit', compact('configuration', 'distributors'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'tso_id' => 'required|exists:tso,id',
            'radius' => 'required|numeric|min:1',
            'route_ids' => 'required|array',
            'route_ids.*' => 'exists:routes,id',
        ]);

        $config = RouteRadiusConfiguration::findOrFail($id);
        $config->update([
            'distributor_id' => $request->distributor_id,
            'tso_id' => $request->tso_id,
            'radius' => $request->radius,
        ]);

        $config->routes()->sync($request->route_ids);

        return redirect()->route('route-radius-configurations.index')->with('success', 'Configuration updated successfully.');
    }

    public function destroy($id)
    {
        $config = RouteRadiusConfiguration::findOrFail($id);
        $config->routes()->detach();
        $config->delete();

        return redirect()->route('route-radius-configurations.index')->with('success', 'Configuration deleted successfully.');
    }
}
