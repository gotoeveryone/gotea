<?php
declare(strict_types=1);

namespace Gotea\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\I18n\FrozenDate;
use Cake\Log\Log;
use Gotea\Client\S3Client;
use SplFileObject;
use Throwable;

/**
 * TitleScore command.
 *
 * @property \Gotea\Model\Table\TitleScoresTable $TitleScores
 */
class TitleScoreCommand extends Command
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->TitleScores = $this->fetchTable('TitleScores');
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int Success or error code.
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        Log::info('タイトル成績の出力処理を開始します。');

        try {
            $scores = $this->TitleScores->findSummaryScores();

            $processDate = FrozenDate::now()->i18nFormat('yyyy-MM-dd');
            $file = new SplFileObject("{$processDate}.csv", 'wr+');

            $scores = $scores->all()->map(function ($score) {
                return [
                    $score->started_timestamp,
                    $score->player1_id,
                    $score->player2_id,
                    $score->winner_player_no,
                ];
            })->toArray();
            foreach ($scores as $fields) {
                if (!$file->fputcsv($fields, ',')) {
                    Log::error('ファイルへの書き込みに失敗しました。');

                    return self::CODE_ERROR;
                }
            }

            $s3Client = new S3Client();
            $s3Client->upload($file, 'predict_scores/input.csv', 'text/csv');

            return self::CODE_SUCCESS;
        } catch (Throwable $ex) {
            Log::error($ex->getMessage());

            throw $ex;
        } finally {
            Log::info('タイトル成績の出力処理を終了します。');
        }
    }
}
