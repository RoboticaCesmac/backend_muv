<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Route;
use App\Models\Vehicle;
use App\Enums\RouteStatusEnum;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UsersExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = Carbon::parse($startDate)->startOfMonth();
        $this->endDate = Carbon::parse($endDate)->endOfMonth();
    }

    public function hasData(): bool
    {
        return Route::where('route_status_id', RouteStatusEnum::getId(RouteStatusEnum::Completed))
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->exists();
    }

    public function sheets(): array
    {
        $sheets = [];
        $currentDate = $this->startDate->copy();

        while ($currentDate <= $this->endDate) {
            // Check if there's data for this month
            $hasData = Route::where('route_status_id', RouteStatusEnum::getId(RouteStatusEnum::Completed))
                ->whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->exists();

            if ($hasData) {
                $monthName = ucfirst($currentDate->locale('pt_BR')->format('F Y'));
                $sheets[] = new UsersSheet($currentDate->copy(), $monthName);
            }

            $currentDate->addMonth();
        }

        return $sheets;
    }
}

class UsersSheet implements FromCollection, WithHeadings
{
    protected $date;
    protected $sheetName;

    public function __construct($date, $sheetName)
    {
        $this->date = $date;
        $this->sheetName = $sheetName;
    }

    public function collection(): Collection
    {
        return Route::select(
            'users.id',
            'users.user_name',
            'users.email',
            DB::raw('COUNT(routes.id) as total_routes'),
            DB::raw('SUM(routes.distance_km) as total_distance'),
            DB::raw('SUM(routes.carbon_footprint) as total_carbon_footprint'),
            DB::raw('SUM(routes.points) as total_points'),
            DB::raw('AVG(routes.carbon_footprint) as average_carbon_footprint'),
            DB::raw('AVG(routes.distance_km) as average_distance'),
            DB::raw('SUM(CASE WHEN routes.vehicle_id IN (' . Vehicle::VEHICLES['gasoline_car'] . ', ' . Vehicle::VEHICLES['diesel_car'] . ') THEN 1 ELSE 0 END) as total_car_routes'),
            DB::raw('SUM(CASE WHEN routes.vehicle_id = ' . Vehicle::VEHICLES['walking'] . ' THEN 1 ELSE 0 END) as total_walking_routes'),
            DB::raw('SUM(CASE WHEN routes.vehicle_id = ' . Vehicle::VEHICLES['bicycle'] . ' THEN 1 ELSE 0 END) as total_bicycle_routes'),
            DB::raw('SUM(CASE WHEN routes.vehicle_id = ' . Vehicle::VEHICLES['bus'] . ' THEN 1 ELSE 0 END) as total_bus_routes')
        )
        ->join('users', 'routes.user_id', '=', 'users.id')
        ->whereMonth('routes.created_at', $this->date->month)
        ->whereYear('routes.created_at', $this->date->year)
        ->where('routes.route_status_id', RouteStatusEnum::getId(RouteStatusEnum::Completed))
        ->groupBy('users.id', 'users.user_name', 'users.email')
        ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome do Usuário',
            'Email',
            'Total de Rotas',
            'Distância Total (KM)',
            'Total de Carbono Gerado',
            'Total de Pontos',
            'Média de Carbono por Rota',
            'Média de Distância por Rota',
            'Total de Rotas de Carro',
            'Total de Rotas a Pé',
            'Total de Rotas de Bicicleta',
            'Total de Rotas de Ônibus'
        ];
    }

    public function title(): string
    {
        return $this->sheetName;
    }
} 