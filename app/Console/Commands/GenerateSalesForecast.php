<?php

namespace App\Console\Commands;

use App\Services\SalesForecastingService;
use Illuminate\Console\Command;

class GenerateSalesForecast extends Command
{
    protected $signature = 'forecast:generate 
                            {--type=all : Period type (daily, weekly, monthly, all)}
                            {--historical=90 : Days of historical data to use}
                            {--forecast=30 : Days to forecast ahead}';

    protected $description = 'Generate sales forecasts based on historical data';

    public function handle(): int
    {
        $type = $this->option('type');
        $historical = (int) $this->option('historical');
        $forecast = (int) $this->option('forecast');

        $this->info("Generating {$type} forecasts...");
        $this->info("Using {$historical} days of historical data");
        $this->info("Forecasting {$forecast} days ahead");

        try {
            $service = new SalesForecastingService($historical, $forecast);

            $bar = $this->output->createProgressBar($type === 'all' ? 3 : 1);
            $bar->start();

            $results = match($type) {
                'daily' => ['daily' => $service->generateDailyForecast()],
                'weekly' => ['weekly' => $service->generateWeeklyForecast()],
                'monthly' => ['monthly' => $service->generateMonthlyForecast()],
                default => $service->generateAllForecasts(),
            };

            $bar->finish();
            $this->newLine(2);

            foreach ($results as $period => $forecasts) {
                $this->info("✓ {$period}: {$forecasts->count()} forecasts generated");
            }

            // Show accuracy if available
            $this->newLine();
            $this->info('Evaluating model accuracy...');
            
            $accuracy = $service->evaluateAccuracy();
            if ($accuracy['mape'] !== null) {
                $this->table(
                    ['Metric', 'Value'],
                    [
                        ['MAPE', $accuracy['mape'] . '%'],
                        ['Accuracy', $accuracy['accuracy'] . '%'],
                        ['Samples', $accuracy['samples']],
                        ['Rating', $accuracy['interpretation']],
                    ]
                );
            } else {
                $this->warn($accuracy['message']);
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}