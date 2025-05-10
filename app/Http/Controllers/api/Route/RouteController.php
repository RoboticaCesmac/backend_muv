<?php

namespace App\Http\Controllers\api\Route;

use App\Enums\RouteStatusEnum;
use App\Http\Requests\api\Auth\Route\RouteFinishRequest;
use App\Http\Requests\api\Auth\Route\RoutePointRequest;
use Illuminate\Routing\Controller;
use App\Http\Requests\api\Auth\Route\RouteStartRequest;
use App\Http\Resources\Route\RouteIndexResource;
use App\Models\Route;
use App\Services\api\Route\RouteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteController extends Controller
{
    private RouteService $routeService;

    public function __construct(RouteService $routeService)
    {
        $this->routeService = $routeService;
    }

    public function getPaginated(Request $request)
    {
        $routes = $this->routeService->getPaginated($request);

        return RouteIndexResource::collection($routes);
    }

    public function start(RouteStartRequest $request)
    {
        $route = $this->routeService->start($request->validated());

        return response()->json(['message' => 'Rota Iniciada com sucesso']);
    }

    public function finish(RouteFinishRequest $request)
    {
        $route = $this->routeService->finish($request->validated());

        return response()->json(['message' => 'Rota Finalizada com sucesso', 'data' => $route]);
    }

    public function points(RoutePointRequest $request)
    {
        $this->routeService->points($request->validated());

        return response()->json(['message' => 'Ponto adicionado com sucesso']);
    }

    public function routeScreen()
    {
        $user = Auth::user();

        $existsRouteInProgress = Route::where('user_id', $user->id)->where('route_status_id', RouteStatusEnum::getId(RouteStatusEnum::InProgress))->exists();

        return response()->json(['data' => [
            'can_do' => [
                'start' => [
                    'can' => !$existsRouteInProgress,
                    'message' => 'Apenas é possivel iniciar uma rota se não houver uma rota em progresso',
                ],
                'finish' => [
                    'can' => $existsRouteInProgress,
                    'message' => 'Apenas é possivel finalizar uma rota se houver uma rota em progresso',
                ],
            ],
        ]]);
    }
}
