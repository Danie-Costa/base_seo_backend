<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PipelineRun;
use App\Models\PipelineAgentRun;
use Illuminate\Support\Facades\Artisan;

class RunPipelineCommand extends Command
{
    /**
     * Nome e assinatura do comando
     */
    protected $signature = 'pipeline:run 
        {pipelineId : ID da pipeline}';

    /**
     * Descrição do comando
     */
    protected $description = 'Executa uma pipeline';

    /**
     * Lógica do comando
     */
    public function handle(): int
    {
        $pipelineId = $this->argument('pipelineId');
        $this->info("▶ Iniciando pipeline #{$pipelineId}");

        $pipelineRun = PipelineRun::find($pipelineId);
        if (!$pipelineRun) {
            $this->error("❌ Pipeline #{$pipelineId} não encontrada.");
            return Command::FAILURE;
        }
        if($pipelineRun->status == 'pending') {
            $pipelineRun->status = 'running';
            $pipelineRun->started_at = now();
            $pipelineRun->save();             
            $this->info("⚙ Pipeline #{$pipelineId} está em execução.");
        }
        Artisan::call('pipeline:orchestrator', [
            'pipelineRunId' => $pipelineRun->id,
        ]);
        $this->info('✅ Pipeline finalizada com sucesso');
        return Command::SUCCESS;
    }
}
