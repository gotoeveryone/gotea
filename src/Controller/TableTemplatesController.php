<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * Notifications Controller
 *
 * @property \Gotea\Model\Table\TableTemplatesTable $TableTemplates
 * @method \Gotea\Model\Entity\TableTemplate[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TableTemplatesController extends AppController
{
    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event)
    {
        $this->Authorization->authorize($this->request, 'access');

        parent::beforeFilter($event);
    }

    /**
     * 一覧表示処理
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        return $this->renderWith('表テンプレート一覧', 'index');
    }

    /**
     * 新規登録画面表示処理
     *
     * @return \Cake\Http\Response|null
     */
    public function new(): ?Response
    {
        $tableTemplate = $this->TableTemplates->newEntity([]);

        $this->set(compact('tableTemplate'));

        return $this->renderWith('表テンプレート追加');
    }

    /**
     * 編集画面表示処理
     *
     * @param int $id サロゲートキー
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function edit(int $id): ?Response
    {
        $tableTemplate = $this->TableTemplates->get($id, [
            'contain' => [],
        ]);

        $this->set('tableTemplate', $tableTemplate);

        return $this->renderWith('表テンプレート編集');
    }

    /**
     * 新規登録処理
     *
     * @return \Cake\Http\Response|null
     */
    public function create(): ?Response
    {
        $tableTemplate = $this->TableTemplates->newEntity($this->getRequest()->getData());
        if (!$this->TableTemplates->save($tableTemplate)) {
            $this->set(compact('tableTemplate'));

            return $this->renderWithErrors(400, $tableTemplate->getErrors(), '表テンプレート追加', 'new');
        }

        $this->Flash->success(__('The table template has been saved.'));

        return $this->redirect(['_name' => 'table_templates']);
    }

    /**
     * 更新処理
     *
     * @param int $id サロゲートキー
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function update(int $id): ?Response
    {
        $tableTemplate = $this->TableTemplates->get($id);
        $tableTemplate = $this->TableTemplates->patchEntity($tableTemplate, $this->getRequest()->getData());
        if (!$this->TableTemplates->save($tableTemplate)) {
            $this->set(compact('tableTemplate'));

            return $this->renderWithErrors(400, $tableTemplate->getErrors(), '表テンプレート編集', 'edit');
        }

        $this->Flash->success(__('The table template has been saved.'));

        return $this->redirect(['_name' => 'table_templates']);
    }

    /**
     * 削除処理
     *
     * @param int $id サロゲートキー
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function delete(int $id): ?Response
    {
        $tableTemplate = $this->TableTemplates->get($id);
        if ($this->TableTemplates->delete($tableTemplate)) {
            $this->Flash->success(__('The table template has been deleted.'));
        } else {
            $this->Flash->error(__('The table template could not be deleted. Please, try again.'));
        }

        return $this->redirect(['_name' => 'table_templates']);
    }
}
