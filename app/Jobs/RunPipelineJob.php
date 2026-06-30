<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use App\Models\PipelineRun;
use Throwable;

class RunPipelineJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $pipelineRunId;

    public function __construct(int $pipelineRunId)
    {
        $this->pipelineRunId = $pipelineRunId;
    }

    public function handle(): void
    {
        $pipelineRun = PipelineRun::findOrFail($this->pipelineRunId);

        $pipelineRun->update([
            'status' => 'running',
            'started_at' => now(),
        ]);

        Artisan::call('pipeline:run', [
            'pipelineId' => $pipelineRun->id,
        ]);
    }

    public function failed(Throwable $e): void
    {
        PipelineRun::where('id', $this->pipelineRunId)->update([
            'status' => 'failed',
            'finished_at' => now(),
        ]);
    }
}
