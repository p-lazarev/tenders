<?php

namespace app\commands;

use app\models\Tender;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Json;


class TenderController extends Controller {

    private int $stored = 0;
    private int $updated = 0;
    private int $error = 0;

    /**
     * This command store tenders from n (10 by default) last pages
     * @param int $page_limit
     * @return int Exit code
     */
    public function actionIndex(int $page_limit = 10)
    {
        $this->logInfo('Parsing started');
        $this->getTenders($page_limit);
        $this->logInfo("Parsing end. Stored: {$this->stored}, Updated: {$this->updated}, Error: {$this->error}");

        return ExitCode::OK;
    }

    private function getTenders(
        int $page_limit = 10,
        string $uri = 'https://public.api.openprocurement.org/api/0/tenders?descending=1'
    ) {
        static $current_page = 1;

        $this->logInfo("Request page {$current_page}");
        $page_json = Json::decode($this->request($uri));

        foreach ($page_json['data'] as $tender_json) {
            $this->logInfo("Getting tender id: {$tender_json['id']}");

            if ($tender = Tender::findOne(['tender_id' => $tender_json['id']])) {
                $this->logInfo("Tender already stored, check for update");

                if ($tender->date_modified == $tender_json['dateModified']) {
                    $this->logInfo("Doesn't need to update");

                    continue;
                }

                $this->logInfo("Getting update");
            } else {
                $tender = new Tender();
            }

            $this->logInfo('Request tender data');
            $tender_data = $this->request("https://public.api.openprocurement.org/api/0/tenders/{$tender_json['id']}");
            $tender_data = Json::decode($tender_data);

            $tender->setAttributes([
                'tender_id' => $tender_data['data']['id'],
                'description' => $tender_data['data']['description'],
                'value_amount' => $tender_data['data']['value']['amount'],
                'date_modified' => $tender_data['data']['dateModified'],
            ]);

            $is_update = (bool) $tender->id;

            if (!$tender->save()) {
                $this->error++;
                $this->logError(['Saving tender error', $tender->errors]);

                continue;
            }

            if ($is_update) {
                $this->updated++;
                $this->logInfo('Update success');
            } else {
                $this->stored++;
                $this->logInfo('Store success');
            }

        }

        if ($page_limit == $current_page) {
            $this->logInfo('Stopped by limit');
            return;
        }

        $this->logInfo('Looking next page');

        if ($page_json['next_page']['uri']) {
            $current_page++;
            $this->getTenders($page_limit, $page_json['next_page']['uri']);
        }

    }

    /**
     * @param $uri
     * @return false|string|void
     */
    private function request($uri)
    {
        try {
            $response = file_get_contents($uri);
        } catch (\Exception $e) {
            $this->logError(["Request error", $e->getMessage()]);

            exit();
        }

        return $response;
    }

    private function log(string $type, $message)
    {
        \Yii::$type($message, 'parser');
    }

    private function logInfo($message)
    {
        $this->log('info', $message);
    }

    private function logError($message)
    {
        $this->log('error', $message);
    }
}