<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\BitMobilityDataImporterJob;
use App\Jobs\BoltDataImporterJob;
use App\Jobs\DottDataImporterJob;
use App\Jobs\HulajDataImporterJob;
use App\Jobs\LimeDataImporterJob;
use App\Jobs\QuickDataImporterJob;
use App\Jobs\SpinDataImporterJob;
use App\Models\ImportInfo;

class DataImporterService
{
    private array $importerJobs = [
        BitMobilityDataImporterJob::class,
        BoltDataImporterJob::class,
        DottDataImporterJob::class,
        HulajDataImporterJob::class,
        LimeDataImporterJob::class,
        SpinDataImporterJob::class,
        QuickDataImporterJob::class,
    ];

    public function run(string $whoRunsIt = "admin"): void
    {
        $importInfo = ImportInfo::query()->create([
            "who_runs_it" => $whoRunsIt,
            "status" => "started",
        ]);

        foreach ($this->importerJobs as $importerJob) {
            dispatch(new $importerJob($importInfo->id))->onQueue("importers");
        }
    }
}
