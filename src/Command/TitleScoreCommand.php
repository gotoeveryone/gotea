<?php
declare(strict_types=1);

namespace Gotea\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\I18n\FrozenDate;
use Cake\Log\Log;
use Gotea\Utility\FileBuilder;
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
            $total = $scores->count();

            $processDate = FrozenDate::now()->i18nFormat('yyyy-MM-dd');
            $file = new SplFileObject("{$processDate}.csv", 'wa');

            $limit = 10000;
            $offset = 0;

            while ($offset < $total) {
                $tmpScores = $scores->limit($limit)->offset($offset)->map(function ($score) {
                    return [
                        $score->started_timestamp,
                        $score->player1_id,
                        $score->player2_id,
                        $score->winner_player_no,
                    ];
                });
                $end = $offset + iterator_count($tmpScores);
                Log::info("出力対象: {$offset}件目 ~ {$end}件目");
                foreach ($tmpScores as $fields) {
                    if (!$file->fputcsv($fields, ',')) {
                        Log::error('ファイルへの書き込みに失敗しました。');

                        return self::CODE_ERROR;
                    }
                }
                $offset = $offset + $limit;
            }

            return self::CODE_SUCCESS;
        } catch (Throwable $ex) {
            throw $ex;
        } finally {
            Log::info('タイトル成績の出力処理を終了します。');
        }
    }
}
